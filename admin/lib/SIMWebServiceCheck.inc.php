<?php
class SIMWebServiceCheck
{
    public function get_configuracion_checking_laboral($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLConfiguracion = "SELECT * FROM ConfiguracionCheckinLaboral WHERE IDClub = $IDClub AND Activo = 'S'";
            $QRYConfiguracion = $dbo->query($SQLConfiguracion);

            if ($dbo->rows($QRYConfiguracion) > 0) :
                $message = "Datos encontrados";
                while ($Datos = $dbo->fetchArray($QRYConfiguracion)) :

                    $Check[IDClub] = $Datos[IDClub];
                    $Check[Nombre] = $Datos[Nombre];
                    $Check[IconoIngresoInactivo] = CLUB_ROOT . $Datos[IconoIngresoInactivo];
                    $Check[IconoIngresoActivo] = CLUB_ROOT . $Datos[IconoIngresoActivo];
                    $Check[IconoSalidaActivo] = CLUB_ROOT . $Datos[IconoSalidaActivo];
                    $Check[IconoSalidaInactivo] = CLUB_ROOT . $Datos[IconoSalidaInactivo];
                    $Check[TextoIntro] = $Datos[TextoIntro];
                    $Check[TextoConfirmacionIngreso] = $Datos[TextoConfirmacionIngreso];
                    $Check[TextoConfirmacionSalida] = $Datos[TextoConfirmacionSalida];
                    $Check[ColorActivo] = $Datos[ColorActivo];
                    $Check[ColorInactivo] = $Datos[ColorInactivo];
                    $Check[PermiteBloquearBotones] = $Datos[PermiteBloquearBotones];
                    $Check[PermiteMostrarHorasExtra] = $Datos[PermiteMostrarHorasExtra];
                    $Check[LabelHorasExtra] = $Datos[LabelHorasExtra];
                    $Check[PermiteIngresarObservaciones] = $Datos[PermiteIngresarObservaciones];
                    $Check[LabelIngresarObservaciones] = $Datos[LabelIngresarObservaciones];
                    $Check[PermiteIngresarLugarTrabajo] = $Datos[PermiteIngresarLugarTrabajo];
                    $Check[ObligatorioIngresarLugarTrabajo] = $Datos[ObligatorioIngresarLugarTrabajo];
                    $Check[LabelIngresarLugarTrabajo] = $Datos[LabelIngresarLugarTrabajo];

                    //Configuracion Checkin despues del turno
                    $Check[PermiteRegistrarTurnoExtra] = $Datos[PermiteRegistrarTurnoExtra];
                    $Check[IconoIngresoTurnoExtraInactivo] = CLUB_ROOT . $Datos[IconoIngresoTurnoExtraInactivo];
                    $Check[IconoIngresoExtraTurnoActivo] = CLUB_ROOT .  $Datos[IconoIngresoExtraTurnoActivo];
                    $Check[IconoSalidaTurnoExtraActivo] = CLUB_ROOT . $Datos[IconoSalidaTurnoExtraActivo];
                    $Check[IconoSalidaTurnoExtraInactivo] = CLUB_ROOT . $Datos[IconoSalidaTurnoExtraInactivo];
                    $Check[TextoConfirmacionTurnoExtraIngreso] = $Datos[TextoConfirmacionTurnoExtraIngreso];
                    $Check[TextoConfirmacionTurnoExtraoSalida] = $Datos[TextoConfirmacionTurnoExtraoSalida];

                    //sitio de trabajo
                    $SQLSitioTrabajo = "SELECT Nombre,IDSitioTrabajo FROM SitioTrabajo WHERE IDClub = $IDClub AND Publicar = 'S'";
                    $QRYSitioTrabajo = $dbo->query($SQLSitioTrabajo);
                    $sitiostrabajo = array();
                    while ($Datos2 = $dbo->fetchArray($QRYSitioTrabajo)) :
                        $CheckSitioTrabajo[IDSitioTrabajo] = $Datos2[IDSitioTrabajo];
                        $CheckSitioTrabajo[Nombre] = $Datos2[Nombre];
                        array_push($sitiostrabajo, $CheckSitioTrabajo);
                    endwhile;


                    $Check[OpcionesLugaresTrabajo] = $sitiostrabajo;


                    array_push($response, $Check);

                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $Check;

            else :
                $respuesta["message"] = "No hay datos de configuración, por favor verificar";
                $respuesta["success"] = false;
                $respuesta["response"] = "";
            endif;

        else :

            $respuesta["message"] = "Faltan parametros para la configuración";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

        endif;

        return $respuesta;
    }

