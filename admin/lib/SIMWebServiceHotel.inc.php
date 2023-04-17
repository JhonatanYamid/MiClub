<?php
class SIMWebServiceHotel
{



    function get_fechas_disponibles_hotel($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();

        $response = array();
        $fecha_actual = date("Y-m-d");
        $dias_transcurridos = date('z');



        // $dias =  365 - $dias_transcurridos; //cantidad de dias para marcar en el calendario
        $dias = 80;
        $fenal_seguida = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
        for ($i = 0; $i <= $dias; $i++) :
            $resp = SIMWebServiceHotel::get_valida_fecha($IDClub, $fecha_actual, $fenal_seguida, "", "");
            $habitaciones = "N";
            $num_habitacion = 0;
            $total = 0;
            foreach ($resp["response"] as $key => $resultado) {
                foreach ($resultado["Habitaciones"] as $key2 => $habitacion) {
                    foreach ($habitacion["HabitacionTorre"] as $key3 => $hab_torre) {

                        $total = $total + 1;
                    }
                    if ($total > 0) :
                        $datos["Fecha"] = $fecha_actual;
                        array_push($response, $datos);
                    endif;
                }
            }

            $fecha_actual = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
            $fenal_seguida = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
        endfor;


        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;


        return $respuesta;
    }


    public function verifica_temporada($FechaInicio, $FechaFin, $IDClub)
    {
        $dbo = &SIMDB::get();
        //VALIDAMOS QUE LAS FECHAS ESTEN ENTRE EL RANGO DE TEMPORADA ALTA
       $sql = "SELECT * FROM TemporadaAlta WHERE FechaInicio <= '$FechaFin' AND FechaFin >= '$FechaInicio' and IDClub = '" . $IDClub . "'";
       
      /*  $sql = "SELECT * FROM TemporadaAlta WHERE FechaInicio <= '$FechaInicio' AND FechaFin >= '$FechaInicio' and IDClub = '" . $IDClub . "'"; */
        $TemporadaAlta = $dbo->query($sql);
        $ArrayTemporadaAlta = $dbo->fetchArray($TemporadaAlta);
        return (int) $ArrayTemporadaAlta["IDTemporadaAlta"];
    }

