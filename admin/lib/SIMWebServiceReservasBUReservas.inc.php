<?php
// SCRIPT CON LOS NUEVOS SERVICIOS QUE TIENEN ALGO QUE VER CON DE RESERVAS.

class SIMWebServiceReservas
{
    public function get_configuracion_reservas($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT TipoInicio,MostrarBotonMisListasEspera,LabelBotonMisListasEspera,MostrarHistorialMisReservas,TextoBotonHistorialMisReservas FROM ConfiguracionReservas WHERE IDClub = $IDClub AND Activo = 1";
            $QRYDatos = $dbo->query($SQLDatos);
            if ($dbo->rows($QRYDatos) > 0) :
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $IDClub;
                    $InfoDatos[TipoInicio] = $Datos[TipoInicio];
                    $InfoDatos[MostrarBotonMisListasEspera] = $Datos[MostrarBotonMisListasEspera];
                    $InfoDatos[LabelBotonMisListasEspera] = $Datos[LabelBotonMisListasEspera];
                    $InfoDatos[MostrarHistorialMisReservas] = $Datos[MostrarHistorialMisReservas];
                    $InfoDatos[TextoBotonHistorialMisReservas] = $Datos[TextoBotonHistorialMisReservas];

                    array_push($response, $InfoDatos);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_categorias_reservas($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLDatos = "SELECT * FROM CategoriasServicios WHERE IDClub = $IDClub AND Activo = 1";
            $QRYDatos = $dbo->query($SQLDatos);

            if ($dbo->rows($QRYDatos) > 0) :

                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                while ($Datos = $dbo->fetchArray($QRYDatos)) :

                    $InfoDatos[IDClub] = $IDClub;
                    $InfoDatos[IDSeccion] = $Datos[IDCategoriasServicios];
                    $InfoDatos[Nombre] = $Datos[Nombre];
                    $InfoDatos[Icono] = SERVICIO_ROOT . $Datos[Icono];
                    $InfoDatos[SoloIcono] = $Datos[SoloIcono];

                    array_push($response, $InfoDatos);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_clubes_asociados_reserva($IDClub, $IDSocio)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            // SACAMOS EL IDCLUBPADRE DEL CLUB 
            $SQLPadre = "SELECT IDClubPadre FROM Club WHERE IDClub = $IDClub ";
            $QRYPadre = $dbo->query($SQLPadre);
            $Padre = $dbo->fetchArray($QRYPadre);

            // CLUB DEL SOCIO
            $SQLClubes = "SELECT * FROM SocioClubPermiso WHERE IDSocio = $IDSocio";
            $QRYClubes = $dbo->query($SQLClubes);
            if ($dbo->rows($QRYClubes)) :

                if ($Padre[IDClubPadre] != 157) :
                    $ClubSocio = $dbo->getFields("Socio", "IDClub", "IDSocio = $IDSocio");
                    $ClubesPosibles[] = $ClubSocio;
                endif;

                //$ClubSocio = $dbo->getFields("Socio","IDClub","IDSocio = $IDSocio");
                //$ClubesPosibles[] = $ClubSocio;

                while ($Datos = $dbo->fetchArray($QRYClubes)) :
                    $ClubesPosibles[] = $Datos[IDClub];
                endwhile;

                $Clubes = implode(",", $ClubesPosibles);
                $CondicionClubes = " AND IDClub IN ($Clubes)";

            elseif ($Padre[IDClubPadre] == 157) :

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;

                return $respuesta;
            endif;

            if ($Padre[IDClubPadre] > 0) :

                $SQLClubes = "SELECT SoloIconoSedes, IDClub, Foto, Nombre FROM Club WHERE IDClubPadre = $Padre[IDClubPadre] $CondicionClubes";
                $QRYClubes = $dbo->query($SQLClubes);

                if ($dbo->rows($QRYClubes) > 0) :

                    $message = $dbo->rows($QRYClubes) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    while ($DatosClub = $dbo->fetchArray($QRYClubes)) :

                        $InfoDatos[IDClub] = $DatosClub[IDClub];
                        $InfoDatos[Nombre] = $DatosClub[Nombre];
                        $InfoDatos[Icono] = CLUB_ROOT . $DatosClub[Foto];
                        $InfoDatos[SoloIcono] = $DatosClub[SoloIconoSedes];

                        array_push($response, $InfoDatos);
                    endwhile;

                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;

                else :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;

            else :
                $respuesta["message"] = "El Club no tiene otras sedes por favor cambiar la configuración de las reservas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function Abonos($IDClub, $ValorReserva, $IDServicio, $datos_club, $datos_club_otros, $datos_persona)
    {
        $dbo = SIMDB::get();
        $response = array();

        $SQLAbonosServicio = "SELECT * FROM PorcentajeAbono WHERE IDClub = $IDClub AND IDServicio = $IDServicio AND Activo = 1";
        $QRYAbonosServicio = $dbo->query($SQLAbonosServicio);

        while ($Abonos = $dbo->fetchArray($QRYAbonosServicio)) :

            $InfoResponse[IDPorcentajeAbono] = $Abonos[IDPorcentajeAbono];
            $InfoResponse[ValorPorcentaje] = $Abonos[Porcentaje] . "%";

            $ValorAPagarPorcentaje = ($Abonos[Porcentaje] * $ValorReserva) / 100;

            $TextoValor = number_format($ValorAPagarPorcentaje, 0, ",", ".");
            $ValorAPagarPorcentajeTexto = $datos_club_otros["SignoPago"] . " " . $TextoValor . " " . $datos_club_otros["TextoPago"];

            $InfoResponse[ValorAPagarPorcentajeTexto] = $ValorAPagarPorcentajeTexto;
            $InfoResponse[ValorAPagarPorcentaje] = $ValorAPagarPorcentaje;

            /* SACAMOS TODOS LOS PARAMETROS PARA EL ARREGLO POST QUE SE ENVIA A LAS PASARELA DE PAGOS */

            $moneda = "COP";
            $refVenta = time();
            $llave_encripcion = $datos_club["ApiKey"];
            $usuarioId = $datos_club["MerchantId"];
            $accountId = (string) $datos_club["AccountId"];
            $descripcion = "Abono Mi Club " . $datos_club[Nombre];
            $extra1 = $Abonos[IDPorcentajeAbono];
            $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda";
            $firma = md5($firma_cadena);

            $emailSocio = $datos_persona["CorreoElectronico"];
            if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                $emailComprador = $emailSocio;
            } else {
                $emailComprador = "";
            }

            // ESTA URLS SE VUELVEN DINAMICAS DEPENDIENDO DE LA MODALIDAD DE PAGO; SE PUEDEN VER EN LOS PAGOS SIMPasarelaPagos.inc.php
            $url_respuesta = URLROOT . "respuesta_transaccion.php";
            $url_confirmacion = URLROOT . "confirmacion_pagos.php";

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
            $datos_post["valor"] = $accountId;
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
            $datos_post["valor"] = (string) $ValorPagado;
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

            $datos_post["llave"] = "Modulo";
            $datos_post["valor"] = (string) "Taloneras";
            array_push($response_parametros, $datos_post);

            $datos_post["llave"] = "IDSocio";
            $datos_post["valor"] = $IDSocio;
            array_push($response_parametros, $datos_post);

            $InfoResponse["ParametrosPost"] = $response_parametros;

            $datos_post_pago = array();
            $datos_post_pago["iva"] = 0;
            $datos_post_pago["purchaseCode"] = $refVenta;
            $datos_post_pago["totalAmount"] = $ValorAPagarPorcentaje * 100;
            $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
            $InfoResponse["ParametrosPaGo"] = $datos_post_pago;

            array_push($response, $InfoResponse);

        endwhile;

        return $response;
    }



    public function get_servicios($IDClub, $TipoApp = "", $IDUsuario = "", $IDSocio = "", $IDCategoriasServicios = "", $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMWebServicePublicidad.inc.php";
        require_once LIBDIR . "SIMWebServiceSorteos.inc.php";

        $response = array();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;

            $SQLPermisoServicio = "SELECT DISTINCT IDServicio FROM SocioPermisoReserva WHERE IDSocio = $IDSocio AND IDClub = $IDClubAsociado";
            $QRYPermisoServicio = $dbo->query($SQLPermisoServicio);
            if ($dbo->rows($QRYPermisoServicio) > 0) :
                while ($Datos = $dbo->fetchArray($QRYPermisoServicio)) :
                    $arrayservicios[] = $Datos[IDServicio];
                endwhile;
                $servicios = implode(",", $arrayservicios);
                $condicionserviciosespeciales = " AND IDServicio IN ($servicios)";
            endif;
        endif;

        //Consulto los modulos que se configuraron para un socio especifico
        $sql_tiposoc_mod = "SELECT IDServicioMaestro,InvitadoSeleccion From PermisoSocioModulo Where IDClub = '" . $IDClub . "' and InvitadoSeleccion <> '' ";
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

            if ($TipoApp != "Empleado") {
                $array_mod_esp = explode("|", $row_tiposoc_mod["IDServicioMaestro"]);
                foreach ($array_mod_esp as $IDModulo) {
                    if (!empty($IDModulo)) {
                        $array_id_mod_esp[$IDModulo] = $array_soc_id;
                    }
                }
            }
        }
        //FIN Consulto los modulos que se configuraron para un socio especifico

        //reviso si tienen modulo personalizado por perfil de socio
        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
        $sql_servicio_perfil = "Select * From TipoSocioModulo Where IDClub = '" . $IDClub . "' and TipoSocio = '" . $TipoSocio . "' Limit 1";
        $r_servicio_perfil = $dbo->query($sql_servicio_perfil);
        if ($dbo->rows($r_servicio_perfil) > 0) {
            $row_servicio_perfil = $dbo->fetchArray($r_servicio_perfil);
            if (!empty($row_servicio_perfil["IDServicioMaestro"])) {
                $array_id_servicio = explode("|", $row_servicio_perfil["IDServicioMaestro"]);
                if (count($array_id_servicio) > 0) {
                    $id_servicio_ver = implode(",", $array_id_servicio);

                    if ($row_servicio_perfil[Ocultar] == 1) :
                        $condicion_servicio = " and IDServicioMaestro not in (" . $id_servicio_ver . ")";
                    else :
                        $condicion_servicio = " and IDServicioMaestro in (" . $id_servicio_ver . ")";
                    endif;
                }
            }
        }


        //Consulto los servicios activos del club
        $sql_servicio_club = "Select * From  ServicioClub Where IDClub = '" . $IDClub . "' and Activo = 'S'";
        $qry_servicio_club = $dbo->query($sql_servicio_club);
        $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
        while ($r_servicio_club = $dbo->fetchArray($qry_servicio_club)) {
            $array_id_servicios[] = $r_servicio_club["IDServicioMaestro"];
            $array_nom_servicios[$r_servicio_club["IDServicioMaestro"]] = $r_servicio_club["TituloServicio"];
        }

        if (count($array_id_servicios) > 0) :
            $id_servicios = implode(",", $array_id_servicios);
        endif;

        //traer servicios del usuario
        if ($TipoApp == "Empleado" && !empty($IDUsuario)) :
            unset($array_id_servicios);
            $id_servicios = "0";
            $response_servicio = array();
            $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
            $qry_servicios = $dbo->query($sql_servicios);
            while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                $array_id_servicios[] = $r_servicio["IDServicioMaestro"];
            } //end while
            if (count($array_id_servicios) > 0) :
                $id_servicios = implode(",", $array_id_servicios);
            endif;
        endif;

        // SI LA CATEGORIA EXISTE SE DEBE FILTRAR PARA LOS NUEVOS SERVICIOS DE RESEVAS           

        if (!empty($IDCategoriasServicios)) :
            $SQLCategorias = "SELECT * FROM CategoriasServiciosServicios WHERE IDCategoriasServicios = $IDCategoriasServicios";
            $QRYCategorias = $dbo->query($SQLCategorias);
            while ($Servicios = $dbo->fetchArray($QRYCategorias)) :
                $arrayservicios[] = $Servicios[IDServicio];
            endwhile;

            $ServicioCategorias = implode(",", $arrayservicios);
            $CondicionCategorias = " AND IDServicio IN ($ServicioCategorias) ";
        endif;

        $sql = "SELECT * FROM Servicio WHERE IDClub = '$IDClub' and IDServicioMaestro in ($id_servicios) $condicion_servicio  $CondicionCategorias $condicionserviciosespeciales ORDER BY Nombre ";


        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $servicio["IDClub"] = $r["IDClub"];
                $servicio["IDServicio"] = $r["IDServicio"];

                //Si tiene un nombre personalizado se lo pongo de lo contrario le pongo el general
                if (!empty($array_nom_servicios[$r["IDServicioMaestro"]])) :
                    $servicio["Nombre"] = $array_nom_servicios[$r["IDServicioMaestro"]];
                else :
                    $servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                endif;

                $foto = "";
                if (!empty($r["Icono"])) {
                    $foto = SERVICIO_ROOT . $r["Icono"];
                } else {
                    $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                    if (!empty($icono_maestro)) {
                        $foto = SERVICIO_ROOT . $icono_maestro;
                    }
                }

                $invitadoclub = "";
                $invitadoexterno = "";
                $servicio["Icono"] = $foto;
                $servicio["General"] = $dbo->getFields("ServicioMaestro", "General", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");

                //TEMPORAL....Consulto alguna disponibilidad para ver el numero de invitados, esto se debe hacer por el servicio que consulta disponibilidades
                $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");

                //Consulto cual es la mayor disponibildad en tiempo apara armar el dia maximo en el servicio cuando se empieza por el elemento
                $sql_disponibilidad = "Select * From Disponibilidad Where IDServicio = '" . $r["IDServicio"] . "'";
                $result_disponibilidad = $dbo->query($sql_disponibilidad);
                $dia_mayor = 0;
                while ($row_disponibilidad = $dbo->fetchArray($result_disponibilidad)) {
                    $medicion_tiempo_anticipacion = $row_disponibilidad["MedicionTiempoAnticipacion"];
                    $valor_anticipacion_turno = (int) $row_disponibilidad["Anticipacion"];
                    switch ($medicion_tiempo_anticipacion):
                        case "Dias":
                            $dias_anticipacion_turno = $valor_anticipacion_turno;
                            break;
                        case "Horas":
                            $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 24);
                            break;
                        case "Minutos":
                            $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 1440);
                            break;
                        default:
                            $dias_anticipacion_turno = 0;
                    endswitch;