    public function get_mis_horas_extras($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) :

            if ($IDSocio > 0) {
                $campo = "IDSocio";
                $valor = $IDSocio;
                $Tabla = "Socio";
            } elseif ($IDUsuario > 0) {
                $campo = "IDUsuario";
                $valor = $IDUsuario;
                $Tabla = "Usuario";
            }

            $SQLCheckin = "SELECT * FROM CheckinLaboral WHERE IDClub = $IDClub AND " . $campo . "='" . $valor . "' AND Estado = '2' ORDER BY FechaMovimientoEntrada DESC";
            $QRYCheckin = $dbo->query($SQLCheckin);

            if ($dbo->rows($QRYCheckin) > 0) :
                $message = "Datos encontrados";
                while ($Datos = $dbo->fetchArray($QRYCheckin)) :

                    if ($Datos["IDSocio"] > 0) {
                        $id = $Datos["IDSocio"];
                    } elseif ($Datos["IDUsuario"] > 0) {
                        $id = $Datos["IDUsuario"];
                    }

                    //Nombre Jefe
                    $NombreJefe = $dbo->getFields($Tabla, "NombreJefe", "IDClub = '" . $IDClub . "' AND " . $campo . "='" . $id . "'");


                    //horas extras
                    $TiempoExtraEntrada = SIMUtil::Calcular_diferencia_horas($Datos['HoraEntradaEstablecida'], $Datos['FechaMovimientoEntrada'], 'Entrada');
                    $TiempoExtraSalida = SIMUtil::Calcular_diferencia_horas($Datos['HoraSalidaEstablecida'], $Datos['FechaMovimientoSalida'], 'Salida');

                    $fecha = explode(" ", $Datos["FechaMovimientoEntrada"]);
                    $HorasAprobadas = "entrada " . $TiempoExtraEntrada . " salida " . $TiempoExtraSalida;
                    $Check["IDHoraExtra"] = $Datos["IDCheckinLaboral"];
                    $Check["NombreJefe"] = $NombreJefe;
                    $Check["Fecha"] = $fecha[0];
                    $Check["HorasAprobadas"] = $HorasAprobadas;
                    $Check["Descripcion"] =  $Datos["ComentarioRevision"];



                    array_push($response, $Check);

                endwhile;
            endif;


            //Extras despues del turno
            $SQLCheckin2 = "SELECT * FROM CheckinLaboralHorasExtras WHERE IDClub = $IDClub AND " . $campo . "='" . $valor . "' AND Estado = '2' ORDER BY FechaMovimientoEntrada DESC";
            $QRYCheckin2 = $dbo->query($SQLCheckin2);

            if ($dbo->rows($QRYCheckin2) > 0) :
                $message = "Datos encontrados";
                while ($Datos2 = $dbo->fetchArray($QRYCheckin2)) :

                    if ($Datos2["IDSocio"] > 0) {
                        $id = $Datos2["IDSocio"];
                    } elseif ($Datos2["IDUsuario"] > 0) {
                        $id = $Datos2["IDUsuario"];
                    }

                    //Nombre Jefe
                    $NombreJefe = $dbo->getFields($Tabla, "NombreJefe", "IDClub = '" . $IDClub . "' AND " . $campo . "='" . $id . "'");

                    //horas extras despues de turno
                    $TiempoExtraDespuesDeTurno = SIMUtil::Calcular_diferencia_horas($Datos2['FechaMovimientoEntrada'], $Datos2['FechaMovimientoSalida'], "", "ExtrasDespuesDeTurno");


                    $fecha2 = explode(" ", $Datos2["FechaMovimientoEntrada"]);
                    $HorasAprobadasDespuesDeTurno = "despues del turno " . $TiempoExtraDespuesDeTurno;
                    $Check2["IDHoraExtra"] = $Datos2["IDCheckinLaboral"];
                    $Check2["NombreJefe"] = $NombreJefe;
                    $Check2["Fecha"] = $fecha2[0];
                    $Check2["HorasAprobadas"] = $HorasAprobadasDespuesDeTurno;
                    $Check2["Descripcion"] =  $Datos2["ComentarioRevision"];



