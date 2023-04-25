<?php
    class SIMWebServiceNuba
    {
        public function get_configuracion_reservas($IDClub, $IDModulo)
        {
            $dbo = SIMDB::get();
            $response = array();

            if(!empty($IDClub)):

                if(!empty($IDModulo)){
                    $Condicion=" and IDModulo = '".$IDModulo."'";
                }
                else{
                    $Condicion=" and IDModulo = 0";
                }

                $sql = "SELECT LabelHeaderHorario, LabelHeaderResumen, LabelBotonSeleccionarHijo, LabelHeaderSeleccionarHijo, LabelHeaderSeleccionHijo,
                                LabelBotonReservar, LabelBotonMisReservas, LabelBotonEliminarReserva,OcultarFiltroHijo
                            FROM ConfiguracionReservaHorario WHERE IDClub = $IDClub " . $Condicion;
                $qry = $dbo->query($sql);

                if($dbo->rows($qry) > 0):

                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    while($row = $dbo->fetchArray($qry)):

                        $result["LabelHeaderHorario"] = $row["LabelHeaderHorario"];
                        $result["LabelHeaderResumen"] = $row["LabelHeaderResumen"];
                        $result["LabelBotonSeleccionarHijo"] = $row["LabelBotonSeleccionarHijo"];
                        $result["LabelHeaderSeleccionarHijo"] = $row["LabelHeaderSeleccionarHijo"];
                        $result["LabelHeaderSeleccionHijo"] = $row["LabelHeaderSeleccionHijo"];
                        $result["LabelBotonReservar"] = $row["LabelBotonReservar"];
                        $result["LabelBotonMisReservas"] = $row["LabelBotonMisReservas"];
                        $result["LabelBotonEliminarReserva"] = $row["LabelBotonEliminarReserva"];
                        $result["OcultarFiltroHijo"] = $row["OcultarFiltroHijo"];

                    endwhile;

                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $result;

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

        public function get_hijos_reserva($IDClub, $IDSocio, $IDModulo)
        {
            $dbo = SIMDB::get();
            $response = array();

            if(!empty($IDClub) && !empty($IDSocio)):

                $accion = $dbo->getFields("Socio","Accion","IDSocio = $IDSocio");

                $sql = "SELECT IDSocio, CONCAT(Nombre, ' ' ,Apellido) as Nombre
                        FROM Socio
                        WHERE AccionPadre = '$accion' AND LOWER(TipoSocio) = 'HIJO' AND IDClub = $IDClub";
                $qry = $dbo->query($sql);

                if($dbo->rows($qry) > 0):

                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    while($row = $dbo->fetchArray($qry)):

                        $result["IDHijo"] = $row["IDSocio"];
                        $result["Nombre"] = $row["Nombre"];

                        array_push($response, $result);
                    endwhile;

                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;

                else:
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Notienehijosregistrados', LANG);
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

        public function get_servicios_reserva($IDClub, $IDSocio, $IDHijo, $IDModulo)
        {
            $dbo = SIMDB::get();
            $response = array();
            $arrServicios = array();
            $ids = 0;
            $hoy = new DateTime();

            if(!empty($IDModulo)){
                $Condicion=" and IDModulo = '".$IDModulo."'";                
                $CondicionServiciosJardin=" and s.IDServicio in (39576)";                
            }
            else{
                $Condicion=" and IDModulo = 0";
                $IDServiciosJardin="39573,39574,39575";
                $CondicionServiciosJardin=" and s.IDServicio in (39573,39574,39575)";
            }
            $sql = "SELECT OcultarFiltroHijo
                            FROM ConfiguracionReservaHorario WHERE IDClub = $IDClub " . $Condicion . " Limit 1";
                    $qry = $dbo->query($sql);
                    $row = $dbo->fetchArray($qry);


            if(!empty($IDClub) && !empty($IDSocio)):
                if( empty($IDHijo) && (empty($IDModulo) || $row["OcultarFiltroHijo"]=="N") ){
                    $respuesta["message"] = "Debe seleccionar un hijo: " .$IDModulo;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $soloIcono = $dbo->getFields("Club", "SoloIcono", "IDClub = $IDClub");
                $hijo = $dbo->getFields("Socio", array("FechaNacimiento","IDCategoria",), "IDSocio = $IDHijo");
                $FechaNacimiento = new DateTime($hijo['FechaNacimiento']);
                $diff = $FechaNacimiento->diff($hoy);
                $edadDias = $diff->days;

                //Busca las taloneras disponibles del socio titular o del hijo como beneficiario
                /*
                $sqlSocioTalonera = "SELECT IDTalonera, IDServicio, CantidadPendiente, TodosLosServicios
                                        FROM SocioTalonera
                                        WHERE (IDSocio = $IDSocio OR SociosPosibles LIKE '%$IDHijo%') AND Activo = 1 AND CantidadPendiente > 0
                                        ORDER BY FechaCompra ASC ";
                */                                        
                $fecha_actual= date("Y-m-d");
                $sqlSocioTalonera = "SELECT IDTalonera, IDServicio, CantidadPendiente, TodosLosServicios
                                        FROM SocioTalonera
                                        WHERE (SociosPosibles LIKE '%$IDHijo%') AND Activo = 1 AND CantidadPendiente > 0 and FechaVencimiento >= '$fecha_actual'
                                        ORDER BY FechaCompra ASC ";

                $qrySocioTalonera = $dbo->query($sqlSocioTalonera);

                
                while ($rowSocioTalonera = $dbo->fetchArray($qrySocioTalonera)):

                    $idServicio = $rowSocioTalonera['IDServicio'];
                    $cupos = (int)$rowSocioTalonera['CantidadPendiente'];

                    //agrega el servicio que esta en SocioTalonera si no es 0
                    if($idServicio > 0):
                        if(array_key_exists($idServicio, $arrServicios))
                            $arrServicios[$idServicio] = $arrServicios[$idServicio] + $cupos;
                        else
                            $arrServicios[$idServicio] = $cupos;
                    endif;

                    //valida si es una talonera para todos los servicios
                    if($rowSocioTalonera['TodosLosServicios'] == 1):
                        //consulta los servicios disponibles del club
                        $sqlServicios = "SELECT s.IDServicio FROM ServicioClub as sc, Servicio as s WHERE sc.IDServicioMaestro = s.IDServicioMaestro AND s.IDServicio != $idServicio AND sc.IDClub = $IDClub AND s.IDClub = $IDClub AND sc.Activo = 'S'";
                    else:
                        //si no es para todos los servicios consulta los servicios asignados a la taloneraarray_key_exists
                        $sqlServicios = "SELECT IDServicio FROM TaloneraServicios WHERE IDServicio != $idServicio AND IDTalonera = ". $rowSocioTalonera['IDTalonera'];
                    endif;

                    $qryServicios = $dbo->query($sqlServicios);
                    while ($rowServicios = $dbo->fetchArray($qryServicios)):
                        if(array_key_exists($rowServicios['IDServicio'], $arrServicios))
                            $arrServicios[$rowServicios['IDServicio']] = $arrServicios[$rowServicios['IDServicio']] + $cupos;
                        else
                            $arrServicios[$rowServicios['IDServicio']] = $cupos;

                    endwhile;

                endwhile;

                if (count($arrServicios) > 0):
                    $ids = implode(",", array_keys($arrServicios));
                endif;

                //consulta los servicios a los que puede acceder el usuario, teniendo en cuenta los servicios obtenidos por la consulta de taloneras
                $sql = "SELECT s.IDServicioMaestro, s.IDServicio, IF(sc.TituloServicio = '', sm.Nombre, sc.TituloServicio) as Nombre , IF(s.Icono = '', sm.Icono, s.Icono) as Icono,
                               s.ValidarEdad, s.EdadMinima, s.EdadMaxima, s.MinimoHorasSeguidas
                        FROM Servicio as s, ServicioClub as sc, ServicioMaestro as sm
                        WHERE
                            s.IDServicioMaestro = sm.IDServicioMaestro AND
                            sm.IDServicioMaestro = sc.IDServicioMaestro AND
                            s.IDServicio IN ($ids) AND sc.IDClub = $IDClub AND                              
                            sc.IDClub = $IDClub AND sc.Activo = 'S' ".$CondicionServiciosJardin."
                        ORDER BY sc.Orden, IF(sc.TituloServicio = '', sm.Nombre, sc.TituloServicio)";

                $qry = $dbo->query($sql);

                while($row = $dbo->fetchArray($qry)):

                    $add = true;

                    $result = [
                        "IDClub" => $IDClub,
                        "IDServicio" => $row["IDServicio"],
                        "Nombre" => $row["Nombre"],
                        "Icono" => SERVICIO_ROOT . $row["Icono"],
                        "SoloIcono" => $soloIcono,
                        "MaximoHoras" => $arrServicios[$row["IDServicio"]],
                        "MinimoHorasAlDia" => $row["MinimoHorasSeguidas"],
                        "MaximoHorasAlDia" => 0,
                        "MinimoHorasSeguidas" => $row["MinimoHorasSeguidas"]
                    ];

                    if($row['ValidarEdad'] == 'S'):
                        $add = false;
                        //valida la edad del hijo en dias
                        $edadMin = (int)$row['EdadMinima'] * 365;
                        $edadMax = (int)$row['EdadMaxima'] * 365;

                        //si el hijo no tiene una categoria es porque aun no puede acceder a ningun servicio
                        if($hijo['IDCategoria'] > 0):
                            $edades = $dbo->getFields("Categoria", "Edad", "IDCategoria = ".$hijo['IDCategoria']);
                            $arrEdades = explode('|', substr($edades,1,-1));
                            $edadIn = round((int)$arrEdades[0] * 30.4167);
                            $edadFn = round((int)end($arrEdades) * 30.4167);

                            if($edadIn >= $edadMin && $edadFn <= $edadMax)
                                $add = true;
                        endif;

                    endif;

                    if($add):

                        $repetir = $dbo->fetchAll("ConfiguracionRepetirReserva", "IDServicio = ".$row["IDServicio"]." AND IDClub = $IDClub", "array");

                        $result['PermiteSeleccionarRepetir'] = $repetir ? $repetir['PermiteSeleccionarRepetir'] : 'N';
                        $result['TextoTituloSeleccionarRepetir'] = $repetir ? $repetir['TextoTituloSeleccionarRepetir'] : '';
                        $result['TextoSeleccionarRepetir'] = $repetir ? $repetir['TextoSeleccionarRepetir'] : '';
                        $result['SemanasSeguidasARepetir'] = $repetir ? $repetir['SemanasSeguidasARepetir'] : '';
                        $result['MensajeConfirmacionRepetir'] = $repetir ? $repetir['MensajeConfirmacionRepetir'] : '';
                        $result['MensajeConfirmacionEliminarRepetir'] = $repetir ? $repetir['MensajeConfirmacionEliminarRepetir'] : '';
                        $result['MensajeNoPuedeSeleccionarRepetirActivo'] = $repetir ? $repetir['MensajeNoPuedeSeleccionarRepetirActivo'] : '';

                        array_push($response, $result);
                    endif;

                endwhile;

                if(count($response) > 0):

                    $respuesta["message"] = count($response) . " ".SIMUtil::get_traduccion('', '', 'Encontrados',LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;

                else:

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Notieneserviciosdisponibles', LANG);
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

        public function get_elementos_reservas($IDClub, $IDSocio, $IDHijo, $IDServicio, $IDModulo)
        {

            $dbo = SIMDB::get();
            setlocale(LC_TIME, 'es_ES.UTF-8');

            $response = array();
            $today = date("Y-m-d");
            $diasTxt = SIMResources::$dias_semana;
            $date = $today;
            $fechaInicio = $today;
            $disponibilidades = array();

            if(!empty($IDClub) && !empty($IDSocio) && !empty($IDServicio)):

                //Obtiene la hora minima, la hora maxima y el intervalo minimo para hacer el recorrido
                $sqlMaxMin = "SELECT MIN(sd.HoraDesde) as HoraDesde, MAX(sd.HoraHasta) as HoraHasta, MIN(d.Intervalo) as Intervalo
                                FROM ServicioDisponibilidad as sd, Disponibilidad as d
                                WHERE sd.IDServicio = $IDServicio AND sd.IDDisponibilidad = d.IDDisponibilidad";
                $qryMaxMin = $dbo->query($sqlMaxMin);
                $rowMaxMin = $dbo->fetchArray($qryMaxMin);

                $horaDesde = $rowMaxMin['HoraDesde'];
                $horaHasta = $rowMaxMin['HoraHasta'];
                $intervalo = $rowMaxMin['Intervalo'];
                $intervaloSeg = $intervalo * 60;

                //Busca todas las disponibilidades para crear el arreglo con las horas habilitadas y las que no
                $sql = "SELECT IDDia, IDServicioElemento, HoraDesde, HoraHasta
                        FROM ServicioDisponibilidad WHERE IDServicio = $IDServicio";
                $qry = $dbo->query($sql);

                while($row = $dbo->fetchArray($qry)):
                    $disponibilidades[] = $row;
                endwhile;

                //Obtiene el numero de semanas que se van a mostrar 
                $numSemanas = $dbo->getFields("ConfiguracionReservaHorario","NumeroSemanas","IDClub = $IDClub");

                for($i=0; $i<$numSemanas; $i++):

                    $diasDisponibles = array();
                    $horasDisponibles = array();

                    $week = date("W",strtotime($date));
                    $dayW = date("Y-m-d", strtotime('last Monday',strtotime($date)));
                    
                    //recorre cada uno de los dias de la semana y busca su disponibilidad
                    for($j=1; $j<6; $j++):

                        //echo "aca " . $dayW = date("Y-m-d", strtotime('last Monday',strtotime($date)));
                        //$dayW = date('Y-m-d', strtotime('01/01 +' . ($week - 1) . ' weeks first day +'. $j .'day'));
                        $diaNum = date("d",strtotime($dayW));

                        if($j == 0)
                            $fechaInicio = $dayW;
                        
                        //recorre las disponibilidades para mostrar la del dia #j
                        foreach($disponibilidades as $disponibilidad):
                            
                            $dias = explode('|', $disponibilidad['IDDia'],-1);
                            $elementos = explode('|', substr($disponibilidad['IDServicioElemento'],1,-1));

                            $hora = $horaDesde;
                            $horaInicio = strtotime($disponibilidad['HoraDesde']);
                            $horaFin = strtotime($disponibilidad['HoraHasta']);

                            //recorre la hora minima a maxima de todas las disponibilidades y verifica que tiempo esta disponible avanzando segun el intervalo 
                            while (strtotime($hora) <= strtotime($horaHasta)):

                                $fechaCompleta = $dayW." ".$hora;
                                $hraDispnible = "S";

                                if(strtotime($fechaCompleta) <= strtotime(date("Y-m-d H:i:s")) || !in_array($j, $dias) || strtotime($hora) < $horaInicio || strtotime($hora) > $horaFin)
                                    $hraDispnible = "N";
                                
                                //busca fechaCompleta en horasDisponibles para que no se repita una hora en el mismo dia
                                $keyHora = array_search($fechaCompleta, array_column($horasDisponibles, 'FechaCompleta'));     
                                
                                //si la fecha se repite valida la disponibilidad, si esta disponible la va a dejar en "S"
                                if($keyHora !== false):

                                    if($horasDisponibles[$keyHora]['Disponible'] != $hraDispnible && $hraDispnible == "S"):
                                        $horasDisponibles[$keyHora]['Disponible'] = "S";
                                    endif;

                                else:
                                    //Si la fecha no existe en el arreglo de $horasDiponibles la agrega
                                    $horaDisponible = [
                                        "Hora" => $hora,
                                        "HoraVisual" => date("h:ia", strtotime($hora)),
                                        "DiaMes" => $diaNum,
                                        "Fecha" => $dayW,
                                        "FechaVisual" => ucfirst(strftime('%A %d de %B, %Y', strtotime($dayW))),
                                        "LabelNoDisponible" => "",//SIMUtil::get_traduccion('', '', 'nodisponible', LANG),
                                        "Disponible" => $hraDispnible,
                                        "HorasADescontar" => 1,
                                        "IDElemento" => $elementos[0],
                                        "FechaCompleta" => $fechaCompleta
                                    ];

                                    array_push($horasDisponibles, $horaDisponible);
                                
                                endif;  

                                //busca si la hora esta en un cierre
                                $cierre = $dbo->getFields("ServicioCierre","Descripcion","IDServicio = $IDServicio AND ('$dayW' BETWEEN FechaInicio AND FechaFin) AND ('$hora' BETWEEN HoraInicio AND HoraFin)");

                                if($cierre):
                                    //busca fechaCompleta en horasDisponibles para obtener el key a modificar
                                    $keyHora = array_search($fechaCompleta, array_column($horasDisponibles, 'FechaCompleta')); 

                                    $horasDisponibles[$keyHora]['LabelNoDisponible'] = $cierre;
                                    $horasDisponibles[$keyHora]['Disponible'] = 'N';
                                endif;

                                //aumenta segun el intervalo la hora actual
                                $hora = date("H:i:s", (strtotime($hora) + $intervaloSeg));
                                
                            endwhile;

                        endforeach;
                                       
                        $diaDisponible = [
                            "DiaTexto" => ucfirst(strftime("%a\n%d", strtotime($dayW))),
                            "DiaMes" => $diaNum
                        ];

                        array_push($diasDisponibles, $diaDisponible);                      
                        $dayW = date("Y-m-d",strtotime($dayW."+ 1 day"));

                    endfor;
              
                    if(!empty($diasDisponibles)):
                        $semana = [
                            "FechaInicio" => $fechaInicio,
                            "FechaInicioVisual" => strftime('%d de %B', strtotime($fechaInicio)),
                            "FechaFin" => $dayW,
                            "FechaFinVisual" => strftime('%d de %B', strtotime($dayW)),
                            "DiasSemanaHabilitados" => $diasDisponibles,
                            "Disponibilidad" => $horasDisponibles
                        ];

                        array_push($response, $semana);
                    endif;

                    $date = date("Y-m-d",strtotime($date."+ 1 week"));

                endfor;

                if(!empty($response)):

                    $respuesta["message"] = count($response) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;

                else:
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohaydisponibilidadparaestosservicios', LANG);
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

        public function set_reserva_horario($IDClub, $IDSocio, $IDHijo, $IDServicio, $Fechas, $IDModulo)
        {

            
            $dbo = SIMDB::get();
            setlocale(LC_TIME, 'es_ES.UTF-8');

            $response = array();
            $fechasOk = array();
            $errores = array();

            if(!empty($IDClub)  && !empty($IDSocio) && !empty($IDServicio) && !empty($Fechas)):

                if(empty($IDHijo) && empty($IDModulo)){
                    $respuesta["message"] = "aaa".SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $Fechas = json_decode($Fechas, true);

                foreach ($Fechas as $horario):
                    $Fecha = $horario['Fecha'];
                    $Hora = $horario['Hora'];
                    $IDElemento = $horario['IDElemento'];
                    $diaSemana = date("w",$Fecha);
                    $horaVisual = date("h:ia", strtotime($Hora));
                                                                 
                    $reserva = SIMWebService::set_reserva_generalV2($IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, "", "", "", "", "", "", "", "", "", "", "", "", "", "", $IDHijo, "Socio", "", "", "", "", "", "", "", "");

                    if($reserva['success']):
                    
                        if(array_key_exists($Fecha, $fechasOk))
                            $fechasOk[$Fecha] .= ", ";

                        $fechasOk[$Fecha] .= $horaVisual;

                    else:
                        if(array_key_exists($Fecha, $errores))
                            $errores[$Fecha] .= ", ";

                        $errores[$Fecha] .= $horaVisual.": ".$reserva['message'];
                    endif;

                endforeach;

                if(count($fechasOk) > 0){

                    uksort($fechasOk,function($a,$b){
                        return strtotime($a) > strtotime($b) ? 1 : 0;
                    });

                    $message = "Reservas exitosas para las fechas:\n";

                    foreach($fechasOk as $fecha => $horas):
                        $fechaVisual = ucfirst(strftime('%a %d %b, %Y', strtotime($fecha)));
                        $message .= "* ".$fechaVisual.":\n ".$horas."\n";
                    endforeach;
                }

                if(count($errores) > 0){
                    uksort($errores,function($a,$b){
                        return strtotime($a) > strtotime($b) ? 1 : 0;
                    });

                    $message .= "Error al reservar en las fechas:\n";

                    foreach($errores as $fechaErr => $horasErr):
                        $fechaVisualErr = ucfirst(strftime('%a %d %b, %Y', strtotime($fechaErr)));
                        $message .= "* ".$fechaVisualErr.":\n ".$horasErr."\n";
                    endforeach;
                }

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else:

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG) . "SE:".$IDServicio." FEC " . $Fechas;
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;
        }

        public function get_mis_reservas_horario($IDClub, $IDSocio, $IDModulo)
        {
            $dbo = SIMDB::get();
            setlocale(LC_TIME, 'es_ES.UTF-8');

            $response = array();
            $hijos = array();
            $idsHijos = 0;
            $hoy = date("Y-m-d");

            if(!empty($IDSocio)):

                $socio = $dbo->getFields("Socio",array("Accion","CONCAT(Nombre, ' ' ,Apellido) as Nombre"),"IDSocio = $IDSocio");

                $sql = "SELECT IDReservaGeneral, IDServicio, IDServicioElemento,AccionBeneficiario,NombreBeneficiario, Fecha, Hora
                        FROM ReservaGeneral
                        WHERE
                            (IDSocio in ($IDSocio) OR IDSocioReserva = $IDSocio OR AccionBeneficiario = '".$socio['Accion']."') AND
                            IDClub = $IDClub AND
                            IDEstadoReserva = 1 AND
                            Fecha >= CURDATE()
                        ORDER BY Fecha ASC, Hora ASC";

                $qry = $dbo->query($sql);

                if($dbo->rows($qry) > 0):

                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                    while($row = $dbo->fetchArray($qry)):

                        $result["IDClub"] = $IDClub;
                        $result["IDSocio"] = $IDSocio;
                        $result["Socio"] = $socio['Nombre'];
                        $result["IDReserva"] = $row['IDReservaGeneral'];
                        $result["IDServicio"] = $row["IDServicio"];

                        $servicio = $dbo->fetchAll("Servicio", " IDServicio = ".$row["IDServicio"], "array");
                        $idServicioMaestro = $servicio["IDServicioMaestro"];

                        $iconoServicio = $servicio["Icono"];
                        $foto = "";

                        if (!empty($iconoServicio)):
                            $foto = SERVICIO_ROOT . $iconoServicio;
                        else:
                            $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = $id_servicio_maestro");
                            if (!empty($icono_maestro))
                                $foto = SERVICIO_ROOT . $icono_maestro;

                        endif;

                        $result["Icono"] = $foto;

                        $nombreServicio = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = $IDClub AND IDServicioMaestro = $idServicioMaestro");
                        if (empty($nombreServicio))
                            $nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $idServicioMaestro");

                        $result["NombreServicio"] = $nombreServicio;
                        $result["IDElemento"] = $row["IDServicioElemento"];
                        $result["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = " . $row["IDServicioElemento"]);
                        $result["Fecha"] = $row["Fecha"];
                        $result["Hora"] = $row["Hora"];

                        $strTime = strtotime($row["Fecha"]." ".$row["Hora"]);
                        $result["FechaVisual"] = ucfirst(strftime('%A %d de %B, %Y', $strTime));
                        $result["HoraVisual"] = date("h:i a", $strTime);
                        $result["IDHijo"] = $dbo->getFields("Socio","IDSocio","Accion = '".$row['AccionBeneficiario']."'");
                        $result["NombreHijo"] = $row['NombreBeneficiario'];

                        array_push($response, $result);
                    endwhile;

                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;

                else:

                    
                    //array_push($response, $reserva);

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Notienereservasregistradas', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = array();
                    return $respuesta;
                    
                    
                endif;
            else:

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;
        }

        //Funcion para verificar reglas previo a realizar una reserva.
        public function set_verificar_horario($IDClub, $IDSocio, $IDHijo, $IDServicio, $Fechas, $IDModulo)
        {
            $dbo = SIMDB::get();

            if(!empty($IDClub) && !empty($IDSocio) && !empty($IDServicio) && !empty($Fechas)):

                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else:

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;
        }
    }

?>