                    if ((int) $dias_anticipacion_turno >= (int) $dia_mayor) :
                        $dia_mayor = $dias_anticipacion_turno;
                    endif;
                }


                $invitadoclub = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                if (!empty($invitadoclub)) :
                    $servicio["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $servicio["NumeroMinimoInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroMinimoInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                else :
                    $servicio["NumeroInvitadoClub"] = "";
                endif;
                if (!empty($invitadoexterno)) :
                    $servicio["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $servicio["NumeroMinimoInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroMinimoInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                else :
                    $servicio["NumeroInvitadoExterno"] = "";
                endif;

                /*
                    //Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
                    $medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $id_disponibilidad  . "'" );
                    $valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $id_disponibilidad  . "'" );
                    switch($medicion_tiempo_anticipacion):
                    case "Dias":
                    $dias_anticipacion_turno = $valor_anticipacion_turno;
                    break;
                    case "Horas":
                    $dias_anticipacion_turno = (int)($valor_anticipacion_turno/24);
                    break;
                    case "Minutos":
                    $dias_anticipacion_turno = (int)($valor_anticipacion_turno/1440);
                    break;
                    default:
                    $dias_anticipacion_turno = 0;
                    endswitch;
                    $servicio["DiasMaximoReserva"] = "$dias_anticipacion_turno";
                     */
                $servicio["DiasMaximoReserva"] = "$dia_mayor";

                //temporal lagartos

                if ($IDClub == 7 && $r["IDServicio"] == 43) { //Clases tenis especial
                    $dias_maximo_especial = 9 - (int) date("w");
                    $servicio["DiasMaximoReserva"] = "$dias_maximo_especial";
                }

                //FIN TEMPORAL

                $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $r["IDServicioMaestro"] . "' ", "array");

                if (!empty($r["LabelElementoBoton"])) {
                    $servicio["LabelElemento"] = $r["LabelElementoBoton"];
                } else {
                    $servicio["LabelElemento"] = $datos_maestro["LabelElementoBoton"];
                }

                $servicio["LabelElementoSocio"] = utf8_encode($r["LabelElementoSocio"]);
                $servicio["LabelElementoExterno"] = utf8_encode($r["LabelElementoExterno"]);

                $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                if (empty($labelauxiliar)) {
                    $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                }

                if (!empty($r["LabelAuxiliar"])) {
                    $servicio["LabelAuxiliar"] = $r["LabelAuxiliar"];
                } else {
                    $servicio["LabelAuxiliar"] = $datos_maestro["LabelAuxiliar"];
                }

                if (!empty($r["HorarioAcordeon"])) {
                    $servicio["HorarioAcordeon"] = $r["HorarioAcordeon"];
                } else {
                    $servicio["HorarioAcordeon"] = $datos_maestro["HorarioAcordeon"];
                }

                if (!empty($r["LabelTipoReserva"])) {
                    $servicio["LabelTipoReserva"] = $r["LabelTipoReserva"];
                } else {
                    $servicio["LabelTipoReserva"] = $datos_maestro["LabelTipoReserva"];
                }

                //$servicio[ "LabelTipoReserva" ] = $datos_maestro["LabelTipoReserva"];

                $servicio["TextoLegal"] = $r["TextoLegal"];
                $servicio["DiasAnticipacion"] = "$r[DiasAnticipacion]";

                $servicio["AcordeonAbiertoPrimeraOpcion"] = "N";
                $servicio["ColorLetraHora"] = "$r[ColorLetraHora]";
                $servicio["ColorFondoHora"] = "$r[ColorFondoHora]";
                $servicio["NegrillaLetraHora"] = "S";

                if ($IDClub == 25) : //Gun club y Pereira no permite beneficiarios y ya estab configurado asi se pone esta validacion especial
                    $servicio["PermiteBeneficiario"] = "N";
                    $servicio["LabelBeneficiario"] = "";
                else :

                    if (!empty($r["PermiteBeneficiario"])) {
                        $servicio["PermiteBeneficiario"] = $r["PermiteBeneficiario"];
                    } else {
                        $servicio["PermiteBeneficiario"] = $datos_maestro["PermiteBeneficiario"];
                    }

                    if (!empty($r["LabelBeneficiario"])) {
                        $servicio["LabelBeneficiario"] = $r["LabelBeneficiario"];
                    } else {
                        $servicio["LabelBeneficiario"] = $datos_maestro["LabelBeneficiario"];
                    }

                endif;

                $servicio["HoraDesde"] = $r["HoraHasta"];
                $servicio["HoraCancelacion"] = $r["HoraCancelacion"];
                $servicio["IntervaloHora"] = $r["IntervaloHora"];
                $servicio["MinutosReserva"] = "$r[MinutosReserva]";
                $servicio["TurnosMaximo"] = "$r[TurnosMaximo]";
                $id_servicio_inicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");


                if ((int)$r["IDServicioInicial"] > 0) {
                    $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $r["IDServicioInicial"] . "'");
                } else {
                    $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $id_servicio_inicial . "'");
                }





                $servicio["TipoSorteo"] = $r[TipoSorteo];


                $servicio["Orden"] = $dbo->getFields("ServicioClub", "Orden", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                $servicio["Georeferenciacion"] = "$r[Georeferenciacion]";
                $servicio["Latitud"] = $r["Latitud"];
                $servicio["Longitud"] = $r["Longitud"];
                $servicio["Rango"] = $r["Rango"];
                $servicio["MensajeFueraRango"] = $r["MensajeFueraRango"];
                $servicio["MostrarBotonBuscar"] = $r["MostrarBotonBuscar"];
                $servicio["MultipleAuxiliar"] = $r["MultipleAuxiliar"];
                $servicio["MaximoInvitadosSalon"] = $r["MaximoInvitadosSalon"];
                $servicio["PermiteListaEspera"] = $r["PermiteListaEspera"];
                $servicio["PermiteListaEsperaAuxiliar"] = $r["PermiteListaEsperaAuxiliar"];
                $servicio["PermiteAgregarGrupo"] = $r["PermiteAgregarGrupo"];
                $servicio["PermiteEditarAuxiliar"] = $r["PermiteEditarAuxiliar"];
                $servicio["PermiteEditarAdicionales"] = $r["PermiteEditarAdicionales"];
                $servicio["AdicionalesObligatorio"] = $r["AdicionalesObligatorio"];
                $servicio["LabelEncabezadoInvitados"] = $r["LabelEncabezadoInvitados"];
                $servicio["LabelEncabezadoBeneficiarios"] = $r["LabelEncabezadoBeneficiarios"];
                $servicio["TextoAbrirMapa"] = $r["TextoAbrirMapa"];
                $servicio["SoloIcono"] = $dbo->getFields("Club", "SoloIcono", "IDClub = '" . $IDClub . "'");
                $servicio["SoloFechaSeleccionada"] = $datos_maestro["SoloFechaSeleccionada"];
                $servicio["LabelReservaMultiple"] = $datos_maestro["LabelReservaMultiple"];
                $servicio["PermiteAdicionarServicios"] = $r["PermiteAdicionarServicios"];
                $servicio["MostrarDecimal"] = $r["MostrarDecimal"];
                $servicio["PermiteIrDomicilio"] = $r["PermiteIrDomicilio"];
                $servicio["LabelPermiteIrDomicilio"] = $r["LabelPermiteIrDomicilio"];
                $servicio["IDModuloDomicilio"] = $r["IDModuloDomicilio"];
                $servicio["PermiteTurnoVistaBotones"] = $r["PermiteTurnoVistaBotones"];
                $servicio["TipoVistaTurno"] = $r["TipoVistaTurno"];
                $servicio["OcultarHora"] = $r["OcultarHora"];
                $servicio["RequiereHoraFinal"] = $r["RequiereHoraFinal"];
                //Externos
                $servicio["PermiteInvitadoExternoCedula"] = $r["PermiteInvitadoExternoCedula"];
                $servicio["PermiteInvitadoExternoCorreo"] = $r["PermiteInvitadoExternoCorreo"];
                $servicio["PermiteInvitadoExternoFechaNacimiento"] = $r["PermiteInvitadoExternoFechaNacimiento"];
                $servicio["InvitadoExternoPago"] = $r["InvitadoExternoPago"];
                $servicio["LabelInvitadoExternoPago"] = $r["LabelInvitadoExternoPago"];
                $servicio["InvitadoExternoValor"] = $r["InvitadoExternoValor"];
                $servicio["CamposDinamicosInvitadoExternoHabilitado"] = $r["CamposDinamicosInvitadoExternoHabilitado"];

                $servicio["PermiteFiltroElementoFechaPorTexto"] = $r["PermiteFiltroElementoFechaPorTexto"];
                $servicio["PermiteFiltroElementoFechaPorBoton"] = $r["PermiteFiltroElementoFechaPorBoton"];
                $servicio["LabelFiltroElementoFechaPorTexto"] = $r["LabelFiltroElementoFechaPorTexto"];
                $servicio["LabelFiltroElementoFechaPorBoton"] = $r["LabelFiltroElementoFechaPorBoton"];
                $servicio["PermiteGuardarCalendarioDispositivo"] = $r["PermiteGuardarCalendarioDispositivo"];

                $servicio["LabelAdicionales"] = $r["LabelAdicionales"];
                $servicio["EncabezadoAdicionales"] = $r["EncabezadoAdicionales"];
                $servicio["LabelSeleccioneAdicionales"] = $r["LabelSeleccioneAdicionales"];
                $servicio["MensajeAdicionalesObligatorio"] = $r["MensajeAdicionalesObligatorio"];

                // CADDIES
                $servicio[PermiteAdicionarCaddies] = $r[PermiteAdicionarCaddies];
                $servicio[LabelAdicionarCaddies] = $r[LabelAdicionarCaddies];
                $servicio[ObligatorioSeleccionarCaddie] = $r[ObligatorioSeleccionarCaddie];
                $servicio[MensajeCaddiesObligatorio] = $r[MensajeCaddiesObligatorio];

                $servicio["MostrarImagenEncabezado"] = $r["MostrarImagenEncabezado"];

                $servicio["PermiteFechaHoraPreseleccion"] = $r["PermiteFechaHoraPreseleccion"];
                $servicio["ColorHoraPreseleccionada"] = $r["ColorHoraPreseleccionada"];

                $servicio["PermiteTicketsDescuento"] = $r["PermiteTicketsDescuento"];
                $servicio["TextoIntroTicketsDescuento"] = $r["TextoIntroTicketsDescuento"];
                $servicio["LabelTicketsDescuento"] = $r["LabelTicketsDescuento"];

                $servicio["PermiteInterfazAuxiliaresInvitadosBotones"] = $r["PermiteInterfazAuxiliaresInvitadosBotones"];

                $servicio["MostrarPreguntaListaEsperaTodos"] = $r["MostrarPreguntaListaEsperaTodos"];
                $servicio["LabelPreguntaListaEsperaTodos"] = $r["LabelPreguntaListaEsperaTodos"];


                $fotoencabezado = "";
                if (!empty($r["ImagenEncabezado"])) {
                    $fotoencabezado = SERVICIO_ROOT . $r["ImagenEncabezado"];
                } else {
                    $fotoencabezado = "";
                }

                $servicio["ImagenEncabezado"] = $fotoencabezado;

                $zonahoraria = date_default_timezone_get();
                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                $servicio["GMT"] = SIMWebservice::timezone_offset_string($offset);

                // PARA LOS TIPO SORTEO
                if ($r[TipoSorteo] == '1') :

                    $servicio["PrimerServicio"] = 'sorteo';
                    $Servicio = SIMWebServiceSorteos::get_servicios_sorteo_turnos($IDClub, $IDSocio, "", $servicio[IDServicio]);
                    $servicio[Nombre] = $Servicio[response][0][Nombre];
                    $servicio[Icono] = $Servicio[response][0][Icono];
                    $servicio[CantidadTurnosSorteo] = $Servicio[response][0][CantidadTurnos];
                    $servicio[MinutosReserva] = $Servicio[response][0][MinutosReserva];
                    $servicio[IntroSeleccionFechaSorteo] = $Servicio[response][0][IntroSeleccionFecha];
                    $servicio[LabelBotonAgregarInvitadoSorteo] = $Servicio[response][0][LabelBotonAgregarInvitado];
                    $servicio[LabelElementoSocio] = $Servicio[response][0][LabelAgregarInvitadoSocio];
                    $servicio[IntroAgregarInvitadoSorteo] = $Servicio[response][0][IntroAgregarInvitado];
                    $servicio[PermiteInvitadoExternoCedula] = $Servicio[response][0][PermiteInvitadoExternoCedula];
                    $servicio[PermiteInvitadoExternoCorreo] = $Servicio[response][0][PermiteInvitadoExternoCorreo];
                    $servicio[PermiteInvitadoExternoFechaNacimiento] = $Servicio[response][0][PermiteInvitadoExternoFechaNacimiento];
                    $servicio[MinimoInvitadosSeleccionSorteo] = $Servicio[response][0][MinimoInvitadosSeleccion];
                    $servicio[LabelElementoExterno] = $Servicio[response][0][LabelAgregarInvitadoExterno];

                endif;

                //Campos Reservas
                $response_campos = array();
                $sql_campos = "SELECT * FROM ServicioCampo WHERE Publicar = 'S' and IDServicio= '" . $r["IDServicio"] . "' ORDER BY Orden";
                $qry_campos = $dbo->query($sql_campos);
                if ($dbo->rows($qry_campos) > 0) {
                    while ($r_campos = $dbo->fetchArray($qry_campos)) {
                        $campos["IDClub"] = $IDClub;
                        $campos["IDServicio"] = $r_campos["IDServicio"];
                        $campos["IDServicioCampo"] = $r_campos["IDServicioCampo"];
                        $campos["Nombre"] = $r_campos["Nombre"];
                        $campos["Descripcion"] = utf8_encode($r_campos["Descripcion"]);
                        $campos["Tipo"] = $r_campos["Tipo"];
                        $campos["Valor"] = $r_campos["Valor"];
                        $campos["Obligatorio"] = $r_campos["Obligatorio"];

                        array_push($response_campos, $campos);
                    } //end while
                }
                $servicio["CamposReserva"] = $response_campos;

                //Servicios Asociados
                $response_servicio_asociado = array();
                $datos_guardados = explode("|||", $r["IDServicioAsociado"]);
                foreach ($datos_guardados as $id_servicio) :
                    if (!empty($id_servicio)) {
                        $servicio_asoc["IDServicio"] = $id_servicio;
                        $id_servicio_mestro_menu = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                        $asoc_servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                        $asoc_servicio["NombrePersonalizado"] = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                        if (!empty($asoc_servicio["NombrePersonalizado"])) {
                            $NombreServicio = $asoc_servicio["NombrePersonalizado"];
                        } else {
                            $NombreServicio = $asoc_servicio["Nombre"];
                        }

                        $servicio_asoc["Nombre"] = $NombreServicio;
                        array_push($response_servicio_asociado, $servicio_asoc);
                    }
                endforeach;
                $servicio["ServicioAsociado"] = $response_servicio_asociado;


                //Tipos de Reservas
                $response_tiporeservas = array();
                $sql_tiporeservas = "SELECT * FROM ServicioTipoReserva WHERE Activo = 'S' and IDServicio= '" . $r["IDServicio"] . "' " . $condicion_tipo . " ORDER BY Orden";
                $qry_tiporeservas = $dbo->query($sql_tiporeservas);
                if ($dbo->rows($qry_tiporeservas) > 0) {
                    while ($r_tiporeservas = $dbo->fetchArray($qry_tiporeservas)) {
                        $tiporeserva["IDClub"] = $IDClub;
                        $tiporeserva["IDServicio"] = $r_tiporeservas["IDServicio"];
                        $tiporeserva["IDServicioTipoReserva"] = $r_tiporeservas["IDServicioTipoReserva"];
                        $tiporeserva["Nombre"] = $r_tiporeservas["Nombre"];
                        array_push($response_tiporeservas, $tiporeserva);
                    } //end while
                }

                $servicio["TipoReserva"] = $response_tiporeservas;

                if ($IDClub == 227 && $r["IDServicio"] == 46131) {
                    require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
                    $respuesta = SIMWebServiceCountryMedellin::get_tipo_reserva($IDSocio, $r["IDServicio"], $Fecha);
                    $servicio["TipoReserva"] = $respuesta;
                }

                //Tipos de pagos recibidos
                $response_tipo_pago = array();
                $sql_tipo_pago = "SELECT * FROM ServicioTipoPago STP, TipoPago TP  WHERE STP.IDTipoPago = TP.IDTipoPago and IDServicio= '" . $r["IDServicio"] . "' ";
                $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                if ($dbo->rows($qry_tipo_pago) > 0) {
                    $servicio["PagoReserva"] = "S";
                    while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                        $desactivado = false;

                        if (!$desactivado) {
                            $tipopago["IDClub"] = $IDClub;
                            $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                            $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                            $tipopago["Talonera"] = $r_tipo_pago["Talonera"];
                            $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                            $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);

                            if ($IDClub == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                                $tipopago["Nombre"] = "Pago en recepción";
                            else
                                $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                            $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                            //Para el condado y es pagos online muestro la imagen de placetopay

                            switch ($r_tipo_pago["IDTipoPago"]) {
                                case "1":
                                    $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                    break;
                                case "2":
                                    $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                    break;
                                case "3":
                                    $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                    $imagen = "https://www.miclubapp.com/file/noticia/icsi.png";
                                    break;
                                case "11":
                                    $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                    break;
                                case "9":
                                    $imagen = "https://www.miclubapp.com/file/noticia/ictarjeta.png";
                                    break;
                                case "12":
                                    $imagen = "https://www.miclubapp.com/file/noticia/iccredibancopago.png";
                                    break;
                                default:
                                    $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                    break;
                            }

                            $tipopago["Imagen"] = $imagen;
                            array_push($response_tipo_pago, $tipopago);
                        }
                    } //end while
                    $servicio["TipoPago"] = $response_tipo_pago;
                } else {
                    $servicio["PagoReserva"] = "N";
                }


                $servicio["MensajePagoReserva"] = $r["MensajePagoReserva"];

                $resp_publicidad = SIMWebServicePublicidad::get_publicidad($IDClub, "", "", "Socio", $r["IDServicio"]);
                $servicio["PublicidadInfo"] = $resp_publicidad["response"][0];
                $flag_mostrar = 0;

                // Verificar si es un modulo que se debe revisar permiso
                if (array_key_exists($r["IDServicioMaestro"], $array_id_mod_esp)) {
                    if (in_array($IDSocio, $array_id_mod_esp[$r["IDServicioMaestro"]])) {
                        $flag_mostrar = 0;
                    } else {
                        $flag_mostrar = 1;
                    }
                } else {
                    $flag_mostrar = 0;
                }

                if ($flag_mostrar == 0) {
                    array_push($response, $servicio);
                }
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

    // NUEVAS

    public function get_servicios_agrupados($id_club, $TipoApp = "", $IDUsuario = "", $IDSocio = "")
    {
        $dbo = &SIMDB::get();

        require_once LIBDIR . "SIMWebServicePublicidad.inc.php";

        $response = array();

        //Consulto los modulos que se configuraron para un socio especifico
        $sql_tiposoc_mod = "SELECT IDServicioMaestro,InvitadoSeleccion From PermisoSocioModulo Where IDClub = '" . $id_club . "' and InvitadoSeleccion <> '' ";
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

            if ($TipoApp != "Empleado") {
                $array_mod_esp = explode("|", $row_tiposoc_mod["IDServicioMaestro"]);
                foreach ($array_mod_esp as $IDModulo) {
                    if (!empty($IDModulo)) {
                        $array_id_mod_esp[$IDModulo] = $array_soc_id;
                    }
                }
            }
        }
        //FIN Consulto los modulos que se configuraron para un socio especifico

        //reviso si tienen modulo personalizado por perfil de socio
        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
        $sql_servicio_perfil = "Select * From TipoSocioModulo Where IDClub = '" . $id_club . "' and TipoSocio = '" . $TipoSocio . "' Limit 1";
        $r_servicio_perfil = $dbo->query($sql_servicio_perfil);
        if ($dbo->rows($r_servicio_perfil) > 0) {
            $row_servicio_perfil = $dbo->fetchArray($r_servicio_perfil);
            if (!empty($row_servicio_perfil["IDServicioMaestro"])) {
                $array_id_servicio = explode("|", $row_servicio_perfil["IDServicioMaestro"]);
                $array_id_modulo = explode("|", $row_servicio_perfil["IDModulo"]);
                if (count($array_id_servicio) > 0) {
                    $id_servicio_ver = implode(",", $array_id_servicio);
                    if ($row_modulo_perfil[Ocultar] == 1) :
                        $condicion_servicio = " and IDServicioMaestro not in (" . $id_servicio_ver . ")";
                    else :
                        $condicion_servicio = " and IDServicioMaestro in (" . $id_servicio_ver . ")";
                    endif;
                }
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


        //Consulto los servicios activos del club
        $sql_servicio_club = "Select * From  ServicioClub Where IDClub = '" . $id_club . "' and Activo = 'S'";
        $qry_servicio_club = $dbo->query($sql_servicio_club);
        $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
        while ($r_servicio_club = $dbo->fetchArray($qry_servicio_club)) {
            $array_id_servicios[] = $r_servicio_club["IDServicioMaestro"];
            $array_nom_servicios[$r_servicio_club["IDServicioMaestro"]] = $r_servicio_club["TituloServicio"];
        }
        if (count($array_id_servicios) > 0) :
            $id_servicios = implode(",", $array_id_servicios);
        endif;

        // Consulto los SubModulos por TipoSocio
        $sql_subModulos = "SELECT * FROM SubModulo WHERE IDClub = " . $id_club . $condicion_modulo;
        $q_SubModulos = $dbo->query($sql_subModulos);
        $n_SubModulos = $dbo->rows($q_SubModulos);
        if ($n_SubModulos > 0) {
            $arr_Servicios = array();
            while ($r_SubModulo = $dbo->assoc($q_SubModulos)) {

                $id_Servicios = str_replace('|', ',', $r_SubModulo['IDServicio']);
                $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $id_club . "' and IDServicio in (" . $id_Servicios . ")  ORDER BY Nombre ";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $cont = 0;
                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    while ($r = $dbo->fetchArray($qry)) {
                        $servicio["IDClub"] = $r["IDClub"];
                        $servicio["IDServicio"] = $r["IDServicio"];

                        //Si tiene un nombre personalizado se lo pongo de lo contrario le pongo el general
                        if (!empty($array_nom_servicios[$r["IDServicioMaestro"]])) :
                            $servicio["Nombre"] = $array_nom_servicios[$r["IDServicioMaestro"]];
                        else :
                            $servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        endif;

                        $foto = "";
                        if (!empty($r["Icono"])) {
                            $foto = SERVICIO_ROOT . $r["Icono"];
                        } else {
                            $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                            if (!empty($icono_maestro)) {
                                $foto = SERVICIO_ROOT . $icono_maestro;
                            }
                        }

                        $invitadoclub = "";
                        $invitadoexterno = "";
                        $servicio["Icono"] = $foto;
                        $servicio["General"] = $dbo->getFields("ServicioMaestro", "General", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");

                        //TEMPORAL....Consulto alguna disponibilidad para ver el numero de invitados, esto se debe hacer por el servicio que consulta disponibilidades
                        $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");

                        //Consulto cual es la mayor disponibildad en tiempo apara armar el dia maximo en el servicio cuando se empieza por el elemento
                        $sql_disponibilidad = "Select * From Disponibilidad Where IDServicio = '" . $r["IDServicio"] . "'";
                        $result_disponibilidad = $dbo->query($sql_disponibilidad);
                        $dia_mayor = 0;
                        while ($row_disponibilidad = $dbo->fetchArray($result_disponibilidad)) {
                            $medicion_tiempo_anticipacion = $row_disponibilidad["MedicionTiempoAnticipacion"];
                            $valor_anticipacion_turno = (int) $row_disponibilidad["Anticipacion"];
                            switch ($medicion_tiempo_anticipacion):
                                case "Dias":
                                    $dias_anticipacion_turno = $valor_anticipacion_turno;
                                    break;
                                case "Horas":
                                    $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 24);
                                    break;
                                case "Minutos":
                                    $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 1440);
                                    break;
                                default:
                                    $dias_anticipacion_turno = 0;
                            endswitch;

                            if ((int) $dias_anticipacion_turno >= (int) $dia_mayor) :
                                $dia_mayor = $dias_anticipacion_turno;
                            endif;
                        }

                        $invitadoclub = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)) :
                            $servicio["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $servicio["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)) :
                            $servicio["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $servicio["NumeroInvitadoExterno"] = "";
                        endif;


                        $servicio["DiasMaximoReserva"] = "$dia_mayor";

                        //temporal lagartos

                        if ($id_club == 7 && $r["IDServicio"] == 43) { //Clases tenis especial
                            $dias_maximo_especial = 9 - (int) date("w");
                            $servicio["DiasMaximoReserva"] = "$dias_maximo_especial";
                        }

                        //FIN TEMPORAL

                        $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $r["IDServicioMaestro"] . "' ", "array");

                        if (!empty($r["LabelElementoBoton"])) {
                            $servicio["LabelElemento"] = $r["LabelElementoBoton"];
                        } else {
                            $servicio["LabelElemento"] = $datos_maestro["LabelElementoBoton"];
                        }

                        $servicio["LabelElementoSocio"] = utf8_encode($r["LabelElementoSocio"]);
                        $servicio["LabelElementoExterno"] = utf8_encode($r["LabelElementoExterno"]);

                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $id_club . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        if (!empty($r["LabelAuxiliar"])) {
                            $servicio["LabelAuxiliar"] = $r["LabelAuxiliar"];
                        } else {
                            $servicio["LabelAuxiliar"] = $datos_maestro["LabelAuxiliar"];
                        }

                        if (!empty($r["HorarioAcordeon"])) {
                            $servicio["HorarioAcordeon"] = $r["HorarioAcordeon"];
                        } else {
                            $servicio["HorarioAcordeon"] = $datos_maestro["HorarioAcordeon"];
                        }

                        if (!empty($r["LabelTipoReserva"])) {
                            $servicio["LabelTipoReserva"] = $r["LabelTipoReserva"];
                        } else {
                            $servicio["LabelTipoReserva"] = $datos_maestro["LabelTipoReserva"];
                        }

                        $servicio["LabelTipoReserva"] = $datos_maestro["LabelTipoReserva"];

                        $servicio["TextoLegal"] = $r["TextoLegal"];
                        $servicio["DiasAnticipacion"] = "$r[DiasAnticipacion]";

                        $servicio["AcordeonAbiertoPrimeraOpcion"] = "N";
                        $servicio["ColorLetraHora"] = "$r[ColorLetraHora]";
                        $servicio["ColorFondoHora"] = "$r[ColorFondoHora]";
                        $servicio["NegrillaLetraHora"] = "S";

                        if ($id_club == 25) : //Gun club y Pereira no permite beneficiarios y ya estab configurado asi se pone esta validacion especial
                            $servicio["PermiteBeneficiario"] = "N";
                            $servicio["LabelBeneficiario"] = "";
                        else :

                            if (!empty($r["PermiteBeneficiario"])) {
                                $servicio["PermiteBeneficiario"] = $r["PermiteBeneficiario"];
                            } else {
                                $servicio["PermiteBeneficiario"] = $datos_maestro["PermiteBeneficiario"];
                            }

                            if (!empty($r["LabelBeneficiario"])) {
                                $servicio["LabelBeneficiario"] = $r["LabelBeneficiario"];
                            } else {
                                $servicio["LabelBeneficiario"] = $datos_maestro["LabelBeneficiario"];
                            }

                        endif;

                        $servicio["HoraDesde"] = $r["HoraHasta"];
                        $servicio["HoraCancelacion"] = $r["HoraCancelacion"];
                        $servicio["IntervaloHora"] = $r["IntervaloHora"];
                        $servicio["MinutosReserva"] = "$r[MinutosReserva]";
                        $servicio["TurnosMaximo"] = "$r[TurnosMaximo]";
                        $id_servicio_inicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");


                        if ((int)$r["IDServicioInicial"] > 0) {
                            $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $r["IDServicioInicial"] . "'");
                        } else {
                            $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $id_servicio_inicial . "'");
                        }




                        $servicio["Orden"] = $dbo->getFields("ServicioClub", "Orden", "IDClub = '" . $id_club . "' and IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        $servicio["Georeferenciacion"] = "$r[Georeferenciacion]";
                        $servicio["Latitud"] = $r["Latitud"];
                        $servicio["Longitud"] = $r["Longitud"];
                        $servicio["Rango"] = $r["Rango"];
                        $servicio["MensajeFueraRango"] = $r["MensajeFueraRango"];
                        $servicio["MostrarBotonBuscar"] = $r["MostrarBotonBuscar"];
                        $servicio["MultipleAuxiliar"] = $r["MultipleAuxiliar"];
                        $servicio["MaximoInvitadosSalon"] = $r["MaximoInvitadosSalon"];
                        $servicio["PermiteListaEspera"] = $r["PermiteListaEspera"];
                        $servicio["PermiteListaEsperaAuxiliar"] = $r["PermiteListaEsperaAuxiliar"];
                        $servicio["PermiteAgregarGrupo"] = $r["PermiteAgregarGrupo"];
                        $servicio["PermiteEditarAuxiliar"] = $r["PermiteEditarAuxiliar"];
                        $servicio["PermiteEditarAdicionales"] = $r["PermiteEditarAdicionales"];
                        $servicio["AdicionalesObligatorio"] = $r["AdicionalesObligatorio"];
                        $servicio["LabelEncabezadoInvitados"] = $r["LabelEncabezadoInvitados"];
                        $servicio["LabelEncabezadoBeneficiarios"] = $r["LabelEncabezadoBeneficiarios"];
                        $servicio["TextoAbrirMapa"] = $r["TextoAbrirMapa"];
                        $servicio["SoloIcono"] = $dbo->getFields("Club", "SoloIcono", "IDClub = '" . $id_club . "'");
                        $servicio["SoloFechaSeleccionada"] = $datos_maestro["SoloFechaSeleccionada"];
                        $servicio["LabelReservaMultiple"] = $datos_maestro["LabelReservaMultiple"];
                        $servicio["PermiteAdicionarServicios"] = $r["PermiteAdicionarServicios"];
                        $servicio["MostrarDecimal"] = $r["MostrarDecimal"];
                        $servicio["PermiteIrDomicilio"] = $r["PermiteIrDomicilio"];
                        $servicio["LabelPermiteIrDomicilio"] = $r["LabelPermiteIrDomicilio"];
                        $servicio["IDModuloDomicilio"] = $r["IDModuloDomicilio"];
                        //Externos
                        $servicio["PermiteInvitadoExternoCedula"] = $r["PermiteInvitadoExternoCedula"];
                        $servicio["PermiteInvitadoExternoCorreo"] = $r["PermiteInvitadoExternoCorreo"];
                        $servicio["InvitadoExternoPago"] = $r["InvitadoExternoPago"];
                        $servicio["LabelInvitadoExternoPago"] = $r["LabelInvitadoExternoPago"];
                        $servicio["InvitadoExternoValor"] = $r["InvitadoExternoValor"];

                        $servicio["PermiteFiltroElementoFechaPorTexto"] = $r["PermiteFiltroElementoFechaPorTexto"];
                        $servicio["PermiteFiltroElementoFechaPorBoton"] = $r["PermiteFiltroElementoFechaPorBoton"];
                        $servicio["LabelFiltroElementoFechaPorTexto"] = $r["LabelFiltroElementoFechaPorTexto"];
                        $servicio["LabelFiltroElementoFechaPorBoton"] = $r["LabelFiltroElementoFechaPorBoton"];
                        $servicio["PermiteGuardarCalendarioDispositivo"] = $r["PermiteGuardarCalendarioDispositivo"];

                        $servicio["LabelAdicionales"] = $r["LabelAdicionales"];
                        $servicio["EncabezadoAdicionales"] = $r["EncabezadoAdicionales"];
                        $servicio["LabelSeleccioneAdicionales"] = $r["LabelSeleccioneAdicionales"];
                        $servicio["MensajeAdicionalesObligatorio"] = $r["MensajeAdicionalesObligatorio"];

                        // CADDIES
                        $servicio[PermiteAdicionarCaddies] = $r[PermiteAdicionarCaddies];
                        $servicio[LabelAdicionarCaddies] = $r[LabelAdicionarCaddies];
                        $servicio[ObligatorioSeleccionarCaddie] = $r[ObligatorioSeleccionarCaddie];
                        $servicio[MensajeCaddiesObligatorio] = $r[MensajeCaddiesObligatorio];


                        $servicio["MostrarImagenEncabezado"] = $r["MostrarImagenEncabezado"];
                        $fotoencabezado = "";
                        if (!empty($r["ImagenEncabezado"])) {
                            $fotoencabezado = SERVICIO_ROOT . $r["ImagenEncabezado"];
                        } else {
                            $fotoencabezado = "";
                        }

                        $servicio["ImagenEncabezado"] = $fotoencabezado;

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $servicio["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        //Campos Reservas
                        $response_campos = array();
                        $sql_campos = "SELECT * FROM ServicioCampo WHERE Publicar = 'S' and IDServicio= '" . $r["IDServicio"] . "' ORDER BY Nombre";
                        $qry_campos = $dbo->query($sql_campos);
                        if ($dbo->rows($qry_campos) > 0) {
                            while ($r_campos = $dbo->fetchArray($qry_campos)) {
                                $campos["IDClub"] = $id_club;
                                $campos["IDServicio"] = $r_campos["IDServicio"];
                                $campos["IDServicioCampo"] = $r_campos["IDServicioCampo"];
                                $campos["Nombre"] = $r_campos["Nombre"];
                                $campos["Descripcion"] = utf8_encode($r_campos["Descripcion"]);
                                $campos["Tipo"] = $r_campos["Tipo"];
                                $campos["Valor"] = $r_campos["Valor"];
                                $campos["Obligatorio"] = $r_campos["Obligatorio"];

                                array_push($response_campos, $campos);
                            } //end while
                        }
                        $servicio["CamposReserva"] = $response_campos;

                        //Servicios Asociados
                        $response_servicio_asociado = array();
                        $datos_guardados = explode("|||", $r["IDServicioAsociado"]);
                        foreach ($datos_guardados as $id_servicio) :
                            if (!empty($id_servicio)) {
                                $servicio_asoc["IDServicio"] = $id_servicio;
                                $id_servicio_mestro_menu = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                                $asoc_servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                                $asoc_servicio["NombrePersonalizado"] = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                                if (!empty($asoc_servicio["NombrePersonalizado"])) {
                                    $NombreServicio = $asoc_servicio["NombrePersonalizado"];
                                } else {
                                    $NombreServicio = $asoc_servicio["Nombre"];
                                }

                                $servicio_asoc["Nombre"] = $NombreServicio;
                                array_push($response_servicio_asociado, $servicio_asoc);
                            }
                        endforeach;
                        $servicio["ServicioAsociado"] = $response_servicio_asociado;

                        //Tipos de Reservas
                        $response_tiporeservas = array();
                        $sql_tiporeservas = "SELECT * FROM ServicioTipoReserva WHERE Activo = 'S' and IDServicio= '" . $r["IDServicio"] . "' " . $condicion_tipo . " ORDER BY Orden";
                        $qry_tiporeservas = $dbo->query($sql_tiporeservas);
                        if ($dbo->rows($qry_tiporeservas) > 0) {
                            while ($r_tiporeservas = $dbo->fetchArray($qry_tiporeservas)) {
                                $tiporeserva["IDClub"] = $id_club;
                                $tiporeserva["IDServicio"] = $r_tiporeservas["IDServicio"];
                                $tiporeserva["IDServicioTipoReserva"] = $r_tiporeservas["IDServicioTipoReserva"];
                                $tiporeserva["Nombre"] = $r_tiporeservas["Nombre"];
                                array_push($response_tiporeservas, $tiporeserva);
                            } //end while
                        }

                        $servicio["TipoReserva"] = $response_tiporeservas;

                        //Tipos de pagos recibidos
                        $response_tipo_pago = array();
                        $sql_tipo_pago = "SELECT * FROM ServicioTipoPago STP, TipoPago TP  WHERE STP.IDTipoPago = TP.IDTipoPago and IDServicio= '" . $r["IDServicio"] . "' ";
                        $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                        if ($dbo->rows($qry_tipo_pago) > 0) {
                            $servicio["PagoReserva"] = "S";
                            while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                                $desactivado = false;

                                if (!$desactivado) {
                                    $tipopago["IDClub"] = $id_club;
                                    $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                                    $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                                    $tipopago["Talonera"] = $r_tipo_pago["Talonera"];
                                    $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                                    $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $id_club);

                                    if ($id_club == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                                        $tipopago["Nombre"] = "Pago en recepción";
                                    else
                                        $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                                    $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                                    //Para el condado y es pagos online muestro la imagen de placetopay

                                    switch ($r_tipo_pago["IDTipoPago"]) {
                                        case "1":
                                            $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                            break;
                                        case "2":
                                            $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                            break;
                                        case "3":
                                            $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                            $imagen = "https://www.miclubapp.com/file/noticia/icsi.png";
                                            break;
                                        case "11":
                                            $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                            break;
                                        case "9":
                                            $imagen = "https://www.miclubapp.com/file/noticia/ictarjeta.png";
                                            break;
                                        case "12":
                                            $imagen = "https://www.miclubapp.com/file/noticia/iccredibancopago.png";
                                            break;
                                        default:
                                            $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                            break;
                                    }

                                    $tipopago["Imagen"] = $imagen;
                                    array_push($response_tipo_pago, $tipopago);
                                }
                            } //end while
                            $servicio["TipoPago"] = $response_tipo_pago;
                        } else {
                            $servicio["PagoReserva"] = "N";
                        }

                        $servicio["MensajePagoReserva"] = $r["MensajePagoReserva"];

                        $resp_publicidad = SIMWebServicePublicidad::get_publicidad($id_club, "", "", "Socio", $r["IDServicio"]);
                        $servicio["PublicidadInfo"] = $resp_publicidad["response"][0];

                        $flag_mostrar = 0;
                        // Verificar si es un modulo que se debe revisar permiso
                        if (array_key_exists($r["IDServicioMaestro"], $array_id_mod_esp)) {
                            if (in_array($IDSocio, $array_id_mod_esp[$r["IDServicioMaestro"]])) {
                                $flag_mostrar = 0;
                            } else {
                                $flag_mostrar = 1;
                            }
                        } else {
                            $flag_mostrar = 0;
                        }

                        if ($flag_mostrar == 0) {
                            $response[$r_SubModulo['IDModulo']][$cont] = $servicio;
                            array_push($arr_Servicios, $servicio["IDServicio"]);
                            $cont++;
                        }
                    } //ednw hile
                } //End if
            }
        }

        unset($servicio);
        unset($cont);

        //traer servicios del usuario
        if ($TipoApp == "Empleado" && !empty($IDUsuario)) :
            unset($array_id_servicios);
            $id_servicios = "0";
            $response_servicio = array();
            $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
            $qry_servicios = $dbo->query($sql_servicios);
            while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                $array_id_servicios[] = $r_servicio["IDServicioMaestro"];
            } //end while
            if (count($array_id_servicios) > 0) :
                $id_servicios = implode(",", $array_id_servicios);
            endif;
        endif;

        $arr_Servicios = implode(',', $arr_Servicios);
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $id_club . "' and  IDServicioMaestro in (" . $id_servicios . ") and IDServicio not in (" . $arr_Servicios . ") " . $condicion_servicio . " ORDER BY Nombre ";
        // $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $id_club . "' and  IDServicioMaestro in (" . $id_servicios . ")" . $condicion_servicio . " ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $cont = 0;
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $servicio["IDClub"] = $r["IDClub"];
                $servicio["IDServicio"] = $r["IDServicio"];

                //Si tiene un nombre personalizado se lo pongo de lo contrario le pongo el general
                if (!empty($array_nom_servicios[$r["IDServicioMaestro"]])) :
                    $servicio["Nombre"] = $array_nom_servicios[$r["IDServicioMaestro"]];
                else :
                    $servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                endif;

                $foto = "";
                if (!empty($r["Icono"])) {
                    $foto = SERVICIO_ROOT . $r["Icono"];
                } else {
                    $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                    if (!empty($icono_maestro)) {
                        $foto = SERVICIO_ROOT . $icono_maestro;
                    }
                }

                $invitadoclub = "";
                $invitadoexterno = "";
                $servicio["Icono"] = $foto;
                $servicio["General"] = $dbo->getFields("ServicioMaestro", "General", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");

                //TEMPORAL....Consulto alguna disponibilidad para ver el numero de invitados, esto se debe hacer por el servicio que consulta disponibilidades
                $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");

                //Consulto cual es la mayor disponibildad en tiempo apara armar el dia maximo en el servicio cuando se empieza por el elemento
                $sql_disponibilidad = "Select * From Disponibilidad Where IDServicio = '" . $r["IDServicio"] . "'";
                $result_disponibilidad = $dbo->query($sql_disponibilidad);
                $dia_mayor = 0;
                while ($row_disponibilidad = $dbo->fetchArray($result_disponibilidad)) {
                    $medicion_tiempo_anticipacion = $row_disponibilidad["MedicionTiempoAnticipacion"];
                    $valor_anticipacion_turno = (int) $row_disponibilidad["Anticipacion"];
                    switch ($medicion_tiempo_anticipacion):
                        case "Dias":
                            $dias_anticipacion_turno = $valor_anticipacion_turno;
                            break;
                        case "Horas":
                            $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 24);
                            break;
                        case "Minutos":
                            $dias_anticipacion_turno = (int) ($valor_anticipacion_turno / 1440);
                            break;
                        default:
                            $dias_anticipacion_turno = 0;
                    endswitch;

                    if ((int) $dias_anticipacion_turno >= (int) $dia_mayor) :
                        $dia_mayor = $dias_anticipacion_turno;
                    endif;
                }

                $invitadoclub = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                if (!empty($invitadoclub)) :
                    $servicio["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                else :
                    $servicio["NumeroInvitadoClub"] = "";
                endif;
                if (!empty($invitadoexterno)) :
                    $servicio["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                else :
                    $servicio["NumeroInvitadoExterno"] = "";
                endif;


                $servicio["DiasMaximoReserva"] = "$dia_mayor";

                //temporal lagartos

                if ($id_club == 7 && $r["IDServicio"] == 43) { //Clases tenis especial
                    $dias_maximo_especial = 9 - (int) date("w");
                    $servicio["DiasMaximoReserva"] = "$dias_maximo_especial";
                }

                //FIN TEMPORAL

                $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $r["IDServicioMaestro"] . "' ", "array");

                if (!empty($r["LabelElementoBoton"])) {
                    $servicio["LabelElemento"] = $r["LabelElementoBoton"];
                } else {
                    $servicio["LabelElemento"] = $datos_maestro["LabelElementoBoton"];
                }

                $servicio["LabelElementoSocio"] = utf8_encode($r["LabelElementoSocio"]);
                $servicio["LabelElementoExterno"] = utf8_encode($r["LabelElementoExterno"]);

                $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $id_club . "'");
                if (empty($labelauxiliar)) {
                    $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                }

                if (!empty($r["LabelAuxiliar"])) {
                    $servicio["LabelAuxiliar"] = $r["LabelAuxiliar"];
                } else {
                    $servicio["LabelAuxiliar"] = $datos_maestro["LabelAuxiliar"];
                }

                if (!empty($r["HorarioAcordeon"])) {
                    $servicio["HorarioAcordeon"] = $r["HorarioAcordeon"];
                } else {
                    $servicio["HorarioAcordeon"] = $datos_maestro["HorarioAcordeon"];
                }

                if (!empty($r["LabelTipoReserva"])) {
                    $servicio["LabelTipoReserva"] = $r["LabelTipoReserva"];
                } else {
                    $servicio["LabelTipoReserva"] = $datos_maestro["LabelTipoReserva"];
                }

                $servicio["LabelTipoReserva"] = $datos_maestro["LabelTipoReserva"];

                $servicio["TextoLegal"] = $r["TextoLegal"];
                $servicio["DiasAnticipacion"] = "$r[DiasAnticipacion]";

                $servicio["AcordeonAbiertoPrimeraOpcion"] = "N";
                $servicio["ColorLetraHora"] = "$r[ColorLetraHora]";
                $servicio["ColorFondoHora"] = "$r[ColorFondoHora]";
                $servicio["NegrillaLetraHora"] = "S";

                if ($id_club == 25) : //Gun club y Pereira no permite beneficiarios y ya estab configurado asi se pone esta validacion especial
                    $servicio["PermiteBeneficiario"] = "N";
                    $servicio["LabelBeneficiario"] = "";
                else :

                    if (!empty($r["PermiteBeneficiario"])) {
                        $servicio["PermiteBeneficiario"] = $r["PermiteBeneficiario"];
                    } else {
                        $servicio["PermiteBeneficiario"] = $datos_maestro["PermiteBeneficiario"];
                    }

                    if (!empty($r["LabelBeneficiario"])) {
                        $servicio["LabelBeneficiario"] = $r["LabelBeneficiario"];
                    } else {
                        $servicio["LabelBeneficiario"] = $datos_maestro["LabelBeneficiario"];
                    }

                endif;

                $servicio["HoraDesde"] = $r["HoraHasta"];
                $servicio["HoraCancelacion"] = $r["HoraCancelacion"];
                $servicio["IntervaloHora"] = $r["IntervaloHora"];
                $servicio["MinutosReserva"] = "$r[MinutosReserva]";
                $servicio["TurnosMaximo"] = "$r[TurnosMaximo]";
                $id_servicio_inicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");


                if ((int)$r["IDServicioInicial"] > 0) {
                    $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $r["IDServicioInicial"] . "'");
                } else {
                    $servicio["PrimerServicio"] = $dbo->getFields("ServicioInicial", "Nombre", "IDServicioInicial = '" . $id_servicio_inicial . "'");
                }

                $servicio["Orden"] = $dbo->getFields("ServicioClub", "Orden", "IDClub = '" . $id_club . "' and IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                $servicio["Georeferenciacion"] = "$r[Georeferenciacion]";
                $servicio["Latitud"] = $r["Latitud"];
                $servicio["Longitud"] = $r["Longitud"];
                $servicio["Rango"] = $r["Rango"];
                $servicio["MensajeFueraRango"] = $r["MensajeFueraRango"];
                $servicio["MostrarBotonBuscar"] = $r["MostrarBotonBuscar"];
                $servicio["MultipleAuxiliar"] = $r["MultipleAuxiliar"];
                $servicio["MaximoInvitadosSalon"] = $r["MaximoInvitadosSalon"];
                $servicio["PermiteListaEspera"] = $r["PermiteListaEspera"];
                $servicio["PermiteListaEsperaAuxiliar"] = $r["PermiteListaEsperaAuxiliar"];
                $servicio["PermiteAgregarGrupo"] = $r["PermiteAgregarGrupo"];
                $servicio["PermiteEditarAuxiliar"] = $r["PermiteEditarAuxiliar"];
                $servicio["PermiteEditarAdicionales"] = $r["PermiteEditarAdicionales"];
                $servicio["AdicionalesObligatorio"] = $r["AdicionalesObligatorio"];
                $servicio["LabelEncabezadoInvitados"] = $r["LabelEncabezadoInvitados"];
                $servicio["LabelEncabezadoBeneficiarios"] = $r["LabelEncabezadoBeneficiarios"];
                $servicio["TextoAbrirMapa"] = $r["TextoAbrirMapa"];
                $servicio["SoloIcono"] = $dbo->getFields("Club", "SoloIcono", "IDClub = '" . $id_club . "'");
                $servicio["SoloFechaSeleccionada"] = $datos_maestro["SoloFechaSeleccionada"];
                $servicio["LabelReservaMultiple"] = $datos_maestro["LabelReservaMultiple"];
                $servicio["PermiteAdicionarServicios"] = $r["PermiteAdicionarServicios"];
                $servicio["MostrarDecimal"] = $r["MostrarDecimal"];
                $servicio["PermiteIrDomicilio"] = $r["PermiteIrDomicilio"];
                $servicio["LabelPermiteIrDomicilio"] = $r["LabelPermiteIrDomicilio"];
                $servicio["IDModuloDomicilio"] = $r["IDModuloDomicilio"];
                //Externos
                $servicio["PermiteInvitadoExternoCedula"] = $r["PermiteInvitadoExternoCedula"];
                $servicio["PermiteInvitadoExternoCorreo"] = $r["PermiteInvitadoExternoCorreo"];
                $servicio["InvitadoExternoPago"] = $r["InvitadoExternoPago"];
                $servicio["LabelInvitadoExternoPago"] = $r["LabelInvitadoExternoPago"];
                $servicio["InvitadoExternoValor"] = $r["InvitadoExternoValor"];

                $servicio["PermiteFiltroElementoFechaPorTexto"] = $r["PermiteFiltroElementoFechaPorTexto"];
                $servicio["PermiteFiltroElementoFechaPorBoton"] = $r["PermiteFiltroElementoFechaPorBoton"];
                $servicio["LabelFiltroElementoFechaPorTexto"] = $r["LabelFiltroElementoFechaPorTexto"];
                $servicio["LabelFiltroElementoFechaPorBoton"] = $r["LabelFiltroElementoFechaPorBoton"];
                $servicio["PermiteGuardarCalendarioDispositivo"] = $r["PermiteGuardarCalendarioDispositivo"];

                $servicio["LabelAdicionales"] = $r["LabelAdicionales"];
                $servicio["EncabezadoAdicionales"] = $r["EncabezadoAdicionales"];
                $servicio["LabelSeleccioneAdicionales"] = $r["LabelSeleccioneAdicionales"];
                $servicio["MensajeAdicionalesObligatorio"] = $r["MensajeAdicionalesObligatorio"];

                // CADDIES
                $servicio[PermiteAdicionarCaddies] = $r[PermiteAdicionarCaddies];
                $servicio[LabelAdicionarCaddies] = $r[LabelAdicionarCaddies];
                $servicio[ObligatorioSeleccionarCaddie] = $r[ObligatorioSeleccionarCaddie];
                $servicio[MensajeCaddiesObligatorio] = $r[MensajeCaddiesObligatorio];


                $servicio["MostrarImagenEncabezado"] = $r["MostrarImagenEncabezado"];
                $fotoencabezado = "";
                if (!empty($r["ImagenEncabezado"])) {
                    $fotoencabezado = SERVICIO_ROOT . $r["ImagenEncabezado"];
                } else {
                    $fotoencabezado = "";
                }

                $servicio["ImagenEncabezado"] = $fotoencabezado;

                $zonahoraria = date_default_timezone_get();
                $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                $servicio["GMT"] = SIMWebservice::timezone_offset_string($offset);

                //Campos Reservas
                $response_campos = array();
                $sql_campos = "SELECT * FROM ServicioCampo WHERE Publicar = 'S' and IDServicio= '" . $r["IDServicio"] . "' ORDER BY Nombre";
                $qry_campos = $dbo->query($sql_campos);
                if ($dbo->rows($qry_campos) > 0) {
                    while ($r_campos = $dbo->fetchArray($qry_campos)) {
                        $campos["IDClub"] = $id_club;
                        $campos["IDServicio"] = $r_campos["IDServicio"];
                        $campos["IDServicioCampo"] = $r_campos["IDServicioCampo"];
                        $campos["Nombre"] = $r_campos["Nombre"];
                        $campos["Descripcion"] = utf8_encode($r_campos["Descripcion"]);
                        $campos["Tipo"] = $r_campos["Tipo"];
                        $campos["Valor"] = $r_campos["Valor"];
                        $campos["Obligatorio"] = $r_campos["Obligatorio"];

                        array_push($response_campos, $campos);
                    } //end while
                }
                $servicio["CamposReserva"] = $response_campos;

                //Servicios Asociados
                $response_servicio_asociado = array();
                $datos_guardados = explode("|||", $r["IDServicioAsociado"]);
                foreach ($datos_guardados as $id_servicio) :
                    if (!empty($id_servicio)) {
                        $servicio_asoc["IDServicio"] = $id_servicio;
                        $id_servicio_mestro_menu = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                        $asoc_servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                        $asoc_servicio["NombrePersonalizado"] = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                        if (!empty($asoc_servicio["NombrePersonalizado"])) {
                            $NombreServicio = $asoc_servicio["NombrePersonalizado"];
                        } else {
                            $NombreServicio = $asoc_servicio["Nombre"];
                        }

                        $servicio_asoc["Nombre"] = $NombreServicio;
                        array_push($response_servicio_asociado, $servicio_asoc);
                    }
                endforeach;
                // $servicio["ServicioAsociado"] = $response_servicio_asociado;

                //Tipos de Reservas
                $response_tiporeservas = array();
                $sql_tiporeservas = "SELECT * FROM ServicioTipoReserva WHERE Activo = 'S' and IDServicio= '" . $r["IDServicio"] . "' " . $condicion_tipo . " ORDER BY Orden";
                $qry_tiporeservas = $dbo->query($sql_tiporeservas);
                if ($dbo->rows($qry_tiporeservas) > 0) {
                    while ($r_tiporeservas = $dbo->fetchArray($qry_tiporeservas)) {
                        $tiporeserva["IDClub"] = $id_club;
                        $tiporeserva["IDServicio"] = $r_tiporeservas["IDServicio"];
                        $tiporeserva["IDServicioTipoReserva"] = $r_tiporeservas["IDServicioTipoReserva"];
                        $tiporeserva["Nombre"] = $r_tiporeservas["Nombre"];
                        array_push($response_tiporeservas, $tiporeserva);
                    } //end while
                }

                // $servicio["TipoReserva"] = $response_tiporeservas;

                //Tipos de pagos recibidos
                $response_tipo_pago = array();
                $sql_tipo_pago = "SELECT * FROM ServicioTipoPago STP, TipoPago TP  WHERE STP.IDTipoPago = TP.IDTipoPago and IDServicio= '" . $r["IDServicio"] . "' ";
                $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                if ($dbo->rows($qry_tipo_pago) > 0) {
                    $servicio["PagoReserva"] = "S";
                    while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                        $desactivado = false;

                        if (!$desactivado) {
                            $tipopago["IDClub"] = $id_club;
                            $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                            $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                            $tipopago["Talonera"] = $r_tipo_pago["Talonera"];
                            $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                            $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $id_club);

                            if ($id_club == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                                $tipopago["Nombre"] = "Pago en recepción";
                            else
                                $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                            $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                            //Para el condado y es pagos online muestro la imagen de placetopay

                            switch ($r_tipo_pago["IDTipoPago"]) {
                                case "1":
                                    $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                    break;
                                case "2":
                                    $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                    break;
                                case "3":
                                    $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                    $imagen = "https://www.miclubapp.com/file/noticia/icsi.png";
                                    break;
                                case "11":
                                    $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                    break;
                                case "9":
                                    $imagen = "https://www.miclubapp.com/file/noticia/ictarjeta.png";
                                    break;
                                case "12":
                                    $imagen = "https://www.miclubapp.com/file/noticia/iccredibancopago.png";
                                    break;
                                default:
                                    $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                    break;
                            }

                            $tipopago["Imagen"] = $imagen;
                            array_push($response_tipo_pago, $tipopago);
                        }
                    } //end while
                    $servicio["TipoPago"] = $response_tipo_pago;
                } else {
                    $servicio["PagoReserva"] = "N";
                }

                $servicio["MensajePagoReserva"] = $r["MensajePagoReserva"];

                $resp_publicidad = SIMWebServicePublicidad::get_publicidad($id_club, "", "", "Socio", $r["IDServicio"]);
                $servicio["PublicidadInfo"] = $resp_publicidad["response"][0];

                $flag_mostrar = 0;

                // Verificar si es un modulo que se debe revisar permiso
                if (array_key_exists($r["IDServicioMaestro"], $array_id_mod_esp)) {
                    if (in_array($IDSocio, $array_id_mod_esp[$r["IDServicioMaestro"]])) {
                        $flag_mostrar = 0;
                    } else {
                        $flag_mostrar = 1;
                    }
                } else {
                    $flag_mostrar = 0;
                }

                if ($flag_mostrar == 0) {
                    $response['Reserva'][$cont] = $servicio;
                    $cont++;
                }
            } //ednw hile
        } //End if

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        if ($respuesta["response"] == null) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"]['Reserva'] = null;
        } //end else

        return $respuesta;
    }

    public function get_tipo_reserva($IDClub, $TipoApp, $IDUsuario, $IDSocio, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();
        $condicion_elemento = "";
        if (!empty($IDServicio)) {

            $response = array();
            $sql_tiporeservas = "SELECT * FROM ServicioTipoReserva WHERE Activo = 'S' and IDServicio= '$IDServicio' AND SoloAdmin = 0 ORDER BY Orden";
            $qry_tiporeservas = $dbo->query($sql_tiporeservas);
            if ($dbo->rows($qry_tiporeservas) > 0) {
                while ($r_tiporeservas = $dbo->fetchArray($qry_tiporeservas)) {
                    $tiporeserva["IDClub"] = $IDClub;
                    $tiporeserva["IDServicio"] = $r_tiporeservas["IDServicio"];
                    $tiporeserva["IDServicioTipoReserva"] = $r_tiporeservas["IDServicioTipoReserva"];
                    $tiporeserva["Nombre"] = $r_tiporeservas["Nombre"];

                    $iconoservicio = $r_tiporeservas["Icono"];
                    $foto = "";
                    if (!empty($iconoservicio)) {
                        $foto = SERVICIO_ROOT . $iconoservicio;
                    }
                    $tiporeserva["Icono"] = $foto;
                    array_push($response, $tiporeserva);
                } //end while
            }

            if ($IDClub = 227 && $IDServicio == 46131) {
                require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
                $response = SIMWebServiceCountryMedellin::get_tipo_reserva($IDSocio, $IDServicio, $Fecha,);
            }


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

    public function get_elementos($IDClub, $IDSocio, $IDServicio, $IDUsuario = "", $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $condicion_elemento = "";
        if (!empty($IDUsuario)) :
            $sql_elemento_usuario = "Select * From UsuarioServicioElemento Where IDUsuario = '" . $IDUsuario . "'";
            $result_elemento_usuario = $dbo->query($sql_elemento_usuario);
            while ($row_elemento_usuario = $dbo->fetchArray($result_elemento_usuario)) :
                $array_id_elemento[] = $row_elemento_usuario["IDServicioElemento"];
            endwhile;
            if (count($array_id_elemento) > 0) :
                $condicion_elemento = " and IDServicioElemento in (" . implode(",", $array_id_elemento) . ") ";
            endif;
        endif;

        $response = array();
        $sql = "SELECT SE.* FROM ServicioElemento SE, Servicio S WHERE SE.IDServicio = S.IDServicio and SE.Publicar = 'S' and S.IDClub = '" . $IDClub . "' and SE.IDServicio = '" . $IDServicio . "' " . $condicion_elemento . " ORDER BY SE.Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $elemento["IDElemento"] = $r["IDServicioElemento"];
                $elemento["IDFiltro"] = $r["IDServicioElemento"];
                $elemento["IDClub"] = $IDClub;
                $elemento["IDServicio"] = $r["IDServicio"];
                $elemento["IDPadre"] = $r["IDPadre"];
                $elemento["Nombre"] = $r["Nombre"];
                $elemento["Tipo"] = "Elemento";
                if (!empty($r["Foto"])) {
                    $FotoElemento = ELEMENTOS_ROOT . $r["Foto"];
                } else {
                    $FotoElemento = "";
                }

                $elemento["Foto"] = $FotoElemento;

                array_push($response, $elemento);
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

    public function get_horas($IDClub, $IDSocio, $IDServicio, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $response = array();
        $sql = "SELECT * FROM Servicio WHERE IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                // Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
                $sql_dispo_elemento_primera = "Select HoraDesde,HoraHasta From ServicioDisponibilidad Where IDServicio = '" . $IDServicio . "' and Activo <>'N' Order by HoraDesde Limit 1";
                $qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
                $row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);

                $minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'");

                $horaInicial = $row_dispo_elemento_primera["HoraDesde"];
                $minutoAnadir = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_dispo_elemento_primera["IDDisponibilidad"] . "'");
                $hora_final = strtotime($row_dispo_elemento_primera["HoraHasta"]);
                $hora_actual = $row_dispo_elemento_primera["HoraDesde"];

                while ($hora_actual <= $hora_final) :

                    $servicio_hora["IDClub"] = $r["IDClub"];
                    $servicio_hora["IDServicio"] = $r["IDServicio"];

                    if (strlen($horaInicial) != 8) :
                        $horaInicial .= ":00";
                    endif;

                    $servicio_hora["Hora"] = $horaInicial;
                    $zonahoraria = date_default_timezone_get();
                    $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                    $servicio_hora["GMT"] = SIMWebservice::timezone_offset_string($offset);

                    array_push($response, $servicio_hora);

                    $segundos_horaInicial = strtotime($horaInicial);
                    $segundos_minutoAnadir = $minutoAnadir * 60;
                    $array_horas = $nuevaHora = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                    $hora_actual = strtotime($nuevaHora);
                    $horaInicial = $nuevaHora;

                endwhile;
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

    public function get_campos($IDClub, $IDSocio, $IDServicio, $IDClubAsociado = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        $response = array();
        $sql = "SELECT SE.* FROM ServicioElemento SE, Servicio S WHERE SE.IDServicio = S.IDServicio and SE.Publicar = 'S' and S.IDClub = '" . $IDClub . "' and SE.IDServicio = '" . $IDServicio . "' ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $elemento["IDClub"] = $IDClub;
                $elemento["IDServicio"] = $r["IDServicio"];
                $elemento["IDCampo"] = $r["IDServicioElemento"];
                $elemento["Nombre"] = $r["Nombre"];

                array_push($response, $elemento);
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

    public function get_reservas_socio($IDClub, $IDSocio, $Limite = 0, $IDReserva = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();

        if ($IDClub == 227) :
            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $respuesta = SIMWebServiceCountryMedellin::App_ConsultarReservas($IDClub, $datos_socio);
            return $respuesta;
        endif;

        if ($IDClub == 125) :
            require_once LIBDIR . "SIMUruguay.inc.php";
            $respuesta = SIMUruguay::get_reservas_socio($IDClub, $IDSocio, $Limite, $IDReserva, $IDUsuario);
            return $respuesta;
        endif;

        $response = array();

        $array_id_consulta[] = $IDSocio;

        $socio_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
        // Si esta en blanco quiere decir que es socio cabeza y debe consultar las reservas de sus beneficiarios
        if ($socio_padre == "") :
            $accion_padre = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            $sql_beneficiarios = "SELECT * FROM Socio WHERE ( AccionPadre = '" . $accion_padre . "' and AccionPadre <> '') and IDClub = '" . $IDClub . "' ORDER BY Nombre Desc ";
            $qry_beneficiarios = $dbo->query($sql_beneficiarios);
            while ($r_beneficiario = $dbo->fetchArray($qry_beneficiarios)) :
                $array_id_consulta[] = $r_beneficiario[IDSocio];
            endwhile;
        endif;

        if (count($array_id_consulta) > 0 && empty($IDReserva)) :
            $where_beneficiario = "and (IDSocio in (" . implode(",", $array_id_consulta) . ") or IDSocioBeneficiario in (" . implode(",", $array_id_consulta) . ") or IDSocioReserva = '" . $IDSocio . "')";
        endif;

        if (!empty($IDReserva)) {
            $condicion_reserva = " and IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        if (empty($IDUsuario)) {
            $condicion_fecha = " and Fecha >= CURDATE() ";
        } else {
            $condicion_fecha = "  ";
        }

        $sql = "SELECT * FROM ReservaGeneral WHERE (IDClub = '$IDClub' OR IDClubOrigen = $IDClub) and IDEstadoReserva = 1  " . $condicion_fecha . $where_beneficiario . " " . $condicion_reserva . "ORDER BY Fecha ASC, Hora ASC  " . $condicion_limite;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

            while ($row_reserva = $dbo->fetchArray($qry)) :

                //si es el dia actual solo muestro las que estan pendientes

                $mostra_reserva = 1;
                $fecha_hoy = date("Y-m-d");

                $FechaHReserva = date("Y-m-d") . " " . $row_reserva["Hora"];
                $NuevaFechaHoraReserva = strtotime('+30 minute', strtotime($FechaHReserva));
                $NuevaFechaHoraReserva = date("H:i:s", $NuevaFechaHoraReserva);

                if (($row_reserva["Fecha"] == $fecha_hoy && $NuevaFechaHoraReserva <= date("H:i:s") && empty($IDUsuario))) {
                    $mostra_reserva = 0;
                    if ($dbo->rows($qry) == 1) {
                        $reserva["IDClub"] = "";
                        $reserva["IDSocio"] = "";
                        $reserva["IDReserva"] = "";
                        $reserva["IDServicio"] = "";
                        $id_servicio_maestro = "";
                        $reserva["NombreServicio"] = "";
                        $reserva["IDElemento"] = "";
                        $reserva["NombreElemento"] = "";
                        $reserva["Fecha"] = "";
                        $reserva["Tee"] = "";

                        array_push($response, $reserva);

                        $respuesta["message"] = "No tienes reservas programadas.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                        return $respuesta;
                    }
                }

                //$mostra_reserva=1;

                if ($mostra_reserva == 1) {

                    $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");

                    // Verifico si es una reserva asociada para no mostrarla en el resultado
                    $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                    $qry_auto = $dbo->query($sql_auto);
                    if ($dbo->rows($qry_auto) <= 0) {

                        $reserva["IDClub"] = $IDClub;
                        $reserva["IDSocio"] = $row_reserva["IDSocio"];
                        if ((int) ($row_reserva["IDSocioBeneficiario"]) <= 0) {
                            $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocio"] . "'");
                        } else {
                            $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'");
                        }

                        $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                        $reserva["IDServicio"] = $row_reserva["IDServicio"];
                        $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];

                        $iconoservicio = $datos_servicio["Icono"];
                        $foto = "";
                        if (!empty($iconoservicio)) {
                            $foto = SERVICIO_ROOT . $iconoservicio;
                        } else {
                            $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                            if (!empty($icono_maestro)) {
                                $foto = SERVICIO_ROOT . $icono_maestro;
                            }
                        }

                        $reserva["Icono"] = $foto;

                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        }

                        if ((int) $row_reserva["IDServicioTipoReserva"] > 0 && $IDClub != "9") :
                            $nombre_servicio_personalizado .= " (" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row_reserva["IDServicioTipoReserva"] . "'") . ")";
                        endif;

                        $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                        $reserva["NombreServicioPersonalizado"] = $nombre_servicio_personalizado;

                        //Para el polo se asigna cancha y equipo
                        if ($row_reserva["IDClub"] == "37" && $row_reserva["IDServicio"] == "3575" || ($row_reserva["IDClub"] == "143" && $row_reserva["IDServicio"] == "28122")) :
                            if (!empty($row_reserva["Cancha"]) && !empty($row_reserva["Equipo"])) :
                                $otros_datos_reserva = " Cancha " . $row_reserva["Cancha"] . " - " . $row_reserva["Equipo"];
                            else :
                                $otros_datos_reserva = "Pendiente de asignar cancha y equipo";
                            endif;
                        endif;

                        if ((int) $row_reserva["IDSocioBeneficiario"] > 0) {
                            $socio_benef = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                            $otros_datos_reserva = "(" . $socio_benef . ")";
                        } else {
                            $otros_datos_reserva = "(" . $reserva["Socio"] . ")";
                        }

                        if (!empty($datos_servicio["IdentificadorServicio"])) {
                            $otros_datos_reserva = " " . $row_reserva["IdentificadorServicio"] . "-" . $row_reserva["ConsecutivoServicio"];
                        }

                        $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                        $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                        $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'") . " " . $otros_datos_reserva;
                        $reserva["Fecha"] = $row_reserva["Fecha"];
                        $reserva["Tee"] = $row_reserva["Tee"];
                        $reserva["CantidadInvitadoSalon"] = $row_reserva["CantidadInvitadoSalon"];
                        $reserva["PagadaOnline"] = $row_reserva["Pagado"];
                        $reserva["FechaTransaccion"] = $row_reserva["FechaTransaccion"];
                        $reserva["IDServicioTipoReserva"] = $row_reserva["IDServicioTipoReserva"];
                        $reserva["MensajeTransaccion"] = "Mensaje transacción: " . $row_reserva["MensajeTransaccion"];

                        $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                        $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                        $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                        $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                        $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                        $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                        $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                        $reserva["LabelReconfimarBoton"] = $datos_servicio["LabelReconfimarBoton"];
                        $reserva["PermiteReconfirmar"] = $datos_servicio["PermiteReconfirmar"];
                        $reserva["LabelInvitados"] = $datos_servicio["LabelInvitados"];
                        $reserva["AdicionalesObligatorio"] = $datos_servicio["AdicionalesObligatorio"];
                        $reserva["TextoLegal"] = $datos_servicio["TextoLegal"];
                        $reserva["OcultarBotonEditarInvitados"] = $datos_servicio["OcultarBotonEditarInvitados"];
                        $reserva["LabelElemento"] = $datos_servicio["LabelElementoBoton"];
                        $reserva["OcultarHora"] = $datos_servicio["OcultarHora"];


                        //Externos
                        $reserva["PermiteInvitadoExternoCedula"] = $datos_servicio["PermiteInvitadoExternoCedula"];
                        $reserva["PermiteInvitadoExternoCorreo"] = $datos_servicio["PermiteInvitadoExternoCorreo"];
                        $reserva["PermiteInvitadoExternoFechaNacimiento"] = $datos_servicio["PermiteInvitadoExternoFechaNacimiento"];
                        $reserva["InvitadoExternoPago"] = $datos_servicio["InvitadoExternoPago"];
                        $reserva["LabelInvitadoExternoPago"] = $datos_servicio["LabelInvitadoExternoPago"];
                        $reserva["InvitadoExternoValor"] = $datos_servicio["InvitadoExternoValor"];

                        // Config Eliminar
                        $reserva["EliminarParaTodosOParaMi"] = $datos_servicio["EliminarParaTodosOParaMi"];
                        $reserva["MensajeEliminarParaTodosOParaMi"] = $datos_servicio["MensajeEliminarParaTodosOParaMi"];
                        $reserva["BotonEliminarReserva"] = $datos_servicio["BotonEliminarReserva"];
                        $reserva["LabelEliminarParaMi"] = $datos_servicio["LabelEliminarParaMi"];
                        $reserva["LabelEliminarParaTodos"] = $datos_servicio["LabelEliminarParaTodos"];
                        $reserva["CamposDinamicosInvitadoExternoHabilitado"] = $datos_servicio["CamposDinamicosInvitadoExternoHabilitado"];

                        $reserva["BotonEditarAdicionales"] = $datos_servicio["BotonEditarAdicionales"];
                        $reserva["LabelAdicionales"] = $datos_servicio["LabelAdicionales"];
                        $reserva["EncabezadoAdicionales"] = $datos_servicio["EncabezadoAdicionales"];
                        $reserva["LabelSeleccioneAdicionales"] = $datos_servicio["LabelSeleccioneAdicionales"];
                        $reserva["MensajeAdicionalesObligatorio"] = $datos_servicio["MensajeAdicionalesObligatorio"];

                        $reserva["PermiteEditarReserva"] = $datos_servicio["PermiteEditarReserva"];

                        if ($row_reserva[Pagado] != 'S') :
                            $reserva["PermitePago"] = $datos_servicio["PermitePagarMasTarde"];
                            $reserva["LabelBotonPago"] = $datos_servicio["PagarTotalLabel"];
                        endif;

                        // CONFIGURACIONES CADDIES
                        $reserva[PermiteAdicionarCaddies] = $datos_servicio[PermiteEditarCaddies];
                        $reserva[LabelAdicionarCaddies] = $datos_servicio[LabelAdicionarCaddies];
                        $reserva[ObligatorioSeleccionarCaddie] = $datos_servicio[ObligatorioSeleccionarCaddie];
                        $reserva[MensajeCaddiesObligatorio] = $datos_servicio[MensajeCaddiesObligatorio];

                        $reserva[LabelTicketsDescuento] = $datos_servicio[LabelTicketsDescuento];

                        if (($IDClub == "112" && $datos_servicio["IDServicio"] == 19939)) {
                            $reserva["TipoBotonInvitacion"] = "Capitan";
                        } else {
                            $reserva["TipoBotonInvitacion"] = "";
                        }

                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                        $response_auxiliar_reserva = array();
                        if (!empty($row_reserva["IDAuxiliar"])) :
                            $Array_Auxiliar = explode(",", $row_reserva["IDAuxiliar"]);
                            if (count($Array_Auxiliar) > 0) :
                                foreach ($Array_Auxiliar as $id_auxiliar) :
                                    if (!empty($id_auxiliar)) :
                                        $array_datos_auxiliar["IDAuxiliar"] = $id_auxiliar;
                                        $array_datos_auxiliar["Nombre"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $id_auxiliar . "'");
                                        $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $id_auxiliar . "'");
                                        $array_datos_auxiliar["Tipo"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                                        array_push($response_auxiliar_reserva, $array_datos_auxiliar);
                                    endif;
                                endforeach;
                            endif;

                            $reserva["ListaAuxiliar"] = $response_auxiliar_reserva;

                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["Auxiliar"] = utf8_encode($dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'"));
                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                        else :
                            unset($reserva['IDAuxiliar']);
                            unset($reserva['Auxiliar']);
                            unset($reserva['TipoAuxiliar']);
                            $reserva["ListaAuxiliar"] = array();
                        endif;

                        if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                            $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                            $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                        else :
                            unset($reserva['IDTipoModalidad']);
                            unset($reserva['Modalidad']);
                        endif;

                        if (strlen($row_reserva["Hora"]) != 8) :
                            $row_reserva["Hora"] .= ":00";
                        endif;

                        $reserva["Hora"] = $row_reserva["Hora"];




                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        if ($row_reserva["IDDisponibilidad"] <= 0) :
                            $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                        else :
                            $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                        endif;

                        //hora hasta
                        $numero_de_turnos =  $dbo->getFields("ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = '" . $row_reserva["IDServicioTipoReserva"] . "'");
                        $intervalo_horas = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $intervalo_horas *= $numero_de_turnos;
                        $hora_hasta = strtotime("+" . $intervalo_horas . " minute", strtotime($row_reserva["Hora"]));
                        $reserva["HoraFin"] =  date("H:i ", $hora_hasta);

                        $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)) :
                            $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            $reserva["NumeroMinimoInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroMinimoInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)) :
                            $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            $reserva["NumeroMinimoInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroMinimoInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoExterno"] = "";
                        endif;

                        if ($row_reserva["IDInvitadoBeneficiario"] > 0) :

                            $Invitado = $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");

                            if (empty($Invitado)) :
                                $Invitado = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                            endif;

                            $reserva["Beneficiario"] = " Inv. " . $Invitado;

                        else :

                            if ($row_reserva["IDSocioBeneficiario"] > 0) :
                                $Beneficiario = strtoupper(utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'")));
                            else :
                                $Beneficiario = "";
                            endif;

                            $reserva["Beneficiario"] = " Benef. " . $Beneficiario;

                        endif;

                        if ($row_reserva[IDCaddie] > 0) :
                            // CADDIE DE LA RESERVA
                            $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $row_reserva[IDCaddie]";
                            $QRYCaddie = $dbo->query($SQLCaddie);

                            if ($dbo->rows($QRYCaddie) > 0) :

                                while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :
                                    $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                    $Caddie[IDCaddie] = $DatoCaddie[IDCaddie];
                                    $Caddie[Nombre] = $DatoCaddie[Nombre];
                                    $Caddie[Categoria] = $datos_categoria[Categoria];
                                    $Caddie[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                    $Caddie[Precio] = $DatoCaddie[Precio];
                                    $Caddie[Disponible] = $DatoCaddie[Disponible];
                                    $Caddie[Texto] = $DatoCaddie[Descripcion];

                                endwhile;

                                $reserva[CaddieSocio] = $Caddie;
                            endif;
                        endif;



                        //Invitados Reserva
                        $response_invitados_reserva = array();
                        $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $total_invitado = $dbo->rows($sql_invitados_reserva);
                        while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)) :
                            $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];
                            $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                            $invitado_reserva[NombreExterno] = $r_invitados_reserva["Nombre"];
                            $invitado_reserva[Correo] = $r_invitados_reserva["Correo"];
                            $invitado_reserva[Cedula] = $r_invitados_reserva["Cedula"];
                            $invitado_reserva["SeleccionadoGrupo"] = $r_invitados_reserva["Confirmado"];
                            if ($r_invitados_reserva["IDSocio"] == 0) :
                                $tipo_invitado = "Externo";
                            else :
                                $tipo_invitado = "Socio";
                            endif;

                            $invitado_reserva[TipoInvitado] = $tipo_invitado;

                            $Ticket[IDTicket] = $r_invitados_reserva[IDTicketDescuento];

                            $Tickets = $dbo->fetchAll("TicketDescuento", "IDTicketDescuento = $r_invitados_reserva[IDTicketDescuento]");
                            $Ticket[ValorPorcentajeTexto] = $Tickets[ValorDescuento] . "% - " . $Tickets[Nombre];
                            $Ticket[Descripcion] = $Tickets[Descripcion];

                            $invitado_reserva[TicketDescuento] = $Ticket;

                            //Adicionales
                            $response_adicionales_inv = array();
                            $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                            FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                            WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                            IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $r_invitados_reserva["IDReservaGeneralInvitado"] . "'
                                                                                            GROUP BY IDServicioPropiedad
                                                                                            ORDER BY SP.Nombre";
                            $r_carac = $dbo->query($sql_carac);
                            while ($row_carac = $dbo->FetchArray($r_carac)) {

                                $adicionales_inv["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                                $adicionales_inv["EtiquetaCampo"] = $row_carac["Caracteristica"];
                                $adicionales_inv["TipoCampo"] = $row_carac["TipoCampo"];
                                $adicionales_inv["Valores"] = $row_carac["Valores"];
                                $adicionales_inv["ValoresID"] = $row_carac["Valor"];
                                $adicionales_inv["Total"] = $row_carac["Total"];
                                array_push($response_adicionales_inv, $adicionales_inv);
                            }

                            $invitado_reserva["Adicionales"] = $response_adicionales_inv;
                            //Fin Adicionales



                            // CADDIE INVITADO

                            $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $r_invitados_reserva[IDCaddie]";
                            $QRYCaddie = $dbo->query($SQLCaddies);

                            while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                                $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                $CaddieInvitado[IDCaddie] = $DatoCaddie[IDCaddie];
                                $CaddieInvitado[Nombre] = $DatoCaddie[Nombre];
                                $CaddieInvitado[Categoria] = $datos_categoria[Categoria];
                                $CaddieInvitado[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                $CaddieInvitado[Precio] = $DatoCaddie[Precio];
                                $CaddieInvitado[Disponible] = $DatoCaddie[Disponible];
                                $CaddieInvitado[Texto] = $DatoCaddie[Descripcion];

                            endwhile;

                            $invitado_reserva["Caddie"] = $CaddieInvitado;

                            // CAMPOS ADICIONALES INIVTADOS

                            $reponse_campos_invitados = array();
                            $SQLCampos = "SELECT * FROM RespuestasCampoInvitadoExterno WHERE IDReservaGeneralInvitado = $r_invitados_reserva[IDReservaGeneralInvitado]";
                            $QRYCampos = $dbo->query($SQLCampos);

                            while ($Datos = $dbo->fetchArray($QRYCampos)) :

                                $InfoCampos[IDCampoInvitadoExterno] = $Datos[IDCampoInvitadoExterno];
                                $InfoCampos[Valor] = $Datos[Valor];
                                array_push($reponse_campos_invitados, $InfoCampos);
                            endwhile;

                            $invitado_reserva["CamposDinamicos"] = $reponse_campos_invitados;

                            array_push($response_invitados_reserva, $invitado_reserva);
                        endwhile;

                        /*
                            //Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
                            if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
                            //Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
                            if ($id_disponibilidad>0):
                            $minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            endif;
    
                            if($total_invitado<$minimo_invitado_reserva):
                            $invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
                            $invitado_reserva[IDSocio]=$IDSocio;
                            $invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));
                            $tipo_invitado = "Socio";
                            $invitado_reserva[TipoInvitado]=$tipo_invitado;
                            array_push($response_invitados_reserva, $invitado_reserva);
                            endif;
                            endif;
                                */

                        $reserva["Invitados"] = $response_invitados_reserva;

                        //Reservas asociadas
                        $response_reserva_asociada = array();
                        $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                        foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                            array_push($response_reserva_asociada, $datos_reserva);
                        endforeach;
                        $reserva["ReservaAsociada"] = $response_reserva_asociada;

                        //Adicionales
                        $response_adicionales = array();
                        $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                FROM ReservaGeneralAdicional RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'
                                                                                GROUP BY IDServicioPropiedad
                                                                                ORDER BY SP.Nombre";
                        $r_carac = $dbo->query($sql_carac);
                        while ($row_carac = $dbo->FetchArray($r_carac)) {

                            $adicionales["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                            $adicionales["EtiquetaCampo"] = $row_carac["Caracteristica"];
                            $adicionales["TipoCampo"] = $row_carac["TipoCampo"];
                            $adicionales["Valores"] = $row_carac["Valores"];
                            $adicionales["ValoresID"] = $row_carac["Valor"];
                            $adicionales["Total"] = $row_carac["Total"];
                            array_push($response_adicionales, $adicionales);
                        }

                        $reserva["Adicionales"] = $response_adicionales;
                        //Fin Adicionales

                        //preguntas Reservas
                        $sql_otros = "Select Valor,IDServicioCampo From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'";

                        $result_otros = $dbo->query($sql_otros);
                        $response_otros = array();
                        while ($row_otros = $dbo->fetchArray($result_otros)) :

                            $array_otros["Nombre"] = $dbo->getFields("ServicioCampo", "Nombre", "IDServicioCampo = '" . $row_otros["IDServicioCampo"] . "'");
                            $array_otros["Valor"] = (string)$row_otros["Valor"];

                            array_push($response_otros, $array_otros);
                        endwhile;

                        $reserva["CamposReserva"] = $response_otros;
                        //fin preguntas Reservas

                        array_push($response, $reserva);
                        unset($row_reserva);
                    } // fin verificar si fue un areserva automatica
                }

            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            if ($IDClub == 51) { //Para condado muestro false
                $respuesta["message"] = "No tienes reservas programadas.";
                $respuesta["success"] = false;
                $respuesta["response"] = $response;
            } else {

                $reserva["IDClub"] = "";
                $reserva["IDSocio"] = "";
                $reserva["IDReserva"] = "";
                $reserva["IDServicio"] = "";
                $id_servicio_maestro = "";
                $reserva["NombreServicio"] = "";
                $reserva["IDElemento"] = "";
                $reserva["NombreElemento"] = "";
                $reserva["Fecha"] = "";
                $reserva["Tee"] = "";

                array_push($response, $reserva);
                $respuesta["message"] = "No tienes reservas programadas.";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } //end else

        return $respuesta;
    }

    public function get_reservas_empleado($IDClub, $IDUsuario, $Limite = 0, $IDReserva = "")
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDReserva)) {
            $condicion_reserva = " and IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        $sql = "SELECT * FROM ReservaGeneral WHERE IDClub = '" . $IDClub . "' and IDEstadoReserva = 1 and Fecha >= CURDATE() and IDUsuarioReserva = '" . $IDUsuario . "' ORDER BY Fecha Desc  " . $condicion_limite;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

            while ($row_reserva = $dbo->fetchArray($qry)) :

                // Verifico si es una reserva asociada para no mostrarla en el resultado
                $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                $qry_auto = $dbo->query($sql_auto);
                if ($dbo->rows($qry_auto) <= 0) {

                    $reserva["IDClub"] = $IDClub;
                    $reserva["IDSocio"] = $IDSocio;
                    $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                    $reserva["IDServicio"] = $row_reserva["IDServicio"];
                    $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
                    $reserva["NombreServicio"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                    $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                    $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                    $reserva["Fecha"] = $row_reserva["Fecha"];
                    $reserva["Tee"] = $row_reserva["Tee"];

                    if (!empty($row_reserva["IDAuxiliar"])) :
                        $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                        $reserva["Auxiliar"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                        $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                        $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                    else :
                        unset($reserva['IDAuxiliar']);
                        unset($reserva['Auxiliar']);
                        unset($reserva['TipoAuxiliar']);
                    endif;

                    if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                        $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                        $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                    else :
                        unset($reserva['IDTipoModalidad']);
                        unset($reserva['Modalidad']);
                    endif;

                    if (strlen($row_reserva["Hora"]) != 8) :
                        $row_reserva["Hora"] .= ":00";
                    endif;

                    $reserva["Hora"] = $row_reserva["Hora"];

                    $zonahoraria = date_default_timezone_get();
                    $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                    $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                    if ($row_reserva["IDDisponibilidad"] <= 0) :
                        $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                    else :
                        $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                    endif;

                    $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                    if (!empty($invitadoclub)) :
                        $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    else :
                        $reserva["NumeroInvitadoClub"] = "";
                    endif;
                    if (!empty($invitadoexterno)) :
                        $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                    else :
                        $reserva["NumeroInvitadoExterno"] = "";
                    endif;

                    if ($row_reserva["IDInvitadoBeneficiario"] > 0) :
                        $reserva["Beneficiario"] = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                    endif;
                    if ($row_reserva["IDSocioBeneficiario"] > 0) :
                        $reserva["Beneficiario"] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                    endif;

                    //Invitados Reserva
                    $response_invitados_reserva = array();
                    $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                    $total_invitado = $dbo->rows($sql_invitados_reserva);
                    while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)) :
                        $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                        $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                        $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];
                        $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                        $invitado_reserva[NombreExterno] = strtoupper($r_invitados_reserva["Nombre"]);
                        if ($r_invitados_reserva["IDSocio"] == 0) :
                            $tipo_invitado = "Externo";
                        else :
                            $tipo_invitado = "Socio";
                        endif;

                        $invitado_reserva[TipoInvitado] = $tipo_invitado;

                        array_push($response_invitados_reserva, $invitado_reserva);
                    endwhile;

                    /*
                        //Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
                        if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
                        //Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
                        if ($id_disponibilidad>0):
                        $minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        endif;
    
                        if($total_invitado<$minimo_invitado_reserva):
                        $invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
                        $invitado_reserva[IDSocio]=$IDSocio;
                        $invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));
                        $tipo_invitado = "Socio";
                        $invitado_reserva[TipoInvitado]=$tipo_invitado;
                        array_push($response_invitados_reserva, $invitado_reserva);
                        endif;
                        endif;
                            */

                    $reserva["Invitados"] = $response_invitados_reserva;

                    //Reservas asociadas
                    $response_reserva_asociada = array();
                    $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                    foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                        array_push($response_reserva_asociada, $datos_reserva);
                    endforeach;
                    $reserva["ReservaAsociada"] = $response_reserva_asociada;

                    array_push($response, $reserva);
                } // fin verificar si fue un areserva automatica
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No tienes reservas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    // NUEVAS

    public function get_agenda($IDClub, $IDUsuario, $Fecha)
    {
        $dbo = &SIMDB::get();
        if (empty($Fecha)) :
            $Fecha = date("Y-m-d");
        endif;

        if (!empty($IDUsuario)) {
            //Consulto el servicio que tiene permiso y el elemnto para consultar la agenda
            $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
            $qry_servicios = $dbo->query($sql_servicios);
            $response_agenda = array();
            $response = array();
            $agenda_dia = false;
            while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                //Consulto solo los elementos al los que tiene permiso de ver
                //$sql_elementos = "Select * From UsuarioServicioElemento Where IDUsuario = '".$IDUsuario."'";
                $sql_elementos = "Select *
                                        From UsuarioServicioElemento USES, ServicioElemento SE
                                        Where SE.IDServicioElemento = USES.IDServicioElemento
                                        and IDServicio = '" . $r_servicio["IDServicio"] . "'
                                        and IDUsuario = '" . $IDUsuario . "'";

                $qry_elementos = $dbo->query($sql_elementos);
                while ($row_elemento = $dbo->fetchArray($qry_elementos)) :
                    //Si el elemnto pertenece al servicio lo consulto
                    $horas = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $r_servicio["IDServicio"], $Fecha, $row_elemento["IDServicioElemento"], "Agenda", "", "", "", "S", "", $IDUsuario);

                    if ($horas["response"][0]) :
                        if (count($horas["response"][0]["Disponibilidad"][0]) > 0) :
                            $agenda_dia = true;
                            array_push($response, $horas["response"][0]);
                        endif;
                    endif;
                endwhile;
            } //end while

            //Para los auxiliares monitores muestro los elemtos donde esten reservados
            $sql_aux = "SELECT A.IDAuxiliar, IDServicio FROM UsuarioAuxiliar UA, Auxiliar A WHERE UA.IDAuxiliar=A.IDAuxiliar and UA.IDUsuario='" . $IDUsuario . "' ";
            $result_aux = $dbo->query($sql_aux);
            while ($row_aux = $dbo->fetchArray($result_aux)) {
                // Consulto las reserva en esta fecha de este usuario
                $sql_reserva = "SELECT IDServicioElemento From ReservaGeneral Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "' and IDAuxiliar like '%" . $row_aux["IDAuxiliar"] . "%' ";
                $r_reserva = $dbo->query($sql_reserva);
                while ($row_reserva = $dbo->fetchArray($r_reserva)) {
                    $array_elemento[$row_reserva["IDServicioElemento"]] = $row_reserva["IDServicioElemento"];
                }
                if (count($array_elemento > 0)) {
                    foreach ($array_elemento as $id_elemento_aux) {
                        unset($array_disponibilidad);
                        $horas = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $row_aux["IDServicio"], $Fecha, $id_elemento_aux, "Agenda", "", "", "", "S");
                        if ($horas["response"][0]) :
                            if (count($horas["response"][0]["Disponibilidad"][0]) > 0) :
                                $agenda_dia = true;
                                // Solo muestro donde este reservado el auxiliar
                                foreach ($horas["response"][0]["Disponibilidad"][0] as $datos_disponibilidad) {
                                    $array_id_aux = explode(",", $datos_disponibilidad["IDAuxiliar"]);
                                    if (in_array($row_aux["IDAuxiliar"], $array_id_aux)) {
                                        $array_disponibilidad[] = $datos_disponibilidad;
                                        //print_r($datos_disponibilidad["IDAuxiliar"]);
                                        //echo "<br>";
                                    }
                                }
                                if (count($array_disponibilidad) <= 0) {
                                    $array_disponibilidad = array();
                                }
                                $horas["response"][0]["Disponibilidad"][0] = $array_disponibilidad;
                                array_push($response, $horas["response"][0]);
                            endif;
                        endif;
                    }
                }
            }

            if ($agenda_dia) :
                //$response["Agenda"] = $response_agenda;
                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else :
                //$response["Agenda"] = $response_agenda;
                $respuesta["message"] = "No tiene reservas para hoy.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        } else {
            $respuesta["message"] = "28. Atencion faltan parametros en agenda";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function verifica_sancion_socio($IDClub, $IDSocio, $IDServicio, $FechaReserva, $IDBeneficiario = "")
    {
        $dbo = &SIMDB::get();
        $sancion_vigente = false;
        $reserva_no_cumplida = 0;
        $reserva_anterior = "";
        $contador_reserva_no_cumplida_seguida = 0;
        $contador_reserva_parcial = 0;
        $numero_dia_sancion = 0;
        $contador_reserva_socio = 0;
        $contador_reserva_parcial_seguida = 0;
        $FechaUltimaNoCumplida = "";
        $FechaUltimaNoCumplidaP = "";
        if (!empty($IDSocio) && !empty($IDClub) && !empty($IDServicio)) {

            $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $IDServicio . "'");
            //Consulto si hay sanciones publicadas ene l club
            $sql_sancion = "Select * From Sancion Where IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $IDServicioMaestro . "' and Publicar = 'S'";


            //para cantegril hago una consulta de la sancion para todos los servicios
            if ($IDClub == 185) {
                $IDServicioMaestro = 0;
                $sql_sancion = "Select * From Sancion Where IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $IDServicioMaestro . "' and Publicar = 'S'";
            }

            $result_sancion = $dbo->query($sql_sancion);
            $total_sanciones = $dbo->rows($result_sancion);
            if ($total_sanciones > 0) :

                $array_socio[] = $IDSocio;

                // PARA EL CAMPESTRE PEREIRA SE DEBE VALIDAR TODO EL NUCLEO FAMILIAR
                if ($IDClub == 15) :

                    $sqlSocio =  "SELECT Accion, IDSocio FROM Socio WHERE IDSocio = '" . $IDSocio . "' AND IDClub = '" . $IDClub . "'";
                    $qrySocio = $dbo->query($sqlSocio);
                    $datos_socio = $dbo->fetchArray($qrySocio);

                    $accion_socio = $datos_socio["Accion"];

                    $sql_nucleo = "Select IDSocio From Socio Where Accion = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                    $result_nucleo = $dbo->query($sql_nucleo);
                    while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                        $array_socio[] = $row_nucleo["IDSocio"];
                    endwhile;

                endif;

                if (count($array_socio) > 0) :
                    $id_socio_nucleo = implode(",", $array_socio);
                endif;

                if ($IDClub == 107) :
                    // PARA VISTA HERMOSA DEBEMOS VALIDAR POR BIMESTRE
                    $MesFecha = date('n', strtotime($FechaReserva));
                    $AnioFecha = date('Y', strtotime($FechaReserva));
                    if ($MesFecha % 2 == 0) :
                        $BimestreMes1 = "0" . $MesFecha - 1;
                        $BimestreMes2 = "0" . $MesFecha;
                    else :
                        $BimestreMes1 = "0" . $MesFecha;
                        $BimestreMes2 = "0" . $MesFecha + 1;
                    endif;

                    $CondicionBimestre = " AND (MONTH(Fecha) = '$BimestreMes1' OR MONTH(Fecha) = '$BimestreMes2')";
                endif;


                if (!empty($IDBeneficiario)) :
                    $sql_reserva_socio = "SELECT IDReservaGeneral, Cumplida, Fecha
                                            From ReservaGeneral
                                            Where (IDSocio = '$IDBeneficiario' OR IDSocioBeneficiario = '$IDBeneficiario')
                                            and IDServicio = '$IDServicio' and
                                            IDEstadoReserva = 1 and CumplidaCabeza = 'N' $CondicionBimestre
                                            Order By Fecha Desc Limit 10";
                else :
                    $sql_reserva_socio = "Select IDReservaGeneral, Cumplida, Fecha
                                            From ReservaGeneral
                                            Where IDSocio in ('" . $id_socio_nucleo . "')
                                            and IDServicio = '" . $IDServicio . "' and
                                            IDEstadoReserva = 1 and CumplidaCabeza = 'N' $CondicionBimestre
                                            Order By Fecha Desc Limit 10";
                endif;

                //para cantegril hago una consulta de las reservas para todos los servicios
                if ($IDClub == 185) {
                    $MesActual = date('m');
                    $CondicionMes = " AND (MONTH(Fecha) = '$MesActual' )";

                    if (!empty($IDBeneficiario)) :
                        $sql_reserva_socio = "SELECT IDReservaGeneral, Cumplida, Fecha
                                                From ReservaGeneral
                                                Where (IDSocio = '$IDBeneficiario' OR IDSocioBeneficiario = '$IDBeneficiario')
                                                 and
                                                IDEstadoReserva = 1 and CumplidaCabeza = 'N'  $CondicionMes
                                                Order By Fecha Desc Limit 10";
                    else :
                        $sql_reserva_socio = "Select IDReservaGeneral, Cumplida, Fecha
                                                From ReservaGeneral
                                                Where IDSocio in ('" . $id_socio_nucleo . "')
                                                and
                                                IDEstadoReserva = 1 and CumplidaCabeza = 'N'  $CondicionMes
                                                Order By Fecha Desc Limit 10";
                    endif;
                }


                $result_reserva_socio = $dbo->query($sql_reserva_socio);

                while ($row_reserva_socio = $dbo->fetchArray($result_reserva_socio)) :

                    if ($reserva_anterior == "" && $row_reserva_socio["Cumplida"] == "N") {
                        $reserva_anterior = "N";
                    }

                    if ($row_reserva_socio["Cumplida"] == "N") :
                        $contador_reserva_no_cumplida++;
                        if (empty($FechaUltimaNoCumplida)) {
                            $FechaUltimaNoCumplida = $row_reserva_socio["Fecha"];
                        }

                    endif;

                    if ($row_reserva_socio["Cumplida"] == "P") :
                        $contador_reserva_parcial++;
                        if (empty($FechaUltimaNoCumplida)) {
                            $FechaUltimaNoCumplidaP = $row_reserva_socio["Fecha"];
                        }

                    endif;

                    if (($reserva_anterior == "N" || $reserva_anterior == "P") && $row_reserva_socio["Cumplida"] == "N") :
                        $contador_reserva_no_cumplida_seguida++;
                    endif;

                    if (($reserva_anterior == "N" || $reserva_anterior == "P") && $row_reserva_socio["Cumplida"] == "P") :
                        $contador_reserva_parcial_seguida++;
                    endif;

                    $reserva_anterior = $row_reserva_socio["Cumplida"];

                    if ($contador_reserva_socio == 0) :
                        $fecha_ultima_reserva = $row_reserva_socio["Fecha"];
                    endif;
                    $contador_reserva_socio++;
                endwhile;

                if (($contador_reserva_no_cumplida_seguida) > 0) {
                    $contador_reserva_no_cumplida_seguida++;
                }

                if (($contador_reserva_parcial_seguida) > 0) {
                    $contador_reserva_parcial_seguida++;
                }

                /*
                    echo "<br>RESE " . $contador_reserva_socio;
                    echo "<br>SEG " . $contador_reserva_no_cumplida_seguida;
                    echo "<br>NO CUM " . $contador_reserva_no_cumplida;
                    echo "<br>Parcial " . $contador_reserva_parcial;
                    echo "<br>Parcial Seguida " . $contador_reserva_parcial_seguida;
                    */

                while ($row_sancion = $dbo->fetchArray($result_sancion)) :
                    //Consulto solo si se ha encontrado una sancion
                    if ($numero_dia_sancion == 0) :
                        if ($row_sancion["Cumplida"] == "N") :
                            if ($row_sancion["Seguida"] == "S") :
                                if ($contador_reserva_no_cumplida_seguida >= $row_sancion["NumeroIncumplida"]) :
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            elseif ($row_sancion["Seguida"] == "N") :
                                if ($contador_reserva_no_cumplida >= $row_sancion["NumeroIncumplida"]) :
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            endif;
                        elseif ($row_sancion["Cumplida"] == "P") :
                            if ($row_sancion["Seguida"] == "S") :
                                if ($contador_reserva_parcial_seguida >= $row_sancion["NumeroIncumplida"]) :
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            elseif ($row_sancion["Seguida"] == "N") :
                                if ($contador_reserva_parcial >= $row_sancion["NumeroIncumplida"]) :
                                    $numero_dia_sancion = $row_sancion["NumeroDiasBloqueo"];
                                endif;
                            endif;

                        endif;
                    endif;
                endwhile;

                //Si se encontro numero de dias en sanciones consulto si ya la cumplio
                if ($numero_dia_sancion > 0) :
                    //sumo los dias de sancion a la fecha de ultima reserva registrada
                    $fecha_hoy = date("Y-m-j");
                    $fecha_hoy = $FechaReserva;
                    $fecha_actual = $fecha_ultima_reserva;
                    if (empty($FechaUltimaNoCumplida)) {
                        $FechaUltimaNoCumplida = $FechaUltimaNoCumplidaP;
                    }

                    $fecha_final_sancion = strtotime('+' . (int) $numero_dia_sancion . ' day', strtotime($FechaUltimaNoCumplida));
                    $fecha_final_sancion = date('Y-m-j', $fecha_final_sancion);
                    if (strtotime($fecha_hoy) <= strtotime($fecha_final_sancion)) :
                        $sancion_vigente = true;
                    //echo "<br>ID SANC  " . $numero_dia_sancion;
                    endif;
                endif;
            endif;
        }

        return $sancion_vigente;
    }

    public function get_reservas_socio_invitado($IDClub, $IDSocio, $Limite = 0, $IDReserva = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        $array_id_consulta[] = $IDSocio;

        if (!empty($IDReserva)) {
            $condicion_reserva = " and RG.IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        //Selecciono las reservas donde el socio esta como invitado
        $sql = "SELECT * FROM ReservaGeneral RG,ReservaGeneralInvitado RGI WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and RGI.IDSocio = '" . $IDSocio . "' and RG.IDEstadoReserva = 1 and RG.Fecha >= CURDATE() " . $where_beneficiario . " " . $condicion_reserva . "ORDER BY RG.Fecha Desc  " . $condicion_limite;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {

            $message = $dbo->rows($qry) . " Encontrados";

            while ($row_reserva = $dbo->fetchArray($qry)) :

                $mostra_reserva = 1;
                $fecha_hoy = date("Y-m-d");
                if ($row_reserva["Fecha"] == $fecha_hoy && $row_reserva["Hora"] <= date("H:i:s") && empty($IDUsuario)) {
                    $mostra_reserva = 0;
                    if ($dbo->rows($qry) == 1) {
                        $reserva["IDClub"] = "";
                        $reserva["IDSocio"] = "";
                        $reserva["IDReserva"] = "";
                        $reserva["IDServicio"] = "";
                        $id_servicio_maestro = "";
                        $reserva["NombreServicio"] = "";
                        $reserva["IDElemento"] = "";
                        $reserva["NombreElemento"] = "";
                        $reserva["Fecha"] = "";
                        $reserva["Tee"] = "";
                        array_push($response, $reserva);
                        $respuesta["message"] = "No tienes reservas programadas.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                        return $respuesta;
                    }
                }

                if ($row_reserva[IDClubOrigen] > 0) :
                    $IDClub = $row_reserva[IDClubOrigen];
                endif;

                //$mostra_reserva=1;

                if ($mostra_reserva == 1) {

                    // Verifico si es una reserva asociada para no mostrarla en el resultado
                    $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                    $qry_auto = $dbo->query($sql_auto);
                    if ($dbo->rows($qry_auto) <= 0) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");

                        $reserva["IDClub"] = $IDClub;
                        $reserva["IDSocio"] = $IDSocio;
                        $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                        $reserva["IDServicio"] = $row_reserva["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");

                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        }

                        $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'");
                        $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                        $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                        $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                        $reserva["Fecha"] = $row_reserva["Fecha"];
                        $reserva["Tee"] = $row_reserva["Tee"];
                        $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                        $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                        $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                        $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                        $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                        $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                        $reserva["AdicionalesObligatorio"] = $datos_servicio["AdicionalesObligatorio"];
                        $reserva["TextoLegal"] = $datos_servicio["TextoLegal"];
                        $reserva["OcultarHora"] = $datos_servicio["OcultarHora"];

                        // Config Eliminar
                        $reserva["EliminarParaTodosOParaMi"] = $datos_servicio["EliminarParaTodosOParaMi"];
                        $reserva["MensajeEliminarParaTodosOParaMi"] = $datos_servicio["MensajeEliminarParaTodosOParaMi"];
                        $reserva["BotonEliminarReserva"] = $datos_servicio["BotonEliminarReserva"];
                        $reserva["LabelEliminarParaMi"] = $datos_servicio["LabelEliminarParaMi"];
                        $reserva["LabelEliminarParaTodos"] = $datos_servicio["LabelEliminarParaTodos"];

                        // INVITADOS EXTERNOS
                        $servicio["PermiteInvitadoExternoCedula"] = $datos_servicio["PermiteInvitadoExternoCedula"];
                        $servicio["PermiteInvitadoExternoCorreo"] = $datos_servicio["PermiteInvitadoExternoCorreo"];
                        $servicio["PermiteInvitadoExternoFechaNacimiento"] = $datos_servicio["PermiteInvitadoExternoFechaNacimiento"];


                        // CONFIGURACIONES CADDIES
                        $reserva[PermiteAdicionarCaddies] = $datos_servicio[PermiteEditarCaddies];
                        $reserva[LabelAdicionarCaddies] = $datos_servicio[LabelAdicionarCaddies];
                        $reserva[ObligatorioSeleccionarCaddie] = $datos_servicio[ObligatorioSeleccionarCaddie];
                        $reserva[MensajeCaddiesObligatorio] = $datos_servicio[MensajeCaddiesObligatorio];

                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                        if (!empty($row_reserva["IDAuxiliar"])) :
                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["Auxiliar"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                        else :
                            unset($reserva['IDAuxiliar']);
                            unset($reserva['Auxiliar']);
                            unset($reserva['TipoAuxiliar']);
                        endif;

                        if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                            $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                            $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                        else :
                            unset($reserva['IDTipoModalidad']);
                            unset($reserva['Modalidad']);
                        endif;

                        if (strlen($row_reserva["Hora"]) != 8) :
                            $row_reserva["Hora"] .= ":00";
                        endif;

                        $reserva["Hora"] = $row_reserva["Hora"];

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        if ($row_reserva["IDDisponibilidad"] <= 0) :
                            $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "'");
                        else :
                            $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                        endif;
                        //hora hasta
                        $numero_de_turnos =  $dbo->getFields("ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = '" . $row_reserva["IDServicioTipoReserva"] . "'");
                        $intervalo_horas = $dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $intervalo_horas *= $numero_de_turnos;
                        $hora_hasta = strtotime("+" . $intervalo_horas . " minute", strtotime($row_reserva["Hora"]));
                        $reserva["HoraFin"] =  date("H:i ", $hora_hasta);

                        $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)) :
                            $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)) :
                            $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoExterno"] = "";
                        endif;

                        if ($row_reserva["IDInvitadoBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                        endif;
                        if ($row_reserva["IDSocioBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                        endif;

                        // CADDIE DE LA RESERVA
                        $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $row_reserva[IDCaddie]";
                        $QRYCaddie = $dbo->query($SQLCaddies);

                        while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                            $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                            $Caddie[IDCaddie] = $DatoCaddie[IDCaddie];
                            $Caddie[Nombre] = $DatoCaddie[Nombre];
                            $Caddie[Categoria] = $datos_categoria[Categoria];
                            $Caddie[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                            $Caddie[Precio] = $DatoCaddie[Precio];
                            $Caddie[Disponible] = $DatoCaddie[Disponible];
                            $Caddie[Texto] = $DatoCaddie[Descripcion];

                        endwhile;
                        $reserva[CaddieSocio] = $Caddie;


                        //Invitados Reserva
                        $response_invitados_reserva = array();
                        $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $total_invitado = $dbo->rows($sql_invitados_reserva);
                        while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)) :
                            $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];

                            if ($r_invitados_reserva["IDSocio"] == $IDSocio) {
                                $IDInvitacionEliminar = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            }

                            $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                            $invitado_reserva[NombreExterno] = strtoupper($r_invitados_reserva["Nombre"]);
                            if ($r_invitados_reserva["IDSocio"] == 0) :
                                $tipo_invitado = "Externo";
                            else :
                                $tipo_invitado = "Socio";
                            endif;

                            $invitado_reserva[TipoInvitado] = $tipo_invitado;

                            //Adicionales
                            $response_adicionales_inv = array();
                            $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                             FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                             WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                          IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $r_invitados_reserva["IDReservaGeneralInvitado"] . "'
                                                                                             GROUP BY IDServicioPropiedad
                                                                                             ORDER BY SP.Nombre";
                            $r_carac = $dbo->query($sql_carac);
                            while ($row_carac = $dbo->FetchArray($r_carac)) {

                                $adicionales_inv["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                                $adicionales_inv["EtiquetaCampo"] = $row_carac["Caracteristica"];
                                $adicionales_inv["TipoCampo"] = $row_carac["TipoCampo"];
                                $adicionales_inv["Valores"] = $row_carac["Valores"];
                                $adicionales_inv["ValoresID"] = $row_carac["Valor"];
                                $adicionales_inv["Total"] = $row_carac["Total"];
                                array_push($response_adicionales_inv, $adicionales_inv);
                            }

                            $invitado_reserva["Adicionales"] = $response_adicionales_inv;
                            //Fin Adicionales


                            // CADDIE INVITADO

                            $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $r_invitados_reserva[IDCaddie]";
                            $QRYCaddie = $dbo->query($SQLCaddies);

                            while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                                $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                $CaddieInvitado[IDCaddie] = $DatoCaddie[IDCaddie];
                                $CaddieInvitado[Nombre] = $DatoCaddie[Nombre];
                                $CaddieInvitado[Categoria] = $datos_categoria[Categoria];
                                $CaddieInvitado[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                $CaddieInvitado[Precio] = $DatoCaddie[Precio];
                                $CaddieInvitado[Disponible] = $DatoCaddie[Disponible];
                                $CaddieInvitado[Texto] = $DatoCaddie[Descripcion];

                            endwhile;

                            $invitado_reserva["Caddie"] = $CaddieInvitado;

                            array_push($response_invitados_reserva, $invitado_reserva);
                        endwhile;

                        /*
                            //Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
                            if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
                            //Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
                            if ($id_disponibilidad>0):
                            $minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            endif;
    
                            if($total_invitado<$minimo_invitado_reserva):
                            $invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
                            $invitado_reserva[IDSocio]=$IDSocio;
                            $invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));
                            $tipo_invitado = "Socio";
                            $invitado_reserva[TipoInvitado]=$tipo_invitado;
                            array_push($response_invitados_reserva, $invitado_reserva);
                            endif;
                            endif;
                             */

                        $reserva["Invitados"] = $response_invitados_reserva;

                        //Reservas asociadas
                        $response_reserva_asociada = array();
                        $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                        foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                            array_push($response_reserva_asociada, $datos_reserva);
                        endforeach;
                        $reserva["ReservaAsociada"] = $response_reserva_asociada;
                        $reserva["IDReservaGeneralInvitado"] = $IDInvitacionEliminar;
                        //Reservada por
                        $id_socio_reserva = $dbo->getFields("ReservaGeneral", "IDSocio", "IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $reserva["InvitadoPor"] = utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $id_socio_reserva . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $id_socio_reserva . "'")));

                        //Adicionales
                        $response_adicionales = array();
                        $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                    FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                    WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                 IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $IDInvitacionEliminar . "'
                                                                    ORDER BY SP.Nombre";
                        $r_carac = $dbo->query($sql_carac);
                        while ($row_carac = $dbo->FetchArray($r_carac)) {

                            $adicionales["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                            $adicionales["EtiquetaCampo"] = $row_carac["Caracteristica"];
                            $adicionales["TipoCampo"] = $row_carac["TipoCampo"];
                            $adicionales["Valores"] = $row_carac["Valores"];
                            $adicionales["ValoresID"] = $row_carac["Valor"];
                            $adicionales["Total"] = $row_carac["Total"];
                            array_push($response_adicionales, $adicionales);
                        }

                        $reserva["Adicionales"] = $response_adicionales;
                        //Fin Adicionales

                        //preguntas Reservas
                        $sql_otros = "Select Valor,IDServicioCampo From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'";

                        $result_otros = $dbo->query($sql_otros);
                        $response_otros = array();
                        while ($row_otros = $dbo->fetchArray($result_otros)) :

                            $array_otros["Nombre"] = $dbo->getFields("ServicioCampo", "Nombre", "IDServicioCampo = '" . $row_otros["IDServicioCampo"] . "'");
                            $array_otros["Valor"] = $row_otros["Valor"];

                            array_push($response_otros, $array_otros);
                        endwhile;

                        $reserva["CamposReserva"] = $response_otros;
                        //fin preguntas Reservas



                        array_push($response, $reserva);
                    }
                } // fin verificar si fue un areserva automatica
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No tienes reservas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_reservas_invitado($IDClub, $NumeroDocumentoInvitado, $IDSocio, $Limite = 0, $IDReserva = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        $response = array();
        $array_id_consulta[] = $IDSocio;

        if (!empty($IDReserva)) {
            $condicion_reserva = " and RG.IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        //Selecciono las reservas donde el socio esta como invitado
        $sql = "SELECT * FROM ReservaGeneral RG,ReservaGeneralInvitado RGI WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and RGI.Cedula = '" . $NumeroDocumentoInvitado . "' and RG.IDEstadoReserva = 1 and RG.Fecha >= CURDATE() " . $condicion_reserva . "ORDER BY RG.Fecha Desc  " . $condicion_limite;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {

            $message = $dbo->rows($qry) . " Encontrados";

            while ($row_reserva = $dbo->fetchArray($qry)) :
                $mostra_reserva = 1;
                $fecha_hoy = date("Y-m-d");
                if ($row_reserva["Fecha"] == $fecha_hoy && $row_reserva["Hora"] <= date("H:i:s") && empty($IDUsuario)) {
                    $mostra_reserva = 0;
                    if ($dbo->rows($qry) == 1) {
                        $reserva["IDClub"] = "";
                        $reserva["IDSocio"] = "";
                        $reserva["IDReserva"] = "";
                        $reserva["IDServicio"] = "";
                        $id_servicio_maestro = "";
                        $reserva["NombreServicio"] = "";
                        $reserva["IDElemento"] = "";
                        $reserva["NombreElemento"] = "";
                        $reserva["Fecha"] = "";
                        $reserva["Tee"] = "";
                        array_push($response, $reserva);
                        $respuesta["message"] = "No tienes reservas programadas.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                        return $respuesta;
                    }
                }

                if ($row_reserva[IDClubOrigen] > 0) :
                    $IDClub = $row_reserva[IDClubOrigen];
                endif;

                // $mostra_reserva = 1;

                if ($mostra_reserva == 1) {
                    // Verifico si es una reserva asociada para no mostrarla en el resultado
                    $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                    $qry_auto = $dbo->query($sql_auto);
                    if ($dbo->rows($qry_auto) <= 0) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");

                        $reserva["IDClub"] = $IDClub;
                        $reserva["IDSocio"] = $IDSocio;
                        $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                        $reserva["IDServicio"] = $row_reserva["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");

                        $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        if (empty($nombre_servicio_personalizado)) {
                            $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        }

                        $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'");
                        $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                        $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                        $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'");
                        $reserva["Fecha"] = $row_reserva["Fecha"];
                        $reserva["Tee"] = $row_reserva["Tee"];
                        $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                        $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                        $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                        $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                        $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                        $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                        $reserva["AdicionalesObligatorio"] = $datos_servicio["AdicionalesObligatorio"];
                        $reserva["TextoLegal"] = $datos_servicio["TextoLegal"];

                        // Config Eliminar
                        $reserva["EliminarParaTodosOParaMi"] = $datos_servicio["EliminarParaTodosOParaMi"];
                        $reserva["MensajeEliminarParaTodosOParaMi"] = $datos_servicio["MensajeEliminarParaTodosOParaMi"];
                        $reserva["BotonEliminarReserva"] = $datos_servicio["BotonEliminarReserva"];
                        $reserva["LabelEliminarParaMi"] = $datos_servicio["LabelEliminarParaMi"];
                        $reserva["LabelEliminarParaTodos"] = $datos_servicio["LabelEliminarParaTodos"];

                        // INVITADOS EXTERNOS
                        $servicio["PermiteInvitadoExternoCedula"] = $datos_servicio["PermiteInvitadoExternoCedula"];
                        $servicio["PermiteInvitadoExternoCorreo"] = $datos_servicio["PermiteInvitadoExternoCorreo"];
                        $servicio["PermiteInvitadoExternoFechaNacimiento"] = $datos_servicio["PermiteInvitadoExternoFechaNacimiento"];


                        // CONFIGURACIONES CADDIES
                        $reserva[PermiteAdicionarCaddies] = $datos_servicio[PermiteEditarCaddies];
                        $reserva[LabelAdicionarCaddies] = $datos_servicio[LabelAdicionarCaddies];
                        $reserva[ObligatorioSeleccionarCaddie] = $datos_servicio[ObligatorioSeleccionarCaddie];
                        $reserva[MensajeCaddiesObligatorio] = $datos_servicio[MensajeCaddiesObligatorio];

                        $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                        if (empty($labelauxiliar)) {
                            $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                        }

                        $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                        if (!empty($row_reserva["IDAuxiliar"])) :
                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["Auxiliar"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                            $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                        else :
                            unset($reserva['IDAuxiliar']);
                            unset($reserva['Auxiliar']);
                            unset($reserva['TipoAuxiliar']);
                        endif;

                        if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                            $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                            $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                        else :
                            unset($reserva['IDTipoModalidad']);
                            unset($reserva['Modalidad']);
                        endif;

                        if (strlen($row_reserva["Hora"]) != 8) :
                            $row_reserva["Hora"] .= ":00";
                        endif;

                        $reserva["Hora"] = $row_reserva["Hora"];

                        $zonahoraria = date_default_timezone_get();
                        $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                        $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                        if ($row_reserva["IDDisponibilidad"] <= 0) :
                            $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "'");
                        else :
                            $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                        endif;

                        $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                        if (!empty($invitadoclub)) :
                            $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoClub"] = "";
                        endif;
                        if (!empty($invitadoexterno)) :
                            $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                        else :
                            $reserva["NumeroInvitadoExterno"] = "";
                        endif;

                        if ($row_reserva["IDInvitadoBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                        endif;
                        if ($row_reserva["IDSocioBeneficiario"] > 0) :
                            $reserva["Beneficiario"] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                        endif;

                        // CADDIE DE LA RESERVA
                        $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $row_reserva[IDCaddie]";
                        $QRYCaddie = $dbo->query($SQLCaddies);

                        while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                            $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                            $Caddie[IDCaddie] = $DatoCaddie[IDCaddie];
                            $Caddie[Nombre] = $DatoCaddie[Nombre];
                            $Caddie[Categoria] = $datos_categoria[Categoria];
                            $Caddie[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                            $Caddie[Precio] = $DatoCaddie[Precio];
                            $Caddie[Disponible] = $DatoCaddie[Disponible];
                            $Caddie[Texto] = $DatoCaddie[Descripcion];

                        endwhile;
                        $reserva[CaddieSocio] = $Caddie;


                        //Invitados Reserva
                        $response_invitados_reserva = array();
                        $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $total_invitado = $dbo->rows($sql_invitados_reserva);
                        while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)) :
                            $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];

                            if ($r_invitados_reserva["IDSocio"] == $IDSocio) {
                                $IDInvitacionEliminar = $r_invitados_reserva["IDReservaGeneralInvitado"];
                            }

                            $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                            $invitado_reserva[NombreExterno] = strtoupper($r_invitados_reserva["Nombre"]);
                            if ($r_invitados_reserva["IDSocio"] == 0) :
                                $tipo_invitado = "Externo";
                            else :
                                $tipo_invitado = "Socio";
                            endif;

                            $invitado_reserva[TipoInvitado] = $tipo_invitado;

                            //Adicionales
                            $response_adicionales_inv = array();
                            $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                             FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                             WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                          IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $r_invitados_reserva["IDReservaGeneralInvitado"] . "'
                                                                                             GROUP BY IDServicioPropiedad
                                                                                             ORDER BY SP.Nombre";
                            $r_carac = $dbo->query($sql_carac);
                            while ($row_carac = $dbo->FetchArray($r_carac)) {

                                $adicionales_inv["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                                $adicionales_inv["EtiquetaCampo"] = $row_carac["Caracteristica"];
                                $adicionales_inv["TipoCampo"] = $row_carac["TipoCampo"];
                                $adicionales_inv["Valores"] = $row_carac["Valores"];
                                $adicionales_inv["ValoresID"] = $row_carac["Valor"];
                                $adicionales_inv["Total"] = $row_carac["Total"];
                                array_push($response_adicionales_inv, $adicionales_inv);
                            }

                            $invitado_reserva["Adicionales"] = $response_adicionales_inv;
                            //Fin Adicionales


                            // CADDIE INVITADO

                            $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $r_invitados_reserva[IDCaddie]";
                            $QRYCaddie = $dbo->query($SQLCaddies);

                            while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                                $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                $CaddieInvitado[IDCaddie] = $DatoCaddie[IDCaddie];
                                $CaddieInvitado[Nombre] = $DatoCaddie[Nombre];
                                $CaddieInvitado[Categoria] = $datos_categoria[Categoria];
                                $CaddieInvitado[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                $CaddieInvitado[Precio] = $DatoCaddie[Precio];
                                $CaddieInvitado[Disponible] = $DatoCaddie[Disponible];
                                $CaddieInvitado[Texto] = $DatoCaddie[Descripcion];

                            endwhile;

                            $invitado_reserva["Caddie"] = $CaddieInvitado;

                            array_push($response_invitados_reserva, $invitado_reserva);
                        endwhile;

                        /*
                            //Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
                            if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
                            //Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
                            if ($id_disponibilidad>0):
                            $minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            endif;
    
                            if($total_invitado<$minimo_invitado_reserva):
                            $invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
                            $invitado_reserva[IDSocio]=$IDSocio;
                            $invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));
                            $tipo_invitado = "Socio";
                            $invitado_reserva[TipoInvitado]=$tipo_invitado;
                            array_push($response_invitados_reserva, $invitado_reserva);
                            endif;
                            endif;
                             */

                        $reserva["Invitados"] = $response_invitados_reserva;

                        //Reservas asociadas
                        $response_reserva_asociada = array();
                        $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                        foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                            array_push($response_reserva_asociada, $datos_reserva);
                        endforeach;
                        $reserva["ReservaAsociada"] = $response_reserva_asociada;
                        $reserva["IDReservaGeneralInvitado"] = $IDInvitacionEliminar;
                        //Reservada por
                        $id_socio_reserva = $dbo->getFields("ReservaGeneral", "IDSocio", "IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                        $reserva["InvitadoPor"] = utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $id_socio_reserva . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $id_socio_reserva . "'")));

                        //Adicionales
                        $response_adicionales = array();
                        $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                    FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                    WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                 IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $IDInvitacionEliminar . "'
                                                                    ORDER BY SP.Nombre";
                        $r_carac = $dbo->query($sql_carac);
                        while ($row_carac = $dbo->FetchArray($r_carac)) {

                            $adicionales["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                            $adicionales["EtiquetaCampo"] = $row_carac["Caracteristica"];
                            $adicionales["TipoCampo"] = $row_carac["TipoCampo"];
                            $adicionales["Valores"] = $row_carac["Valores"];
                            $adicionales["ValoresID"] = $row_carac["Valor"];
                            $adicionales["Total"] = $row_carac["Total"];
                            array_push($response_adicionales, $adicionales);
                        }

                        $reserva["Adicionales"] = $response_adicionales;
                        //Fin Adicionales

                        //preguntas Reservas
                        $sql_otros = "Select Valor,IDServicioCampo From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'";

                        $result_otros = $dbo->query($sql_otros);
                        $response_otros = array();
                        while ($row_otros = $dbo->fetchArray($result_otros)) :

                            $array_otros["Nombre"] = $dbo->getFields("ServicioCampo", "Nombre", "IDServicioCampo = '" . $row_otros["IDServicioCampo"] . "'");
                            $array_otros["Valor"] = $row_otros["Valor"];

                            array_push($response_otros, $array_otros);
                        endwhile;

                        $reserva["CamposReserva"] = $response_otros;
                        //fin preguntas Reservas

                        array_push($response, $reserva);
                    }
                } // fin verificar si fue un areserva automatica
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No tienes reservas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_lista_espera($IDClub, $IDSocio, $IDServicio, $IDServicioElemento, $IDAuxiliar, $FechaInicio, $FechaInicioFin, $HoraInicio, $HoraFin, $AceptoTerminos, $Celular, $Tipo, $IDClubAsociado, $EligeListaEsperaTodos)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($FechaInicio) && !empty($Tipo)) {

            //verifico que el socio exista y pertenezca al club
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ", "array");

            if (!empty($datos_socio["IDSocio"])) {

                if (!empty($IDClubAsociado)) :
                    $IDClub = $IDClubAsociado;
                endif;


                if ($EligeListaEsperaTodos == 'S') :
                    // SACAMOS TODOS LOS ELEMENTOS DEL SERVICIO
                    $SQLElementos = "SELECT IDServicioElemento FROM ServicioElemento WHERE IDServicio = $IDServicio";
                    $QRYElementos = $dbo->query($SQLElementos);

                    while ($Datos = $dbo->fetchArray($QRYElementos)) :
                        $sql_lista_espera = $dbo->query("Insert Into ListaEspera (IDClub, IDSocio, IDServicio, IDAuxiliar, IDServicioElemento, FechaInicio, FechaFin, HoraInicio, HoraFin, AceptoTerminos, Celular, Tipo, UsuarioTrCr, FechaTrCr)
                        Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDServicio . "','" . $IDAuxiliar . "','$Datos[IDServicioElemento]','" . $FechaInicio . "','" . $FechaInicioFin . "', '" . $HoraInicio . "','" . $HoraFin . "','S','" . $Celular . "','" . $Tipo . "','App',NOW())");
                    endwhile;
                else :
                    $sql_lista_espera = $dbo->query("Insert Into ListaEspera (IDClub, IDSocio, IDServicio, IDAuxiliar, IDServicioElemento, FechaInicio, FechaFin, HoraInicio, HoraFin, AceptoTerminos, Celular, Tipo, UsuarioTrCr, FechaTrCr)
                                                Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDServicio . "','" . $IDAuxiliar . "','" . $IDServicioElemento . "','" . $FechaInicio . "','" . $FechaInicioFin . "', '" . $HoraInicio . "','" . $HoraFin . "','S','" . $Celular . "','" . $Tipo . "','App',NOW())");
                endif;
                $respuesta["message"] = 'Guardado con exito.';
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = 'Error el socio no existe o no pertenece al club';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. " . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_tipo_pago_reserva($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago = "", $IDTaloneraDisponible = "", $IDClubAsociado = "", $IDPorcentajeAbono = "", $PagarMasTarde = "", $PagaConBonos = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClubAsociado)) :
            $IDClub = $IDClubAsociado;
        endif;

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva) && (!empty($IDTipoPago) || !empty($PagarMasTarde))) :

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'");
            $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

            if (!empty($id_reserva)) :

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $valorCodigo = $dbo->getFields("ClubCodigoPago", "Valor", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");

                    $datoReserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = '" . $IDReserva . "'");

                    if ($IDClub == 28) :
                        $valorReserva = SIMUtil::calcular_tarifa($IDClub, $IDSocio, $datoReserva['IDServicio'], $datoReserva['Fecha'], $datoReserva['Hora'], $datoReserva['IDServicioElemento'], $IDReserva, $datoReserva['IDServicioTipoReserva']);
                    endif;

                    if (empty($id_codigo)) :

                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    elseif ($codigo_disponible != "S") :

                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    elseif ($valorCodigo != $valorReserva && ($IDClub == 8 || $IDClub == 28)) :

                        $respuesta["message"] = "El codigo que intenta redimir esta registrado por otro valor";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    else :

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_actualiza_codigo);

                    endif;

                elseif ($IDTipoPago == 16) : // PAGO CON TALONERA

                    if ($IDTaloneraDisponible > 0) :
                        require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
                        $respuesta = SIMWebServiceTaloneras::pagar_reserva($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $IDTaloneraDisponible);
                        return $respuesta;
                    else :
                        $respuesta["message"] = "Lo sentimos pero no tiene una talonera disponible para el pago";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                elseif ($PagarMasTarde == 'S') :

                    $sql_tipo_pago = "UPDATE ReservaGeneral SET PagoParaMasTarde = '$PagarMasTarde',  MedioPago =  'Pago mas Tarde', TipoMedioPago = 'Pago mas Tarde' WHERE IDReservaGeneral = '$IDReserva' AND IDClub = '$IDClub'";
                    $dbo->query($sql_tipo_pago);

                elseif ($PagaConBonos == 'S' && empty($IDTipoPago)) :

                    // CONSULTAMOS LOS BONOS DISPONIBLES
                    $SQLCodigos = "SELECT * FROM ClubCodigoPago WHERE IDSocio = $IDSocio AND Disponible = 'S' AND Valor > 0 ORDER BY Valor ASC";
                    $QRYCodigos = $dbo->query($SQLCodigos);

                    $Suma = 0;
                    while ($Codigos = $dbo->fetchArray($QRYCodigos)) :

                        $Suma += $Codigos[Valor];

                        if ($Suma < $ValorReserva) :
                            $UpdateCodigo = "UPDATE ClubCodigoPago SET Disponible = 'S' WHERE IDClubCodigoPago = $Codigos[IDClubCodigoPago]";
                            $dbo->query($UpdateCodigo);
                        else :
                            break;
                        endif;

                    endwhile;

                    $sql_tipo_pago = "UPDATE ReservaGeneral SET Pagado = 'S',  MedioPago =  'Pago con bonos', TipoMedioPago = 'Pago con bonos' WHERE IDReservaGeneral = '$IDReserva' AND IDClub = '$IDClub'";
                    $dbo->query($sql_tipo_pago);

                elseif ($PagaConBonos == 'S' && !empty($IDTipoPago)) :

                    // CONSULTAMOS LOS BONOS DISPONIBLES
                    $SQLCodigos = "SELECT * FROM ClubCodigoPago WHERE IDSocio = $IDSocio AND Disponible = 'S' AND Valor > 0 ORDER BY Valor ASC";
                    $QRYCodigos = $dbo->query($SQLCodigos);

                    $Suma = 0;
                    while ($Codigos = $dbo->fetchArray($QRYCodigos)) :

                        $Suma += $Codigos[Valor];

                        if ($Suma < $ValorReserva) :
                            $UpdateCodigo = "UPDATE ClubCodigoPago SET Disponible = 'S' WHERE IDClubCodigoPago = $Codigos[IDClubCodigoPago]";
                            $dbo->query($UpdateCodigo);
                        else :
                            break;
                        endif;

                    endwhile;

                    $sql_tipo_pago = "UPDATE ReservaGeneral SET IDTipoPago =  '$IDTipoPago',  MedioPago =  'Pago con bonos y MOtro', TipoMedioPago = 'Pago con bonos y Otro' WHERE IDReservaGeneral = '$IDReserva' AND IDClub = '$IDClub'";
                    $dbo->query($sql_tipo_pago);

                endif;

                if ($IDTipoPago == 3) :
                    $Actualiza = ", Pagado = 'S'";
                endif;

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) :
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                $sql_tipo_pago = "UPDATE ReservaGeneral SET IDPorcentajeAbono = '$IDPorcentajeAbono', IDTipoPago =  '$IDTipoPago', CodigoPago = '$CodigoPago' $Actualiza WHERE IDReservaGeneral = '$IDReserva' AND IDClub = '$IDClub'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            else :
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_parametros_pago_reserva($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = SIMDB::get();

        $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", "IDClub = $IDClub");
        $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub");
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        if (!empty($IDReserva)) :
            $SQLReserva = "SELECT * FROM ReservaGeneral WHERE IDReservaGeneral = $IDReserva";
            $QRYReserva = $dbo->query($SQLReserva);

            if ($dbo->rows($QRYReserva) > 0) :
                while ($DatosReserva = $dbo->fetchArray($QRYReserva)) :

                    $datos_servicio = $dbo->fetchAll("Servicio", "IDServicio = $DatosReserva[IDServicio]");

                    $InfoResponse[IDReserva] = $DatosReserva[IDReservaGeneral];

                    $ValorReserva = $DatosReserva[ValorPagado];
                    $InfoResponse[ValorReserva] = $ValorReserva;

                    $TextoValor = number_format((float)$ValorReserva, 1, ",", ".");
                    if ($IDClub == 124) :
                        $TextoValor = $ValorReserva;
                    endif;

                    $InfoResponse[ValorPagoTexto] = $datos_club_otros["SignoPago"] . " " . $TextoValor . " " . $datos_club_otros["TextoPago"];

                    $InfoResponse[Action] = $datos_club["URL_PAYU"];

                    $moneda = "COP";
                    $refVenta = time();
                    $llave_encripcion = $datos_club["ApiKey"];
                    $usuarioId = $datos_club["MerchantId"];

                    $iva = 0; //impuestos calculados de la transacciÛn
                    $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                    $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total

                    $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
                    $descripcion = "Pago Reserva Mi Club";
                    if ($IDClub == 28) {
                        $descripcion = "Pago Reserva " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                    }
                    $url_respuesta = URLROOT . "respuesta_transaccion.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                    $url_confirmacion = URLROOT . "confirmacion_pagos.php";
                    $emailSocio = $datos_socio["CorreoElectronico"];
                    if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                        $emailComprador = $emailSocio;
                    } else {
                        $emailComprador = "";
                    }

                    $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                    $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                    $extra1 = $IDReserva;

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
                    $datos_post["valor"] = (string) $ValorReserva;
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

                    $datos_post["llave"] = "Modulo";
                    $datos_post["valor"] = (string) "Reservas";
                    array_push($response_parametros, $datos_post);

                    $datos_post["llave"] = "IDSocio";
                    $datos_post["valor"] = $IDSocio;
                    array_push($response_parametros, $datos_post);

                    $InfoResponse["ParametrosPost"] = $response_parametros;

                    $datos_post_pago = array();
                    $datos_post_pago["iva"] = 0;
                    $datos_post_pago["purchaseCode"] = $refVenta;
                    $datos_post_pago["totalAmount"] = $ValorReserva * 100;
                    $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                    $InfoResponse["ParametrosPaGo"] = $datos_post_pago;

                    $InfoResponse[PermiteSistemaAbono] = $datos_servicio["PermiteSistemaAbono"];

                    if ($datos_servicio["PermiteSistemaAbono"] == 'S') :
                        $InfoResponse[PagarAbonoLabel] = $datos_servicio["PagarAbonoLabel"];
                        require_once LIBDIR . "SIMWebServiceReservas.inc.php";
                        $OpcionesSistemaAbono = SIMWebServiceReservas::Abonos($IDClub, $ValorReserva, $IDServicio, $datos_club, $datos_club_otros, $datos_persona);
                        $InfoResponse[OpcionesSistemaAbono] = $OpcionesSistemaAbono;
                    endif;

                    $InfoResponse[PagarTotalLabel] = $datos_servicio["PagarTotalLabel"];

                    require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
                    $IDTaloneraDisponible = SIMWebServiceTaloneras::talonera_disponible($IDClub, $IDSocio, $IDServicio, $Fecha, $IDSocioBeneficiario);

                    if ($IDTaloneraDisponible == null)
                        $IDTaloneraDisponible = 0;

                    $InfoResponse[IDTaloneraDisponible] = $IDTaloneraDisponible;

                    $response_tipo_pago = array();
                    $sql_tipo_pago = "SELECT * FROM ServicioTipoPago STP, TipoPago TP  WHERE STP.IDTipoPago = TP.IDTipoPago and IDServicio= '" . $datos_servicio["IDServicio"] . "' ";
                    $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                    if ($dbo->rows($qry_tipo_pago) > 0) {
                        $servicio["PagoReserva"] = "S";
                        while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                            $desactivado = false;

                            if (!$desactivado) {
                                $tipopago["IDClub"] = $IDClub;
                                $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                                $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                                $tipopago["Talonera"] = $r_tipo_pago["Talonera"];
                                $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                                $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);

                                if ($IDClub == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                                    $tipopago["Nombre"] = "Pago en recepción";
                                else
                                    $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                                $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                                //Para el condado y es pagos online muestro la imagen de placetopay

                                switch ($r_tipo_pago["IDTipoPago"]) {
                                    case "1":
                                        $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                        break;
                                    case "2":
                                        $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                        break;
                                    case "3":
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        $imagen = "https://www.miclubapp.com/file/noticia/icsi.png";
                                        break;
                                    case "11":
                                        $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                        break;
                                    case "9":
                                        $imagen = "https://www.miclubapp.com/file/noticia/ictarjeta.png";
                                        break;
                                    case "12":
                                        $imagen = "https://www.miclubapp.com/file/noticia/iccredibancopago.png";
                                        break;
                                    default:
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;
                                }

                                $tipopago["Imagen"] = $imagen;
                                array_push($response_tipo_pago, $tipopago);
                            }
                        } //end while
                        $InfoResponse[TipoPago] = $response_tipo_pago;
                    }

                    $InfoResponse["MensajePagoReserva"] = $datos_servicio["MensajePagoReserva"];

                    $response = $InfoResponse;
                endwhile;

                $respuesta["message"] = 'Pago reserva';
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = 'La reserva no existe';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function SistemaBonos($IDClub, $IDSocio, $IDReservaGeneral, $ValorReserva, $IDServicio, $datos_club, $datos_club_otros, $datos_persona)
    {
        $dbo = SIMDB::get();
        $Bonos = array();
        $TotalBonos = 0;
        $ValorReservaTemporal = $ValorReserva;

        $datos_servicio = $dbo->fetchAll("Servicio", "IDServicio = $IDServicio");

        $response[LabelMensajeBonos] = $datos_servicio[LabelMensajeBonos];

        // CONSULTAMOS LOS BONOS DISPONIBLES
        $SQLCodigos = "SELECT * FROM ClubCodigoPago WHERE IDSocio = $IDSocio AND Disponible = 'S' AND Valor > 0 ORDER BY Valor ASC";
        $QRYCodigos = $dbo->query($SQLCodigos);

        $CompletaValorTotal = 'N';
        while ($Codigos = $dbo->fetchArray($QRYCodigos)) :

            $InfoBonos[Nombre] = $Codigos[Codigo];
            $InfoBonos[ValorTexto] = $Codigos[Valor];

            $TotalBonos += $Codigos[Valor];

            $arrayInfoBonos[] = $Codigos;

            array_push($Bonos, $InfoBonos);
        endwhile;

        $response[Bonos] = $Bonos;
        $response[ValorBonosTotalTexto] = $TotalBonos;

        $Suma = 0;

        foreach ($arrayInfoBonos as $id => $Codigos) :

            $Suma += $Codigos[Valor];

            if ($Suma > $ValorReserva) :
                $Suma -= $Codigos[Valor];
                $ValorReservaRestante = $ValorReserva - $Suma;
                $CompletaValorTotal = 'N';
                break;
            elseif ($Suma == $ValorReserva) :
                $ValorReservaRestante = 0;
                $CompletaValorTotal = 'S';
                break;
            endif;

        endforeach;


        $response[ValorBonosRestante] = $TotalBonos - $Suma;
        $response[CompletaValorTotal] = $CompletaValorTotal;

        $ParametrosPagoRestante[IDReserva] = $IDReservaGeneral;
        $ParametrosPagoRestante[ValorRestante] = $ValorReservaRestante;

        $ParametrosPagoRestante[Action] = $datos_club[URL_PAYU];

        $moneda = "COP";
        $refVenta = time();
        $llave_encripcion = $datos_club["ApiKey"];
        $usuarioId = $datos_club["MerchantId"];

        $iva = 0; //impuestos calculados de la transacciÛn
        $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
        $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total

        $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
        $descripcion = "Pago Reserva Mi Club";
        if ($IDClub == 28) {
            $descripcion = "Pago Reserva " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
        }
        $url_respuesta = URLROOT . "respuesta_transaccion.php"; //Esta es la p·gina a la que se direccionar· al final del pago
        $url_confirmacion = URLROOT . "confirmacion_pagos.php";
        $emailSocio = $datos_socio["CorreoElectronico"];
        if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
            $emailComprador = $emailSocio;
        } else {
            $emailComprador = "";
        }

        $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
        $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
        $extra1 = $IDReserva;

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
        $datos_post["valor"] = (string) $IDReservaGeneral;
        array_push($response_parametros, $datos_post);

        $datos_post["llave"] = "extra2";
        $datos_post["valor"] = $IDClub;
        array_push($response_parametros, $datos_post);

        $datos_post["llave"] = "refVenta";
        $datos_post["valor"] = $refVenta;
        array_push($response_parametros, $datos_post);

        $datos_post["llave"] = "valor";
        $datos_post["valor"] = (string) $ValorReservaRestante;
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

        $datos_post["llave"] = "Modulo";
        $datos_post["valor"] = (string) "Reservas";
        array_push($response_parametros, $datos_post);

        $datos_post["llave"] = "IDSocio";
        $datos_post["valor"] = $IDSocio;
        array_push($response_parametros, $datos_post);

        $ParametrosPagoRestante[ParametrosPost] = $response_parametros;

        $datos_post_pago = array();
        $datos_post_pago["iva"] = 0;
        $datos_post_pago["purchaseCode"] = $refVenta;
        $datos_post_pago["totalAmount"] = $ValorReserva * 100;
        $datos_post_pago["ipAddress"] = SIMUtil::get_IP();

        $ParametrosPagoRestante[ParametrosPaGo] = $datos_post_pago;

        $response[ParametrosPagoRestante] = $ParametrosPagoRestante;

        return $response;
    }

    public function get_tickets_descuento_servicio($IDClub, $IDClubAsociado, $IDSocio, $IDServicio)
    {
        $dbo = SIMDB::get();

        if (!empty($IDClub) && !empty($IDServicio) && !empty($IDSocio)) :

            $response = array();

            $SQLTickets = "SELECT TD.* FROM TicketDescuento TD, TicketDescuentoServicio TDS WHERE TD.IDTicketDescuento = TDS.IDTicketDescuento AND TDS.IDServicio = $IDServicio AND (TD.IDSocio = $IDSocio OR TD.IDSocio = 0) AND TD.Activo  = 1";
            $QRYTickets = $dbo->query($SQLTickets);

            if ($dbo->rows($QRYTickets) > 0) :
                while ($Tickets = $dbo->fetchArray($QRYTickets)) :
                    $InfoResponse[IDTicket] = $Tickets[IDTicketDescuento];
                    $InfoResponse[ValorPorcentajeTexto] = $Tickets[ValorDescuento] . "% - " . $Tickets[Nombre];
                    $InfoResponse[Descripcion] = $Tickets[Descripcion];

                    array_push($response, $InfoResponse);
                endwhile;

                $respuesta["message"] = 'Tickets';
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = 'No hay tickets';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else :
            $respuesta["message"] = 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_campos_invitado_externo($IDClub, $IDClubAsociado, $IDSocio, $IDServicio)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDServicio) && !empty($IDClub)) :
            $SQLCampos = "SELECT * FROM CampoInvitadoExterno WHERE IDServicio = $IDServicio AND Activo = 1 Order by Orden";
            $QRYCampos = $dbo->query($SQLCampos);
            if ($dbo->rows($QRYCampos) > 0) :
                while ($Campos = $dbo->fetchArray($QRYCampos)) :
                    // Validación reglas tarjetas rotativas Club Villa Peru
                    if ($IDClub == 220 && $Campos['IdentificadorPregunta'] == 'PTR') {
                        // if ($IDClub == 8 && $Campos['IdentificadorPregunta'] == 'PTR') {
                        if (!empty($Campos['Valores'])) {
                            $arr_Tarjetas = explode('|', $Campos['Valores']);
                            $TarjetasDisponibles = array();
                            foreach ($arr_Tarjetas as $tarjeta) {
                                $IDTipoTarjetaRotativa = $dbo->getFields("TipoTarjetaRotativa", "IDTipoTarjetaRotativa", "IDClub = $IDClub AND Nombre ='$tarjeta' AND Publicar ='S'");
                                if ($IDTipoTarjetaRotativa != false) {

                                    $ValidaDisponibilidadTarjeta = $dbo->getFields("TarjetaRotativa", "IDTarjetaRotativa", "IDSocio = $IDSocio AND  IDTipoTarjetaRotativa = $IDTipoTarjetaRotativa");

                                    if ($ValidaDisponibilidadTarjeta != false) {
                                        array_push($TarjetasDisponibles, "{$tarjeta}");
                                    }
                                }
                            }
                            $Campos['Valores'] = implode(',', $TarjetasDisponibles);

                            $InfoResponse[IDCampoInvitadoExterno] = $Campos[IDCampoInvitadoExterno];
                            $InfoResponse[TipoCampo] = $Campos[TipoCampo];
                            $InfoResponse[EtiquetaCampo] = $Campos[EtiquetaCampo];
                            $InfoResponse[Obligatorio] = $Campos[Obligatorio];
                            $InfoResponse[Valores] = $Campos[Valores];
                            $InfoResponse[Orden] = $Campos[Orden];

                            array_push($response, $InfoResponse);
                        }
                    } else {
                        $InfoResponse[IDCampoInvitadoExterno] = $Campos[IDCampoInvitadoExterno];
                        $InfoResponse[TipoCampo] = $Campos[TipoCampo];
                        $InfoResponse[EtiquetaCampo] = $Campos[EtiquetaCampo];
                        $InfoResponse[Obligatorio] = $Campos[Obligatorio];
                        $InfoResponse[Valores] = $Campos[Valores];
                        $InfoResponse[Orden] = $Campos[Orden];

                        array_push($response, $InfoResponse);
                    }



                endwhile;

                $respuesta["message"] = 'Campos';
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = 'No hay datos';
                $respuesta["success"] = true;
                $respuesta["response"] = [];
            endif;
        else :
            $respuesta["message"] = 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    //lista de espera
    public function get_lista_espera_reserva_socio($IDClub, $IDSocio)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDSocio) && !empty($IDClub)) :
            $SQL_lista_espera = "SELECT * FROM ListaEspera WHERE IDSocio = $IDSocio";
            $QRYListaEspera = $dbo->query($SQL_lista_espera);
            if ($dbo->rows($QRYListaEspera) > 0) :
                $message = $dbo->rows($QRYListaEspera) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRYListaEspera)) :

                    $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $Datos["IDServicio"] . "' ", "array");
                    //se llama IDReserva pero se le pasa al app el IDListaEspera por si se quiere eliminar
                    $InfoResponse["IDReserva"] = $Datos["IDListaEspera"];

                    //nombre servicio
                    $id_servicio = $Datos["IDServicio"];
                    $id_servicio_mestro_menu = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                    $asoc_servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                    $asoc_servicio["NombrePersonalizado"] = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . $IDClub . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                    if (!empty($asoc_servicio["NombrePersonalizado"])) {
                        $NombreServicio = $asoc_servicio["NombrePersonalizado"];
                    } else {
                        $NombreServicio = $asoc_servicio["Nombre"];
                    }
                    $InfoResponse["NombreServicio"] = $NombreServicio;


                    //icono

                    if (!empty($datos_servicio["Icono"])) {
                        $foto = SERVICIO_ROOT . $datos_servicio["Icono"];
                    } else {
                        $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                        if (!empty($icono_maestro)) {
                            $foto = SERVICIO_ROOT . $icono_maestro;
                        }
                    }
                    $InfoResponse["Icono"] = $foto;

                    /*El de TextoAdicional es porque pueden poner ahí cualquier información adicional que cada cliente o club requiera,ejemplo ahí nombres de profesores, o nombres de invitados o cualquier otra info */
                    $InfoResponse["TextoAdicional"] = "";
                    $InfoResponse["Fecha"] = $Datos["FechaInicio"];
                    $InfoResponse["Hora"] = $Datos["HoraInicio"];



                    array_push($response, $InfoResponse);
                endwhile;
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = 'No hay datos';
                $respuesta["success"] = true;
                $respuesta["response"] = [];
            endif;

        else :
            $respuesta["message"] = 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_historial_misreservas($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        $Html = '<!doctype html>
                            <html>
                            <head>
                            <meta charset="UTF-8">
                            <title>Detalle Factura</title>
                            <style>
                            .tabla {
                            font-family: Verdana, Arial, Helvetica, sans-serif;
                            font-size:12px;
                            text-align: center;
                            width: 95%;
                            align: center;
                            }

                            .tabla th {
                            padding: 5px;
                            font-size: 12px;
                            background-color: #83aec0;
                            background-repeat: repeat-x;
                            color: #FFFFFF;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #558FA6;
                            border-bottom-color: #558FA6;
                            font-family: "Trebuchet MS", Arial;
                            text-transform: uppercase;
                            }

                            .tabla .modo1 {
                            font-size: 10px;
                            font-weight:bold;
                            background-color: #e2ebef;
                            background-repeat: repeat-x;
                            color: #34484E;
                            font-family: "Trebuchet MS", Arial;
                            }
                            .tabla .modo1 td {
                            padding: 5px;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #A4C4D0;
                            border-bottom-color: #A4C4D0;
                            text-align:right;
                            }

                            .tabla .modo1 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight:bold;
                            text-align: left;
                            background-color: #e2ebef;
                            background-repeat: repeat-x;
                            color: #34484E;
                            font-family: "Trebuchet MS", Arial;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #A4C4D0;
                            border-bottom-color: #A4C4D0;
                            }

                            .tabla .modo2 {
                            font-size: 12px;
                            font-weight:bold;
                            background-color: #fdfdf1;
                            background-repeat: repeat-x;
                            color: #990000;
                            font-family: "Trebuchet MS", Arial;
                            text-align:center;
                            }
                            .tabla .modo2 td {
                            padding: 5px;
                            }
                            .tabla .modo2 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight:bold;
                            background-color: #fdfdf1;
                            background-repeat: repeat-x;
                            color: #990000;
                            font-family: "Trebuchet MS", Arial;
                            text-align:left;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #EBE9BC;
                            border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                            text-decoration: none;
                            padding: 10px;
                            font-weight: 600;
                            font-size: 20px;
                            color: #ffffff;
                            background-color: #1883ba;
                            border-radius: 6px;
                            border: 2px solid #0016b0;
                            }

                            </style>
                            </head>
                            <body>
                            <table class="tabla">
                            <tr><th>Servicio</th><th>Fecha</th><th>Hora</th></tr>';

        $cuerpo_factura .= $datos_factura;
        $cuerpo_factura .= $botonPago;
        $cuerpo_factura .= '

                                                            ';





        if (!empty($IDSocio) && !empty($IDClub)) :
            $SQL = "SELECT * FROM ReservaGeneral WHERE IDSocio = " . $IDSocio . " Order by Fecha DESC,IDServicio ASC  LIMIT 30";
            $QRY = $dbo->query($SQL);
            if ($dbo->rows($QRY) > 0) :

                $message = $dbo->rows($QRY) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRY)) :
                    $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $Datos["IDServicio"] . "' ", "array");
                    //nombre servicio
                    $id_servicio = $Datos["IDServicio"];
                    $id_servicio_mestro_menu = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
                    $asoc_servicio["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                    $asoc_servicio["NombrePersonalizado"] = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . $IDClub . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                    if (!empty($asoc_servicio["NombrePersonalizado"])) {
                        $NombreServicio = $asoc_servicio["NombrePersonalizado"];
                    } else {
                        $NombreServicio = $asoc_servicio["Nombre"];
                    }
                    $Html .= "<tr><td>" . $NombreServicio . "</td><td>" . $Datos["Fecha"] . "</td><td>" . $Datos["Hora"] . "</td>";
                endwhile;

                $Html .= "</table></body></html>";
                $InfoResponse["HistorialHtml"] = $Html;
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $InfoResponse;

            else :
                $Html = "No se encontraron datos";
                $InfoResponse["HistorialHtml"] = $Html;
                $respuesta["message"] = 'No hay datos';
                $respuesta["success"] = true;
                $respuesta["response"] = $InfoResponse;
            endif;

        else :
            $Html = "No se encontraron datos";
            $respuesta["message"] = 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function eliminar_lista_espera_socio($IDClub, $IDSocio, $IDReserva)
    {

        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {

            $sql_elimina_lista_espera_socio = $dbo->query("DELETE FROM ListaEspera WHERE IDClub = '" . $IDClub . "' AND IDListaEspera='" . $IDReserva . "' AND IDSocio = '" . $IDSocio . "'");

            $respuesta["message"] = "eliminado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = "eliminado";
        } else {
            $respuesta["message"] = "LER. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_horainicio_servicio($IDClub, $TipoApp, $IDUsuario, $IDSocio, $Fecha, $IDServicio, $IDClubAsociado)
    {
        $HoraInicial = "05:00:00";
        $HoraFinal = "22:00:00";
        $response_horas = array();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDServicio) && !empty($Fecha)) {
            $InfoDisponibilidad["Fecha"] = $Fecha;
            while (strtotime($HoraInicial) <= strtotime($HoraFinal)) {
                $array_hora["Hora"] = $HoraInicial;
                $array_hora["GMT"] = "-05:00";
                $HoraInicial = strtotime('+1 hour', strtotime($HoraInicial));
                $HoraInicial = date('H:i:s', $HoraInicial);
                array_push($response_horas, $array_hora);
            }
            $InfoDisponibilidad["Horas"] = $response_horas;
            $respuesta["message"] = "Horas Encontradas";
            $respuesta["success"] = true;
            $respuesta["response"] = $InfoDisponibilidad;
        } else {
            $respuesta["message"] = "H1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_hora_fin_servicio($IDClub, $TipoApp, $IDUsuario, $IDSocio, $Fecha, $IDServicio, $IDClubAsociado)
    {
        $HoraInicial = "05:00:00";
        $HoraFinal = "22:00:00";
        $response_horas = array();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDServicio) && !empty($Fecha)) {
            $InfoDisponibilidad["Fecha"] = $Fecha;
            while (strtotime($HoraInicial) <= strtotime($HoraFinal)) {
                $array_hora["Hora"] = $HoraInicial;
                $array_hora["GMT"] = "-05:00";
                $HoraInicial = strtotime('+1 hour', strtotime($HoraInicial));
                $HoraInicial = date('H:i:s', $HoraInicial);
                array_push($response_horas, $array_hora);
            }
            $InfoDisponibilidad["Horas"] = $response_horas;
            $respuesta["message"] = "Horas Encontradas";
            $respuesta["success"] = true;
            $respuesta["response"] = $InfoDisponibilidad;
        } else {
            $respuesta["message"] = "H1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
}