                    array_push($response, $Check2);

                endwhile;
            endif;


            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "Faltan Parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

        endif;

        return $respuesta;
    }

    public function set_checking_laboral($IDClub, $IDSocio, $IDUsuario, $Latitud, $Longitud, $Entrada, $Observaciones, $IDSitioTrabajo = "", $EntradaDespuesDeTurno = "")
    {
        $dbo = SIMDB::get();

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($Entrada)) :

            //VERIFICO CONFIGURACION DEL CLUB
            $datos_config_checkin = $dbo->fetchAll("ConfiguracionCheckinLaboral", " IDClub = '" . $IDClub . "' ", "array");


            $FechaActual = date("Y-m-d H:i:s");
            $Hoy = date("Y-m-d");

            // SABER SI ES UN USUARIO (EMPLEADO) O ES UN SOCIO
            if ($IDUsuario > 0) :
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $Tabla = "Usuario";
            else :
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $Tabla = "Socio";
            endif;

            // SACAMOS SI FUE UNA SALIDA O UNA ENTRADA PARA GUARDAR
            if ($Entrada == 'N') :
                $CampoMovimiento = "Salida";
                $CampoMovimientoLat = "LatitudSalida";
                $CampoMovimientoLon = "LongitudSalida";
                $CampoMovimientoFecha = "FechaMovimientoSalida";
                $CampoMovimientoFechaDespuesDelTurno = "FechaMovimientoSalidaDespuesDelTurno";
                $ValorMovimiento = 'S';
                $UltimoMovimiento = "S";
                $actualizo_salida_sin_dato = "";
            else :
                $CampoMovimiento = "Entrada";
                $CampoMovimientoLat = "LatitudEntrada";
                $CampoMovimientoLon = "LongitudEntrada";
                $CampoMovimientoFecha = "FechaMovimientoEntrada";
                $CampoMovimientoFechaDespuesDelTurno = "FechaMovimientoEntradaDespuesDelTurno";
                $ValorMovimiento = 'S';
                $UltimoMovimiento = "E";
                $actualizo_salida_sin_dato = " FechaMovimientoSalida = '', Salida='', LatitudSalida='', LongitudSalida = '', ";
            endif;


            // revisamos que el campo observaciones no este vacio
            if ($datos_config_checkin["ObligatorioIngresarObservaciones"] == "S" && empty($Observaciones)) {
                $respuesta["message"] = "No puede estar vacio el campo observaciones.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            //revisamos que el socio o el usuario en el campo cargo no sea Student || Apprentice debido a que no pueden marcar entrda o salida
            $sql_cargo = "SELECT Cargo FROM $Tabla WHERE " . $Campo . " = '" . $Valor . "' LIMIT 1";
            $r_query_cargo = $dbo->query($sql_cargo);
            $Datos = $dbo->fetchArray($r_query_cargo);
            $DatosCargo = $Datos;
            $mensaje = "";
            $array_cargos = SIMResources::$cargo_my_office;
            foreach ($array_cargos as $indice => $valor) {
                if ($valor == $DatosCargo["Cargo"]) {

                    $mensaje = "Noesposibleregistrarelcheckinsucargonotienepermisospararealizarestafuncion";
                }
            }

            if (!empty($mensaje)) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '',  $mensaje, LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = "";
            } else {

                //seleccionamos la hora del socio o usuario que tiene en ese momento definida y la insertamos en la tabla CheckinLaboral
                $sql_horarios = "SELECT HoraInicioLaboral,HoraFinalLaboral FROM $Tabla WHERE " . $Campo . " = '" . $Valor . "' LIMIT 1";
                $r_query_horarios = $dbo->query($sql_horarios);
                $DatosHorario = $dbo->fetchArray($r_query_horarios);
                $DatosHora = $DatosHorario;
                $HoraEntradaEstablecida = $DatosHora["HoraInicioLaboral"];
                $HoraSalidaEstablecida = $DatosHora["HoraFinalLaboral"];


                //Revisamos si el registro ya tiene un movimiento en el dia
                $sql_mov = "SELECT IDCheckinLaboral,Entrada,Salida,FechaMovimientoSalida FROM CheckinLaboral WHERE " . $Campo . " = '" . $Valor . "' and FechaTrCr >= '" . $Hoy . "'  LIMIT 1";
                $r_query = $dbo->query($sql_mov);
                $movimiento = $dbo->fetchArray($r_query);
                $DatosMovimiento = $movimiento;

                //CHECKIN NORMAL
                if (empty($EntradaDespuesDeTurno)) {
                    if ($DatosMovimiento["IDCheckinLaboral"] <= 0) {
                        // if ($dbo->rows($r_query) <= 0) {
                        // REGISTRAMOS EL MOVIMIENTO
                        $SQLInsert = "INSERT INTO CheckinLaboral    (IDClub,IDSitioTrabajo, $Campo, $CampoMovimientoLat, $CampoMovimientoLon, $CampoMovimiento, $CampoMovimientoFecha, UltimoMovimiento,Estado, ObservacionEntrada,HoraEntradaEstablecida,HoraSalidaEstablecida,UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd) 
                        VALUES  ('$IDClub','$IDSitioTrabajo','$Valor','$Latitud','$Longitud','$ValorMovimiento','$FechaActual','" . $UltimoMovimiento . "',1,'$Observaciones','$HoraEntradaEstablecida','$HoraSalidaEstablecida','WS-$Campo-$Valor','$FechaActual','WS-$Campo-$Valor','$FechaActual')";
                        $dbo->query($SQLInsert);
                    } else  if ($DatosMovimiento["Entrada"] == "S" && $DatosMovimiento["Salida"] == "") {




                        // $IDCheck = $row_mov["IDCheckinLaboral"];
                        $IDCheck = $DatosMovimiento["IDCheckinLaboral"];
                        $SQLUpdate = "UPDATE CheckinLaboral 
                              SET     $Campo='" . $Valor . "', 
                                      $CampoMovimientoLat='" . $Latitud . "', 
                                      $CampoMovimientoLon= '" . $Longitud . "', 
                                      $CampoMovimiento='" . $ValorMovimiento . "', 
                                      $CampoMovimientoFecha='" . $FechaActual . "', 
                                      UltimoMovimiento='" . $UltimoMovimiento . "',
                                      Estado = 1,
                                      " . $actualizo_salida_sin_dato . "
                                      UsuarioTrEd='WS-$Campo-$Valor', 
                                      ObservacionSalida='" . $Observaciones . "',
                                      FechaTrEd=NOW()
                                   
                                WHERE  IDCheckinLaboral    = '" . $IDCheck . "'";
                        $dbo->query($SQLUpdate);
                    }
                } else

                    //CHECKIN DESPUES DEL TURNO      
                    if (!empty($EntradaDespuesDeTurno)) {

                        // SACAMOS SI FUE UNA SALIDA O UNA ENTRADA PARA GUARDAR
                        if ($EntradaDespuesDeTurno == 'N') :
                            $CampoMovimiento = "Salida";
                            $CampoMovimientoLat = "LatitudSalida";
                            $CampoMovimientoLon = "LongitudSalida";
                            $CampoMovimientoFecha = "FechaMovimientoSalida";
                            $CampoMovimientoFechaDespuesDelTurno = "FechaMovimientoSalidaDespuesDelTurno";
                            $ValorMovimiento = 'S';
                            $UltimoMovimiento = "S";
                            $actualizo_salida_sin_dato = "";
                        else :
                            $CampoMovimiento = "Entrada";
                            $CampoMovimientoLat = "LatitudEntrada";
                            $CampoMovimientoLon = "LongitudEntrada";
                            $CampoMovimientoFecha = "FechaMovimientoEntrada";
                            $CampoMovimientoFechaDespuesDelTurno = "FechaMovimientoEntradaDespuesDelTurno";
                            $ValorMovimiento = 'S';
                            $UltimoMovimiento = "E";
                            $actualizo_salida_sin_dato = " FechaMovimientoSalida = '', Salida='', LatitudSalida='', LongitudSalida = '', ";
                        endif;


                        //Revisamos si el registro ya tiene un movimiento en el dia
                        $sql_mov_despues_del_turno = "SELECT IDCheckinLaboral,Entrada,Salida,FechaMovimientoSalida FROM CheckinLaboralHorasExtras WHERE " . $Campo . " = '" . $Valor . "' and FechaTrCr >= '" . $Hoy . "'  LIMIT 1";
                        $r_query_despues_del_turno = $dbo->query($sql_mov_despues_del_turno);
                        $movimiento_despues_del_turno = $dbo->fetchArray($r_query_despues_del_turno);
                        $DatosMovimiento_despues_del_turno = $movimiento_despues_del_turno;

                        // revisamos los movimientos despues del turno para que todas las horas sean como extras
                        $sql_mov2 = "SELECT FechaMovimientoEntrada,IDCheckinLaboral,UltimoMovimiento FROM CheckinLaboralHorasExtras WHERE " . $Campo . " = '" . $Valor . "' AND UltimoMovimiento='E' and DATE(FechaTrCr) = '" . $Hoy . "'";

                        $r_query2 = $dbo->query($sql_mov2);
                        $movimiento2 = $dbo->fetchArray($r_query2);
                        $DatosMovimiento2 = $movimiento2;



                        if ($DatosMovimiento2["UltimoMovimiento"] == "E") {

                            $IDCheck = $DatosMovimiento2["IDCheckinLaboral"];
                            $SQLUpdate2 = "UPDATE CheckinLaboralHorasExtras 
                                  SET     $Campo='" . $Valor . "', 
                                          $CampoMovimientoLat='" . $Latitud . "', 
                                          $CampoMovimientoLon= '" . $Longitud . "', 
                                          $CampoMovimiento='" . $ValorMovimiento . "', 
                                          $CampoMovimientoFecha='" . $FechaActual . "', 
                                          UltimoMovimiento='" . $UltimoMovimiento . "',
                                          Estado = 1,
                                          " . $actualizo_salida_sin_dato . "
                                          UsuarioTrEd='WS-$Campo-$Valor', 
                                          ObservacionSalida='" . $Observaciones . "',
                                          FechaTrEd=NOW()
                                       
                                    WHERE  IDCheckinLaboral    = '" . $IDCheck . "'";
                            /* echo $SQLUpdate2; */
                            // echo "Entro el if";
                            $dbo->query($SQLUpdate2);
                        } else {
                            $SQLInsert2 = "INSERT INTO CheckinLaboralHorasExtras    (IDClub,IDSitioTrabajo, $Campo, $CampoMovimientoLat, $CampoMovimientoLon, $CampoMovimiento, $CampoMovimientoFecha, UltimoMovimiento,Estado, ObservacionEntrada,HoraEntradaEstablecida,HoraSalidaEstablecida,UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd) 
                                VALUES  ('$IDClub','$IDSitioTrabajo','$Valor','$Latitud','$Longitud','$ValorMovimiento','$FechaActual','" . $UltimoMovimiento . "',1,'$Observaciones','$HoraEntradaEstablecida','$HoraSalidaEstablecida','WS-$Campo-$Valor','$FechaActual','WS-$Campo-$Valor','$FechaActual')";
                            $dbo->query($SQLInsert2);
                            //echo "Entro el else";
                        }
                    }


                //MENSAJE AL GUARDAR
                if ($CampoMovimiento == "Entrada") {
                    if (!empty($datos_config_checkin["MensajeGuardarEntrada"])) {
                        $MensajeGuardar = $datos_config_checkin["MensajeGuardarEntrada"];
                    } else {
                        $MensajeGuardar = $CampoMovimiento . " " . SIMUtil::get_traduccion('', '', 'Registradaconexito', LANG) . "!";
                    }
                } else {
                    if (!empty($datos_config_checkin["MensajeGuardarSalida"])) {
                        $MensajeGuardar = $datos_config_checkin["MensajeGuardarSalida"];
                    } else {
                        $MensajeGuardar = $CampoMovimiento . " " . SIMUtil::get_traduccion('', '', 'Registradaconexito', LANG) . "!";
                    }
                }



                $respuesta["message"] = $MensajeGuardar;
                $respuesta["success"] = true;
                $respuesta["response"] = "";
            }
        else :

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'FaltanParametrosparaelcheck-in', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = "";

        endif;

        return $respuesta;
    }


    public function get_mi_checking_laboral($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDClub)) :

            $FechaActual = date("Y-m-d 00:00:00");
            $Hoy = date("Y-m-d");

            // SABER SI ES UN USUARIO (EMPLEADO) O ES UN SOCIO
            if ($IDUsuario > 0) :
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
            else :
                $Campo = "IDSocio";
                $Valor = $IDSocio;
            endif;

            // $SQLUltimoMovimiento = "SELECT Entrada,Salida,UltimoMovimiento FROM CheckinLaboral WHERE $Campo = $Valor AND FechaMovimiento >= '" . $FechaActual . "' ORDER BY FechaMovimiento DESC LIMIT 1";
            $SQLUltimoMovimiento = "SELECT Entrada,Salida, UltimoMovimiento,FechaMovimientoEntrada FROM CheckinLaboral WHERE $Campo = $Valor AND DATE(FechaMovimientoEntrada)='" . $Hoy . "'  ORDER BY FechaMovimientoEntrada DESC LIMIT 1";
            $QRYUltimoMovimiento = $dbo->query($SQLUltimoMovimiento);
            $DatosMovimiento = $dbo->fetchArray($QRYUltimoMovimiento);
            // echo $SQLUltimoMovimiento;

            // SI EXISTEN DATOS ACTIVAMOS LA SALIDA SI NO ACTIVAMOS LA ENTRADA 
            if ($dbo->rows($QRYUltimoMovimiento) > 0) :
                if ($DatosMovimiento["UltimoMovimiento"] == "E") {
                    $Data[ActivarIconoSalida] = 'S';
                    $Data[ActivarIconoEntrada] = 'N';
                } else {
                    $Data[ActivarIconoSalida] = 'N';
                    $Data[ActivarIconoEntrada] = 'N';
                }

            else :
                $Data[ActivarIconoSalida] = 'N';
                $Data[ActivarIconoEntrada] = 'S';
            endif;




            // SI EXISTEN DATOS ACTIVAMOS LA SALIDA SI NO ACTIVAMOS LA ENTRADA 1 que se creo
            /*             if ($dbo->rows($QRYUltimoMovimiento) > 0) :
                if ($DatosMovimiento["UltimoMovimiento"] == "E") {
                    $Data[ActivarIconoSalida] = 'S';
                    $Data[ActivarIconoEntrada] = 'N';
                } else {
                    $Data[ActivarIconoSalida] = 'N';
                    $Data[ActivarIconoEntrada] = 'S';
                }

            else :
                $Data[ActivarIconoSalida] = 'N';
                $Data[ActivarIconoEntrada] = 'S';
            endif; */


            //anterior
            /*      if ($dbo->rows($QRYUltimoMovimiento) > 0) {
                if ($DatosMovimiento["UltimoMovimiento"] == "E") {
                    $Data[ActivarIconoSalida] = 'S';
                    $Data[ActivarIconoEntrada] = 'N';
                } else if ($DatosMovimiento["UltimoMovimiento"] == "S" && $DatosMovimiento["FechaMovimientoEntrada"] == "0000-00-00 00:00:00") {
                    $Data[ActivarIconoSalida] = 'N';
                    $Data[ActivarIconoEntrada] = 'S';
                } else if ($DatosMovimiento["UltimoMovimiento"] == "S" && $DatosMovimiento["FechaMovimientoEntrada"] > "0000-00-00 00:00:00") {


                    $SQLUltimoMovimientoDespuesDeTurno = "SELECT Entrada,Salida, UltimoMovimiento,FechaMovimientoEntrada FROM CheckinLaboralHorasExtras WHERE $Campo = $Valor ORDER BY FechaTrCr DESC LIMIT 1";
                    $QRYUltimoMovimientoDespuesDeTurno = $dbo->query($SQLUltimoMovimientoDespuesDeTurno);
                    $DatosMovimientoDespuesDeTurno = $dbo->fetchArray($QRYUltimoMovimientoDespuesDeTurno);
                    if ($dbo->rows($QRYUltimoMovimientoDespuesDeTurno) > 0) {
                        if ($DatosMovimientoDespuesDeTurno["UltimoMovimiento"] == "E") {
                            $Data[ActivarIconoSalida] = 'S';
                            $Data[ActivarIconoEntrada] = 'N';
                        } else {
                            $Data[ActivarIconoSalida] = 'N';
                            $Data[ActivarIconoEntrada] = 'S';
                        }
                    } else {
                        $Data[ActivarIconoSalida] = 'N';
                        $Data[ActivarIconoEntrada] = 'S';
                    }
                }
            } else {
                $Data[ActivarIconoSalida] = 'N';
                $Data[ActivarIconoEntrada] = 'S';
            } */



            // SI EXISTEN DATOS ACTIVAMOS LA SALIDA SI NO ACTIVAMOS LA ENTRADA  CHECKIN DESPUES DEL TURNO
            $SQLUltimoMovimiento_despues_de_turno = "SELECT Entrada,Salida, UltimoMovimiento,FechaMovimientoEntrada FROM CheckinLaboralHorasExtras WHERE $Campo = $Valor AND DATE(FechaMovimientoEntrada)='" . $Hoy . "'  ORDER BY FechaMovimientoEntrada DESC LIMIT 1";
            $QRYUltimoMovimiento_despues_de_turno = $dbo->query($SQLUltimoMovimiento_despues_de_turno);
            $DatosMovimiento_despues_de_turno = $dbo->fetchArray($QRYUltimoMovimiento_despues_de_turno);

            //echo $SQLUltimoMovimiento_despues_de_turno;

            if ($dbo->rows($QRYUltimoMovimiento_despues_de_turno) > 0) :
                if ($DatosMovimiento_despues_de_turno["UltimoMovimiento"] == "E") {
                    $Data[ActivarIconoTurnoExtraSalida] = 'S';
                    $Data[ActivarIconoTurnoExtraEntrada] = 'N';
                } else {
                    $Data[ActivarIconoTurnoExtraSalida] = 'N';
                    $Data[ActivarIconoTurnoExtraEntrada] = 'S';
                }

            else :
                $Data[ActivarIconoTurnoExtraSalida] = 'N';
                $Data[ActivarIconoTurnoExtraEntrada] = 'S';
            endif;




            //Averiguo el horario

            $SQLConfiguracion = "SELECT * FROM ConfiguracionCheckinLaboral WHERE IDClub = $IDClub AND Activo = 'S'";
            $QRYConfiguracion = $dbo->query($SQLConfiguracion);
            $Datos = $dbo->fetchArray($QRYConfiguracion);
            $PermiteRecordatorioIngreso = $Datos["PermiteRecordatorioIngreso"];
            $PermiteRecordatorioSalida = $Datos["PermiteRecordatorioSalida"];
            $MinutosAntes = $Datos["MinutosAntesRecordatorio"];
            $MensajeRecordatorioIngreso = $Datos["MensajeRecordatorioIngreso"];
            $TituloRecordatorioIngreso = $Datos["TituloRecordatorioIngreso"];
            $MensajeRecordatorioSalida = $Datos["MensajeRecordatorioSalida"];
            $TituloRecordatorioSalida = $Datos["TituloRecordatorioSalida"];



            $sql_horario = "SELECT HoraInicioLaboral, HoraFinalLaboral FROm Socio Where IDSocio = '" . $IDSocio . "' ";
            $r_horario = $dbo->query($sql_horario);
            $row_horario = $dbo->fetchArray($r_horario);

            if ($row_horario["HoraInicioLaboral"] != "00:00:00" && $PermiteRecordatorioIngreso == "S") {
                //Hora inicio recordatorio    
                $FechaHora = date('Y-m-d') . " " . $row_horario["HoraInicioLaboral"];
                $mihora = date($FechaHora);
                $NuevaHora = strtotime('-' . $MinutosAntes . ' minute', strtotime($mihora));
                $HoraInicioRecordatorio = date("H:i:s", $NuevaHora);
                $notif_local_ingreso["Dias"] = array("L", "M", "X", "J", "V");
                $notif_local_ingreso["Hora"] = $HoraInicioRecordatorio;
                $notif_local_ingreso["Titulo"] = $TituloRecordatorioIngreso;
                $notif_local_ingreso["Mensaje"] = $MensajeRecordatorioIngreso;
                $Data["NotificacionCheckIn"] = $notif_local_ingreso;
            }

            if ($row_horario["HoraFinalLaboral"] != "00:00:00" && $PermiteRecordatorioSalida == "S") {
                //Hora Fin recordatorio    
                $FechaHora = date('Y-m-d') . " " . $row_horario["HoraFinalLaboral"];
                $mihora = date($FechaHora);
                $NuevaHora = strtotime('-' . $MinutosAntes . ' minute', strtotime($mihora));
                $HoraFinRecordatorio = date("H:i:s", $NuevaHora);
                $notif_local_salida["Dias"] = array("L", "M", "X", "J", "V");
                $notif_local_salida["Hora"] = $HoraFinRecordatorio;
                $notif_local_salida["Titulo"] = $TituloRecordatorioSalida;
                $notif_local_salida["Mensaje"] = $MensajeRecordatorioSalida;
                $Data["NotificacionCheckOut"] = $notif_local_salida;
            }




            array_push($response, $Data);

            $respuesta["message"] = "Movimiento";
            $respuesta["success"] = true;
            $respuesta["response"] = $Data;

        else :

            $respuesta["message"] = "Faltan parametros para ver los datos";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

        endif;

        return $respuesta;
    }

    public function set_checkin_funcionarios($IDClub, $IDUsuario, $Tipo)
    {
        $dbo = SIMDB::get();
        $mov = $Tipo == 'E' ? 'un ingreso' : 'una salida';
        $hoy = date("Y-m-j");
        $now = date("Y-m-j H:i:s");
        $error = "Error!, el usuario ya ha registrado $mov";

        $idPlanificacionDiaria = $dbo->getFields("PlanificacionDiaria", "IDPlanificacionDiaria", "IDClub = $IDClub AND Activo = 'S' AND IDUsuario = $IDUsuario AND Fecha = '$hoy'");
        if (!$idPlanificacionDiaria)
            $idPlanificacionDiaria = 0;

        $sqlMov = "SELECT IDCheckinFuncionarios, Entrada, Salida, UltimoMovimiento 
                    FROM CheckinFuncionarios
                    WHERE IDUsuario = $IDUsuario AND DATE(FechaTrCr) = DATE('$hoy')";
        $resultMov = $dbo->query($sqlMov);
        $movimiento = $dbo->fetchArray($resultMov);

        //revisa si existen movimientos en el dia 
        if ($dbo->rows($resultMov) > 0) {

            //si existen movimientos valida que el movimiento que va a ingresar no sea igual al ultimo movimiento registrado
            if ($movimiento['UltimoMovimiento'] != $Tipo) {

                $arrCheck = [
                    "UltimoMovimiento" => $Tipo,
                    "UsuarioTrEd" => SIMUser::get("Nombre"),
                    "FechaTrEd" => $now
                ];

                if ($Tipo == 'S') {
                    $arrCheck['Salida'] = 1;
                    $arrCheck['FechaSalida'] = $now;
                }

                $idCheck = $dbo->update($arrCheck, 'CheckinFuncionarios', 'IDCheckinFuncionarios', $movimiento['IDCheckinFuncionarios']);
            }
            // else {
            //     $message = $error;
            // }
        } else {

            //si no hay movimientos valida que el dato que se va a ingresar sea una entrada
            if ($Tipo == 'E') {
                $arrCheck = [
                    "IDClub" => $IDClub,
                    "IDUsuario" => $IDUsuario,
                    "IDPlanificacionDiaria" => $idPlanificacionDiaria,
                    "Entrada" => 1,
                    "FechaEntrada" => $now,
                    "Estado" => '1',
                    "UltimoMovimiento" => $Tipo,
                    "UsuarioTrCr" => SIMUser::get("Nombre"),
                    "FechaTrCr" => $now
                ];

                $idCheck = $dbo->insert($arrCheck, 'CheckinFuncionarios', 'IDCheckinFuncionarios');
            }
            // else {
            //     $message = "Error!, el usuario no tiene un ingreso registrado";
            // }
        }

        return true;
    }
}
