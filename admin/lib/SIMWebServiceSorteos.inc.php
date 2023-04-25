<?php

class SIMWebServiceSorteos
{
    public function get_servicios_sorteo_turnos($IDClub, $IDSocio, $IDUsuario, $IDServicio = "")
    {
        $dbo = SIMDB::get();
        $response = array();

        if(!empty($IDClub)):

            if(!empty($IDServicio)):
                $condicion = " AND IDServicio = $IDServicio";
            endif;
            $SQLSorteo = "SELECT * FROM SorteosServicios WHERE IDClub = '$IDClub' AND Activo = 1 $condicion";
            $QRYSorteo = $dbo->query($SQLSorteo);
            if ($dbo->rows($QRYSorteo) > 0):
                $message = $dbo->rows($QRYDatos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($Datos = $dbo->fetchArray($QRYSorteo)):

                    $InfoSorteo[IDClub] = $Datos[IDClub];
                    $InfoSorteo[IDServicio] = $Datos[IDServicio];
                    $InfoSorteo[Nombre] = $Datos[Nombre];
                    $InfoSorteo[Icono] = SERVICIO_ROOT . $Datos[Icono];
                    $InfoSorteo[CantidadTurnos] = $Datos[CantidadTurnos];
                    $InfoSorteo[MinimoInvitadosSeleccion] = $Datos[MinimoInvitadosSeleccion];
                    $InfoSorteo[LabelIntroSeleccionTurno] = $Datos[LabelIntroSeleccionTurno];
                    $InfoSorteo[IntroSeleccionFecha] = $Datos[IntroSeleccionFecha];
                    $InfoSorteo[MinutosReserva] = $Datos[MinutosReserva];
                    $InfoSorteo[SoloIcono] = $Datos[SoloIcono];
                    $InfoSorteo[LabelBotonAgregarInvitado] = $Datos[LabelBotonAgregarInvitado];
                    $InfoSorteo[LabelAgregarInvitadoSocio] = $Datos[LabelAgregarInvitadoSocio];
                    $InfoSorteo[LabelAgregarInvitadoExterno] = $Datos[LabelAgregarInvitadoExterno];
                    $InfoSorteo[IntroAgregarInvitado] = $Datos[IntroAgregarInvitado];
                    $InfoSorteo[PermiteInvitadoExternoCedula] = $Datos[PermiteInvitadoExternoCedula];
                    $InfoSorteo[PermiteInvitadoExternoCorreo] = $Datos[PermiteInvitadoExternoCorreo];
                    $InfoSorteo[PermiteInvitadoExternoFechaNacimiento] = $Datos[PermiteInvitadoExternoFechaNacimiento];

                    array_push($response, $InfoSorteo);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_fechas_sorteo_turnos($IDClub, $IDSocio, $IDUsuario, $IDServicio)
    {
        $dbo = SIMDB::get();
        $response = array();
        
        if(!empty($IDServicio)):

            $DatosFechas = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio,"","","","","","");        

            if (count($DatosFechas[response][0][Fechas]) > 0):
                $message = count($DatosFechas[response][Fechas]) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                
                foreach($DatosFechas[response][0][Fechas] as $id => $Fecha):
                    //Verifico si esa fecha ya fue sorteada para no mostrarla mas                    
                    $FechaSorteada = $dbo->getFields("ReservaSorteo", "IDReservaSorteo", "Fecha  = '".$Fecha["Fecha"]."' and IDServicio = '".$IDServicio."'  and Sorteado = 1 LIMIT 1");
                    if($FechaSorteada==""){
                        $InfoFechas["Fecha"] = $Fecha["Fecha"];
                        $InfoFechas["FechaVisual"] = SIMUtil::tiempo($Fecha["Fecha"]);
                        array_push($response, $InfoFechas);
                    }
                endforeach;             

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_socios_sorteos_club($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Tag, $Fecha)
    {
        $dbo = &SIMDB::get();

        if(!empty($IDClub)):

            $foto = "";
            // Secciones Socio
            if (!empty($numero_documento)):
                $array_condiciones[] = " NumeroDocumento  = '" . $numero_documento . "'";
            endif;

            // Seccion Especifica
            if (!empty($numero_derecho)):
                $array_condiciones[] = " Accion  = '" . $numero_derecho . "'";
            endif;

            // Tag
            if (!empty($Tag)):
                //$Tag = utf8_decode($Tag);
                $array_buscar = explode(" ", $Tag);
                foreach ($array_buscar as $key => $value) {
                    $array_condiciones_nombre[] = " (	Nombre  like '%" . $value . "%' or Apellido like '%" . $value . "%' or Accion like '%" . $value . "%' or NumeroDocumento like '%" . $value . "%' or Accion like '%" . $value . "%' or Predio like '%" . $value . "%' )";
                }
                if (count($array_condiciones_nombre) > 0) {
                    $condicion_nombre = implode(" and ", $array_condiciones_nombre);
                }

                $array_condiciones[] = $condicion_nombre;
            endif;

            if (!empty($IDSocio) && empty($Tag)):
                $sql_fav = "SELECT * FROM SocioFavorito WHERE IDSocio = '" . $IDSocio . "'";
                $qry_fav = $dbo->query($sql_fav);
                while ($r_fav = $dbo->fetchArray($qry_fav)) {
                    $array_favoritos[] = $r_fav["IDSocio2"];
                }
                if (count($array_favoritos) > 0):
                    $array_condiciones[] = " IDSocio  in  (" . implode(",", $array_favoritos) . ")";
                else:
                    $array_condiciones[] = " IDSocio  in  (0)";
                endif;
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Socio WHERE IDClub = '" . $IDClub . "' and (IDEstadoSocio = 1 OR IDEstadoSocio = 5) and IDSocio <> '" . $IDSocio . "'" . $condiciones_noticia . " ORDER BY Nombre ";
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
                    if (!empty($IDSocio)):
                        $socio_favorito = $dbo->getFields("SocioFavorito", "IDSocioFavorito", "IDSocio = '" . $IDSocio . "' and IDSocio2 = '" . $r["IDSocio"] . "'");
                        if (!empty($socio_favorito)):
                            $favorito = "S";
                        else:
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
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavorutiliceelbuscador,noseencontraronregistros', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
        
    }

    public function validar_invitados_sorteo($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Invitados)
    {   
        $dbo = SIMDB::get();
        $response = array();
        $CantidadSocios = 1;
        $CantidadExternos = 0;

        $datos_invitado = json_decode($Invitados, true);

        if(!empty($IDServicio) && !empty($IDClub)):

            $SQLSorteo = "SELECT * FROM SorteosServicios WHERE IDClub = '$IDClub' AND IDServicio = '$IDServicio' AND Activo = 1";
            $QRYSorteo = $dbo->query($SQLSorteo);
            if ($dbo->rows($QRYSorteo) > 0):
                
                while ($Datos = $dbo->fetchArray($QRYSorteo)):
                    // VALIDAMOS LA CANTIDADA DE INIVTADOS NO EXECEDA
                    $CantidadMinima = $Datos[MinimoInvitadosSeleccion];                    
                    $CantidadMaxima = $Datos[MaximoInvitadosSeleccion];                    
                    $CantidadInvitados = count($datos_invitado);

                    if($CantidadInvitados < $CantidadMinima):
                        $respuesta["message"] = "La cantidad minima de invitados debe ser $CantidadMinima";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;         

                    if($CantidadInvitados > $CantidadMaxima):
                        $respuesta["message"] = "La cantidad maxima de invitados debe ser $CantidadMaxima";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;               

                endwhile;

                // VALIDAMOS QUE LOS INVITADOS NO TENGAN RESERVAS ACTIVAS PARA LA FECHA 

                $ValidaECantidad = 0; // DATO PARA SABER SI VALIDAMOS QUE EXISTO POR SOCIO UNO INVITADO EXTERNO
                if(count($datos_invitado) > 0):
                    foreach($datos_invitado as $Invitado):
                        $IDSocioValida = $Invitado[IDSocio];
                        $NombreInvitado = $Invitado[Nombre];
                        if(!empty($IDSocioValida)):
                            $CantidadSocios++;
                            // BSCAMOS LAS RESERVAS DEL SOCIO
                            $SQLReserva = "SELECT RG.IDReservaGeneral FROM ReservaGeneral RG WHERE (RG.IDSocio = '$IDSocioValida' OR RG.IDSocioBeneficiario = '$IDSocioValida') AND RG.IDClub = '$IDClub' AND RG.Fecha = '$Fecha'";
                            $QRYReserva = $dbo->query($SQLReserva);

                            if($dbo->rows($QRYReserva) > 0):
                                $respuesta["message"] = "Un invitado tiene una reserva personal para la misma fecha y no es posible invitarlo";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            // BUSCAMOS DONDE ESTE INVITADO
                            $SQLReservaInvitado = "SELECT RG.IDReservaGeneral FROM ReservaGeneral RG, ReservaGeneralInvitado RGI WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral AND RGI.IDSocio = '$IDSocioValida' AND RG.IDClub = '$IDClub' AND RG.Fecha = '$Fecha'";
                            $QRYReservaInvitado = $dbo->query($SQLReservaInvitado);

                            if($dbo->rows($QRYReservaInvitado) > 0):
                                $respuesta["message"] = "Un invitado tiene una reserva como invitado para la misma fecha y no es posible invitarlo";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            // VALIDAMOS QUE NO ESTE EN OTRA RESERVA DE SORTEO
                            $SQLEnSorteo = "SELECT IDReservaSorteo FROM ReservaSorteo WHERE IDSocio = $IDSocioValida AND Fecha = '$Fecha'";
                            $QRYEnSorteo = $dbo->query($SQLEnSorteo);

                            if($dbo->rows($QRYEnSorteo) > 0):
                                $respuesta["message"] = "EL invitado $NombreInvitado ya esta inscrito en otro sorteo para la misma fecha y no puede ser agregado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            // VALIDAMOS QUE NO ESTE EN OTRA RESERVA DE SORTEO COMO INVITADO
                            $SQLEnSorteoInvitado = "SELECT IDReservaSorteoInvitado FROM ReservaSorteo RS, ReservaSorteoInvitado RSI WHERE RSI.IDReservaSorteo = RS.IDReservaSorteo AND RSI.IDSocio = $IDSocioValida AND RS.Fecha = '$Fecha'";
                            $QRYEnSorteoInvitado = $dbo->query($SQLEnSorteoInvitado);

                            if($dbo->rows($QRYEnSorteoInvitado) > 0):
                                $respuesta["message"] = "EL invitado $NombreInvitado ya esta inscrito en otro sorteo como invitado para la misma fecha y no puede ser agregado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                        else:
                            $CantidadExternos++;
                            $ValidaECantidad = 1;
                        endif;

                    endforeach;

                    if(($IDClub == 8 || $IDClub == 201) && ($CantidadSocios < $CantidadExternos) && $ValidaECantidad == 1):
                        $respuesta["message"] = "Deben exisitr misma cantidad de socios y de invitados, por cada socio del club debe existir un invitado externo";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;
            endif;

            $respuesta["message"] = "Todo Correcto con los invitados";
            $respuesta["success"] = true;
            $respuesta["response"] = "";

        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
        
    }

    public function get_elemento_fecha_sorteo_turnos($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha)
    {
        $dbo = SIMDB::get();
        $response = array();

        if(!empty($IDServicio) && !empty($IDClub) && !empty($Fecha)):

            $DatosDisponibilidad = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha,"","","","","","","","","");
           

            if(count($DatosDisponibilidad[response]) > 0):
                $message = count($DatosDisponibilidad[response]) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                foreach($DatosDisponibilidad[response] as $id => $General):

                    $InfoResponse[IDClub] = $General[IDClub];
                    $InfoResponse[IDServicio] = $General[IDServicio];
                    $InfoResponse[Fecha] = $General[Fecha];

                    $DisponibilidadResponse = array();

                    foreach($General[Disponibilidad] as $id => $Disponibilidad):
                        foreach($Disponibilidad as $id => $InfoDisponibilidad):
                            $ResponseDisponibilidad[Hora] = $InfoDisponibilidad[Hora];
                            $ResponseDisponibilidad[HoraVisual] = $InfoDisponibilidad[Hora];
                            $ResponseDisponibilidad[Disponible] = $InfoDisponibilidad[Disponible];
                            $ResponseDisponibilidad[IDElemento] = $InfoDisponibilidad[IDElemento];
                            $ResponseDisponibilidad[NombreElemento] = $InfoDisponibilidad[NombreElemento];
                            $ResponseDisponibilidad[LabelDisponible] = $InfoDisponibilidad[LabelDisponible];
                            $ResponseDisponibilidad[LabelNoDisponible] = "No Disponible";
                            $ResponseDisponibilidad[Tee] = $InfoDisponibilidad[Tee];

                            array_push($DisponibilidadResponse,$ResponseDisponibilidad);
                        endforeach;
                    endforeach;

                    $InfoResponse[Disponibilidad] = $DisponibilidadResponse;
                    $InfoResponse[name] = $General[name];

                    // array_push($response,$InfoResponse);
                    $response = $InfoResponse;

                endforeach;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function set_sorteo_servicio($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Invitados, $Elementos)
    {
        $dbo = SIMDB::get();

        if(!empty($IDSocio) && !empty($IDServicio) && !empty($Fecha) && !empty($Elementos)):
            // VALIDAMOS QUE EL SOCIO NO ESTE EN SORTEOS
            $SQLSorteoSocio = "SELECT IDReservaSorteo FROM ReservaSorteo WHERE IDSocio = $IDSocio AND Fecha = '$Fecha'";
            $QRYSorteoSocio = $dbo->query($SQLSorteoSocio);

            if($dbo->rows($QRYSorteoSocio) <= 0):

                $SQLSorteoSocio = "SELECT IDReservaSorteo FROM ReservaSorteo RS, ReservaSorteoInvitado RSI WHERE RS.IDReservaSorteo = RSI.IDReservaSorteo AND RSI.IDSocio = $IDSocio AND RS.Fecha = '$Fecha'";
                $QRYSorteoSocio = $dbo->query($SQLSorteoSocio);
                
                if($dbo->rows($QRYSorteoSocio) <= 0):

                    // INSERTAMOS EL REGISTRO DEL TORNEO
                    $InsertSorteo = "INSERT INTO ReservaSorteo (IDClub, IDServicio, IDSocio, Fecha, FechaTrCr, UsuarioTrCr) VALUES ('$IDClub','$IDServicio','$IDSocio','$Fecha',NOW(),'SOCIO-$IDSocio')";
                    $dbo->query($InsertSorteo);
                    $IDReservaSorteo = $dbo->lastID();

                    // INSERTAMOS LOS ELEMENTOS DE LA RESERVA
                    $Datos_Elementos = json_decode($Elementos, true);
                    if(count($Datos_Elementos) > 0):
                        foreach($Datos_Elementos as $Elemento):
                            $IDElemento = $Elemento[IDElemento];
                            $PosicionElemento = $Elemento[PosicionSorteo];
                            $HoraElemento = $Elemento[Hora];
                            $Tee = $Elemento[Tee];

                            if($Tee == "null" || empty($Tee)):
                                $Tee = "";
                            endif;

                            // VALIDAMOS QUE NO TENGA RESERVAS EN LA MISMA FECHA Y HORA

                            $SQLValida = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE (IDSocio = '$IDSocio' OR IDSocioBeneficiario = '$IDSocio') AND Fecha = '$Fecha' AND Hora = '$HoraElemento'";
                            $QRYValida = $dbo->query($SQLValida);

                            $SQLReserva = "SELECT RG.IDReservaGeneral FROM ReservaGeneral RG, ReservaGeneralInvitado RGI WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral AND RGI.IDSocio = '$IDSocio' AND RG.IDClub = '$IDClub' AND RG.Fecha = '$Fecha' AND RG.Hora = '$HoraElemento'";
                            $QRYReserva = $dbo->query($SQLReserva);

                            if($dbo->rows($QRYValida) > 0):
                                $respuesta["message"] = "NO FU POSIBLE INCRIBIRSE AL SORTEO PORQUE CUENTA CON OTRA RESERVA PARA UNA HORA Y FECHA SELECCIONADA";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;

                                // SE DEBE ELIMINAR TODO LO CREADO

                                $dbo->query("DELETE FROM ReservaSorteo WHERE IDReservaSorteo = $IDReservaSorteo");
                                $dbo->query("DELETE FROM ReservaSorteoElemento WHERE IDReservaSorteo = $IDReservaSorteo");
                                return $respuesta;
                            endif;

                            $InsertElemento = "INSERT INTO ReservaSorteoElemento (IDReservaSorteo,IDElemento,PosicionElemento,Hora,Tee) VALUES ('$IDReservaSorteo','$IDElemento','$PosicionElemento','$HoraElemento','$Tee')";
                            $dbo->query($InsertElemento);
                        endforeach;
                    endif;

                    // INSERTAMOS LOS INVITADOS
                    $Datos_Invitados = json_decode($Invitados, true);
                    if(count($Datos_Invitados) > 0):
                        foreach($Datos_Invitados as $Invitado):
                            $IDSocioInvitado = $Invitado[IDSocio];
                            $NombreInvitado = $Invitado[Nombre];
                            $CedulaInvitado = $Invitado[Cedula];
                            $CorreoInvitado = $Invitado[Correo];
                            $FechaNacimientoInvitado = $Invitado[FechaNacimiento];

                            $InsertInvitado = "INSERT INTO ReservaSorteoInvitado (IDReservaSorteo, IDSocio, Nombre, Correo, Cedula, FechaNacimiento) VALUES ('$IDReservaSorteo','$IDSocioInvitado','$NombreInvitado','$CorreoInvitado','$CedulaInvitado','$FechaNacimientoInvitado')";
                            $dbo->query($InsertInvitado);
                        endforeach;
                    endif;

                    $respuesta["message"] = "Inscripción a sorteo exitosa";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "";
                else:
                    $respuesta["message"] = "Ya estas inscrito a un sorteo en esa misma fecha como invitado.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = "";
                endif;
            else:
                $respuesta["message"] = "Ya estas inscrito a un sorteo en esa misma fecha";
                $respuesta["success"] = false;
                $respuesta["response"] = "";
    
            endif;           
            
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;


        return $respuesta;


    }

    public function get_mis_reserva_sorteo_turnos($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();
        $Hoy = date('Y-m-d');
        if(!empty($IDSocio)):
            $SQLSorteos = "SELECT * FROM ReservaSorteo WHERE IDSocio = '$IDSocio' AND Fecha >= '$Hoy'";
            $QRYSorteos = $dbo->query($SQLSorteos);
            if($dbo->rows($QRYSorteos) > 0):
                $message = $dbo->rows($QRYSorteos) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while($Datos = $dbo->fetchArray($QRYSorteos)):                  

                    $Datos_Servicio = $dbo->fetchAll('Servicio','IDServicio = ' . $Datos[IDServicio]);

                    $NombreServicio = $dbo->getFields("ServicioClub","TituloServicio","IDServicioMaestro = $Datos_Servicio[IDServicioMaestro] AND IDClub = $IDClub");
                    if(empty($NombreServicio)):
                        $NombreServicio = $dbo->getFields("ServicioMaestro","Nombre","IDServicioMaestro = $Datos_Servicio[IDServicioMaestro]");
                    endif;

                    $InfoResponse[IDReserva] = $Datos[IDReservaSorteo];
                    $InfoResponse[Fecha] = $Datos[Fecha];
                    $InfoResponse[FechaVisual] = SIMUtil::tiempo($Datos[Fecha]);

                    $InfoResponse[IconoReserva] = SERVICIO_ROOT . $Datos_Servicio[Icono];
                    $InfoResponse[NombreServicio] = $NombreServicio;

                    $ElementosSorteo = array();

                    // BUSCAMOS LOS ELEMENTOS
                    $SQLElementos = "SELECT * FROM ReservaSorteoElemento WHERE IDReservaSorteo = $Datos[IDReservaSorteo] Order by PosicionElemento ";
                    $QRYElementos = $dbo->query($SQLElementos);
                    while($DatosElementos = $dbo->fetchArray($QRYElementos)):
                        
                        $NombreElemento = $dbo->getFields("ServicioElemento","Nombre","IDServicioElemento = $DatosElementos[IDElemento]");
                        $InfoElementos[Hora] = $DatosElementos[Hora];
                        $InfoElementos[HoraVisual] = $DatosElementos[Hora] . " " . $DatosElementos[Tee];
                        $InfoElementos[IDElemento] = $DatosElementos[IDElemento];
                        $InfoElementos[NombreElemento] = $NombreElemento;
                        $InfoElementos[PosicionSorteo] = $DatosElementos[PosicionElemento];

                        array_push($ElementosSorteo,$InfoElementos);
                    endwhile;

                    $InfoResponse[ElementosSorteo] = $ElementosSorteo;

                    array_push($response,$InfoResponse);

                endwhile;

                // BUSCAMOS DONDE ES INVITADO
                $SQLSorteosInivtado = "SELECT * FROM ReservaSorteoInvitado WHERE IDSocio = '$IDSocio'";
                $QRYSorteosInivtado = $dbo->query($SQLSorteosInivtado);
                while($Datos = $dbo->fetchArray($QRYSorteosInivtado)):                  

                    $Datos_Servicio = $dbo->fetchAll('Servicio','IDServicio = ' . $Datos[IDServicio]);
                    $Datos_Sorteo = $dbo->fetchAll("ReservaSorteo","IDReservaSorteo = $Datos[ReservaSorteo]");

                    $NombreServicio = $dbo->getFields("ServicioClub","TituloServicio","IDServicioMaestro = $Datos_Servicio[IDServicioMaestro] AND IDClub = $IDClub");
                    if(empty($NombreServicio)):
                        $NombreServicio = $dbo->getFields("ServicioMaestro","Nombre","IDServicioMaestro = $Datos_Servicio[IDServicioMaestro]");
                    endif;

                    $InfoResponse[IDReserva] = $Datos_Sorteo[IDReservaSorteo];
                    $InfoResponse[Fecha] = $Datos_Sorteo[Fecha];
                    $InfoResponse[FechaVisual] = SIMUtil::tiempo($Datos_Sorteo[Fecha]);

                    $InfoResponse[IconoReserva] = SERVICIO_ROOT . $Datos_Servicio[Icono];
                    $InfoResponse[NombreServicio] = $NombreServicio;

                    $ElementosSorteo = array();

                    // BUSCAMOS LOS ELEMENTOS
                    $SQLElementos = "SELECT * FROM ReservaSorteoElemento WHERE IDReservaSorteo = $Datos_Sorteo[IDReservaSorteo]";
                    $QRYElementos = $dbo->query($SQLElementos);
                    while($DatosElementos = $dbo->fetchArray($QRYElementos)):
                        
                        $NombreElemento = $dbo->getFields("ServicioElemento","Nombre","IDServicioElemento = $DatosElementos[IDElemento]");
                        $InfoElementos[Hora] = $DatosElementos[Hora];
                        $InfoElementos[HoraVisual] = $DatosElementos[Hora] . " " . $DatosElementos[Tee];
                        $InfoElementos[IDElemento] = $DatosElementos[IDElemento];
                        $InfoElementos[NombreElemento] = $NombreElemento;
                        $InfoElementos[PosicionSorteo] = $DatosElementos[PosicionElemento];

                        array_push($ElementosSorteo,$InfoElementos);
                    endwhile;

                    $InfoResponse[ElementosSorteo] = $ElementosSorteo;
                    $InfoResponse[TagReserva] = "Reservas Sorteos";

                    array_push($response,$InfoResponse);

                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            endif;
        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function eliminar_sorteo_reserva($IDClub, $IDSocio, $IDUsuario, $IDReserva, $Razon = "", $UsuarioElimina = "")
    {
        $dbo = SIMDB::get();
        $hoy = date("Y-m-d");

        if(!empty($IDSocio) && !empty($IDReserva)):

            // BUSCAMOS LA RESERVA
            $DatoSorteo = $dbo->fetchAll("ReservaSorteo","IDReservaSorteo = $IDReserva");

            if(!empty($DatoSorteo)):
                
                // VALIDAMOS QUE EL SORTEO NO HAYA SIDO SORTEADO
                if($DatoSorteo[Sorteado] == 0):

                    // VALIDAMOS QUE SEA EL MISMO DUEÑO DE LA RESERVA
                    if($IDSocio == $DatoSorteo[IDSocio]):

                        $Eliminar = 1;
                        // BUSCAMOS SI HAY ALGUN INIVTADO SOCIO PARA PODER PASAR LA RESERVA A SU NOMBRE
                        /* $SQLInvitados = "SELECT * FROM ReservaSorteoInvitado WHERE IDReservaSorteo = $IDReserva";
                        $QRYInvitados = $dbo->query($SQLInvitados);

                        while($Datos = $dbo->fetchArray($QRYInvitados)):
                            if($Datos[IDSocio] > 0):
                                // DEBEMOS ACTULIZAR EL DUEÑO DE LA RESEVA
                                $Eliminar = 0;
                                $InvitadoEliminar = $Datos[IDReservaSorteoInvitado];
                                $NuevoSocio = $Datos[IDSocio];
                                break;
                            endif;
                        endwhile; */       

                        if($Eliminar == 1):
                            // INSERTAMOS LA INFO EN LA TABLA DE ELIMINADOS
                            $DatoSorteo[FechaTrCr] = date("Y-m-d H:i:s");               

                            if(empty($Razon)):
                                $DatoSorteo[Razon] = "Eliminada Por Socio: $IDSocio";
                                $DatoSorteo[UsuarioTrCr] = "Eliminada Por Socio: $IDSocio";                    
                            else:
                                $DatoSorteo[Razon] = $Razon;
                                $DatoSorteo[UsuarioTrCr] = $UsuarioElimina;
                            endif;                

                            $dbo->insert($DatoSorteo,"ReservaSorteoEliminado","IDReservaSorteo");

                            $DeleteSorteo = "DELETE FROM ReservaSorteo WHERE IDReservaSorteo = $IDReserva";
                            $dbo->query($DeleteSorteo);

                            // GUARDAMOS LOS INIVTADOS     
                            $SQLInvitados = "SELECT * FROM ReservaSorteoInvitado WHERE IDReservaSorteo = $IDReserva";
                            $QRYInvitados = $dbo->query($SQLInvitados);                 
                            while($Datos = $dbo->fetchArray($QRYInvitados)):
                                $dbo->insert($Datos,"ReservaSorteoInvitadoEliminado","IDReservaSorteoInvitado");
                            endwhile;

                            $DeleteInvitados = "DELETE FROM ReservaSorteoInvitado WHERE IDReservaSorteo = $IDReserva";
                            $dbo->query($DeleteInvitados);

                            // GUARDAMOS LOS ELEMENTOS
                            $SQLElementos = "SELECT * FROM ReservaSorteoElemento WHERE IDReservaSorteo = $IDReserva";
                            $QRYElementos = $dbo->query($SQLElementos);

                            while($Datos = $dbo->fetchArray($QRYElementos)):
                                $Datos[Razon] = $Razon;
                                $dbo->insert($Datos, "ReservaSorteoElementoEliminado","IDReservaSorteoElemento");
                            endwhile;  

                            $DeleteElementos = "DELETE FROM ReservaSorteoElemento WHERE IDReservaSorteo = $IDReserva";
                            $dbo->query($DeleteElementos);

                            $respuesta["message"] = "Inscripción a sorteo cancelada con exito";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;

                        else:

                            $UPDATE = "UPDATE ReservaSorteo SET IDSocio = '$NuevoSocio' WHERE IDReservaSorteo = $IDReserva";
                            $dbo->query($UPDATE);

                            $DeleteInvitados = "DELETE FROM ReservaSorteoInvitado WHERE IDReservaSorteoInvitado = $InvitadoEliminar";
                            $dbo->query($DeleteInvitados);

                            $respuesta["message"] = "La inscripcion al sorteo a cambiado de dueño";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;

                        endif;        
                    else:

                        $respuesta["message"] = "No se puede cancelar la inscripcion ya que usted no es el dueño de la reserva";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    endif;
                else:
                    $respuesta["message"] = "El sorteo ya se realizo y no te puedes eliminar";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;                 
            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohayregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
        
    }

    public function reservas_sorteo($IDClub,$IDServicio,$Fecha,$IDElemento)
    {
        $dbo = SIMDB::get();
        $response = array();
        $SQL = "SELECT * FROM ReservaSorteo RS, ReservaSorteoElemento RSE WHERE RS.IDClub = $IDClub AND RS.IDServicio = $IDServicio AND RS.Fecha = '$Fecha' AND RSE.IDElemento = $IDElemento AND RS.IDReservaSorteo = RSE.IDReservaSorteo";
        $QRY = $dbo->query($SQL);
        while($Datos = $dbo->fetchArray($QRY)):
            $InfoResponse[IDReserva] = $Datos[IDReservaSorteo]; 
            $InfoResponse[Hora] = $Datos[Hora]; 
            $InfoResponse[Fecha] = $Datos[Fecha]; 
            $InfoResponse[IDSocio] = $Datos[IDSocio]; 
            $InfoResponse[IDElemento] = $Datos[IDElemento]; 
            $InfoResponse[IDReservaSorteoElemento] = $Datos[IDReservaSorteoElemento]; 
            $InfoResponse[PosicionElemento] = $Datos[PosicionElemento]; 
            $InfoResponse[Tee] = $Datos[Tee]; 

            array_push($response, $InfoResponse);

        endwhile;

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;

    }

}