    public function verifica_temporada_corto_plazo($FechaInicio, $IDClub, $IDHabitacion = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDHabitacion)) :
            $CondicionHabitacion = " AND IDHabitacion = $IDHabitacion ";
        endif;
        //un dia antes de la fecha de reserva para poder hacer la comparacion en dado caso la reserva empiece un sabado
        $FechaInicio = date("Y-m-d", strtotime($FechaInicio . "- 1 days"));
        //dos dias depues de la fecha de reserva para poder hacer la comparacion en dado caso la reserva termine un domingo
        $Fechalimite = date("Y-m-d", strtotime($FechaInicio . "+ 2 days"));
        $sql = "SELECT * FROM TemporadaCortoPlazoHotel WHERE IDClub = '$IDClub' and FechaInicio BETWEEN '$FechaInicio' AND '$Fechalimite' and Nombre like '%PUENTE%'";
        $TemporadaCortoPlazo = $dbo->query($sql);
        $ArrayTemporadaCortoPlazo = $dbo->fetchArray($TemporadaCortoPlazo);
        return $ArrayTemporadaCortoPlazo;
    }

    public function get_configuracion_hotel($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionHotel  WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["BotonAdicional"] = $r["BotonAdicional"];
                $configuracion["BotonNinera"] = $r["BotonNinera"];
                $configuracion["BotonCorral"] = $r["BotonCorral"];
                $configuracion["BotonInvitado"] = $r["BotonInvitado"];
                $configuracion["LabelBotonPagar"] = $r["LabelBotonPagar"];
                $configuracion["LabelBotonAcompanante"] = $r["LabelBotonAcompanante"];
                $configuracion["Observaciones"] = $r["Observaciones"];
                $configuracion["MostrarDetalleHabitacion"] = $r["MostrarDetalleHabitacion"];
                $configuracion["LabelDuenoReserva"] = $r["LabelDuenoReserva"];
                $configuracion["LabelDuenoInvitado"] = $r["LabelDuenoInvitado"];
                $configuracion["InvitadoExternoSocio"] = $r["InvitadoExternoSocio"];
                $configuracion["FormularioInvitado"] = $r["FormularioInvitado"];
                $configuracion["PermiteEditarinvitados"] = $r["PermiteEditarinvitados"];
                $configuracion["SeleccionFechasHeaderLabel"] = $r["SeleccionFechasHeaderLabel"];
                $configuracion["PermiteVistaFechaCalendario"] = $r["PermiteVistaFechaCalendario"];
                $configuracion["PermiteMostrarFechasDisponiblesCalendario"] = $r["PermiteMostrarFechasDisponiblesCalendario"];
                $configuracion["ColorFechasDisponiblesCalendario"] = $r["ColorFechasDisponiblesCalendario"];

                //minutos en la reserva
                if ($r["MinutosReserva"] > 0) {
                    $MinutosReservas = $r["MinutosReserva"];
                } else {
                    $MinutosReservas = "";
                }
                $configuracion["MinutosReserva"] = $MinutosReservas;

                $configuracion["PermiteEliminarReservaSocio"] = $r["PermiteEliminarReservaSocio"];
                $configuracion["LabelEliminarReservasocio"] = $r["LabelEliminarReservasocio"];
                //$configuracion["TipoReserva"] = $r["TipoReserva"];

                $response_campos_form = array();
                $array_campos_form = explode(",", $r["CamposInvitado"]);
                if (count($array_campos_form) > 0) {
                    foreach ($array_campos_form as $key => $value) {
                        $camposform["Campo"] = $value;
                        array_push($response_campos_form, $camposform);
                    }
                }
                $configuracion["CamposInvitado"] = $response_campos_form;

                //Tipos de pagos recibidos
                $response_tipo_pago = array();
                $sql_tipo_pago = "SELECT * FROM HotelTipoPago HTP, TipoPago TP  WHERE HTP.IDTipoPago = TP.IDTipoPago and IDClub = '" . $IDClub . "' ";
                $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                if ($dbo->rows($qry_tipo_pago) > 0) {
                    while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                        $tipopago["IDClub"] = $IDClub;
                        $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                        $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                        $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);
                        $tipopago["Nombre"] = $r_tipo_pago["Nombre"];
                        $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];
                        array_push($response_tipo_pago, $tipopago);
                    } //end while
                }
                $configuracion["TipoPago"] = $response_tipo_pago;

                $response_campo_editar = array();
                $sql_campo_editar = "SELECT IDCampoHotel, Nombre,Tipo,Valores,Obligatorio,Orden FROM CampoHotel CH
																	WHERE CH.IDClub = '" . $IDClub . "' ORDER BY CH.Orden";

                $qry_campo_editar = $dbo->query($sql_campo_editar);
                if ($dbo->rows($qry_campo_editar) > 0) {
                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                        $campo_editar["IDCampo"] = $r_campo_editar["IDCampoHotel"];
                        $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                        $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                        $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                        $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                        array_push($response_campo_editar, $campo_editar);
                    } //ednw while
                }
                $configuracion["CamposReserva"] = $response_campo_editar;

                // TIPOS DE RESERVA HABILITADOS
                $responde_tipo_reserva = array();
                $SQLTipoReserva = "SELECT * FROM TipoReservaHotel WHERE IDClub = $IDClub AND IDConfiguracionHotel = $r[IDConfiguracionHotel]";
                $QRYTipoReserva = $dbo->query($SQLTipoReserva);
                while ($DatosTipo = $dbo->fetchArray($QRYTipoReserva)) :

                    $InfoTipo[Tipo] = $DatosTipo[Tipo];
                    $InfoTipo[Label] = $DatosTipo[LabelTipo];

                    if ($DatosTipo[Tipo] == 'Pasadia') :

                        $configuracion[FormularioInvitadoPasadia] = $DatosTipo["FormularioInvitadoPasadia"];
                        $configuracion[InvitadoExternoSocioPasadia] = $DatosTipo["InvitadoExternoSocioPasadia"];
                        $configuracion[LabelBotonAcompanantePasadia] = $DatosTipo["LabelBotonAcompanantePasadia"];
                        $configuracion[ObservacionesPasadia] = $DatosTipo["ObservacionesPasadia"];
                        $configuracion[LabelDuenoReservaPasadia] = $DatosTipo["LabelDuenoReservaPasadia"];
                        $configuracion[LabelDuenoInvitadoPasadia] = $DatosTipo["LabelDuenoInvitadoPasadia"];
                        $configuracion[BotonAdicionalPasadia] = $DatosTipo["BotonAdicionalPasadia"];
                        $configuracion[BotonNineraPasadia] = $DatosTipo["BotonNineraPasadia"];
                        $configuracion[BotonCorralPasadia] = $DatosTipo["BotonCorralPasadia"];
                        $configuracion[BotonInvitadoPasadia] = $DatosTipo["BotonInvitadoPasadia"];

                        $response_campo_editar_pasadia = array();
                        $sql_campo_editar = "SELECT IDCampoHotel, Nombre,Tipo,Valores,Obligatorio,Orden FROM CampoHotelPasadia CH
                                                                            WHERE CH.IDClub = '" . $IDClub . "' ORDER BY CH.Orden";

                        $qry_campo_editar = $dbo->query($sql_campo_editar);
                        if ($dbo->rows($qry_campo_editar) > 0) {
                            while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                                $campo_editar["IDCampo"] = $r_campo_editar["IDCampoHotel"];
                                $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                                $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                                $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                                $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                                array_push($response_campo_editar_pasadia, $campo_editar);
                            } //ednw while
                        }

                        $configuracion[CamposReservaPasadia] = $response_campo_editar_pasadia;

                        $response_campos_form_pasadia = array();
                        $array_campos_form_pasadia = explode(",", $DatosTipo["CamposInvitadoPasadia"]);
                        if (count($array_campos_form_pasadia) > 0) {
                            foreach ($array_campos_form_pasadia as $key => $value) {
                                $camposform["Campo"] = $value;
                                array_push($response_campos_form_pasadia, $camposform);
                            }
                        }

                        $configuracion[CamposInvitadoPasadia] = $response_campos_form_pasadia;

                    endif;

                    array_push($responde_tipo_reserva, $InfoTipo);
                endwhile;

                $configuracion["TiposReserva"] = $responde_tipo_reserva;




                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "Hotel no está activo";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function valida_cierre($IDClub, $FechaInicio, $IDHabitacion = "", $IDTorre = "", $IDTipoHabitacion = "", $IDSocio = "")
    {
        $dbo = &SIMDB::get();
        $mensaje_cierre = "";
        if (empty($IDHabitacion) && empty($IDTorre) && empty($IDTipoHabitacion)) {
            $sql_servicio = "SELECT * FROM  HotelCierre WHERE IDClub = '" . $IDClub . "' and FechaInicio <= '" . $FechaInicio . "' and FechaFin >= '" . $FechaInicio . "' and IDHabitacion = '' and IDTipoHabitacion = '' and IDTorre='' Limit 1";
        } else {
            $sql_servicio = "SELECT * FROM  HotelCierre WHERE IDClub = '" . $IDClub . "' and FechaInicio <= '" . $FechaInicio . "' and FechaFin >= '" . $FechaInicio . "' and IDHabitacion like '%|" . $IDHabitacion . "|%'
				UNION SELECT * FROM  HotelCierre WHERE IDClub = '" . $IDClub . "' and FechaInicio <= '" . $FechaInicio . "' and FechaFin >= '" . $FechaInicio . "' and IDTipoHabitacion like '%|" . $IDTipoHabitacion . "|%'
				UNION SELECT * FROM  HotelCierre WHERE IDClub = '" . $IDClub . "' and FechaInicio <= '" . $FechaInicio . "' and FechaFin >= '" . $FechaInicio . "' and IDTorre like '%|" . $IDTorre . "|%'
				LIMIT 1";
        }
        $r_cierre = $dbo->query($sql_servicio);
        if ($dbo->rows($r_cierre) > 0) {
            $row_cierre = $dbo->fetchArray($r_cierre);
            $mensaje_cierre = $row_cierre["Descripcion"];
        }
        if ($IDSocio == 159622)
            $mensaje_cierre = "";

        return $mensaje_cierre;
    }

    public function get_valida_fecha($IDClub, $FechaInicio, $FechaFin, $Admin = "", $IDSocio)
    {

        $dbo = &SIMDB::get();

        //SACAMOS LOS ID QUE USAREMOS EN LAS CONSULTAS
        $IDSocio = SIMNet::req("IDSocio");
        $IDClub = SIMNet::req("IDClub");
        //VERIFICAMOS SI EL HOTEL TIENE PERMITIDO LAS RESERVAS DE HOTEL A MAYORES DE 21
        $Hotel_reserva = "SELECT * FROM ConfiguracionHotel Where IDClub=$IDClub";
        $ReservarHotel = $dbo->query($Hotel_reserva);

        $rows = array();
        while ($row = mysqli_fetch_array($ReservarHotel))
            $rows[] = $row;
        foreach ($rows as $row) {
            $permite =  $row["EdadReservaHotel"];
        }

        //VERIFICAMOS LA FECHA DE NACIMIENTO DEL SOCIO
        $Datos = "SELECT * FROM Socio Where IDSocio=$IDSocio";
        $Socio = $dbo->query($Datos);

        $rows1 = array();
        while ($row1 = mysqli_fetch_array($Socio))
            $rows1[] = $row1;
        foreach ($rows1 as $row1) {
            $nac =  $row1["FechaNacimiento"];
        }

        //HACEMOS LOS CALCULOS A VER SI ES O NO MAYOR DE EDAD

        if ($nac == "0000-00-00") {
            $nac_minimo = 0;
            $nac = 1;
        } else {
            $date2 = (date("Y-m-d"));
            $nac_minimo = date("Y-m-d", strtotime("-$permite year", strtotime($date2)));
        }


        if ($permite != '0' and $nac_minimo < $nac) { //mi club prueba

            $respuesta["message"] = "Lo sentimos,no tienes edad para reservar ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $servicio_abierto = "S";



        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        if (strtotime($FechaInicio) >= strtotime($FechaFin)) {
            $respuesta["message"] = "Las fecha final debe ser mayor a la fecha inicial, por favor verifique";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if ($IDClub == 70) {
            $dia_sem_inicio = date("N", strtotime($FechaInicio));
            $dia_sem_fin = date("N", strtotime($FechaFin));

            $IDFestivo = (int) $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $FechaInicio . "' and (IDPais = 1 || IDPais = 10) ");

            //if( ($dia_sem==1 && $IDFestivo<=0) || $dia_sem==2 || $dia_sem==7 ){
            //if( (($dia_sem==1 || $dia_sem==2)  && $IDFestivo<=0) ){
            //if( $dia_sem_inicio == 1 || $dia_sem_inicio == 2  || $dia_sem_fin == 2) {
            if (($dia_sem_inicio == 1 || $dia_sem_fin == 2) && $IDFestivo <= 0) {
                $servicio_abierto = "N";
                $message = "No hay servicio en el dia seleccionado";
            }

            if ($dia_sem_inicio == 1 && $FechaInicio == "2021-07-19") {
                $servicio_abierto = "S";
                $message = "";
            }

            //Verifico si la fecha inicial o final es Domingo valido si es festivo el siguiente dia
            if ($dia_sem_inicio == 7) {
                $siguiente_dia = date("Y-m-d", strtotime($FechaInicio . "+ 1 days"));
                $IDFestivo = (int) $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $siguiente_dia . "' and (IDPais = 1 || IDPais = 10) ");
                if ($IDFestivo <= 0) {
                    $servicio_abierto = "N";
                    $message = "Los lunes no hay servicio, por favor seleccione otra fecha inicial";
                }
            } elseif ($dia_sem_fin == 1) {
                $IDFestivo = (int) $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $FechaFin . "' and (IDPais = 1 || IDPais = 10) ");
                if ($IDFestivo <= 0) {
                    $servicio_abierto = "N";
                    $message = "Solo los lunes festivos hay servicio, por favor seleccione otra fecha final";
                }
            }
        }

        //Verifico por cada fecha que no se encuentre en cierre total

        $ArrayDiasEntreFechas = SIMWebServiceHotel::arrayperiodofechas($FechaInicio, $FechaFin);
        foreach ($ArrayDiasEntreFechas as $i) {
            $cerrado = self::valida_cierre($IDClub, $i, "", "", "", $IDSocio);
            if (!empty($cerrado)) {
                $respuesta["message"] = "CERRADO " . $cerrado;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        $response = array();
        $FechaActual = date("Y-m-d");

        //Validacion especial anapoima semana receso solo se puede toda la semana el dia  x
        $hoy = date("Y-m-d");
        if ($IDClub == 46 && $hoy == "2019-09-19") {
            if ($FechaInicio >= "2019-10-04" && $FechaFin <= "2019-10-15") {
                if ($FechaInicio != "2019-10-07" || $FechaFin != "2019-10-14") {
                    $respuesta["message"] = "Lo sentimos hoy solo es posible reservar la semana completa de receso, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }
        }

        $Parametros = $dbo->query("SELECT * FROM ConfiguracionHotel WHERE IDClub = '" . $IDClub . "'");
        $ArrayParametros = $dbo->fetchArray($Parametros);

        //Las reservas solo después de las 8 am
        $fecha_hora_actual = date("Y-m-d H:i:s");
        $fecha_actual = date("Y-m-d");
        $dia_semana = date("w");
        $fecha_hora_permitida = date("Y");
        if ($dia_semana <= "2") : //Martes
            if ($dia_semana < "2") :
                //calculo el proximo martes
                $sig_martes = strtotime('next tuesday');
                $next_martes = date("Y-m-d 08:00:00", $sig_martes);
            else :
                //hoy
                $next_martes = date("Y-m-d 08:00:00");
            endif;

            if (strtotime($fecha_hora_actual) <= strtotime($next_martes)) :
            //$respuesta["message"] = "La reservas se abrirán desde el martes a las 8 am ";
            //$respuesta["success"] = false;
            //$respuesta["response"] = NULL;
            //return $respuesta;
            endif;
        endif;

        if (!empty($FechaInicio) && !empty($FechaFin)) {




            //VALIDACION PARA BLOQUEO DE TERMPORADA ALTA 

            //EJEMPLO TEMPORADA ALTA (2023-03-10 AL 2023-03-15) , NO DEJA RESERVAR SI ENTRE EL RANGO DE LA SOLICITUD ESTA ALGUNO DE ESOS DIAS

         /*   $TemporadaAlta_bloqueo = $dbo->query("SELECT * FROM TemporadaAlta WHERE  (FechaInicio BETWEEN '$FechaInicio' AND '$FechaFin') OR (FechaFin BETWEEN '$FechaInicio' AND '$FechaFin') AND IDClub = '" . $IDClub . "'"); */

 $TemporadaAlta_bloqueo = $dbo->query("SELECT * FROM TemporadaAlta WHERE FechaInicio <= '$FechaFin' AND FechaFin >= '$FechaInicio' and IDClub = '$IDClub' LIMIT 1");
 
            while ($Datos_bloqueo = $dbo->fetchArray($TemporadaAlta_bloqueo)) :
                $liberacion = $Datos_bloqueo["FechaReserva"];
                if ($liberacion > date("Y-m-d")) :
                    $respuesta["message"] = "Lo sentimos, ha seleccionado fechas no disponibles en la actualidad, por favor elija otro rango de fechas";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            endwhile;



            // verifico si hay evento programado en el dia actual
            $EventoDia = $dbo->query("SELECT * FROM actividad WHERE Inicio = '$FechaInicio' or Fin = '$FechaInicio'");
            $ArrayEvento = $dbo->fetchArray($EventoDia);
            $iconoevento = "";
            if (count($ArrayEvento) > 1) {
                $datos["Evento"] = $ArrayEvento["Nombre"];
            }

            $CortoPlazo = self::verifica_temporada_corto_plazo($FechaInicio, $IDClub);

            if (!empty($CortoPlazo) && $IDClub == 194) :
                $TemporadaActual = "CortoPlazo";


                $DiasCorto =  date_diff(date_create($FechaInicio), date_create($fecha_actual));
                $DiasAntelacion = $DiasCorto->format('%d');
                $FechaAntelacion = $dbo->query("SELECT '$FechaInicio' - INTERVAL $DiasAntelacion DAY ;");
                $FechaAntelacion = $dbo->fetchArray($FechaAntelacion);
                $FechaAntelacion = $FechaAntelacion[0];
                // HAY QUE VERIFICAR ESTA PARTE, POR AHORA LO DEJAMOS ASI.
                $FechaAntelacion = "2022-01-01";

                //FIN


                $liberareserva = date("Y-m-d", strtotime($FechaInicio . "- 1 month"));
            else :
                $IDTemporadaAlta = self::verifica_temporada($FechaInicio, $FechaFin, $IDClub);

                if ($IDTemporadaAlta > 0) :
                    $DiasAntelacion = $dbo->getFields("ConfiguracionHotel", "DiasTAlta", "IDClub = '" . $IDClub . "'");
                    $TemporadaActual = "Alta";
                    //datos de temporada alta
                    $TemporadaAlta = $dbo->query("SELECT * FROM TemporadaAlta WHERE IDTemporadaAlta = '" . $IDTemporadaAlta . "'");
                    $ArrayTemporadaAlta = $dbo->fetchArray($TemporadaAlta);
                    $FechaAntelacion = $ArrayTemporadaAlta["FechaReserva"];
                    $liberareserva = $ArrayTemporadaAlta["FechaReserva"];


                else :
                    $DiasAntelacion = $dbo->getFields("ConfiguracionHotel", "DiasTBaja", "IDClub = '" . $IDClub . "'");
                    $TemporadaActual = "Baja";
                    $FechaAntelacion = $dbo->query("SELECT '$FechaInicio' - INTERVAL $DiasAntelacion DAY ;");
                    $FechaAntelacion = $dbo->fetchArray($FechaAntelacion);
                    $FechaAntelacion = $FechaAntelacion[0];
                    $liberareserva = $FechaAntelacion[0];
                    if ($IDClub == 194) :
                        $FechaAntelacion = "2022-01-01";
                    endif;

                endif;

            endif;

            if ($IDClub == 200) :

                $DiaInicio = date("N", strtotime($FechaInicio));

                if ($TemporadaActual != 'Alta') :
                    $Hoy = date("Y-m-d");
                    $OchoDias = date("Y-m-d", strtotime('+30 days', strtotime($Hoy)));

                    if ($FechaInicio > $OchoDias && ($DiaInicio == '0' || $DiaInicio == '6')) :
                        $respuesta["message"] = "La fecha de inicio no puede ser mayor a 30 dias calendario";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;

                // SI TOMA LA RESERVA EL SABADO Y ES PUENTE DEBE SER HASTA EL LUNES
                if ($DiaInicio == '6') :
                    //   $proximo_Lunes = strtotime('next Monday');
                    // $fecha_prox_lunes = date('Y-m-d', $proximo_Lunes);
                    $fecha_prox_lunes = date("Y-m-d", strtotime($FechaInicio . "+ 2 day"));

                    $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $fecha_prox_lunes . "' and IDPais = 1");
                    if ($IDFestivo > 0) :
                        // COMPROBAR QUE LA FECHA FINAL SEA LA DEL LUNES, SI NO ES ASÍ NO SE PUEDE TOMAR
                        if ($FechaFin != $fecha_prox_lunes) :
                            $respuesta["message"] = "La reserva no se puede efectuar. Como es fin de semana festivo debe ser hasta el lunes festivo";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                endif;
            endif;


            //VALIDACION CLUB NAUTICO, RESERVAS ABIERTAS DESDE EL LUNES PARA FINES DE SEMANA 
            if ($IDClub == 124) :

                $DiaInicio = date("N", strtotime($FechaInicio));

                if ($TemporadaActual != 'Alta') :
                    $Hoy = date("Y-m-d");
                    $semana = date("Y-m-d", strtotime('+7 days', strtotime($Hoy)));

                    if ($FechaInicio > $semana) :
                        $respuesta["message"] = "Lo sentimos, solo se permite reservar para este fin de semana.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    // EVITAMOS QUE HAGAN RESERVAS PARA DIAS DE SEMANA
                    if ($DiaInicio == '1' || $DiaInicio == '2' || $DiaInicio == '3' || $DiaInicio == '4') :

                        $respuesta["message"] = "Lo sentimos, las reservas estan disponibles únicamente para los fines de semana";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    endif;

                else :
                    $Hoy = date("Y-m-d");
                    $mes = date("Y-m-d", strtotime('+30 days', strtotime($Hoy)));

                    if ($FechaInicio > $mes) :
                        $respuesta["message"] = "Lo sentimos, para reservar en esta fecha debe ser como minimo un mes de anticipación.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;


                endif;


            endif;


            if ($FechaActual <> $FechaAntelacion) {

                /*  En cualquier caso, que se quiera adicionar noches de estadía durante una 
                    misma estadía estas noches tendrán que ser la misma cantidad de noches 
                    que reservaron en la reserva inicial.  */

                if ($IDClub == 194) :
                    $DiasCorto =  date_diff(date_create($FechaInicio), date_create($FechaFin));
                    $DiasActuales = $DiasCorto->format('%d');

                    $DiaDespues = date("Y-m-d", strtotime('+1 days', strtotime($FechaInicio)));

                    // BUSCAMOS LA ULTIMA RESERVA DEL SOCIO
                    $SQLReserva = "SELECT * FROM ReservaHotel WHERE IDSocio = $IDSocio ORDER BY IDReserva DESC LIMIT 1";
                    $QRYReserva = $dbo->query($SQLReserva);
                    $DatosReserva = $dbo->fetchArray($QRYReserva);

                    $FechaInicioAntigua = $DatosReserva[FechaInicio];
                    $FechaFinAntigua = $DatosReserva[FechaFin];

                    if ($FechaInicio == $FechaFinAntigua || $FechaInicio == $DiaDespues) :

                        $DiasAntes =  date_diff(date_create($FechaInicioAntigua), date_create($FechaFinAntigua));
                        $DiasAntes = $DiasAntes->format('%d');

                        $sql_reserva_socio = "SELECT * FROM ReservaHotel WHERE FechaFin='" . $FechaInicio . "' and IDSocio = '" . $IDSocio . "'  and  IDClub = '" . $IDClub . "'";
                        $r_reserva_socio = $dbo->query($sql_reserva_socio);
                        if ($dbo->rows($r_reserva_socio) == 0) {
                        } else {
                            $DiasActuales =  $DiasAntes;
                        }
                        if ($DiasAntes <= 7) :
                            if ($DiasAntes != $DiasActuales) :
                                $respuesta["message"] = "Si desea tomar una reserva seguida de la que ya esta debe ser de la misma cantidad de días que la actual";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;
                        else :
                            $respuesta["message"] = "Ha tomado mas de 7 días en la reserva pasada y como solo se puede cantidad igual en la siguiente si es inmediata no puede hacer la reserva";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                    if (empty($IDSocio)) {
                        $DosDiasAntes = date("Y-m-d", strtotime('+0 days', strtotime(date("Y-m-d"))));
                    } else {
                        $DosDiasAntes = date("Y-m-d", strtotime('+4 days', strtotime(date("Y-m-d"))));
                    }



                    if ($FechaInicio < $DosDiasAntes) :
                        $respuesta["message"] = "La reserva debe ser tomada como minimo con 4 dias de anticipación";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                endif;


                $response_habitacion = array();
                $response_torre = array();
                $habitaciones_disponibles = 0;
                $fechaBusqueda = $FechaInicio;
                $contFechas = 0;
                $fin = false;

                while (!$fin) :

                    $Torres = $dbo->query("SELECT * FROM Torre WHERE IDClub = '" . $IDClub . "'");

                    $DiasEstadia =  date_diff(date_create($FechaInicio), date_create($FechaFin));
                    $DiasEstadia = $DiasEstadia->format('%d');

                    while ($r = $dbo->fetchArray($Torres)) {

                        $NombreTorre = utf8_decode($r["Nombre"]);

                        /*
                        if( $Admin!="S" && $IDSocio!="159622" && $IDClub==70){
                        // para san andres antes de publicar si muestra en el admin la habitacion
                        $condicion_especial= " and Habitacion.IDHabitacion not in ( 804 )";
                        }
                        */

                        /*
                        $qry_habitacion="
                        SELECT Habitacion.*
                        FROM
                        Habitacion
                        WHERE IDClub = '".$IDClub."' and Habitacion.Publicar='S' and Habitacion.IDHabitacion NOT IN(
                        SELECT ReservaHotel.IDHabitacion
                        FROM ReservaHotel WHERE IDClub = '".$IDClub."' and
                        (
                        (ReservaHotel.FechaInicio <= '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')    OR
                        (ReservaHotel.FechaInicio < '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')    OR
                        (ReservaHotel.FechaInicio <= '".$FechaFin."' AND ReservaHotel.FechaFin > '".$FechaFin."')                OR
                        (ReservaHotel.FechaInicio < '".$FechaFin."' AND ReservaHotel.FechaFin >= '".$FechaFin."')                OR
                        (ReservaHotel.FechaInicio <= '".$FechaFin."' AND ReservaHotel.FechaFin >= '".$FechaFin."')            OR
                        ( '".$FechaInicio."' <= ReservaHotel.FechaInicio AND '".$FechaFin."' > ReservaHotel.FechaInicio )    OR
                        ( '".$FechaInicio."' < ReservaHotel.FechaInicio AND '".$FechaFin."' >= ReservaHotel.FechaInicio )    OR
                        ( '".$FechaInicio."' < ReservaHotel.FechaFin AND '".$FechaFin."' > ReservaHotel.FechaFin )    OR
                        ( '".$FechaInicio."' < ReservaHotel.FechaFin AND '".$FechaFin."' >= ReservaHotel.FechaFin )
                        )
                        AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
                        )

                        AND  Habitacion.IDTorre = '".$r["IDTorre"]."'
                        ORDER BY Habitacion.NumeroHabitacion ASC";
                        */

                        $qry_habitacion = "
                                            SELECT Habitacion.*
                                            FROM
                                            Habitacion
                                            WHERE IDClub = '" . $IDClub . "' and Habitacion.Publicar='S' and Habitacion.IDHabitacion NOT IN(
                                                        SELECT ReservaHotel.IDHabitacion
                                                        FROM ReservaHotel WHERE IDClub = '" . $IDClub . "' and
                                                        (
                                                                (ReservaHotel.FechaInicio <= '" . $fechaBusqueda . "' AND ReservaHotel.FechaFin > '" . $fechaBusqueda . "')	OR
                                                                (ReservaHotel.FechaInicio < '" . $fechaBusqueda . "' AND ReservaHotel.FechaFin > '" . $fechaBusqueda . "')	OR
                                                                (ReservaHotel.FechaInicio < '" . $FechaFin . "' AND ReservaHotel.FechaFin > '" . $FechaFin . "')				OR
                                                                (ReservaHotel.FechaInicio < '" . $FechaFin . "' AND ReservaHotel.FechaFin > '" . $FechaFin . "')				OR
                                                                (ReservaHotel.FechaInicio < '" . $FechaFin . "' AND ReservaHotel.FechaFin > '" . $FechaFin . "')			OR
                                                                ( '" . $fechaBusqueda . "' <= ReservaHotel.FechaInicio AND '" . $FechaFin . "' > ReservaHotel.FechaInicio )	OR
                                                                ( '" . $fechaBusqueda . "' < ReservaHotel.FechaInicio AND '" . $FechaFin . "' > ReservaHotel.FechaInicio )	OR
                                                                ( '" . $fechaBusqueda . "' < ReservaHotel.FechaFin AND '" . $FechaFin . "' > ReservaHotel.FechaFin )	OR
                                                                ( '" . $fechaBusqueda . "' < ReservaHotel.FechaFin AND '" . $FechaFin . "' > ReservaHotel.FechaFin )
                                                        )
                                                        AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
                                            )

                                            AND  Habitacion.IDTorre = '$r[IDTorre]' ";
                        // ORDER BY Habitacion.NumeroHabitacion ASC

                        /*
                        $qry_habitacion="
                        SELECT Habitacion.*
                        FROM
                        Habitacion
                        WHERE IDClub = '".$IDClub."' and Habitacion.Publicar='S' and Habitacion.IDHabitacion NOT IN(
                        SELECT ReservaHotel.IDHabitacion
                        FROM ReservaHotel WHERE IDClub = '".$IDClub."' and
                        (
                        (ReservaHotel.FechaInicio <= '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')    OR
                        (ReservaHotel.FechaInicio < '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')    OR
                        (ReservaHotel.FechaInicio <= '".$FechaFin."' AND ReservaHotel.FechaFin > '".$FechaFin."')                OR
                        (ReservaHotel.FechaInicio < '".$FechaFin."' AND ReservaHotel.FechaFin >= '".$FechaFin."')                OR
                        (ReservaHotel.FechaInicio <= '".$FechaFin."' AND ReservaHotel.FechaFin >= '".$FechaFin."')

                        )
                        AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
                        )

                        AND  Habitacion.IDTorre = '".$r["IDTorre"]."'
                        ORDER BY Habitacion.NumeroHabitacion ASC";
                        */

                        /*
                        $qry_habitacion="
                        SELECT Habitacion.*
                        FROM
                        Habitacion
                        WHERE IDClub = '".$IDClub."' and Habitacion.Publicar='S' and Habitacion.IDHabitacion NOT IN(
                        SELECT ReservaHotel.IDHabitacion
                        FROM ReservaHotel WHERE IDClub = '".$IDClub."' and
                        (
                        (ReservaHotel.FechaInicio <= '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')    OR
                        (ReservaHotel.FechaInicio < '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')

                        )
                        AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
                        )

                        AND  Habitacion.IDTorre = '".$r["IDTorre"]."'
                        ORDER BY Habitacion.NumeroHabitacion ASC";
                        */

                        $Habitacion = $dbo->query($qry_habitacion);

                        // echo $dbo->rows($Habitacion);

                        $response_habitacion = array();
                        while ($r_habitacion = $dbo->fetchArray($Habitacion)) {

                            $mostrar = "S";
                            $habitaciones_disponibles = 1;
                            //Verifico que no tenga algun cierre este tipo de habitacion
                            $ArrayDiasEntreFechas = SIMWebServiceHotel::arrayperiodofechas($fechaBusqueda, $FechaFin);
                            foreach ($ArrayDiasEntreFechas as $i) {
                                $cerrado = self::valida_cierre($IDClub, $i, $r_habitacion["IDHabitacion"], $r_habitacion["IDTorre"], $r_habitacion["IDTipoHabitacion"], $IDSocio);

                                if (!empty($cerrado)) {

                                    $mostrar = "N";
                                    $habitaciones_disponibles = 0;
                                    //if()

                                }
                            }

                            $habitacion["IDHabitacion"] = $r_habitacion["IDHabitacion"];
                            $habitacion["IDTorre"] = $r["IDTorre"];
                            $habitacion["NombreHabitacion"] = $r_habitacion["NombreHabitacion"] .  " " . $dbo->getFields("TipoHabitacion", "Nombre", "IDTipoHabitacion =" . $r_habitacion["IDTipoHabitacion"]);

                            $NumeroHab = $r_habitacion["NumeroHabitacion"];



                            if ($NumeroHab == 0)
                                $NumeroHab = "";

                            $habitacion["NumeroHabitacion"] = $NumeroHab;
                            $habitacion["CapacidadHabitacion"] = $dbo->getFields("TipoHabitacion", "Capacidad", "IDTipoHabitacion =" . $r_habitacion["IDTipoHabitacion"]);
                            $habitacion["AdicionalHabitacion"] = $dbo->getFields("TipoHabitacion", "Adicional", "IDTipoHabitacion =" . $r_habitacion["IDTipoHabitacion"]);
                            //$habitacion["DescripcionHabitacion"] = $r_habitacion["Descripcion"];
                            $cuerpo_descripcion_habitacion = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r_habitacion["Descripcion"]);
                            $habitacion["DescripcionHabitacion"] = $cuerpo_descripcion_habitacion;


                            if ($IDClub == 194) :
                                // BUSCAMOS EL SOCIO PUEDA VER LA HABITACION
                                if (empty($IDSocio)) {
                                    $condicion = "";
                                } else {
                                    $condicion = "IDSocio = '$IDSocio' AND";
                                }
                                $SQLFraccion = "SELECT IDSocioHabitacion FROM SocioHabitacion WHERE  $condicion  IDHabitacion = $r_habitacion[IDHabitacion] AND Estadias > 0 AND NumeroFracciones > 0 AND (FechaInicioFraccion <= '$FechaInicio' AND FechaFinFraccion >= '$FechaFin') AND Noches >= '$DiasEstadia'";
                                $QRYFraccion = $dbo->query($SQLFraccion);
                                if ($dbo->rows($QRYFraccion) <= 0) :
                                    $mostrar = "N";
                                endif;
                            endif;

                            if ($mostrar == "S") {
                                array_push($response_habitacion, $habitacion);
                            }
                        }

                        $arraytorre["IDTorre"] = $r["IDTorre"];
                        $arraytorre["NombreTorre"] = utf8_encode($NombreTorre);
                        $arraytorre["HabitacionTorre"] = $response_habitacion;
                        array_push($response_torre, $arraytorre);
                    }

                    if (count($habitacion) > 0) {

                        //si la busqueda arroja resultados en las fechas enviadas por el usuario se muestran las habitaciones, en caso contrario se envia un mensaje en las fechas disponibles
                        if ($fechaBusqueda == $FechaInicio) :

                            $datos["IDTemporadaAlta"] = $IDTemporadaAlta;
                            $datos["Temporada"] = $TemporadaActual;
                            $datos["FechaAntelacion"] = $FechaAntelacion;
                            $datos["MaximoHTAlta"] = $ArrayParametros["MaximoHTAlta"];

                            if ($servicio_abierto == "S") {
                                $datos["PermiteListaEspera"] = $datos_club["PermiteListaEsperaHotel"];
                                $datos["Habitaciones"] = $response_torre;
                            } else {
                                $datos["PermiteListaEspera"] = "N";
                                $datos["Habitaciones"] = array();
                            }

                        else :

                            $datos["PermiteListaEspera"] = $datos_club["PermiteListaEsperaHotel"];
                            $datos["Habitaciones"] = array();
                            $datos["MensajeListaEspera"] = "Lo sentimos no hay habitaciones disponibles en sus fechas requeridas,\npero existe disponibilidad para las fechas: $fechaBusqueda a $FechaFin o\n ¿Desea estar en la lista de espera en las fechas seleccionadas?";

                        endif;

                        $fin = true;
                    } else {

                        //si no hay habitaciones en las fechas dadas, aumenta en un dia el valor de la fecha para volver a hacer la busqueda hasta que la fecha de busqueda sea igual a la fecha final
                        if ($cont <= 2 && date("Y-m-d", strtotime($fechaBusqueda . "+ 1 days")) < $FechaFin && $Admin == "N") :
                            $fechaBusqueda = date("Y-m-d", strtotime($fechaBusqueda . "+ 1 days"));
                            $cont++;
                        else :

                            $datos["PermiteListaEspera"] = $datos_club["PermiteListaEsperaHotel"];
                            $datos["Habitaciones"] = array();
                            $datos["MensajeListaEspera"] = "Lo sentimos no hay habitaciones disponibles en sus fechas requeridas. \n ¿Desea estar en la lista de espera en las fechas seleccionadas?";

                            $fin = true;

                        endif;
                    }

                endwhile;

                array_push($response, $datos);

                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

                return $respuesta;
            } else {
                if ($TemporadaActual == "Alta") {
                    $respuesta["message"] = "La reserva no se puede efectuar. Se podra realizar desde: " . SIMUtil::tiempo($FechaAntelacion);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } else {
                    $fecha_reserva = date("Y-m-d", strtotime($FechaInicio . "-  $DiasAntelacion  days"));
                    $respuesta["message"] = "La reserva no se puede efectuar. Se podra realizar desde: " .  $fecha_reserva  . "  ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            }
        } else {
            $respuesta["message"] = "1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } // fin function

    public function set_reserva($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, $Admin = "", $IDusuarioCrea = "", $NombreDuenoReserva = "", $DocumentoDuenoReserva = "", $EmailDuenoReserva = "", $Observaciones = "", $Campos = "")
    {


        $dbo = &SIMDB::get();
        $response = array();
        $hoy = date("Y-m-d");
        $ConfiguracionHotel = $dbo->fetchAll("ConfiguracionHotel", " IDClub = '" . $IDClub . "' ", "array");

        //San Andres habilitar para pruebas el año x
        /*
        if(empty($Admin)){
        if($IDClub==70 && ($IDSocio!=159622 || $IDSocio!=158750 ) && ( substr($FechaInicio,0,4)==2022 || substr($FechaFin,0,4)=="2022" )){
        $respuesta["message"] = "El año 2022  aun no esta disponible para reservas.";
        $respuesta["success"] = false;
        $respuesta["response"] = NULL;
        return $respuesta;
        }
        }
        */

        // echo $ConfiguracionHotel["NumeroReservasActivas"];


        if ($ConfiguracionHotel["NumeroReservasActivas"] > 0) {
            $contadorReservasActivas = 0;
            $sql_conteo_reservas = "SELECT * FROM  ReservaHotel WHERE IDSocio='$IDSocio' AND IDClub='$IDClub' AND (FechaInicio<='$hoy' AND FechaFin >='$hoy' AND '$hoy' <= FechaFin)";
            // echo $sql_conteo_reservas;
            $r_conteo_reservas = $dbo->query($sql_conteo_reservas);
            while ($ConteoReservas = $dbo->fetchArray($r_conteo_reservas)) {
                $contadorReservasActivas += 1;
            }
            if ($contadorReservasActivas >= $ConfiguracionHotel["NumeroReservasActivas"]) {
                $respuesta["message"] = "Lo sentimos ya tiene " . $contadorReservasActivas . " reservas activas" . " y solo se permiten " . $ConfiguracionHotel["NumeroReservasActivas"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        //RESERVAS MAXIMAS POR DIAS
        if ($ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"] > 0) {
            $sql_conteo_reservas = "SELECT COUNT(IDReserva) AS Conteo FROM  ReservaHotel WHERE IDSocio='$IDSocio' AND IDClub='$IDClub' AND FechaInicio='$FechaInicio'";
            $r_conteo_reservas = $dbo->query($sql_conteo_reservas);
            $ConteoReservas = $dbo->fetchArray($r_conteo_reservas);

            if ($ConteoReservas["Conteo"] >= $ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"]) {
                $respuesta["message"] = "Lo sentimos solo se permite hacer " . $ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"] . " reserva el mismo dia.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }


        //Validacion especial anapoima semana receso solo se puede toda la semana el dia  x

        if ($IDClub == 46) {
            //Verifico que no tenga mas reservas
            $sql_reserva_h = "SELECT * FROM ReservaHotel WHERE FechaInicio>='2019-10-07' and FechaFin <= '2019-10-14' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
            $r_reserva_h = $dbo->query($sql_reserva_h);
            if ($dbo->rows($r_reserva_h) > 0) {
                $respuesta["message"] = "Lo sentimos ya tiene una reserva para esa semana";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $PermiteReservar = $datos_socio["PermiteReservarHotel"];
        if ($PermiteReservar == "N") {
            $respuesta["message"] = "No tiene autorizacion para realizar reservas de hotel";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            exit;
        }


        $Parametros = $dbo->query("SELECT * FROM ConfiguracionHotel WHERE IDClub = '" . $IDClub . "'");
        $ArrayParametro = $dbo->fetchArray($Parametros);

        if (!empty($IDSocio) && !empty($CabezaReserva) && ((!empty($Temporada) && !empty($FechaInicio) && !empty($FechaFin) && !empty($IDHabitacion)) || !empty($TipoReserva))) {

            $SQLArrayReserva = $dbo->query("SELECT * FROM ReservaHotel WHERE DATE_ADD( FechaReserva, INTERVAL 30 MINUTE ) < NOW() AND FechaReserva > '2012-07-18 00:00:01' AND Estado = 'pendiente'");
            while ($ArrayReservaDel = $dbo->fetchArray($SQLArrayReserva)) {
                //$id = $dbo->insert( $ArrayReservaDel , "reservalog" , "IDReservaLog" );
                $qry_elimina = "INSERT IGNORE INTO ReservaHotelEliminada (
                    IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
                    Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd)
                    Select
                    IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
                    Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,'UsuarioApp','" . date("Y-m-d H:i:s") . "'
                    From ReservaHotel
                    Where IDReserva  = '" . $ArrayReservaDel["IDReserva"] . "'";
                $sql_copia_reserva = $dbo->query($qry_elimina);
                //$dbo->query("DELETE FROM ReservaHotel WHERE IDReserva = '" . $ArrayReservaDel["IDReserva"] . "'");
            }



            //verificamos si aplica la promocion
            $Promocion = $dbo->query("
									SELECT *
									FROM Promocion WHERE IDClub = '" . $IDClub . "'
									(FechaInicio <= '" . $FechaInicio . "' AND FechaFin > '" . $FechaInicio . "')
									OR
									(FechaInicio < '" . $FechaInicio . "' AND FechaFin >= '" . $FechaInicio . "')
									OR
									(FechaInicio <= '" . $FechaFin . "' AND FechaFin > '" . $FechaFin . "')
									OR
									(FechaInicio < '" . $FechaFin . "' AND FechaFin >= '" . $FechaFin . "')
									OR
									( '" . $FechaInicio . "' <= FechaInicio AND '" . $FechaFin . "' > FechaInicio )
									OR
									( '" . $FechaInicio . "' < FechaInicio AND '" . $FechaFin . "' >= FechaInicio )
									OR
									( '" . $FechaInicio . "' <= FechaFin AND '" . $FechaFin . "' > FechaFin )
									OR
									( '" . $FechaInicio . "' < FechaFin AND '" . $FechaFin . "' >= FechaFin )
								");
            $ArrayPromocion = $dbo->fetchArray($Promocion);
            $IDPromocion = $ArrayPromocion["IDPromocion"];

            //saco el total de dias dependiendo el periodo de fechas
            $DiasPeriodoTarifa = SIMWebServiceHotel::diasperiodotarifa($FechaInicio, $FechaFin);

            //se verifica el adicional en blanco
            if ($Adicional == "") {
                $Adicional = N;
            }

            //datos de la habitacion
            $Habitacion = $dbo->query("SELECT * FROM Habitacion WHERE IDHabitacion = " . $IDHabitacion);
            $ArrayHabitacion = $dbo->fetchArray($Habitacion);

            //datos del tipo de habitacion
            $TipoHabitacion = $dbo->query("SELECT * FROM TipoHabitacion WHERE IDTipoHabitacion = " . $ArrayHabitacion["IDTipoHabitacion"]);
            $ArrayTipoHabitacion = $dbo->fetchArray($TipoHabitacion);

            //datos del tipo de habitacion
            $TipoHabitacion = $dbo->query("SELECT * FROM TipoHabitacion WHERE IDTipoHabitacion = " . $ArrayHabitacion["IDTipoHabitacion"]);
            $ArrayTipoHabitacion = $dbo->fetchArray($TipoHabitacion);

            ///Verifico de nuevo que alguien no haya tomado la habitacion para evitar duplicados
            $FechaInicioValida = $FechaInicio;
            $FechaFinValida = $FechaFin;
            $IDHabitacionValida = $IDHabitacion;
            $suma_rand = rand(0, 2);
            $rand_seg = rand(1, 1) + $suma_rand;
            sleep($rand_seg);

            $sql_valida = "
				SELECT Habitacion.IDHabitacion
				FROM
				Habitacion
				WHERE Habitacion.IDHabitacion NOT IN(
					SELECT ReservaHotel.IDHabitacion
					FROM ReservaHotel WHERE IDClub = '" . $IDClub . "' and
					(
							(ReservaHotel.FechaInicio <= '" . $FechaInicioValida . "' AND ReservaHotel.FechaFin > '" . $FechaInicioValida . "')	OR
							(ReservaHotel.FechaInicio < '" . $FechaInicioValida . "' AND ReservaHotel.FechaFin > '" . $FechaInicioValida . "')	OR
							(ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')				OR
							(ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')				OR
							(ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')			OR
							( '" . $FechaInicioValida . "' <= ReservaHotel.FechaInicio AND '" . $FechaFinValida . "' > ReservaHotel.FechaInicio )	OR
							( '" . $FechaInicioValida . "' < ReservaHotel.FechaInicio AND '" . $FechaFinValida . "' > ReservaHotel.FechaInicio )	OR
							( '" . $FechaInicioValida . "' < ReservaHotel.FechaFin AND '" . $FechaFinValida . "' > ReservaHotel.FechaFin )	OR
							( '" . $FechaInicioValida . "' < ReservaHotel.FechaFin AND '" . $FechaFinValida . "' > ReservaHotel.FechaFin )
					)
					AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
				)
				AND IDHabitacion='" . $IDHabitacionValida . "'
										";

            $Habitacion = $dbo->query($sql_valida);

            if ($dbo->rows($Habitacion) <= 0) {
                $respuesta["message"] = "La reserva ya fue tomada por otra persona por favor intente con otra habitacion";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            //Fin valida si esta duplicada

            if ($Admin == "S") {
                $estado_reserva = "enfirme";
            } else {
                $estado_reserva = "pendiente";
            }

            if ($IDClub == 46) {
                $estado_reserva = "enfirme";
            }

            if (empty($IDusuarioCrea)) {
                $creada_por = "Socio";
            } else {
                $creada_por = "Administrador";
            }

            $numero_invitados = json_decode($AcompananteSocio, true);
            if (count($numero_invitados) > $ArrayTipoHabitacion["Capacidad"]) {
                $respuesta["message"] = "El maximo de invitados permitido es de : " . $ArrayTipoHabitacion["Capacidad"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            $dbo->query("INSERT INTO ReservaHotel ( IDClub, IDUsuarioReserva, IDSocio , IDInvitado, IDHabitacion , IDPromocion , IDTemporadaAlta , Temporada , CabezaReserva , DocumentoDuenoReserva, NombreDuenoReserva, EmailDuenoReserva, Estado , FechaInicio , FechaFin , Ninera , Corral , IVA , Adicional , FechaReserva, Observaciones, FechaTrCr, UsuarioTrCr )
																VALUES ( '" . $IDClub . "', '" . $IDusuarioCrea . "','" . $IDSocio . "' , '" . $IDInvitadoHotel . "','" . $IDHabitacion . "' , '$IDPromocion' , '" . $IDTemporadaAlta . "' ,
																	 				'" . $Temporada . "' , '" . $CabezaReserva . "' , '" . $DocumentoDuenoReserva . "','" . $NombreDuenoReserva . "','" . $EmailDuenoReserva . "','" . $estado_reserva . "' , '" . $FechaInicio . "' , '" . $FechaFin . "' , '" . $Ninera . "' ,
																					'" . $Corral . "' , '" . $ArrayParametro["Iva"] . "' , '" . $Adicional . "' , NOW(), '" . $Observaciones . "', NOW(),'" . $creada_por . "' ) ");
            $IDReserva = $dbo->lastID();


            //ingreso los asistentes
            $NumeroAsistentes = 0;
            $contador_invitado_agregado = 1;
            //$datos_invitado_reserva_h= json_decode($AcompananteSocio);
            $datos_invitado_reserva_h = json_decode($AcompananteSocio, true);
            $contador_inv_socio = 0;
            $contador_inv_externo = 0;
            if (count($datos_invitado_reserva_h) > 0) :
                foreach ($datos_invitado_reserva_h as $datos_invitado_reserva) :
                    if ((int) $datos_invitado_reserva["IDSocio"] > 0) {
                        $contador_inv_socio++;
                        $TipoInvitado = "Socio";
                    } else {
                        $contador_inv_externo++;
                        $TipoInvitado = "Externo";
                    }

                    $IDSocioInvitado = $datos_invitado_reserva["IDSocio"];
                    $IDInvitadoHotel = $datos_invitado_reserva["IDInvitado"];
                    $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                    $Familiar = $datos_invitado_reserva["Familiar"];
                    // Guardo los invitados socios o externos
                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;

                    if (!in_array($IDInvitadoHotel, $array_invitado_agregado)) :
                        $dbo->query("INSERT INTO ReservaHotelDetalleInvitado ( IDReservaHotel , IDReservaHotelInvitado, IDSocioInvitado, TipoInvitado , UsuarioTrCr , FechaTrCr )
																	VALUES ( '" . $IDReserva . "','" . $IDInvitadoHotel . "','" . $IDSocioInvitado . "','" . $TipoInvitado . "','App',NOW() ) ");
                        $array_invitado_agregado[] = $IDSocioInvitado . "-" . $NombreSocioInvitadoTurno;
                        $NumeroAsistentes++;
                    endif;
                    $contador_invitado_agregado++;
                endforeach;
            endif;

            //ingreso el cabeza de reserva para acompanante
            $dbo->query("INSERT INTO ReservaHotelDetalleInvitado ( IDReservaHotel , IDReservaHotelInvitado, IDSocioInvitado, TipoInvitado , UsuarioTrCr , FechaTrCr )
												 VALUES ( '" . $IDReserva . "' , '' ,'" . $IDSocio . "', 'Socio' , 'App',NOW() ) ");
            $NumeroAsistentes++;

            $ValidarSanAndresSofacama = false;
            //inserto campos dinamicos
            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
            $datos_campos = json_decode($Campos, true);
            if (count($datos_campos) > 0) {
                foreach ($datos_campos as $detalle_campo) {
                    $IDCampo = $detalle_campo["IDCampo"];
                    $valorcampo = $detalle_campo["Valor"];
                    $sql_dinamico = "INSERT INTO HotelCampoHotel (IDCampoHotel, IDReserva, IDSocio,Valor,FechaTrCr)
																									VALUES ('" . $IDCampo . "','" . $IDReserva . "','" . $IDSocio . "','" . $valorcampo . "',NOW())";
                    $dbo->query($sql_dinamico);

                    if ($IDCampo == 3 && $valorcampo == 'Si') :
                        $ValidarSanAndresSofacama = true;
                    endif;
                };
            }

            //se resta el adicional por el parametro S en las tarifas
            if ($Adicional == "S") {
                $NumeroAsistentes = $NumeroAsistentes - 1;
            }

            /*
            echo "F INI " . $FechaInicio . "<br>";
            echo "F Fin " .$FechaFin . "<br>";
            echo "TIPO " .$ArrayHabitacion["IDTipoHabitacion"] . "<br>";
            echo "Cab " .$CabezaReserva . "<br>";
            echo "Asis " .$NumeroAsistentes . "<br>";
            echo "Adic " .$Adicional . "<br>";
            echo "Tip Prom " .$ArrayPromocion["TipoPromocion"] . "<br>";
            echo "Desc " .$ArrayPromocion["ValorDescuento"] . "<br>";
            echo "Desc Inv " .$ArrayPromocion["ValorDescuentoInvitado"];
             */

            if ((int) $NumeroAsistentes == 0) {
                $NumeroAsistentes = 1;
            }

            (int)$ValorReserva = SIMWebServiceHotel::calculaterifareserva($IDClub, $FechaInicio, $FechaFin, $ArrayHabitacion["IDTipoHabitacion"], $CabezaReserva, $NumeroAsistentes, $Adicional, $ArrayPromocion["TipoPromocion"], $ArrayPromocion["ValorDescuento"], $ArrayPromocion["ValorDescuentoInvitado"], $contador_inv_socio, $contador_inv_externo, $ValidarSanAndresSofacama, $TipoReserva, $ArrayParametro, $IDSocio, $AcompananteSocio);

            //modifico el valor de la reserva por que no se habia calculado
            $dbo->query("UPDATE `ReservaHotel` SET `Valor` = '" . $ValorReserva . "' , NumeroPersonas = '" . $NumeroAsistentes . "' WHERE `ReservaHotel`.`IDReserva` = '" . $IDReserva . "' LIMIT 1 ;");
            /**** Mail para el club ***/
            $NombreSocioLogueado = "";
            $NombreCabezaReserva = $ArrayInvitado["Nombre"] . " " . $ArrayInvitado["Apellido"];
            $email = $ArrayParametro["EmailAdministrador"];
            $subject = "El socio ($NombreSocioLogueado) realizo una reservacion";
            $mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, "Cola", $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva);
            $headers = "From: reservas@clubpayande.com \r\n";
            $headers .= "Content-Type: text/html\r\n";
            //mail($email, $subject, $mensaje, $headers);
            $emailsim = 'jorgechirivi@gmail.com.com';
            //mail($emailsim, $subject, $mensaje, $headers);

            /**** Mail para el socio ***/
            $Tratamiento = "";
            $NombreCabezaReserva = $ArrayInvitado["Nombre"] . " " . $ArrayInvitado["Apellido"];
            $email = SIMUser::get("Email");
            $subject = "$Tratamiento ($NombreSocioLogueado) su reservacion quedo en estado de cola";
            $mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, "Cola", $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva);
            $headers = "From: reservas@clubpayande.com \r\n";
            $headers .= "CC: " . $ArrayInvitado["Email"] . "\r\n";
            $headers .= "Content-Type: text/html\r\n";
            //mail($email, $subject, $mensaje, $headers);



            if ((int) $ValorReserva >= 0) :
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

                $datos["IDReserva"] = $IDReserva;
                $datos["Valor"] = $ValorReserva;
                $valorConIva = $ValorReserva + (($ValorReserva * $ArrayParametro["Iva"]) / 100);
                $datos["TextoValorMinimo"] = "Recuerda que debes cancelar el 100% del valor de la reserva, es decir: ";
                //Calculo valor minimo
                $minimo = (int) $datos["Valor"];
                $datos["ValorMinimo"] = $minimo;
                //Datos payu
                $ValorTotalReserva = $ValorTotalReserva + $ValorReserva;
                $valorConIva = $ValorReserva + (($ValorReserva * $ArrayParametro["Iva"]) / 100);
                $valorConIva = ($valorConIva);

                //Produccion
                $llave_encripcion = $datos_club["ApiKey"];

                //Produccion
                $usuarioId = $datos_club["MerchantId"];

                $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                $iva = $ArrayParametro["Iva"]; //impuestos calculados de la transacciÛn
                $baseDevolucionIva = $ValorTotalReserva; //el precio sin iva de los productos que tienen iva
                $valor = $ValorTotalReserva + (($ValorTotalReserva * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                $valor = ($valor); //el valor ; //el valor total
                $moneda = "COP"; //la moneda con la que se realiza la compra
                $prueba = $datos_club["IsTest"];
                $descripcion = "Transaccion para pagar las reserva de hotel " . $datos_club["Nombre"];
                $url_respuesta = URLROOT . "respuesta_transaccion_hotel.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                $url_confirmacion = URLROOT . "confirmacion_pagos_hotel.php";
                $emailComprador = $dbo->getFields("Socio", "Email", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha

                //Produccion
                $datos["Action"] = $datos_club["URL_PAYU"];
                $datos["moneda"] = (string) $moneda;
                $datos["ref"] = $refVenta;
                $datos["llave"] = $llave_encripcion;
                $datos["userid"] = $usuarioId;
                $datos["usuarioId"] = $usuarioId;
                $datos["accountId"] = (string) $datos_club["AccountId"];
                $datos["descripcion"] = $descripcion;
                $datos["extra1"] = (string) $IDReserva;
                $datos["extra2"] = (string) $IDClub;
                $datos["refVenta"] = $refVenta;
                //$datos["valor"] = "";    //La genera el pp
                $datos["iva"] = (float) $iva; //Lo genera el app
                $datos["baseDevolucionIva"] = ""; //Lo genera el app
                $datos["firma"] = ""; //Lo genera el app
                $datos["emailComprador"] = $emailComprador;
                $datos["prueba"] = (string) $prueba;
                $datos["url_respuesta"] = (string) $url_respuesta;
                $datos["url_confirmacion"] = (string) $url_confirmacion;
                $datos["BotonPago"] = (string) "S";

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
                $datos_post["valor"] = (string) $IDReserva;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = $valor;
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
                $datos_post["valor"] = (string) $prueba;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "IDSocio";
                $datos_post["valor"] = (string) $IDSocio;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos["ParametrosPost"] = $response_parametros;

                //PAGO
                $datos_post_pago = array();
                $datos_post_pago["iva"] = 0;
                $datos_post_pago["purchaseCode"] = $refVenta;
                $datos_post_pago["totalAmount"] = $valor * 100;
                $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                //FIN PAGO
                if ($Admin == "S") {
                    $con_iva = $datos["Valor"] + ($datos["Valor"] * $ArrayParametro["Iva"] / 100);
                    $mensaje_reserva = "El valor a cancelar es de: $" . ($con_iva);
                }

                if ($datos_club["PasarelaZonaVirtual"] == "S") {
                    $datos["ref"] = $IDSocio;
                    $datos["Action"] = $datos_club["UrlZona"];
                }

                array_push($response, $datos);
                $respuesta["message"] = "Reserva guardada correctamente. " . $mensaje_reserva;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "3. Atencion el valor no puede ser 0";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        } else {
            $respuesta["message"] = "2. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_reservav2($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, $Admin = "", $IDusuarioCrea = "", $NombreDuenoReserva = "", $DocumentoDuenoReserva = "", $EmailDuenoReserva = "", $Observaciones = "", $Campos = "", $TipoReserva = "")
    {

        $dbo = &SIMDB::get();
        $response = array();
        $hoy = date("Y-m-d");
        $ConfiguracionHotel = $dbo->fetchAll("ConfiguracionHotel", " IDClub = '" . $IDClub . "' ", "array");
        //San Andres habilitar para pruebas el año x
        /*
        if(empty($Admin)){
        if($IDClub==70 && ($IDSocio!=159622 || $IDSocio!=158750 ) && ( substr($FechaInicio,0,4)==2022 || substr($FechaFin,0,4)=="2022" )){
        $respuesta["message"] = "El año 2022  aun no esta disponible para reservas.";
        $respuesta["success"] = false;
        $respuesta["response"] = NULL;
        return $respuesta;
        }
        }
        */

        //RESERVAS ACTIVAS
        if ($ConfiguracionHotel["NumeroReservasActivas"] > 0) {
            $contadorReservasActivas = 0;
            $sql_conteo_reservas = "SELECT * FROM  ReservaHotel WHERE IDSocio='$IDSocio' AND IDClub='$IDClub' AND (FechaInicio>='$hoy' OR FechaFin >='$hoy' AND '$hoy' <= FechaFin)";
            // echo $sql_conteo_reservas;
            $r_conteo_reservas = $dbo->query($sql_conteo_reservas);
            while ($ConteoReservas = $dbo->fetchArray($r_conteo_reservas)) {
                $contadorReservasActivas += 1;
            }
            if ($contadorReservasActivas >= $ConfiguracionHotel["NumeroReservasActivas"]) {
                $respuesta["message"] = "Lo sentimos ya tiene " . $contadorReservasActivas . " reserva activa" . " y solo se permiten " . $ConfiguracionHotel["NumeroReservasActivas"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //RESERVAS MAXIMAS POR DIAS
        if ($ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"] > 0) {
            $sql_conteo_reservas = "SELECT COUNT(IDReserva) AS Conteo FROM  ReservaHotel WHERE IDSocio='$IDSocio' AND IDClub='$IDClub' AND FechaInicio='$FechaInicio'";
            $r_conteo_reservas = $dbo->query($sql_conteo_reservas);
            $ConteoReservas = $dbo->fetchArray($r_conteo_reservas);

            if ($ConteoReservas["Conteo"] >= $ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"]) {
                $respuesta["message"] = "Lo sentimos solo se permite hacer " . $ConfiguracionHotel["NumeroReservasPermitidasMismaFecha"] . " reserva el mismo dia.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //Validacion especial anapoima semana receso solo se puede toda la semana el dia  x

        if ($IDClub == 46) {
            //Verifico que no tenga mas reservas
            $sql_reserva_h = "SELECT * FROM ReservaHotel WHERE FechaInicio>='2019-10-07' and FechaFin <= '2019-10-14' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
            $r_reserva_h = $dbo->query($sql_reserva_h);
            if ($dbo->rows($r_reserva_h) > 0) {
                $respuesta["message"] = "Lo sentimos ya tiene una reserva para esa semana";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $PermiteReservar = $datos_socio["PermiteReservarHotel"];
        if ($PermiteReservar == "N") {
            $respuesta["message"] = "No tiene autorizacion para realizar reservas de hotel";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            exit;
        }

        $Parametros = $dbo->query("SELECT * FROM ConfiguracionHotel WHERE IDClub = '" . $IDClub . "'");
        $ArrayParametro = $dbo->fetchArray($Parametros);

        if (!empty($IDSocio) && !empty($CabezaReserva) && ((!empty($Temporada) && !empty($FechaInicio) && !empty($FechaFin) && !empty($IDHabitacion)) || !empty($TipoReserva))) {

            $SQLArrayReserva = $dbo->query("SELECT * FROM ReservaHotel WHERE DATE_ADD( FechaReserva, INTERVAL 30 MINUTE ) < NOW() AND FechaReserva > '2012-07-18 00:00:01' AND Estado = 'pendiente'");
            while ($ArrayReservaDel = $dbo->fetchArray($SQLArrayReserva)) {
                //$id = $dbo->insert( $ArrayReservaDel , "reservalog" , "IDReservaLog" );
                $qry_elimina = "INSERT IGNORE INTO ReservaHotelEliminada (
                    IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
                    Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd)
                    Select
                    IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
                    Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,'UsuarioApp','" . date("Y-m-d H:i:s") . "'
                    From ReservaHotel
                    Where IDReserva  = '" . $ArrayReservaDel["IDReserva"] . "'";
                $sql_copia_reserva = $dbo->query($qry_elimina);
                //$dbo->query("DELETE FROM ReservaHotel WHERE IDReserva = '" . $ArrayReservaDel["IDReserva"] . "'");
            }

            if ($IDClub == 200) :

                /*
                if ($CabezaReserva == 'Invitado') :
                    $respuesta["message"] = "La reserva debe ser tomada por el socio y no por un invitado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
                */


                $SQLReservas = "SELECT * FROM ReservaHotel WHERE IDSocio = '$IDSocio' AND ((FechaInicio <= '$FechaInicio' AND FechaFin >= '$FechaInicio') OR (FechaInicio <= '$FechaFin' AND FechaFin >= '$FechaFin')) ";
                $QRYReservas = $dbo->query($SQLReservas);
                $DiaInicio = date("w", strtotime($FechaInicio));
                //aplicamos esta validacion -1 para evitar el mensaje y que pueda reservar mas de una vez
                if ($dbo->rows($QRYReservas) == "-1" && ($DiaInicio == '0' || $DiaInicio == '6')) :
                    $respuesta["message"] = "Solo puede tener una reserva en las mismas fechas los fines de semana.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                // VALIDAMOS QUE SI TRAE INIVTADOS LA RESERVA SOLO PUEDA SER TOMADA DESDE EL JUEVES A LAS 5PM
                $Invitados = json_decode($AcompananteSocio, true);
                //  if (count($Invitados) > 0) :
                $CabezaReserva;
                //VALIDAMOS QUE LA RESERVA SEA PARA INVITADOS
                if ($CabezaReserva == "Invitado") :

                    $Hora = date("H:i:s");
                    //aplicamos esta validacion -1 para evitar el mensaje y que pueda reservar mas de una vez
                    $fecha_actual = date("Y-m-d");
                    $DiaInicioReserva = date("N", strtotime($fecha_actual));
                    if ($DiaInicioReserva <> 7) :
                        $dias = (7 - $DiaInicioReserva);
                    endif;
                    $maxima_fecha = date("Y-m-d", strtotime($fecha_actual . "+ $dias day"));
                    if ($Hora < "17:00:00" and $DiaInicioReserva == 1) :
                        $respuesta["message"] = "Solo pueden hacer reservas a partir del lunes a las 5:00 PM ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    if ($maxima_fecha < $FechaInicio) :

                        $respuesta["message"] = "Solo pueden hacer reservas para este fin de semana.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    endif;
                endif;

            endif;




            //verificamos si aplica la promocion
            $Promocion = $dbo->query("
									SELECT *
									FROM Promocion WHERE IDClub = '" . $IDClub . "'
									(FechaInicio <= '" . $FechaInicio . "' AND FechaFin > '" . $FechaInicio . "')
									OR
									(FechaInicio < '" . $FechaInicio . "' AND FechaFin >= '" . $FechaInicio . "')
									OR
									(FechaInicio <= '" . $FechaFin . "' AND FechaFin > '" . $FechaFin . "')
									OR
									(FechaInicio < '" . $FechaFin . "' AND FechaFin >= '" . $FechaFin . "')
									OR
									( '" . $FechaInicio . "' <= FechaInicio AND '" . $FechaFin . "' > FechaInicio )
									OR
									( '" . $FechaInicio . "' < FechaInicio AND '" . $FechaFin . "' >= FechaInicio )
									OR
									( '" . $FechaInicio . "' <= FechaFin AND '" . $FechaFin . "' > FechaFin )
									OR
									( '" . $FechaInicio . "' < FechaFin AND '" . $FechaFin . "' >= FechaFin )
								");
            $ArrayPromocion = $dbo->fetchArray($Promocion);
            $IDPromocion = $ArrayPromocion["IDPromocion"];

            //saco el total de dias dependiendo el periodo de fechas
            $DiasPeriodoTarifa = SIMWebServiceHotel::diasperiodotarifa($FechaInicio, $FechaFin);

            //se verifica el adicional en blanco
            if ($Adicional == "") {
                $Adicional = N;
            }

            //datos de la habitacion
            $Habitacion = $dbo->query("SELECT * FROM Habitacion WHERE IDHabitacion = " . $IDHabitacion);
            $ArrayHabitacion = $dbo->fetchArray($Habitacion);

            //datos del tipo de habitacion
            $TipoHabitacion = $dbo->query("SELECT * FROM TipoHabitacion WHERE IDTipoHabitacion = " . $ArrayHabitacion["IDTipoHabitacion"]);
            $ArrayTipoHabitacion = $dbo->fetchArray($TipoHabitacion);

            // DATOS TORRE

            $SQLTorre = "SELECT * FROM Torre WHERE IDTorre = $ArrayHabitacion[IDTorreo]";
            $QRYTorre = $dbo->query($SQLTorre);
            $DatosTorre = $dbo->fetchArray($QRYTorre);

            ///Verifico de nuevo que alguien no haya tomado la habitacion para evitar duplicados
            $FechaInicioValida = $FechaInicio;
            $FechaFinValida = $FechaFin;
            $IDHabitacionValida = $IDHabitacion;
            $suma_rand = rand(0, 2);
            $rand_seg = rand(1, 1) + $suma_rand;
            sleep($rand_seg);

            if (!empty($IDHabitacionValida)) :
                $sql_valida = "
                    SELECT Habitacion.IDHabitacion
                    FROM
                    Habitacion
                    WHERE Habitacion.IDHabitacion NOT IN(
                        SELECT ReservaHotel.IDHabitacion
                        FROM ReservaHotel WHERE IDClub = '" . $IDClub . "' and
                        (
                                (ReservaHotel.FechaInicio <= '" . $FechaInicioValida . "' AND ReservaHotel.FechaFin > '" . $FechaInicioValida . "')	OR
                                (ReservaHotel.FechaInicio < '" . $FechaInicioValida . "' AND ReservaHotel.FechaFin > '" . $FechaInicioValida . "')	OR
                                (ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')				OR
                                (ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')				OR
                                (ReservaHotel.FechaInicio < '" . $FechaFinValida . "' AND ReservaHotel.FechaFin > '" . $FechaFinValida . "')			OR
                                ( '" . $FechaInicioValida . "' <= ReservaHotel.FechaInicio AND '" . $FechaFinValida . "' > ReservaHotel.FechaInicio )	OR
                                ( '" . $FechaInicioValida . "' < ReservaHotel.FechaInicio AND '" . $FechaFinValida . "' > ReservaHotel.FechaInicio )	OR
                                ( '" . $FechaInicioValida . "' < ReservaHotel.FechaFin AND '" . $FechaFinValida . "' > ReservaHotel.FechaFin )	OR
                                ( '" . $FechaInicioValida . "' < ReservaHotel.FechaFin AND '" . $FechaFinValida . "' > ReservaHotel.FechaFin )
                        )
                        AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )
                    )
                    AND IDHabitacion='" . $IDHabitacionValida . "'
                                            ";

                $Habitacion = $dbo->query($sql_valida);

                if ($dbo->rows($Habitacion) <= 0) {
                    $respuesta["message"] = "La reserva ya fue tomada por otra persona por favor intente con otra habitacion";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                // VALIDAMOS LA HABITACIÓN PARA CORTO PLAZO

                if ($Temporada == 'CortoPlazo') :
                    $ValidaHabitacionCortoPlazo = self::verifica_temporada_corto_plazo($FechaInicio, $IDClub, $IDHabitacionValida);

                    if (empty($ValidaHabitacionCortoPlazo)) :
                        $respuesta["message"] = "Esta habitación en esas fechas no tiene disponibilidad a corto plazo, por favor escoger otra habitación";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;
            //Fin valida si esta duplicada
            endif;

            if ($Admin == "S") {
                $estado_reserva = "enfirme";
            } else {
                $estado_reserva = "pendiente";
            }

            if ($IDClub == 46) {
                $estado_reserva = "enfirme";
            }

            if (empty($IDusuarioCrea)) {
                $creada_por = "Socio";
            } else {
                $creada_por = "Administrador";
            }

            $numero_invitados = json_decode($AcompananteSocio, true);
            if (count($numero_invitados) > $ArrayTipoHabitacion["Capacidad"] && $TipoReserva != "Pasadia") {
                $respuesta["message"] = "El maximo de invitados permitido es de : " . $ArrayTipoHabitacion["Capacidad"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            // VALIDAMOS PARA MI SEGUNDA CASA QUE TENGA NOCHES Y ESTADIAS DISPONIBLES
            if ($IDClub == 194) :
                $DiasEstadia =  date_diff(date_create($FechaInicio), date_create($FechaFin));
                $DiasEstadia = $DiasEstadia->format('%d');
                // BUSCAMOS LA ULTIMA RESERVA DEL SOCIO
                $SQLReserva = "SELECT * FROM ReservaHotel WHERE IDSocio = $IDSocio ORDER BY IDReserva DESC LIMIT 1";
                $QRYReserva = $dbo->query($SQLReserva);
                $DatosReserva = $dbo->fetchArray($QRYReserva);

                $FechaInicioAntigua = $DatosReserva[FechaInicio];
                $FechaFinAntigua = $DatosReserva[FechaFin];

                $sql_reserva_socio = "SELECT * FROM ReservaHotel WHERE FechaFin='" . $FechaInicio . "' and IDSocio = '" . $IDSocio . "' and  IDClub = '" . $IDClub . "'";
                $r_reserva_socio = $dbo->query($sql_reserva_socio);
                if ($dbo->rows($r_reserva_socio) == 0) {

                    $cantidad_dias_baja = "2";
                    $cantidad_dias_corto = "3";
                    $cantidad_dias_alta = "7";
                    $sql_reserva_socio1 = "SELECT * FROM ReservaHotel WHERE IDHabitacion = '" . $IDHabitacion . "' and  IDClub = '" . $IDClub . "' and FechaInicio >= now() ORDER BY IDReserva DESC ";
                    $r_reserva_socio1 = $dbo->query($sql_reserva_socio1);

                    while ($DatosReserva1 = $dbo->fetchArray($r_reserva_socio1)) :
                        if ($DatosReserva1["FechaFin"] == $FechaInicio or $DatosReserva1["FechaInicio"] == $FechaFin) :
                            $respuesta["message"] = "Lo sentimos, por temas de logística debes correr tu fecha de llegada";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endwhile;
                } else {
                    $cantidad_dias_baja = "1";
                    $cantidad_dias_corto = "1";
                    $cantidad_dias_alta = "1";
                }


                // SI ES TEMPORADA ALTA DEBE SER MINIMO 3 NOCHES

                if (($Temporada == 'Baja') && $DiasEstadia < $cantidad_dias_baja) :
                    // SI ES TEMPORADA ALTA DEBE SER MINIMO 3 NOCHES




                    $respuesta["message"] = "Para estas fechas debe tomar minimo 2 noches de estadia";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                if (($Temporada == 'CortoPlazo') && $DiasEstadia < $cantidad_dias_corto) :
                    $respuesta["message"] = "Para estas fechas debe tomar minimo 3 noches de estadia";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                if (($Temporada == 'Alta') && $DiasEstadia < $cantidad_dias_alta) :
                    $respuesta["message"] = "Para estas fechas debe tomar minimo 7 noches de estadia";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                $SQLFraccion = "SELECT * FROM SocioHabitacion WHERE IDSocio = '$IDSocio' AND IDHabitacion = $IDHabitacion AND Estadias > 0 AND NumeroFracciones > 0 AND (FechaInicioFraccion <= '$FechaInicio' AND FechaFinFraccion >= '$FechaFin') AND Noches >= '$DiasEstadia'";
                $QRYFraccion = $dbo->query($SQLFraccion);
                $Fraccion = $dbo->fetchArray($QRYFraccion);
                if ($dbo->rows($QRYFraccion) <= 0) :
                    $respuesta["message"] = "No tiene noches o estadias disponibles para tomar la reserva en esta habitación.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                $MaximoNoches = 14 * $Fraccion[NumeroFracciones];
                if ($IDClub == 194) :
                    $sql_reserva_socio = "SELECT * FROM ReservaHotel WHERE FechaFin='" . $FechaInicio . "' and IDSocio = '" . $IDSocio . "'  and  IDClub = '" . $IDClub . "'";
                    $r_reserva_socio = $dbo->query($sql_reserva_socio);
                    if ($dbo->rows($r_reserva_socio) == 0) {
                    } else {
                        $DiasEstadia = 2;
                    }

                endif;
                // VALIDAMOS QUE LAS ESTADIAS DE LARGA NOTIFICACION SEAN ENTRE 2 Y 7 NOCHES
                if (($DiasEstadia < 2 || $DiasEstadia > $MaximoNoches) && $Temporada != 'CortoPlazo') :
                    $respuesta["message"] = "Las estadias de larga notificación deben ser de 2 o mas noches y no superiores a $MaximoNoches noches";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                else :
                    if ($DiasEstadia <= 7) :
                        $EstadiasDescontadas = 1;
                    else :
                        $EstadiasDescontadas = 2;
                    endif;
                endif;

                // SI EL SOCIO YA TIENE UNA RESERVA EN TEMPORADA ALTA EN UN AÑO NO PUEDE MAS

                if ($Temporada == 'Alta') :
                    $HaceUnAño = date("Y-m-d", strtotime('-1 year', strtotime($FechaInicio)));
                    $Hoy = date("Y-m-d");
                    $SQLAlta = "SELECT * FROM ReservaHotel WHERE IDSocio = $IDSocio AND ((FechaInicio = '$HaceUnAño' AND FechaFin = '$FechaInicio') OR (FechaInicio > '$Hoy')) AND Temporada = 'Alta'";
                    $QRYAlta = $dbo->query($SQLAlta);


                    if ($dbo->rows($QRYAlta) > 0) :
                        $respuesta["message"] = "Solo tiene derecho para una reserva al año en temporada alta y ya fue utilizada";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                endif;
            endif;

            $dbo->query("INSERT INTO ReservaHotel ( IDClub, IDUsuarioReserva, IDSocio , IDInvitado, IDHabitacion , IDPromocion , IDTemporadaAlta , Temporada , CabezaReserva , DocumentoDuenoReserva, NombreDuenoReserva, EmailDuenoReserva, Estado , FechaInicio , FechaFin , Ninera , Corral , IVA , Adicional , FechaReserva, Observaciones, FechaTrCr, UsuarioTrCr, TipoReserva )
																VALUES ( '" . $IDClub . "', '" . $IDusuarioCrea . "','" . $IDSocio . "' , '" . $IDInvitadoHotel . "','" . $IDHabitacion . "' , '$IDPromocion' , '" . $IDTemporadaAlta . "' ,
																	 				'" . $Temporada . "' , '" . $CabezaReserva . "' , '" . $DocumentoDuenoReserva . "','" . $NombreDuenoReserva . "','" . $EmailDuenoReserva . "','" . $estado_reserva . "' , '" . $FechaInicio . "' , '" . $FechaFin . "' , '" . $Ninera . "' ,
																					'" . $Corral . "' , '" . $ArrayParametro["Iva"] . "' , '" . $Adicional . "' , NOW(), '" . $Observaciones . "', NOW(),'" . $creada_por . "', '$TipoReserva' ) ");
            $IDReserva = $dbo->lastID();

            // DESCONTAMOS LAS NOCHES PARA ESE SOCIO EN LA HABITACION SELECCIONADA
            if ($IDClub == 194) :

                $SQLFraccion = "SELECT * FROM SocioHabitacion WHERE IDSocio = '$IDSocio' AND IDHabitacion = $IDHabitacion ";
                $QRYFraccion = $dbo->query($SQLFraccion);
                $Fraccion = $dbo->fetchArray($QRYFraccion);

                $DiasEstadia =  date_diff(date_create($FechaInicio), date_create($FechaFin));
                $DiasEstadia = $DiasEstadia->format('%d');
                $Noches = $Fraccion[Noches] - $DiasEstadia;
                $Estadias = $Fraccion[Estadias] - $EstadiasDescontadas;
                $update = "UPDATE SocioHabitacion SET Noches = '$Noches', Estadias = '$EstadiasWHERE IDSocioHabitacion = $Fraccion[IDSocioHabitacion]";
                $dbo->query($update);


            endif;

            //ingreso los asistentes
            $NumeroAsistentes = 0;
            $contador_invitado_agregado = 1;
            //$datos_invitado_reserva_h= json_decode($AcompananteSocio);
            $datos_invitado_reserva_h = json_decode($AcompananteSocio, true);
            $contador_inv_socio = 0;
            $contador_inv_externo = 0;
            if (count($datos_invitado_reserva_h) > 0) :
                foreach ($datos_invitado_reserva_h as $datos_invitado_reserva) :
                    if ((int) $datos_invitado_reserva["IDSocio"] > 0) {
                        $contador_inv_socio++;
                        $TipoInvitado = "Socio";
                    } else {
                        $contador_inv_externo++;
                        $TipoInvitado = "Externo";
                    }

                    $IDSocioInvitado = $datos_invitado_reserva["IDSocio"];
                    $IDInvitadoHotel = $datos_invitado_reserva["IDInvitado"];
                    $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                    $Familiar = $datos_invitado_reserva["Familiar"];
                    // Guardo los invitados socios o externos
                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;

                    if (!in_array($IDInvitadoHotel, $array_invitado_agregado)) :
                        $dbo->query("INSERT INTO ReservaHotelDetalleInvitado ( IDReservaHotel , IDReservaHotelInvitado, IDSocioInvitado, TipoInvitado , UsuarioTrCr , FechaTrCr )
																	VALUES ( '" . $IDReserva . "','" . $IDInvitadoHotel . "','" . $IDSocioInvitado . "','" . $TipoInvitado . "','App',NOW() ) ");
                        $array_invitado_agregado[] = $IDSocioInvitado . "-" . $NombreSocioInvitadoTurno;
                        $NumeroAsistentes++;
                    endif;
                    $contador_invitado_agregado++;
                endforeach;
            endif;

            //ingreso el cabeza de reserva para acompanante
            $dbo->query("INSERT INTO ReservaHotelDetalleInvitado ( IDReservaHotel , IDReservaHotelInvitado, IDSocioInvitado, TipoInvitado , UsuarioTrCr , FechaTrCr )
												 VALUES ( '" . $IDReserva . "' , '' ,'" . $IDSocio . "', 'Socio' , 'App',NOW() ) ");
            $NumeroAsistentes++;

            $ValidarSanAndresSofacama = false;
            //inserto campos dinamicos
            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
            $datos_campos = json_decode($Campos, true);
            if (count($datos_campos) > 0) {
                foreach ($datos_campos as $detalle_campo) {
                    $IDCampo = $detalle_campo["IDCampo"];
                    $valorcampo = $detalle_campo["Valor"];
                    $sql_dinamico = "INSERT INTO HotelCampoHotel (IDCampoHotel, IDReserva, IDSocio,Valor,FechaTrCr)
																									VALUES ('" . $IDCampo . "','" . $IDReserva . "','" . $IDSocio . "','" . $valorcampo . "',NOW())";
                    $dbo->query($sql_dinamico);

                    if ($IDCampo == 3 && $valorcampo == 'Si') :
                        $ValidarSanAndresSofacama = true;
                    endif;
                };
            }

            //se resta el adicional por el parametro S en las tarifas
            if ($Adicional == "S") {
                $NumeroAsistentes = $NumeroAsistentes - 1;
            }

            if ((int) $NumeroAsistentes == 0) {
                $NumeroAsistentes = 1;
            }


            // PROMOCION VIERNES 50%
            if ($IDClub == 200) :
                $DiaInicio = date("N", strtotime($FechaInicio));
                $DiaIFin = date("N", strtotime($FechaFin));
                if ($DiaInicio == '5') :
                    $fecha_prox_lunes = date("Y-m-d", strtotime($FechaInicio . "+ 3 day"));
                    // $fecha_prox_lunes = date('Y-m-d', $proximo_Lunes);
                    $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $fecha_prox_lunes . "' and IDPais = 1");
                    if ($IDFestivo == 0 || empty($IDFestivo)) :
                        $ArrayPromocion["TipoPromocion"] = 'Viernes50';
                        $ArrayPromocion["ValorDescuento"] = 50;
                    endif;
                    //SE HACE EL DESCUENTO DEL 50% EN TODA LA TEMPORADA BAJA
                    if ($TemporadaActual != 'Alta') :
                        $ArrayPromocion["TipoPromocion"] = 'Viernes50';
                        $ArrayPromocion["ValorDescuento"] = 50;
                    endif;

                elseif ($DiaInicio == '1' && $DiaFin == '5') :
                    $ArrayPromocion["TipoPromocion"] = 'Viernes50';
                    $ArrayPromocion["ValorDescuento"] = 50;
                endif;
            endif;

            (float)$ValorReserva = SIMWebServiceHotel::calculaterifareserva($IDClub, $FechaInicio, $FechaFin, $ArrayHabitacion["IDTipoHabitacion"], $CabezaReserva, $NumeroAsistentes, $Adicional, $ArrayPromocion["TipoPromocion"], $ArrayPromocion["ValorDescuento"], $ArrayPromocion["ValorDescuentoInvitado"], $contador_inv_socio, $contador_inv_externo, $ValidarSanAndresSofacama, $TipoReserva, $ArrayParametro, $IDSocio, $AcompananteSocio, $Campos);



            //modifico el valor de la reserva por que no se habia calculado
            $dbo->query("UPDATE `ReservaHotel` SET `Valor` = '" . $ValorReserva . "' , NumeroPersonas = '" . $NumeroAsistentes . "' WHERE `ReservaHotel`.`IDReserva` = '" . $IDReserva . "' LIMIT 1 ;");

            /**** Mail para el club ***/
            $NombreSocioLogueado = "";
            $NombreCabezaReserva = $ArrayInvitado["Nombre"] . " " . $ArrayInvitado["Apellido"];
            $email = $ArrayParametro["EmailAdministrador"];
            $subject = "El socio ($NombreSocioLogueado) realizo una reservacion";
            // $mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, "Cola", $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva);
            $headers = "From: reservas@clubpayande.com \r\n";
            $headers .= "Content-Type: text/html\r\n";
            //mail($email, $subject, $mensaje, $headers);
            $emailsim = 'jorgechirivi@gmail.com.com';
            //mail($emailsim, $subject, $mensaje, $headers);

            /**** Mail para el socio ***/
            $Tratamiento = "";
            $NombreCabezaReserva = $ArrayInvitado["Nombre"] . " " . $ArrayInvitado["Apellido"];
            $email = SIMUser::get("Email");
            $subject = "$Tratamiento ($NombreSocioLogueado) su reservacion quedo en estado de cola";
            // $mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, "Cola", $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva);
            $headers = "From: reservas@clubpayande.com \r\n";
            $headers .= "CC: " . $ArrayInvitado["Email"] . "\r\n";
            $headers .= "Content-Type: text/html\r\n";
            //mail($email, $subject, $mensaje, $headers);

            //MI SEGUNDA CASA ENVIO EL CORREO APENAS SE HAGA EL INSERT DEBIDO A QUE ELLOS NO MANEJAN PAGO LOS DEMAS SE EJECUTAN POR EL CRON
            if ($ArrayParametro[NotificaReserva] == 1 && $IDClub == 194) :
                SIMWebServiceHotel::NotificarReservaHotel($IDClub, $IDReserva, $IDSocio);
                //Notificar al socio
                if ($ArrayParametro[NotificarSocio] == "S") :
                    SIMWebServiceHotel::NotificarReservaHotel($IDClub, $IDReserva, $IDSocio, "Socio");
                endif;
            endif;

            //Notificar al socio
            if ($ArrayParametro[NotificarSocio] == "S") :
                SIMWebServiceHotel::NotificarReservaHotel($IDClub, $IDReserva, $IDSocio, "Socio");
            endif;

            //

            if ((int) $ValorReserva >= 0) :
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

                // Cambio 20220310 Se envia el valor de la reserva con iva a la app

                // $datos["IDReserva"] = $IDReserva;
                // $datos["Valor"] = $ValorReserva;
                // $valorConIva = $ValorReserva + (($ValorReserva * $ArrayParametro["Iva"]) / 100);
                // $datos["TextoValorMinimo"] = "Recuerda que debes cancelar el 100% del valor de la reserva, es decir: ";
                // //Calculo valor minimo
                // $minimo = (int) $datos["Valor"];
                // $datos["ValorMinimo"] = $minimo;
                // //Datos payu
                // $ValorTotalReserva = $ValorTotalReserva + $ValorReserva;
                // $valorConIva = $ValorReserva + (($ValorReserva * $ArrayParametro["Iva"]) / 100);
                // $valorConIva = ($valorConIva);

                $datos["IDReserva"] = $IDReserva;
                $valorConIva = (float)$ValorReserva + (((float)$ValorReserva * $ArrayParametro["Iva"]) / 100);
                $valorConIvaTexto = $valorConIva;
                $datos["Valor"] = $valorConIva;
                $datos["ValorPagoTexto"] = "$" . $valorConIvaTexto;
                $datos["TextoValorMinimo"] = "Recuerda que debes cancelar el 100% del valor de la reserva, es decir: ";
                //Calculo valor minimo
                $minimo = (int) $valorConIva;
                $minimoValorTexto = $valorConIva;
                $datos["ValorMinimo"] = $minimo;
                $datos["ValorPagoMinimoTexto"] = "$" . $minimoValorTexto;
                //Datos payu
                $ValorTotalReserva = $ValorTotalReserva + $ValorReserva;
                $valorConIva = $ValorReserva + (($ValorReserva * $ArrayParametro["Iva"]) / 100);
                $valorConIva = ($valorConIva);

                //Fin Cambio 20220310 Se envia el valor de la reserva con iva a la app

                //Produccion
                $llave_encripcion = $datos_club["ApiKey"];

                //Produccion
                $usuarioId = $datos_club["MerchantId"];

                $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                $iva = $ArrayParametro["Iva"]; //impuestos calculados de la transacciÛn
                $baseDevolucionIva = $ValorTotalReserva; //el precio sin iva de los productos que tienen iva
                $valor = $ValorTotalReserva + (($ValorTotalReserva * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                $valor = ($valor); //el valor ; //el valor total
                $moneda = "COP"; //la moneda con la que se realiza la compra
                $prueba = $datos_club["IsTest"];
                $descripcion = "Transaccion para pagar las reserva de hotel " . $datos_club["Nombre"];
                $url_respuesta = URLROOT . "respuesta_transaccion_hotel.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                $url_confirmacion = URLROOT . "confirmacion_pagos_hotel.php";
                $emailComprador = $dbo->getFields("Socio", "Email", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha

                //Produccion
                $datos["Action"] = $datos_club["URL_PAYU"];
                $datos["moneda"] = (string) $moneda;
                $datos["ref"] = $refVenta;
                $datos["llave"] = $llave_encripcion;
                $datos["userid"] = $usuarioId;
                $datos["usuarioId"] = $usuarioId;
                $datos["accountId"] = (string) $datos_club["AccountId"];
                $datos["descripcion"] = $descripcion;
                $datos["extra1"] = (string) $IDReserva;
                $datos["extra2"] = (string) $IDClub;
                $datos["refVenta"] = $refVenta;
                //$datos["valor"] = "";    //La genera el pp
                $datos["iva"] = (float) $iva; //Lo genera el app
                $datos["baseDevolucionIva"] = ""; //Lo genera el app
                $datos["firma"] = ""; //Lo genera el app
                $datos["emailComprador"] = $emailComprador;
                $datos["prueba"] = (string) $prueba;
                $datos["url_respuesta"] = (string) $url_respuesta;
                $datos["url_confirmacion"] = (string) $url_confirmacion;
                $datos["BotonPago"] = (string) "S";

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
                $datos_post["valor"] = (string) $IDReserva;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = $valor;
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
                $datos_post["valor"] = (string) $prueba;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "IDSocio";
                $datos_post["valor"] = (string) $IDSocio;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos["ParametrosPost"] = $response_parametros;

                //PAGO
                $datos_post_pago = array();
                $datos_post_pago["iva"] = 0;
                $datos_post_pago["purchaseCode"] = $refVenta;
                $datos_post_pago["totalAmount"] = $valor * 100;
                $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                //FIN PAGO
                if ($Admin == "S") {
                    $con_iva = $datos["Valor"] + ($datos["Valor"] * $ArrayParametro["Iva"] / 100);
                    $mensaje_reserva = "El valor a cancelar es de: $" . ($con_iva);
                }

                if ($datos_club["PasarelaZonaVirtual"] == "S") {
                    $datos["ref"] = $IDSocio;
                    $datos["Action"] = $datos_club["UrlZona"];
                }

                array_push($response, $datos);
                $respuesta["message"] = "Reserva guardada correctamente. " . $mensaje_reserva;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "3. Atencion el valor no puede ser 0";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        } else {
            $respuesta["message"] = "2. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function NotificarReservaHotel($IDClub, $IDReservaHotel, $IDSocio, $NotificarSocio = "")
    {
        $dbo = &SIMDB::get();

        $Parametros = $dbo->query("SELECT * FROM ConfiguracionHotel WHERE IDClub = '$IDClub'");
        $ArrayParametro = $dbo->fetchArray($Parametros);

        $datos_reserva = $dbo->fetchAll("ReservaHotel", "IDReserva = $IDReservaHotel");
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");
        $datos_habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $datos_reserva[IDHabitacion]");
        $datos_torre = $dbo->fetchAll("Torre", "IDTorre = $datos_habitacion[IDTorre]");

        $correo = $ArrayParametro[EmailAdministrador];

        if (!empty($datos_torre[CorreoNotificacionTorre])) :
            $correo .= "," . $datos_torre[CorreoNotificacionTorre];
        endif;

        if ($NotificarSocio == "Socio") {
            if ($ArrayParametro[NotificarSocio] == "S" && !empty($datos_socio[CorreoElectronico])) {
                $correo = $datos_socio[CorreoElectronico];
            }
        }


        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        // $Mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, $Estado, $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva, $TipoReserva); 

        $NombreDueno = $datos_reserva[NombreDuenoReserva];
        if (empty($NombreDueno)) :
            $NombreDueno = $datos_socio[Nombre] . " " . $datos_socio[Apellido];
            $Contacto = $datos_socio[Celular];
        endif;

        $EmailDueno = $datos_reserva[EmailDuenoReserva];
        if (empty($EmailDueno)) :
            $Email = $datos_socio[CorreoElectronico];
        endif;

        if ($datos_reserva[TipoReserva] == 'Pasadia') :
            $TablaCampos = "CampoHotelPasadia";
            $datos_torre[Nombre] = "No Aplica";
            $datos_habitacion[NombreHabitacion] = "No Aplica";
            $datos_reserva[Temporada] = "No Aplica";
        else :
            $TablaCampos = " CampoHotel";
        endif;


        $sqlCampos = "SELECT IDCampoHotel, Valor FROM HotelCampoHotel WHERE IDReserva = $IDReservaHotel";
        $qryCampos = $dbo->query($sqlCampos);
        if ($dbo->rows($qryCampos) > 0) :
            $CamposAdicionales = "Campos adicionales<br>";
            while ($Campos = $dbo->fetchArray($qryCampos)) :
                $Campo = $dbo->getFields($TablaCampos, "Nombre", "IDCampoHotel = $Campos[IDCampoHotel]");
                $CamposAdicionales .= "$Campo: $Campos[Valor]<br>";
            endwhile;
        endif;


        $SQLInvitados = "SELECT * FROM ReservaHotelDetalleInvitado WHERE IDReservaHotel = $IDReservaHotel";
        $QRYInvitados = $dbo->query($SQLInvitados);
        if ($dbo->rows($QRYInvitados) > 0) :
            $Invitados = "Invitados<br>";
            while ($Datos = $dbo->fetchArray($QRYInvitados)) :
                if ($Datos[TipoInvitado] == 'Externo') :
                    $Datos_Invitado = $dbo->fetchAll("ReservaHotelInvitado", "IDReservaHotelInvitado = $Datos[IDReservaHotelInvitado]");
                    $Invitados .= "Invitado Externo: $Datos_Invitado[Nombre] $Datos_Invitado[Apellido] Cedula: $Datos_Invitado[NumeroDocumento] <br>";
                else :
                    if ($Datos[IDSocioInvitado] != $IDSocio) :
                        $Datos_Socio_Invitado = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocioInvitado]");
                        $Invitados .= "Invitado Socio: $Datos_Socio_Invitado[Nombre] $Datos_Socio_Invitado[Apellido] Cedula: $Datos_Socio_Invitado[NumeroDocumento] <br>";
                    endif;
                endif;
            endwhile;
        endif;

        $Mensaje =
            "
            Se ha realizado una nueva reserva de hotel
            <br><br>
            Fecha Reserva: $datos_reserva[FechaReserva]<br>            
            A nombre de: $datos_reserva[CabezaReserva] -- $NombreDueno<br>
            Mail: $Email<br>
            Contacto: $Contacto<br>
            Fecha de llegada: $datos_reserva[FechaInicio]<br>
            Fecha de salida: $datos_reserva[FechaFin]<br>
            Temporada: $datos_reserva[Temporada]<br>
            Tipo Reserva: $datos_reserva[TipoReserva]<br>
            Torre: $datos_torre[Nombre]<br>
            Nombre Habitacion: $datos_habitacion[NombreHabitacion]<br>
            Valor: $datos_reserva[Valor]<br>
            Observaciones: $datos_reserva[Observaciones]<br><br>            
            $CamposAdicionales<br>        
            $Invitados
        ";

        $msg = "<br>Cordial Saludo,<br><br>" . $Mensaje . "<br><br>
		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img width='50%' height='50%' src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" . $msg . "</td>
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

        $Asunto = "Nueva Reserva Hotel";

        $mail->Subject = $Asunto;
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
        $mail->FromName = "Club";
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();
    }

    public function NotificarCancelacionReservaHotel($IDClub, $IDReservaHotel, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $Parametros = $dbo->query("SELECT * FROM ConfiguracionHotel WHERE IDClub = '$IDClub'");
        $ArrayParametro = $dbo->fetchArray($Parametros);

        $datos_reserva = $dbo->fetchAll("ReservaHotelEliminada", "IDReserva = $IDReservaHotel");
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");
        $datos_habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $datos_reserva[IDHabitacion]");
        $datos_torre = $dbo->fetchAll("Torre", "IDTorre = $datos_habitacion[IDTorre]");

        $correo = $ArrayParametro[EmailAdministrador];

        if (!empty($datos_torre[CorreoNotificacionTorre])) :
            $correo .= "," . $datos_torre[CorreoNotificacionTorre];
        endif;

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        // $Mensaje = SIMWebServiceHotel::plantillamailreserva($CabezaReserva, $NombreCabezaReserva, $Estado, $ArrayTipoHabitacion["Nombre"], $ArrayHabitacion["NumeroHabitacion"], $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva, $TipoReserva); 

        $NombreDueno = $datos_reserva[NombreDuenoReserva];
        if (empty($NombreDueno)) :
            $NombreDueno = $datos_socio[Nombre] . " " . $datos_socio[Apellido];
            $Contacto = $datos_socio[Celular];
        endif;

        $EmailDueno = $datos_reserva[EmailDuenoReserva];
        if (empty($EmailDueno)) :
            $Email = $datos_socio[CorreoElectronico];
        endif;

        if ($datos_reserva[TipoReserva] == 'Pasadia') :
            $TablaCampos = "CampoHotelPasadia";
            $datos_torre[Nombre] = "No Aplica";
            $datos_habitacion[NombreHabitacion] = "No Aplica";
            $datos_reserva[Temporada] = "No Aplica";
        else :
            $TablaCampos = " CampoHotel";
        endif;


        $sqlCampos = "SELECT IDCampoHotel, Valor FROM HotelCampoHotel WHERE IDReserva = $IDReservaHotel";
        $qryCampos = $dbo->query($sqlCampos);
        if ($dbo->rows($qryCampos) > 0) :
            $CamposAdicionales = "Campos adicionales<br>";
            while ($Campos = $dbo->fetchArray($qryCampos)) :
                $Campo = $dbo->getFields($TablaCampos, "Nombre", "IDCampoHotel = $Campos[IDCampoHotel]");
                $CamposAdicionales .= "$Campo: $Campos[Valor]<br>";
            endwhile;
        endif;


        $SQLInvitados = "SELECT * FROM ReservaHotelDetalleInvitado WHERE IDReservaHotel = $IDReservaHotel";
        $QRYInvitados = $dbo->query($SQLInvitados);
        if ($dbo->rows($QRYInvitados) > 0) :
            $Invitados = "Invitados<br>";
            while ($Datos = $dbo->fetchArray($QRYInvitados)) :
                if ($Datos[TipoInvitado] == 'Externo') :
                    $Datos_Invitado = $dbo->fetchAll("ReservaHotelInvitado", "IDReservaHotelInvitado = $Datos[IDReservaHotelInvitado]");
                    $Invitados .= "Invitado Externo: $Datos_Invitado[Nombre] $Datos_Invitado[Apellido] Cedula: $Datos_Invitado[NumeroDocumento] <br>";
                else :
                    if ($Datos[IDSocioInvitado] != $IDSocio) :
                        $Datos_Socio_Invitado = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocioInvitado]");
                        $Invitados .= "Invitado Socio: $Datos_Socio_Invitado[Nombre] $Datos_Socio_Invitado[Apellido] Cedula: $Datos_Socio_Invitado[NumeroDocumento] <br>";
                    endif;
                endif;
            endwhile;
        endif;

        $Mensaje =
            "
            Se ha realizado una cancelacion reserva de hotel
            <br><br>
            Fecha Reserva: $datos_reserva[FechaReserva]<br>            
            A nombre de: $datos_reserva[CabezaReserva] -- $NombreDueno<br>
            Mail: $Email<br>
            Contacto: $Contacto<br>
            Fecha de llegada: $datos_reserva[FechaInicio]<br>
            Fecha de salida: $datos_reserva[FechaFin]<br>
            Temporada: $datos_reserva[Temporada]<br>
            Tipo Reserva: $datos_reserva[TipoReserva]<br>
            Torre: $datos_torre[Nombre]<br>
            Nombre Habitacion: $datos_habitacion[NombreHabitacion]<br>
            Valor: $datos_reserva[Valor]<br>
            Observaciones: $datos_reserva[Observaciones]<br><br>            
            $CamposAdicionales<br>        
            $Invitados
        ";

        $msg = "<br>Cordial Saludo,<br><br>" . $Mensaje . "<br><br>
		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img width='50%' height='50%' src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" . $msg . "</td>
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

        $Asunto = "Cancelacion Reserva Hotel";

        $mail->Subject = $Asunto;
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
        $mail->FromName = "Club";
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();
    }

    public function calculaterifareserva($IDClub, $fechainicio, $fechafin, $IDTipoHabitacion, $TipoTarifa, $NumeroAsistentes, $Adicional, $TipoPromocion, $ValorDescuento, $ValorDescuentoInvitado, $NumeroInvSocio, $NumeroInvExterno, $ValidarSanAndresSofacama, $TipoReserva, $DatosConfiguracion, $IDSocio, $AcompananteSocio, $Campos = "")
    {
        $dbo = &SIMDB::get();

        $year_inicio = substr($fechainicio, 0, 4);
        $datos_socioReserva = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $ArrayDiasEntreFechas = SIMWebServiceHotel::arrayperiodofechas($fechainicio, $fechafin);

        //echo $ArrayDiasEntreFechas[0];
        //echo $ArrayDiasEntreFechas[1];
        //recorro dia a dia para verificar si no hacen tranpa para la temporada alta
        foreach ($ArrayDiasEntreFechas as $i) {
            $ArrayTemporadaAlta = array();
            $TemporadaAlta = $dbo->query("SELECT * FROM TemporadaAlta WHERE IDClub = '" . $IDClub . "' and  FechaInicio <= '$i' AND FechaFin >= '$i'");
            $ArrayTemporadaAlta = $dbo->fetchArray($TemporadaAlta);

            if ($TipoReserva == 'Pasadia') :
                $CondicionPasadia = " AND TarifaParaPasadia = 1 ";
            else :
                $CondicionHabitacion = " AND IDTipoHabitacion = '$IDTipoHabitacion' ";
            endif;

            if ($DatosConfiguracion[TarifasPorTipoSocio] == 'S') :

                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = $IDSocio");
                $CondicionTipoSocio = " AND TipoSocio = '$TipoSocio'";

            endif;

            if (count($ArrayTemporadaAlta) > 1) {

                $SQLTarifa = "SELECT * FROM Tarifa WHERE IDClub = '$IDClub' $CondicionHabitacion AND TipoTarifa = '$TipoTarifa'  AND Temporada = 'Alta' and Year <= '$year_inicio' $CondicionPasadia $CondicionTipoSocio ORDER BY Year DESC Limit 1 ";
                $TarifaReserva = $dbo->query($SQLTarifa);
                $ArrayTarifaReserva = $dbo->fetchArray($TarifaReserva);

                //verificamos si tiene adicional y se le suma al valor al total de la tarifa
                if ($Adicional == "S") {
                    $SQLTarifa = "SELECT * FROM Tarifa WHERE IDClub = '$IDClub' $CondicionHabitacion AND TipoTarifa = '$TipoTarifa' AND Temporada = 'Alta' and Year <= '$year_inicio' $CondicionPasadia $CondicionTipoSocio ORDER BY Year DESC Limit 1 ";
                    $TarifaReservaAdiconal = $dbo->query($SQLTarifa);
                    $ArrayTarifaReservaAdicional = $dbo->fetchArray($TarifaReservaAdiconal);
                    $ValorAcompananteAdicional = $ArrayTarifaReservaAdicional["Valor"];
                }
            } else {

                $SQLTarifa = "SELECT * FROM Tarifa WHERE IDClub = '$IDClub' $CondicionHabitacion AND TipoTarifa = '$TipoTarifa' AND Temporada = 'Baja' and Year <= '$year_inicio' $CondicionPasadia $CondicionTipoSocio ORDER BY Year DESC Limit 1 ";
                $TarifaReserva = $dbo->query($SQLTarifa);
                $ArrayTarifaReserva = $dbo->fetchArray($TarifaReserva);

                //verificamos si tiene adicional y se le suma al valor al total de la tarifa
                if ($Adicional == "S") {
                    $SQLTarifa = "SELECT * FROM Tarifa WHERE IDClub = '$IDClub' $CondicionHabitacion AND TipoTarifa = '$TipoTarifa' AND Temporada = 'Baja' and Year <= '$year_inicio' $CondicionPasadia $CondicionTipoSocio ORDER BY Year DESC Limit 1 ";
                    $TarifaReservaAdiconal = $dbo->query($SQLTarifa);
                    $ArrayTarifaReservaAdicional = $dbo->fetchArray($TarifaReservaAdiconal);
                    $ValorAcompananteAdicional = $ArrayTarifaReservaAdicional["Valor"];
                }
            }


            //proceso para las tarifas con promociones de viernes
            if ($TipoPromocion == "ViernesGratis") {

                $dialetra = $dbo->query("SELECT DAYNAME('$i') AS DIALETRA");
                $arraydialetra = $dbo->fetchArray($dialetra);
                $dialetra = $arraydialetra["DIALETRA"];

                if ($dialetra != "Friday")
                    $ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];

                if ($dialetra == "Friday") {
                    if ($TipoTarifa == "Invitado") {
                        $ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuentoInvitado) / 100;

                        $ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuentoInvitado) / 100;

                        $ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
                    }
                }
            } else {
                //proceso para las tarifas con promociones de martes y miercoles
                if ($TipoPromocion == "NocheMartesMiercoles") {
                    $dialetra = $dbo->query("SELECT DAYNAME('$i') AS DIALETRA");
                    $arraydialetra = $dbo->fetchArray($dialetra);
                    $dialetra = $arraydialetra["DIALETRA"];

                    if (($dialetra == "Tuesday") || ($dialetra == "Wednesday")) {
                        if ($TipoTarifa == "Invitado") {
                            $ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuentoInvitado) / 100;
                            $ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuentoInvitado) / 100;
                            $ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
                        }

                        if ($TipoTarifa == "Socio") {
                            $ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuento) / 100;
                            $ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuento) / 100;
                            $ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
                        }
                    } else
                        $ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];
                } else {

                    if ($TipoTarifa == "Socio") {
                        if ($IDClub == 70 && $ValidarSanAndresSofacama) :
                            $ValorAdicional = $ArrayTarifaReserva["ValorSocioAdicionalSofacama"];
                        else :
                            $ValorAdicional = $ArrayTarifaReserva["ValorSocioAdicional"];
                        endif;
                    } else {

                        if ($IDClub == 70 && $ValidarSanAndresSofacama) :
                            $ValorAdicional = $ArrayTarifaReserva["ValorExternoAdicionalSofacama"];
                        else :
                            $ValorAdicional = $ArrayTarifaReserva["ValorExternoAdicional"];
                        endif;
                    }

                    //Solo cobro adicional despues del segundo invitado
                    $CargoxAdicionalesSocio = 0;
                    $CargoxAdicionalesExterno = 0;

                    if ($IDClub == 70) :
                        if ((int)$NumeroInvExterno >= 2) {
                            //Para san andres adicional es apartir del tercer invitado
                            $CargoxAdicionalesExterno = $ValorAdicional * ((int)$NumeroInvExterno - 1);
                        }

                        if ((int)$NumeroInvSocio > 1) {
                            $CargoxAdicionalesSocio = $ValorAdicional * (int)((int)$NumeroInvSocio - 2);
                        }
                        $ValorReserva +=  $ArrayTarifaReserva["Valor"] + $CargoxAdicionalesSocio + $CargoxAdicionalesExterno;

                    else :
                        if (($IDClub == 184) && $TipoReserva == 'Pasadia') :

                            // VALIDAMOS LOS CAMPOS PARA SABER EL PRECIO DE LA RESERVA

                            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
                            $datos_campos = json_decode($Campos, true);
                            if (count($datos_campos) > 0) {
                                foreach ($datos_campos as $detalle_campo) {
                                    $IDCampo = $detalle_campo["IDCampo"];
                                    $ValorCampo = $detalle_campo["Valor"];

                                    if ($ValorCampo == 'Guarino' || $ValorCampo == 'Manacacias' || $ValorCampo == 'Orocue') :
                                        $ArrayTarifaReserva["Valor"] = '';
                                        $ArrayTarifaReserva["ValorExternoAdicional"] = (int)10000;
                                        // $ArrayTarifaReserva["ValorSocioAdicional"] = (int)10000;
                                        $ArrayTarifaReserva["ValorSocioAdicional"] = (int)0;
                                    elseif ($ValorCampo == 'Neusa' || $ValorCampo == 'Tomine' || $ValorCampo == 'Tota' || $ValorCampo == 'Sisga') :
                                        $ArrayTarifaReserva["Valor"] = '';
                                        $ArrayTarifaReserva["ValorExternoAdicional"] = (int)10000;
                                        // $ArrayTarifaReserva["ValorSocioAdicional"] = (int)10000;
                                        $ArrayTarifaReserva["ValorSocioAdicional"] = (int)0;
                                    endif;
                                }
                            }

                            $ValorAdicionalExternoPasadia = $ArrayTarifaReserva["ValorExternoAdicional"] * $NumeroInvExterno;
                            $ValorAdicionalInvitadoSocioPasadia = $ArrayTarifaReserva["ValorSocioAdicional"] * $NumeroInvSocio;

                            $ValorReserva +=  $ArrayTarifaReserva["Valor"] + $ValorAdicionalExternoPasadia + $ValorAdicionalInvitadoSocioPasadia;

                            $DiasEstadia =  date_diff(date_create($fechainicio), date_create($fechafin));
                            $DiasEstadia = $DiasEstadia->format('%d') + 1;

                            $ValorReserva = $ValorReserva * $DiasEstadia;



                        else :

                            if ($TipoPromocion == 'Viernes50' && count($ArrayTemporadaAlta) <= 1) :
                                $ArrayTarifaReserva["Valor"] = (($ArrayTarifaReserva["Valor"] * $ValorDescuento) / 100);
                            endif;

                            if ($IDClub == 200) :
                                $datos_invitado_reserva_h = json_decode($AcompananteSocio, true);
                                if (count($datos_invitado_reserva_h) > 0) :
                                    foreach ($datos_invitado_reserva_h as $datos_invitado_reserva) :
                                        $IDSocioInvitado = $datos_invitado_reserva["IDSocio"];
                                        $datos_socioInvitado = $dbo->fetchAll("Socio", "IDSocio = $IDSocioInvitado");
                                        if ($datos_socioReserva[Accion] == $datos_socioInvitado[AccionPadre]) :
                                            $fecha_nacimiento = $datos_socioInvitado[FechaNacimiento];
                                            $dia_actual = date("Y-m-d");
                                            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
                                            $EdadSocio = $edad_diff->format('%y');
                                            if ($EdadSocio > 25) :
                                                $NumeroInvSocio--;
                                                $NumeroInvExterno++;
                                            endif;
                                        endif;
                                    endforeach;
                                endif;

                            endif;

                            $ValorReserva +=  $ArrayTarifaReserva["Valor"];
                            $ValorReserva += $ArrayTarifaReserva["ValorExternoAdicional"] * $NumeroInvExterno;
                            $ValorReserva += $ArrayTarifaReserva["ValorSocioAdicional"] * $NumeroInvSocio;

                            // PARA EL BOSQUE EN CAMPING Y CON MAS DE 4 NOCHES DEBE VALER 20000
                            if ($IDClub == 200) :
                                if ($ArrayTarifaReserva[IDTipoHabitacion] == '489') :
                                    $ValorReserva = 0;
                                    $DiasEstadia =  date_diff(date_create($fechainicio), date_create($fechafin));
                                    $DiasEstadia = $DiasEstadia->format('%d');

                                    $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = $IDSocio");

                                    if ($TipoSocio == 'Asociados Preferentes' && $DiasEstadia >= 4) :
                                        $ValorReserva += 20000;
                                    elseif ($TipoSocio == 'Usuario de Servicios') :
                                        $ValorReserva += 32000;
                                    elseif ($TipoSocio == 'Membresía') :
                                        $ValorReserva += 20000;
                                    endif;


                                    if (count($datos_invitado_reserva_h) > 0) :
                                        foreach ($datos_invitado_reserva_h as $datos_invitado_reserva) :
                                            $IDSocioInvitado = $datos_invitado_reserva["IDSocio"];
                                            if ($IDSocioInvitado > 0) :
                                                $datos_socioInvitado = $dbo->fetchAll("Socio", "IDSocio = $IDSocioInvitado");
                                                if ($datos_socioInvitado[TipoSocio] == 'Asociados Preferentes' && $DiasEstadia >= 4) :
                                                    $ValorReserva += 20000;
                                                elseif ($datos_socioInvitado[TipoSocio] == 'Usuario de Servicios') :
                                                    $ValorReserva += 32000;
                                                elseif ($datos_socioInvitado[TipoSocio] == 'Membresía') :
                                                    $ValorReserva += 20000;
                                                endif;
                                            else :
                                                $ValorReserva += 20000;
                                            endif;
                                        endforeach;
                                    endif;

                                endif;
                            endif;

                        endif;

                    endif;
                }
            }

            //para anapoima se cobra un valor fijo
            if ($IDClub == 46) {
                $ValorReserva = $ArrayTarifaReserva["Valor"];
            }
        }

        return $ValorReserva;
    }

    public function get_mis_reservas($IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDReserva)) :
            $condicion_reserva = "and IDReserva = '" . $IDReserva . "'";
        endif;

        $sql = "SELECT * From ReservaHotel Where FechaInicio >= '" . date("Y-m-d") . "' and IDSocio = '" . $IDSocio . "' " . $condicion_reserva . " Order by IDReserva Desc Limit 10";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $datos["IDReserva"] = $r["IDReserva"];
                $datos["IDSocio"] = $r["IDSocio"];
                $datos["IDHabitacion"] = $r["IDHabitacion"];
                $datos["IDTipoHabitacion"] = $dbo->getFields("Habitacion", "IDTipoHabitacion", "IDHabitacion =" . $datos["IDHabitacion"]);
                $datos["NombreHabitacion"] = $dbo->getFields("TipoHabitacion", "Nombre", "IDTipoHabitacion =" . $datos["IDTipoHabitacion"]);
                $datos["NumeroHabitacion"] = $dbo->getFields("Habitacion", "NumeroHabitacion", "IDHabitacion =" . $datos["IDHabitacion"]);
                $datos["CapacidadHabitacion"] = $dbo->getFields("TipoHabitacion", "Capacidad", "IDTipoHabitacion =" . $datos["IDTipoHabitacion"]);
                $datos["AdicionalHabitacion"] = $dbo->getFields("TipoHabitacion", "Adicional", "IDTipoHabitacion =" . $datos["IDTipoHabitacion"]);
                $datos["Temporada"] = $r["Temporada"];
                $datos["CabezaReserva"] = $r["CabezaReserva"];
                if ($r["CabezaReserva"] == "Socio") :
                    $datos["RealizadaPor"] = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio =" . $r["IDSocio"]) . " " . $dbo->getFields("Socio", "Apellido", "IDSocio =" . $r["IDSocio"]));
                else :
                    $datos["RealizadaPor"] = utf8_encode($dbo->getFields("detallereserva", "Nombre", "IDDetalleReserva =" . $r["IDInvitado"]));
                endif;
                $datos["Estado"] = $r["Estado"];
                $datos["FechaInicio"] = $r["FechaInicio"];
                $datos["FechaFin"] = $r["FechaFin"];
                $datos["Ninera"] = $r["Ninera"];
                $datos["Corral"] = $r["Corral"];
                $datos["Valor"] = $r["Valor"] + ($r["Valor"] * $r["IVA"] / 100);
                $datos["Adicional"] = $r["Adicional"];
                //Consulto acompanantes
                $response_acompanante = array();
                unset($datos_acompanante);
                $sql_acompanante = "Select * From ReservaHotelDetalleInvitado Where IDReservaHotel = '" . $datos["IDReserva"] . "'";
                $r_acompanante = $dbo->query($sql_acompanante);
                $response_invitado = array();
                while ($row = $dbo->fetchArray($r_acompanante)) :
                    if ($row["TipoInvitado"] == "Socio") {
                        $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocioInvitado"] . "' ", "array");
                    } else {
                        $datos_invitado = $dbo->fetchAll("ReservaHotelInvitado", " IDReservaHotelInvitado = '" . $row["IDReservaHotelInvitado"] . "' ", "array");
                    }
                    $NombreAcompanante = utf8_decode(utf8_encode($datos_invitado["Nombre"]) . " " . utf8_encode($datos_invitado["Apellido"]));
                    if (!empty($NombreAcompanante)) {
                        $datos_acompanante[] = $NombreAcompanante;
                    }

                    if ($row["IDSocioInvitado"] != $IDSocio) {
                        $datos_invitad_reserva["TipoInvitado"] = $row["TipoInvitado"];
                        $datos_invitad_reserva["IDReservaHotelInvitado"] = $datos_invitado["IDReservaHotelInvitado"];
                        $datos_invitad_reserva["IDSocioInvitado"] = $row["IDSocioInvitado"];
                        $datos_invitad_reserva["Nombre"] = $NombreAcompanante;
                        $datos_invitad_reserva["NumeroDocumento"] = $datos_invitado["NumeroDocumento"];
                        $datos_invitad_reserva["Nombre"] = utf8_decode(utf8_encode($datos_invitado["Nombre"]));
                        $datos_invitad_reserva["Apellido"] = utf8_decode(utf8_encode($datos_invitado["Apellido"]));
                        $datos_invitad_reserva["Email"] = utf8_decode(utf8_encode($datos_invitado["Email"]));
                        $datos_invitad_reserva["MenorEdad"] = $datos_invitado["MenorEdad"];
                        array_push($response_invitado, $datos_invitad_reserva);
                    }

                endwhile;
                $datos["Acompanantes"] = $datos_acompanante;
                $datos["DatosInvitado"] = $response_invitado;

                // TIPO RESERVA
                $datos["TipoReserva"] = $r["TipoReserva"];

                $SQLPasadia = "SELECT * FROM TipoReservaHotel WHERE IDClub = $IDClub AND Tipo = '$r[TipoReserva]' ORDER BY IDTipoReservaHotel DESC LIMIT 1";
                $QRYPasadia = $dbo->query($SQLPasadia);
                $DatosPasadia = $dbo->fetchArray($QRYPasadia);

                $datos["TipoReservaLabel"] = $DatosPasadia["LabelTipo"];
                $datos[CapacidadPasadia] = $DatosPasadia[CapacidadPasadia];
                $datos[AdicionalPasadia] = $DatosPasadia[AdicionalPasadia];
                if ($datos[CapacidadPasadia] == null) {
                    $datos[CapacidadPasadia] = "0";
                }

                if ($datos[AdicionalPasadia] == null) {
                    $datos[AdicionalPasadia] = "0";
                }

                array_push($response, $datos);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_bd_socio()
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * From socio Where 1";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $datos["IDSocio"] = $r["IDSocio"];
                $datos["NumeroDocumento"] = $r["NumeroDocumento"];
                $datos["IDPadre"] = $r["IDPadre"];
                $datos["Tipo"] = $r["Tipo"];
                $datos["Nombre"] = $r["Nombre"];
                $datos["Apellido"] = $r["Apellido"];
                $datos["Direccion"] = $r["Direccion"];
                $datos["Telefono"] = $r["Telefono"];
                $datos["Celular"] = $r["Celular"];
                $datos["Email"] = $r["Email"];
                $datos["FechaNacimiento"] = $r["FechaNacimiento"];
                $datos["Usuario"] = $r["Usuario"];
                $datos["Clave"] = $r["Clave"];
                $datos["Accion"] = $r["Accion"];
                if ($r["Tipo"] == "TITULAR") :
                    $accion_padre = "";
                else :
                    $accion_padre = $dbo->getFields("socio", "Accion", "IDSocio = '" . $r["IDPadre"] . "'");
                endif;
                $datos["AccionPadre"] = $accion_padre;
                $datos["Autorizado"] = $r["Autorizado"];
                $datos["Parentesco"] = $r["Parentesco"];
                $datos["Credito"] = $r["Credito"];
                $datos["Cartera"] = $r["Cartera"];
                array_push($response, $datos);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_consulta_socio()
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * From socio Where 1";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $datos["IDSocio"] = $r["IDSocio"];
                $datos["NumeroDocumento"] = $r["NumeroDocumento"];
                $datos["IDPadre"] = $r["IDPadre"];
                $datos["Tipo"] = $r["Tipo"];
                $datos["Nombre"] = $r["Nombre"];
                $datos["Apellido"] = $r["Apellido"];
                $datos["Direccion"] = $r["Direccion"];
                $datos["Telefono"] = $r["Telefono"];
                $datos["Celular"] = $r["Celular"];
                $datos["Email"] = $r["Email"];
                $datos["FechaNacimiento"] = $r["FechaNacimiento"];
                $datos["Usuario"] = $r["Usuario"];
                $datos["Clave"] = $r["Clave"];
                $datos["Accion"] = $r["Accion"];
                if ($r["Tipo"] == "TITULAR") :
                    $accion_padre = "";
                else :
                    $accion_padre = $dbo->getFields("socio", "Accion", "IDSocio = '" . $r["IDPadre"] . "'");
                endif;
                $datos["AccionPadre"] = $accion_padre;
                $datos["Autorizado"] = $r["Autorizado"];
                $datos["Parentesco"] = $r["Parentesco"];
                $datos["Credito"] = $r["Credito"];
                $datos["Cartera"] = $r["Cartera"];
                array_push($response, $datos);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_mis_invitados($IDSocio)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * From ReservaHotelInvitado Where IDSocio = '" . $IDSocio . "' Order by Nombre Asc";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $datos["IDInvitadoHotel"] = $r["IDReservaHotelInvitado"];
                $datos["TipoAsistente"] = $r["TipoAsistente"];
                $datos["NumeroDocumento"] = $r["NumeroDocumento"];
                $datos["Nombre"] = $r["Nombre"];
                $datos["Apellido"] = $r["Apellido"];
                $datos["Email"] = $r["Email"];
                $datos["MenorEdad"] = $r["MenorEdad"];
                array_push($response, $datos);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_invitados_hotel($IDSocio, $Documento, $Nombre, $Apellido, $Email, $MenorEdad = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio) && !empty($Documento) && !empty($Nombre)) :
            $sql = "SELECT * From ReservaHotelInvitado Where NumeroDocumento = '" . $Documento . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) <= 0) {
                $sql_invitado = "INSERT INTO ReservaHotelInvitado (IDSocio, TipoAsistente, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, UsuarioTrCr, FechaTrCr)
											Values ('" . $IDSocio . "','invitado','" . $Documento . "','" . $Nombre . "','" . $Apellido . "','" . $Email . "','" . $MenorEdad . "','App',NOW())";
                $dbo->query($sql_invitado);
                $id_detalle = $dbo->lastID();
                $datos["IDInvitadoHotel"] = (string) $id_detalle;
                $datos["Documento"] = (string) $Documento;
                $datos["Nombre"] = (string) $Nombre;
                $datos["Apellido"] = (string) $Apellido;
                $datos["Email"] = (string) $Email;
                array_push($response, $datos);
                $respuesta["message"] = "Invitado creado correctamente ";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $row_detalle = $dbo->fetchArray($qry);
                $sql_invitado = "UPDATE ReservaHotelInvitado set IDSocio = '" . $IDSocio . "', MenorEdad = '" . $MenorEdad . "' Where IDReservaHotelInvitado = '" . $row_detalle["IDReservaHotelInvitado"] . "'";
                $dbo->query($sql_invitado);
                $datos["IDInvitadoHotel"] = $row_detalle["IDReservaHotelInvitado"];
                $datos["Documento"] = (string) $Documento;
                $datos["Nombre"] = (string) $Nombre;
                $datos["Apellido"] = (string) $Apellido;
                $datos["Email"] = (string) $Email;
                array_push($response, $datos);
                $respuesta["message"] = "Invitado guardado correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //end else
        else :
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function set_edita_invitado_hotel($IDSocio, $IDReserva, $IDReservaHotelInvitado, $IDSocioInvitado, $TipoInvitado, $Documento, $Nombre, $Apellido, $Email, $MenorEdad)
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio) && !empty($Documento) && !empty($Nombre)) :
            $sql = "SELECT * From ReservaHotelInvitado Where NumeroDocumento = '" . $Documento . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) <= 0) {
                $sql_invitado = "INSERT INTO ReservaHotelInvitado (IDSocio, TipoAsistente, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, UsuarioTrCr, FechaTrCr)
											Values ('" . $IDSocio . "','invitado','" . $Documento . "','" . $Nombre . "','" . $Apellido . "','" . $Email . "','" . $MenorEdad . "','App',NOW())";
                $dbo->query($sql_invitado);
                $IDInvitado = $dbo->lastID();
            } //End if
            else {
                $row_detalle = $dbo->fetchArray($qry);
                $sql_invitado = "UPDATE ReservaHotelInvitado set IDSocio = '" . $IDSocio . "', MenorEdad = '" . $MenorEdad . "', Nombre='" . $Nombre . "', Apellido = '" . $Apellido . "',Email='" . $Email . "'  Where IDReservaHotelInvitado = '" . $row_detalle["IDReservaHotelInvitado"] . "'";
                $dbo->query($sql_invitado);
                $IDInvitado = $row_detalle["IDReservaHotelInvitado"];
            } //end else

            $sql_detalle = "UPDATE ReservaHotelDetalleInvitado SET IDReservaHotelInvitado = '" . $IDInvitado . "' WHERE TipoInvitado = '" . $TipoInvitado . "' and IDReservaHotel = '" . $IDReserva . "' and IDReservaHotelInvitado = '" . $IDReservaHotelInvitado . "' and IDSocioInvitado = '" . $IDSocioInvitado . "' LIMIT 1";
            $dbo->query($sql_detalle);

            $respuesta["message"] = "Invitado guardado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function set_agrega_invitado_hotel($IDClub, $IDSocio, $IDReserva, $AcompananteSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $datos_reserva = $dbo->fetchAll("ReservaHotel", " IDReserva = '" . $IDReserva . "' ", "array");
        $datos_habitacion = $dbo->fetchAll("Habitacion", " IDHabitacion = '" . $datos_reserva["IDHabitacion"] . "' ", "array");
        if (!empty($IDSocio) && !empty($IDReserva)) :

            $array_invitado_socio = array();
            $sql_invitados = "SELECT * FROM ReservaHotelDetalleInvitado WHERE IDReservaHotel = '" . $IDReserva . "'";
            $r_invitados = $dbo->query($sql_invitados);
            while ($row_invitados = $dbo->fetchArray($r_invitados)) {
                $IDSocioInvitado = $row_invitados["IDSocioInvitado"];
                $array_invitado_socio[] = $IDSocioInvitado;
            }

            //ingreso los asistentes
            $NumeroAsistentes = 0;
            $contador_invitado_agregado = 1;
            //$datos_invitado_reserva_h= json_decode($AcompananteSocio);
            $datos_invitado_reserva_h = json_decode($AcompananteSocio, true);
            $contador_inv_socio = 0;
            $contador_inv_externo = 0;
            if (count($datos_invitado_reserva_h) > 0) :
                foreach ($datos_invitado_reserva_h as $datos_invitado_reserva) :
                    if ((int) $datos_invitado_reserva["IDSocio"] > 0) {
                        $contador_inv_socio++;
                        $TipoInvitado = "Socio";
                    } else {
                        $contador_inv_externo++;
                        $TipoInvitado = "Externo";
                    }

                    $IDSocioInvitado = $datos_invitado_reserva["IDSocio"];
                    $IDInvitadoHotel = $datos_invitado_reserva["IDInvitado"];
                    $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                    $Familiar = $datos_invitado_reserva["Familiar"];
                    // Guardo los invitados socios o externos
                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;

                    if (!in_array($IDInvitadoHotel, $array_invitado_agregado) && !in_array($IDSocioInvitado, $array_invitado_socio)) :
                        $dbo->query("INSERT INTO ReservaHotelDetalleInvitado ( IDReservaHotel , IDReservaHotelInvitado, IDSocioInvitado, TipoInvitado , UsuarioTrCr , FechaTrCr )
													VALUES ( '" . $IDReserva . "','" . $IDInvitadoHotel . "','" . $IDSocioInvitado . "','" . $TipoInvitado . "','App',NOW() ) ");
                        $array_invitado_agregado[] = $IDSocioInvitado . "-" . $NombreSocioInvitadoTurno;
                        $NumeroAsistentes++;
                    endif;
                    $contador_invitado_agregado++;
                endforeach;
            endif;

            self::recalcular_valor_reserva($IDClub, $IDReserva);

            $respuesta["message"] = "Invitado agregado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function recalcular_valor_reserva($IDClub, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $datos_reserva = $dbo->fetchAll("ReservaHotel", " IDReserva = '" . $IDReserva . "' ", "array");
        $datos_habitacion = $dbo->fetchAll("Habitacion", " IDHabitacion = '" . $datos_reserva["IDHabitacion"] . "' ", "array");
        $sql_asistentes = "SELECT IDReservaHotelDetalleInvitado FROM ReservaHotelDetalleInvitado WHERE IDReservaHotel = '" . $IDReserva . "'";
        $r_asistentes = $dbo->query($sql_asistentes);
        $total_asistentes = $dbo->rows($r_asistentes);
        if ($datos_reserva["Adicional"] == null) {
            $datos_reserva["Adicional"] = "0";
        }




        $ValorReserva = SIMWebServiceHotel::calculaterifareserva($IDClub, $datos_reserva["FechaInicio"], $datos_reserva["FechaFin"], $datos_habitacion["IDTipoHabitacion"], $datos_reserva["CabezaReserva"], $total_asistentes, $datos_reserva["Adicional"], "", "", "", $total_asistentes, $total_asistentes, "", "", "", "", "", "");
        //modifico el valor de la reserva por que no se habia calculado
        $obs = $IDClub . "," . $datos_reserva["FechaInicio"] . "," . $datos_reserva["FechaFin"] . "," . $datos_habitacion["IDTipoHabitacion"] . "," . $datos_reserva["CabezaReserva"] . "," . $total_asistentes . "," . $datos_reserva["Adicional"] . "," . $total_asistentes . "," . $total_asistentes;
        $dbo->query("UPDATE `ReservaHotel` SET `Valor` = '" . $ValorReserva . "' , NumeroPersonas = '" . $total_asistentes . "', Observaciones='" . $obs . "' WHERE `ReservaHotel`.`IDReserva` = '" . $IDReserva . "' LIMIT 1 ;");
        return true;
    }

    public function set_elimina_invitado_hotel($IDReserva, $IDSocio, $IDReservaHotelInvitado, $TipoInvitado, $IDSocioInvitado)
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDReserva) && !empty($IDSocio) && !empty($TipoInvitado)) :
            $datos_reserva = $dbo->fetchAll("ReservaHotel", " IDReserva = '" . $IDReserva . "' ", "array");
            $sql = "SELECT * FROM ReservaHotelDetalleInvitado Where TipoInvitado = '" . $TipoInvitado . "' and IDReservaHotel = '" . $IDReserva . "' and IDReservaHotelInvitado = '" . $IDReservaHotelInvitado . "' and IDSocioInvitado = '" . $IDSocioInvitado . "'";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $datos_detalle_invitado = $dbo->fetchArray($qry);
                $sql_invitado = "DELETE FROM  ReservaHotelDetalleInvitado WHERE  IDReservaHotelDetalleInvitado = '" . $datos_detalle_invitado["IDReservaHotelDetalleInvitado"] . "' LIMIT 1";
                $dbo->query($sql_invitado);

                self::recalcular_valor_reserva($datos_reserva["IDClub"], $IDReserva);

                $respuesta["message"] = "Invitado eliminado correctamente ";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = "El invitado no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        else :
            $respuesta["message"] = "H1. Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function actualiza_socio($IDSocio, $NumeroDocumento, $TipoSocio, $Nombre, $Apellido, $Telefono, $Celular, $Email, $FechaNacimiento, $Usuario, $Clave, $Accion, $Autorizado, $IDSocioSistemaExterno, $DocumentoSocioPadre)
    {
        $dbo = &SIMDB::get();
        $response = array();

        //Consulto si el socio existe
        $IDSocioBD = $dbo->getFields("socio", "IDSocio", "IDSocio = '" . $IDSocio . "'");

        if (empty($IDSocioBD)) :
            //crear
            $sql_crear_socio = "Insert into socio (IDSocio, IDPadre, NumeroDocumento, Tipo, Nombre, Apellido, Email, Accion, Usuario, Clave,UsuarioTrCr, FechaTrCr )
											Values ('" . $IDSocio . "','" . $IDPadre . "','" . $NumeroDocumento . "','" . $TipoSocio . "','" . $Nombre . "','" . $Apellido . "','" . $CorreoElectronico . "','" . $Accion . "','" . $Usuario . "','" . $Clave . "','Cron Mi Club',NOW())";
            $dbo->query($sql_crear_socio);
        else :
            //actualizar
            $sql_update_socio = "Update socio Set
											IDPadre='" . $IDPadre . "',
											NumeroDocumento='" . $NumeroDocumento . "',
											Tipo='" . $TipoSocio . "',
											Nombre='" . $Nombre . "',
											Apellido='" . $Apellido . "',
											Email='" . $CorreoElectronico . "',
											Accion='" . $Accion . "',
											Usuario='" . $Usuario . "',
											Clave='" . $Clave . "',
											UsuarioTrEd='Cron Mi Club',
											FechaTrEd=NOW()
											Where IDSocio = '" . $IDSocioBD . "'";

            $dbo->query($sql_update_socio);
        //echo "<br>" . $sql_update_socio;
        //exit;
        endif;

        $respuesta["message"] = "Socio actualizado con exito";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function elimina_reserva_hotel($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {
            if (!empty($IDReserva)) {

                $sql = "SELECT * FROM ReservaHotel WHERE IDReserva = '" . $IDReserva . "'";
                $qry = $dbo->query($sql);
                $r = $dbo->fetchArray($qry);
                if ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4" || $r["EstadoTransaccion"] == "Pendiente" || $r["Estado"] == "enfirme") {
                    $respuesta["message"] = "La reserva ya fue pagada o esta pendiente de confirmación de pago";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $qry_elimina = "INSERT IGNORE INTO ReservaHotelEliminada (
						IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
						Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd)
						Select
						IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,Temporada,CabezaReserva,Estado,FechaInicio,FechaFin,Ninera,Corral,
						Valor,IVA,NumeroPersonas,Adicional,Pagado,FechaReserva,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,UsuarioTrCr,FechaTrCr,'UsuarioApp','" . date("Y-m-d H:i:s") . "'
						From ReservaHotel
						Where IDReserva  = '" . $IDReserva . "'";
                $sql_copia_reserva = $dbo->query($qry_elimina);
                //borro reserva
                $sql_borra_reserva = $dbo->query("Delete From ReservaHotel Where IDReserva  = '" . $IDReserva . "'");

                $NotificaReserva = $dbo->getFields("ConfiguracionHotel", "NotificaReserva", "IDClub = '" . $IDClub . "'");
                if ($NotificaReserva == 1) {
                    SIMWebServiceHotel::NotificarCancelacionReservaHotel($IDClub, $IDReserva, $IDSocio);
                }

                $respuesta["message"] = "La reserva fue eliminada";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Faltan datos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "9. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function valida_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();

        $response = array();
        echo $sql = "SELECT * FROM ReservaHotel WHERE IDReserva = '$IDReserva'";
        exit;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                if ($r["EstadoTransaccion"] == "") :
                    $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4") :
                    $respuesta["message"] = "Reserva pagada correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                else :
                    $respuesta["message"] = "El pago no fue realizado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                endif;
            }
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }



    public function set_tipo_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("ReservaHotel", "IDReserva", "IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    if (empty($id_codigo)) {
                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($codigo_disponible != "S") {
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";

                        $dbo->query($sql_actualiza_codigo);
                    }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update ReservaHotel Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDReserva = '" . $IDReserva . "' and IDClub = '" . $IDClub . "'";

                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_valida_fecha_pasadia($IDClub, $IDSocio, $FechaIncio, $FechaFin)
    {
        $dbo = SIMDB::get();
        //SACAMOS LOS ID QUE USAREMOS EN LAS CONSULTAS
        $IDSocio = SIMNet::req("IDSocio");
        $IDClub = SIMNet::req("IDClub");
        //VERIFICAMOS SI EL HOTEL TIENE PERMITIDO LAS RESERVAS DE HOTEL A MAYORES DE 21
        $Hotel_reserva = "SELECT * FROM ConfiguracionHotel Where IDClub=$IDClub";
        $ReservarHotel = $dbo->query($Hotel_reserva);

        $rows = array();
        while ($row = mysqli_fetch_array($ReservarHotel))
            $rows[] = $row;
        foreach ($rows as $row) {
            $permite =  $row["EdadReservaHotel"];
        }

        //VERIFICAMOS LA FECHA DE NACIMIENTO DEL SOCIO
        $Datos = "SELECT * FROM Socio Where IDSocio=$IDSocio";
        $Socio = $dbo->query($Datos);

        $rows1 = array();
        while ($row1 = mysqli_fetch_array($Socio))
            $rows1[] = $row1;
        foreach ($rows1 as $row1) {
            $nac =  $row1["FechaNacimiento"];
        }

        //HACEMOS LOS CALCULOS A VER SI ES O NO MAYOR DE EDAD

        if ($nac == "0000-00-00") {
            $nac_minimo = 0;
            $nac = 1;
        } else {
            $date2 = (date("Y-m-d"));
            $nac_minimo = date("Y-m-d", strtotime("-$permite year", strtotime($date2)));
        }


        if ($permite != '0' and $nac_minimo < $nac) { //mi club prueba

            $respuesta["message"] = "Lo sentimos,no tienes edad para reservar ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        $response = array();

        $Valido = 1;

        $SQLPasadia = "SELECT * FROM TipoReservaHotel WHERE IDClub = $IDClub AND Tipo = 'Pasadia' ORDER BY 	IDTipoReservaHotel DESC LIMIT 1";
        $QRYPasadia = $dbo->query($SQLPasadia);
        $DatosPasadia = $dbo->fetchArray($QRYPasadia);

        $InfoPasadia[CapacidadPasadia] = $DatosPasadia[CapacidadPasadia];
        $InfoPasadia[AdicionalPasadia] = $DatosPasadia[AdicionalPasadia];
        if ($InfoPasadia[CapacidadPasadia] == null) {
            $InfoPasadia[CapacidadPasadia] = "0";
        }

        if ($InfoPasadia[AdicionalPasadia] == null) {
            $InfoPasadia[AdicionalPasadia] = "0";
        }

        //array_push($response,$InfoPasadia);

        $response = $InfoPasadia;

        if ($Valido == 1) :
            $respuesta["message"] = "Fechas validas para la pasadía";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :
            $respuesta["message"] = "Fechas no validas para la pasadía";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;


        return $respuesta;
    }

    // NUEVAS

    function diasperiodotarifa($fechainicio, $fechafin)
    {
        $dbo = &SIMDB::get();

        //$diasperiodo = $dbo->query("SELECT DATEDIFF('".$fechafin."','".$fechainicio."') + 1 AS DIAS;");
        $diasperiodo = $dbo->query("SELECT DATEDIFF('" . $fechafin . "','" . $fechainicio . "') AS DIAS;");

        $arraydiasperiodo = $dbo->fetchArray($diasperiodo);

        $diasperiodo = $arraydiasperiodo["DIAS"];

        return $diasperiodo;
    }

    function plantillamailreserva($CabezaReserva, $NombreCabezaReserva, $Estado, $TipoHabitacion, $Habitacion, $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva, $TipoReserva = "")
    {
        $html = '
				
					<table width="602" border="0" align="center" cellpadding="0" cellspacing="0" class="bgverde">
						<tr>
						<td height="89" colspan="3"><span><img src="' . MAILRESERVA . 'bg_r1_c1.jpg" alt="01" width="800" height="123" border="0" /></span></td>
						</tr>
						<tr>
						<td width="26" align="left" valign="top"><img src="' . MAILRESERVA . 'bg_r2_c1.jpg" width="26" height="100%" align="left"></td>
						<td width="563" height="34">
							<table width="544" border="0" align="center" cellpadding="0" cellspacing="0" class="fontStyle">
									<tr>
										<td height="44" colspan="2" align="center" valign="bottom" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold">RESERVACION</td>
									</tr>
									<tr>
										<td height="53" colspan="2" align="left" valign="middle" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><div align="center"><span style="color: #333333">Sus datos son los siguientes:</span></div></td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Cabeza Reserva:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $CabezaReserva . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Nombre De Cabeza Reserva:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $NombreCabezaReserva . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Tipo Reserva:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $TipoReserva . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Estado:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Estado . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Tipo Habitacion:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $TipoHabitacion . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Habitacion:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Habitacion . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Fecha Inicio:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $FechaInicio . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Fecha Fin:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $FechaFin . '</td>
									</tr>
									<tr>
										<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Adicional:</span><span class="Estilo10"></span></td>
										<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Adicional . '</td>
									</tr>
							</table>
							<table width="544" border="0" align="center" cellpadding="0" cellspacing="0" class="fontStyle">
								<tr>
									<td height="44" align="left" valign="middle" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold"><div align="center">ACOMPA&Ntilde;ANTES</div></td>
								</tr>';
        foreach ($ArrayAcompanantesReserva as $claveAcompanante => $ValorAcompanante) {
            $html .= '
									<tr>
										<td width="312" height="25" align="left" valign="top" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $ValorAcompanante["Nombre"] . ' ' . $ValorAcompanante["Apellido"] . '</td>
									</tr>';
        }



        $html .= '<tr>
									<td height="55" colspan="2" align="center" valign="top" class="Estilo4"><div align="left"></div></td>
								</tr>
								<tr>
									<td height="55" align="center" valign="top" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Le agradecemos por hacer su reserva en <a style="color: #333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold" href="' . URLROOT . 'admin">' . URLROOT . 'admin</a><font face="Verdana, Arial, Helvetica, sans-serif">.</font></span></td>
								</tr>
							</table></td>
						<td width="26" align="left" valign="top"><img src="' . MAILRESERVA . 'bg_r2_c3.jpg" width="26" height="100%" align="right"></td>
						</tr>

						<tr>
						<td height="35" colspan="5" align="center" valign="middle" bgcolor="#999999"><div align="center" style="color: #000000; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:normal">      Oficinas en Bogot�: Cra. 15 No. 76 - 60. Of. 501. PBX: 6167088. Fax: 6352693. payande@cable.net.co</div></td>
						</tr>
					</table>

				';

        return $html;
    }

    function arrayperiodofechas($FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();
        //$diasperiodo = $dbo->query("SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') + 1 AS DIAS;");
        $diasperiodo = $dbo->query("SELECT DATEDIFF('" . $FechaFin . "','" . $FechaInicio . "')  AS DIAS;");
        //$diasperiodo = $dbo->query("SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') AS DIAS;");
        //echo "SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') + 1 AS DIAS;";
        $arraydiasperiodo = $dbo->fetchArray($diasperiodo);
        $diasperiodo = $arraydiasperiodo["DIAS"];

        $ArrayDiasEntreFechas = array();
        $ArrayDiasEntreFechas[$FechaInicio] = $FechaInicio;

        for ($i = 1; $i < $diasperiodo; $i++) {
            $DiaActual = $dbo->query("SELECT DATE_ADD('" . $FechaInicio . "', INTERVAL $i DAY) AS DIAACTUAL");
            $arrayDiaActual = $dbo->fetchArray($DiaActual);
            $ArrayDiasEntreFechas[$arrayDiaActual["DIAACTUAL"]] = $arrayDiaActual["DIAACTUAL"];
        }

        return $ArrayDiasEntreFechas;
    }
} //end class
