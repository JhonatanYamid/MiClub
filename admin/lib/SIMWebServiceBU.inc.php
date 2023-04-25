public function elimina_reserva_general($IDClub, $IDSocio, $IDReserva, $Admin = "", $Razon = "", $EliminarParaMi="")
{

$dbo = &SIMDB::get();

require_once LIBDIR . "SIMServicioReserva.inc.php";



if (!empty($IDClub) && !empty($IDSocio) && !empty($IDReserva)) {

//verifico que el socio exista y pertenezca al club
$id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
$datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
$datos_servicio = $dbo->fetchAll("Servicio", " IDServicio= '" . $datos_reserva["IDServicio"] . "' ", "array");

//verifico si el servicio está configurado para preguntar algun medio de pago
$ServicioPago="N";
$sql_tip_pag="SELECT IDServicioTipoPago FROM ServicioTipoPago WHERE IDServicio = '".$datos_reserva["IDServicio"]."' Limit 1";
$r_tip_pag=$dbo->query($sql_tip_pag);
while($row_tip_pag=$dbo->fetchArray($r_tip_pag)){
$ServicioPago="S";
//Verifico si la reserva tiene alguna forma de pago si no es que le dio cancelar sin ir a la pasarela
if((int)$datos_reserva["IDTipoPago"]<=0 || $IDClub==88){ $PermiteEliminar="S" ; } } if (!empty($id_socio)) { //Verifico que este en el tiempo limite para reservar $id_disponibilidad=(int) $datos_reserva["IDDisponibilidad"]; if ($id_disponibilidad> 0):
    $tiempo_cancelacion = (int) $dbo->getFields("Disponibilidad", "TiempoCancelacion", "IDDisponibilidad = '" . $id_disponibilidad . "'");
    $medicion_cancelacion = $dbo->getFields("Disponibilidad", "MedicionTiempo", "IDDisponibilidad = '" . $id_disponibilidad . "'");
    switch ($medicion_cancelacion):
    case "Dias":
    $minutos_anticipacion = (60 * 24) * $tiempo_cancelacion;
    break;
    case "Horas":
    $minutos_anticipacion = 60 * $tiempo_cancelacion;
    break;
    case "Minutos":
    $minutos_anticipacion = $tiempo_cancelacion;
    break;
    default:
    $minutos_anticipacion = 2;

    endswitch;
    else:
    $tiempo_cancelacion = 2;
    $medicion_cancelacion = "Horas";
    $minutos_anticipacion = 120;
    endif;

    $fecha_reserva = $datos_reserva["Fecha"];
    $hora_reserva = $datos_reserva["Hora"];
    $aux_reserva = $datos_reserva["IDAuxiliar"];
    $id_servicio = $datos_reserva["IDServicio"];

    //Especial Country para reservas de cancha en cualquier momento si es con profesor segun configuracion
    if (($IDClub == 44 || $IDClub == 8) && empty($aux_reserva) && $id_servicio == 3941):
    $tiempo_cancelacion = 0;
    $medicion_cancelacion = "minutos";
    $minutos_anticipacion = 0;
    endif;
    //FIN ESPECIAL country

    $hora_inicio_reserva = strtotime('-' . $minutos_anticipacion . ' minute', strtotime($fecha_reserva . " " . $hora_reserva));
    $fechahora_actual = strtotime(date("Y-m-d H:i:s"));

    //$id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $id_servicio . "'");
    $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
    //$envia_push_eliminacion = $dbo->getFields("Servicio", "PushEliminaReserva", "IDServicio = '" . $id_servicio . "'");
    $envia_push_eliminacion = $datos_servicio["PushEliminaReserva"];

    $id_servicio_cancha = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");

    //$fechahora_actual = strtotime ( "2016-03-29 07:00:00" );

    //Verifico que la reserva exista
    $id_reservada_existe = $datos_reserva["IDReservaGeneral"];
    if (empty($id_reservada_existe)):
    $respuesta["message"] = "La reserva ya fue eliminada";
    $respuesta["success"] = false;
    $respuesta["response"] = null;
    endif;

    //Especial atc se puede borrar pero si es antes de 3 horas le sale un mensaje
    if ($IDClub == 26):
    $mensaje_eliminacion = "";
    $id_servicio = $datos_reserva["IDServicio"];
    $hora_inicio_reserva_esp = strtotime('-180 minute', strtotime($fecha_reserva . " " . $hora_reserva));
    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)):
    switch ($id_servicio):
    case "1490":
    $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que el turno de coliseo no sea utilizado se le cobrará el costo de coliseo.";
    break;
    case "2106":
    $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que él profesor no sea utilizado en ese horario se le cobrará el costo de la clase.";
    break;

    case "1446":
    case "1484":
    case "2109":
    case "2110":
    case "3450":
    case "4350":
    case "5035":
    case "5039":
    case "7973":
    case "3941":
    $mensaje_eliminacion = "Estimado Usuario,Debido a que su cancelación ha sido fuera del tiempo límite, en caso de que el turno no sea utilizado en ese horario se le cobrará el costo del turno.";
    break;
    endswitch;
    endif;
    endif;
    //FIN Especial atc se puede borrar pero si es antes de 3 horas le sale un mensaje

    //Especial BTCC si elimina antes de 12 horas prof o minitor sale mensaje
    if ($IDClub == 72 && !empty($aux_reserva) && $id_servicio == 8649):
    $hora_inicio_reserva_esp = strtotime('-720 minute', strtotime($fecha_reserva . " " . $hora_reserva));
    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)):
    $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (12 horas), debe dirigirse al caddie master para cancelar el valor del servicio de caddie y monitor.";
    endif;
    endif;

    //Especial BTCC si elimina antes de 12 horas prof o minitor sale mensaje
    if ($IDClub == 72 && $id_servicio == 8539):
    $hora_inicio_reserva_esp = strtotime('-720 minute', strtotime($fecha_reserva . " " . $hora_reserva));
    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)):
    $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (12 horas), debe dirigirse al caddie master para cancelar el valor del servicio de caddie y profesor.";
    endif;
    endif;
    //Especial BTCC si elimina antes de 1 horas prof o minitor sale mensaje
    if ($IDClub == 72 && $id_servicio == 8649 && empty($aux_reserva)):
    $hora_inicio_reserva_esp = strtotime('-60 minute', strtotime($fecha_reserva . " " . $hora_reserva));
    if ($fechahora_actual > $hora_inicio_reserva_esp && empty($Admin)):
    $mensaje_eliminacion = "Debido a que la cancelación ha sido fuera del tiempo límite (1 hora), debe dirigirse al caddie master para cancelar el valor del servicio de caddie.";
    endif;
    endif;
    //FIN Especial BTCC

    //Especial Country para reservas de 6am y 7am solo hasta las 8pm del dia anterior cuando tiene profesor
    if (($IDClub == 44 || $IDClub == 8) && empty($Admin)):
    $dia_manana = date('Y-m-d', time() + 84600);
    $fecha_hoy_v = date("Y-m-d");
    if (((date("H:i:s") >= "20:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva) && ($id_servicio == "3861" || $id_servicio == "36") && ($hora_reserva == '06:00:00' || $hora_reserva == '07:00:00' || $hora_reserva == '08:00:00')):
    $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con profesor/monitor hasta antes de las 8pm para turnos de 6am, 7am y 8am ";
    $respuesta["success"] = false;
    $respuesta["response"] = null;
    return $respuesta;
    endif;
    endif;

    if (($IDClub == 8 && $id_servicio == 32) || ($IDClub == 44 && $id_servicio == 11242 && empty($Admin))):
    $dia_manana = date('Y-m-d', time() + 84600);
    $fecha_hoy_v = date("Y-m-d");
    if (((date("H:i:s") >= "23:59:59" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)):
    $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva antes de las 12 Pm del dia anterior ";
    $respuesta["success"] = false;
    $respuesta["response"] = null;
    return $respuesta;
    endif;
    endif;

    //Especial Arrayanes en golf solo pemite eleiminar hasta las 7pm del dia anterior
    if (($IDClub == 11) && $id_servicio == 122 && empty($Admin)):
    $dia_manana = date('Y-m-d', time() + 84600);
    $fecha_hoy_v = date("Y-m-d");
    if (((date("H:i:s") >= "19:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)):
    $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con antes de las 7pm del dia anterior ";
    $respuesta["success"] = false;
    $respuesta["response"] = null;
    return $respuesta;
    endif;
    endif;

    /*
    //Especial Arrayanes en tenis y clases tenis solo pemite eleiminar hasta las 8pm del dia anterior
    if (($IDClub == 11) && ($id_servicio == 227 || $id_servicio == 129) && empty($Admin)):
    $dia_manana = date('Y-m-d', time() + 84600);
    $fecha_hoy_v = date("Y-m-d");
    if (((date("H:i:s") >= "20:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)):
    $respuesta["message"] = "Lo sentimos solo se permite eliminar la reserva con antes de las 8pm del dia anterior ";
    $respuesta["success"] = false;
    $respuesta["response"] = null;
    return $respuesta;
    endif;
    endif;
    */

    // especial country solo se puede hasta las 7pm del dia anterior en las reservas del fin de semana.
    $dia_reserva = date("w", strtotime($fecha_reserva));
    if (($IDClub == 44) && empty($Admin) && ($dia_reserva == 6 || $dia_reserva == 0) && ($id_servicio == 3889 || $id_servicio == 3888)):
    $dia_manana = date('Y-m-d', time() + 84600);
    $fecha_hoy_v = date("Y-m-d");
    if (((date("H:i:s") >= "19:00:00" && $dia_manana == $fecha_reserva) || $fecha_hoy_v == $fecha_reserva)):
    $mensaje_eliminacion = "Su Reserva está siendo eliminada fuera del horario permitido de cancelación, en caso de no cubrirse este turno se aplicará el Reglamento Vigente.";
    $EliminadaFueraTiempo = S;
    $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
    endif;
    endif;


    //Especial La sabana la reserva no se puede eliminar si existen otras personas en el grupo de golf se asigna a otro miembro del grupo
    if (($IDClub == 95 && $id_servicio == 15964) || ($IDClub == 8 && $id_servicio == 31) || ($IDClub == 32 && $id_servicio == 2062) || ($IDClub == 47 && $id_servicio == 4270) || ($IDClub == 44 && $id_servicio == 3889) || ($IDClub == 44 && $id_servicio == 3888) || ( $IDClub == 110 && $id_servicio == 19454 && ($EliminarParaMi == "S" || empty($EliminarParaMi) )) || ($IDClub == 112 && $id_servicio == 19939) || $EliminarParaMi == "S" ) {
    $permite_reasignar = "S";

    if ($IDClub == 44 || $IDClub == 95 || $IDClub == 110 || $IDClub == 125) {
    $datos_disponibilidad = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_reserva["IDDisponibilidad"] . "' ", "array");
    //quito 1 al dueño de la reserva
    $MinimoPersonasTurno = $datos_disponibilidad["MinimoInvitados"];
    $sql_invi_tot = "SELECT count(IDReservaGeneralInvitado) as TotalInv FROM ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "' ";
    $r_invi_tot = $dbo->query($sql_invi_tot);
    $row_invi_tot = $dbo->fetchArray($r_invi_tot);
    $TotInv = (int) $row_invi_tot["TotalInv"] - 1;
    if ($MinimoPersonasTurno > 1 && $TotInv < ((int) $MinimoPersonasTurno - 1)) { // no se puede eliminar no cumple con la cantidad mimima de personas para tener la reserva en ese caso no se reasigna se elimina $permite_reasignar="N" ; } } $sql_invi="SELECT IDSocio FROM ReservaGeneralInvitado Where IDReservaGeneral  = '" . $IDReserva . "' and IDSocio>0 Limit 1" ; $r_invi=$dbo->query($sql_invi);
        if ($dbo->rows($r_invi) > 0 && $permite_reasignar == "S") {
        $row_invi = $dbo->fetchArray($r_invi);
        $datos_socio_nuevo = "SELECT Nombre,Apellido FROM Socio WHERE IDSocio = '" . $row_invi["IDSocio"] . "' Limit 1";
        $r_socio_nuevo = $dbo->query($datos_socio_nuevo);
        $row_socio_nuevo = $dbo->fetchArray($r_socio_nuevo);
        $NombreNuevoSocio = $row_socio_nuevo["Nombre"] . " " . $row_socio_nuevo["Apellido"];
        $AccionNuevoSocio = $row_socio_nuevo["Accion"];

        $sql_reasigna = "UPDATE ReservaGeneral Set IDSocio = '" . $row_invi["IDSocio"] . "', IDSocioReserva = '" . $row_invi["IDSocio"] . "',CodigoRespuesta = 'La intenta eliminar socio " . $IDSocio . " y se reasigna a socio " . $row_invi["IDSocio"] . "',NombreSocio='" . $NombreNuevoSocio . "',AccionSocio='" . $AccionNuevoSocio . "' WHERE IDReservaGeneral = '" . $IDReserva . "' ";
        $dbo->query($sql_reasigna);
        //borro al nuevo dueño de los invitados
        $sql_borra_reserva_inv = "DELETE FROM ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "' and IDSocio = '" . $row_invi["IDSocio"] . "'";
        $dbo->query($sql_borra_reserva_inv);
        //Envio notificacion al socio nuevo dueño de la reserva
        SIMUtil::notificar_nueva_reserva($IDReserva, $IDTipoReserva);
        $MensajeReasignacion = "Ha sido asignado como dueño de una reserva en la que era invitado Fecha: " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
        SIMUtil::push_notifica_reserva_socio($IDClub, $row_invi["IDSocio"], $MensajeReasignacion);

        //reasigno la reserva pero no la elimino
        $respuesta["message"] = "La reserva fue reasignada a otro miembro del grupo correctamente";
        $respuesta["success"] = true;
        $respuesta["response"] = null;
        return $respuesta;
        }
        }
        //Fin La Sabana

        //Especial campestre pereira solo cancela 2 de martesa a viernes y 2 de sabado a lunes

        if ($IDClub == 15 && empty($Admin) && $id_servicio == 305) {
        $fecha_hoy_semana = date("Y-m-d");
        $year = date('Y', strtotime($fecha_hoy_semana));
        $week = date('W', strtotime($fecha_hoy_semana));
        $dia_reserva = date("w", strtotime($fecha_hoy_semana));

        $fechaInicioSemana = date('Y-m-d', strtotime($year . 'W' . $week));

        if ((int) $dia_reserva >= 2 && (int) $dia_reserva <= 5) { $fecha_inicio_valida=date('Y-m-d', strtotime($fechaInicioSemana . ' +1 day' )); //MARTES $fecha_fin_valida=date('Y-m-d', strtotime($fechaInicioSemana . ' +4 day' )); //Viernes $mensaje="entre martes y viernes" ; } else { $fecha_inicio_valida=date('Y-m-d', strtotime($fechaInicioSemana . ' +5 day' )); //Sabado $fecha_fin_valida=date('Y-m-d', strtotime($fechaInicioSemana . ' +7 day' )); //Lunes $mensaje="entre sabado y lunes" ; } $fechaInicio=$fecha_inicio_valida . " 00:00:00" ; $fechaFin=$fecha_fin_valida . " 00:00:00" ; $sqlValida="SELECT COUNT(IDReservaGeneral) AS Cantidad FROM `ReservaGeneralEliminada` WHERE `IDSocio` = " . $IDSocio . " AND `FechaTrCr` > '" . $fechaInicio . "' AND FechaTrCr < '" . $fechaFin . "' ORDER BY `IDReservaGeneral` DESC" ; $qryValida=$dbo->query($sqlValida);
            $dato = $dbo->fetchArray($qryValida);

            if ($dato["Cantidad"] >= 2) {
            $respuesta["message"] = "Lo sentimos solo puedes cancelar 2 reservas " . $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
            return $respuesta;
            }

            }

            $EliminadaFueraTiempo = "N";
            //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
            if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 44 && ($id_servicio == 3901 || $id_servicio == 3902 || $id_servicio == 8169 || $id_servicio == 3866 || $id_servicio == 3878 || $id_servicio == 5001 || $id_servicio == 3843)) {
            $mensaje_eliminacion = "Su reserva esta siendo cancelada fuera de las horas permitidas de cancelación, recuerde que se cobrará el total en caso de no cubrir este turno.";
            $EliminadaFueraTiempo = S;
            $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
            }

            //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
            if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 44 && ($id_servicio == 3905)) {
            //$mensaje_eliminacion = "Recuerde que puede cancelar su reserva hasta 12 horas antes, de lo contrario será facturado el 50% del valor total del servicio a menos que este sea tomado por otro socio";
            //$EliminadaFueraTiempo=S;
            //$Admin=1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
            }

            //PARA LOS SERVICIOS QUE SE PUEDEN CANCELAR ANTES DE TIEMPO SEGUN CONFIGURACIÓN
            $sqlServicio = "SELECT ValidaEliminacionFueraHora, MensajeEliminacionFueraHora FROM Servicio WHERE IDServicio = " . $id_servicio;
            $qryServicio = $dbo->query($sqlServicio);
            $datos = $dbo->fetchArray($qryServicio);
            if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $datos["ValidaEliminacionFueraHora"]) {
            $EliminadaFueraTiempo = S;
            $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar

            if (empty($datos["MensajeEliminacionFueraHora"])) {
            $mensaje_eliminacion = "Recuerde que puede cancelar su reserva hasta 12 horas antes, de lo contrario será facturado el 50% del valor total del servicio a menos que este sea tomado por otro socio";
            } else {
            $mensaje_eliminacion = $datos["MensajeEliminacionFueraHora"];
            }

            }

            //Especial country masajes tiene un tiempo de eliminacion y si se elimina fuera de ese tiempo muetra un mensaje
            if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && $IDClub == 72 && ($id_servicio != 8649 && $id_servicio != 8539)) {
            $mensaje_eliminacion = "Su reserva esta siendo cancelada fuera de las horas permitidas de cancelación. Recuerde que deberá pagar el total del servicio, a menos que otro socio use el turno.";
            $EliminadaFueraTiempo = S;
            $Admin = 1; //asigno un valor a esta variable para que en la siguiente condicion permita eliminar
            }

            if ($tiempo_cancelacion == 1) {
            $medicion_cancelacion = str_replace("s", "", $medicion_cancelacion);
            }

            if ($fechahora_actual > $hora_inicio_reserva && empty($Admin) && empty($PermiteEliminar)):
            $respuesta["message"] = "No se puede eliminar la reserva debe ser minimo: " . $tiempo_cancelacion . " " . $medicion_cancelacion . " antes";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

            else:

            $datos_reserva_eli = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReserva . "' ", "array");
            if ( ($datos_reserva_eli["IDTipoPago"] == 1 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) ||
            ($datos_reserva_eli["IDTipoPago"] == 12 && $datos_reserva_eli["EstadoTransaccion"] != "A" && empty($Admin)) &&
            $IDClub!=88 ): //Para pagos con payu no dejo que se elimine cuando se devuelva solo hata confirmar el estado del pago
            $respuesta["message"] = "Esperando respuesta de la transaccion";
            //$respuesta["message"] = "Delete From ReservaGeneral Where IDReservaGeneral = '".$row_reserva_auto["IDReservaGeneral"]."'";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            endif;

            //para credibanco las trabsacciones pagadas no se pueden eliminar
            if($datos_reserva_eli["IDTipoPago"] == 12 && empty($Admin)){
            $EstadoTransaccion=$dbo->getFields("PagoCredibanco", "orderStatus", "reserved12 = '" . $IDReserva . "'");
            if($EstadoTransaccion==2){
            $respuesta["message"] = "Transaccion pagada correctamente, para eliminar comuniquese con administrador";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
            }

            }


            //verifico en la disponibilidad si la reserva permite la eliminación cuando fue creada por el starter
            $permite_eliminar_reserva_creada_starter = $dbo->getFields("Disponibilidad", "PermiteEliminarCreadaStarter", "IDDisponibilidad = '" . $id_disponibilidad . "'");
            //verifico que la reserva haya sido creada por el socio si fue por el starter verifico en la disponibilidad si se puede eliminar por el socio
            $reservada_creada_por = $dbo->getFields("ReservaGeneral", "UsuarioTrCr", "IDReservaGeneral = '" . $IDReserva . "'");
            if ($reservada_creada_por == "Starter" && empty($Admin) && $permite_eliminar_reserva_creada_starter == "N"):
            $respuesta["message"] = "No se puede eliminar la reserva fue creada por el Starter y solo el starter puede eliminarla";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            else:
            $borra_automatica = 0;
            //Copio Reserva
            /*
            $sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, IDServicioTipoReserva, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
            Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, IDServicioTipoReserva, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '".$Razon."', NOW(), NOW()
            From ReservaGeneral
            Where IDReservaGeneral = '".$IDReserva."'");
            */
            $IP = SIMUtil::get_IP();
            $sql_copia_reserva = $dbo->query("
            INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral,IDClub,IDSocio,IDSocioReserva,IDUsuarioReserva,IDServicio,IDServicioElemento,IDEstadoReserva ,IDDisponibilidad,IDAuxiliar,IDTipoModalidadEsqui,IDServicioTipoReserva,IDReservaGrupos,IDInvitadoBeneficiario,IDSocioBeneficiario,IDUsuarioCumplida,IDTipoPago,Cumplida,CumplidaCabeza,FechaCumplida,ObservacionCumplida,CantidadInvitadoSalon,Fecha,Hora ,Tee,Observaciones,Tipo,Notificado,NumeroInscripcion,CodigoPago,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,Pagado,PagoPayu,UsuarioTrCr,FechaTrCr,Razon,MensajeEliminacion,EliminadaFueraTiempo,UsuarioTrEd,FechaTrEd,IP)
            Select IDReservaGeneral,IDClub,IDSocio,IDSocioReserva,IDUsuarioReserva,IDServicio,IDServicioElemento,IDEstadoReserva,IDDisponibilidad,IDAuxiliar,IDTipoModalidadEsqui,IDServicioTipoReserva,IDReservaGrupos,IDInvitadoBeneficiario,IDSocioBeneficiario,IDUsuarioCumplida,IDTipoPago,Cumplida,CumplidaCabeza,FechaCumplida,ObservacionCumplida,CantidadInvitadoSalon,Fecha,Hora ,Tee,Observaciones,Tipo,Notificado,NumeroInscripcion,CodigoPago,EstadoTransaccion,FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,Pagado,PagoPayu,UsuarioTrCr,FechaTrCr,'" . $Razon . "','" . $mensaje_eliminacion . "','" . $EliminadaFueraTiempo . "',NOW(),NOW(),'$IP'
            From ReservaGeneral
            Where IDReservaGeneral = '" . $IDReserva . "'");

            //borro reserva
            $sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral = '" . $IDReserva . "'");
            //borro invitados a esa reserva
            $sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral = '" . $IDReserva . "'");

            //Verifico si tiene una reserva asociada para borrarla tambien
            //$sql_asociada = "Select * From ReservaGeneralAutomatica Where IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and Fecha = '" . $fecha_reserva . "'";
            $sql_asociada = "Select * From ReservaGeneralAutomatica Where IDReservaGeneral = '" . $IDReserva . "' and IDClub = '" . $IDClub . "' and Fecha = '" . $fecha_reserva . "'";
            $result_asociada = $dbo->query($sql_asociada);
            while ($row_asociada = $dbo->fetchArray($result_asociada)):
            $sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
            Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '" . $Razon . "', NOW(), NOW()
            From ReservaGeneral
            Where IDReservaGeneral = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
            //borro reserva
            $sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
            //borro invitados a esa reserva
            $sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_asociada["IDReservaGeneralAsociada"] . "'");
            $borra_automatica = 1;
            endwhile;

            //Si la reserva es una clase elimino la cancha que se reservó con la clase

            if ($id_servicio_cancha > 0 && $borra_automatica == 0):
            // Consulto el servicio del club asociado a este servicio maestro
            $IDServicioCanchaClub = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'");

            // Borro la cancha asociada
            //Copio Reserva
            $sql_reserva_auto = "Select * FRom ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicio = '" . $IDServicioCanchaClub . "' and IDEstadoReserva = 1 and Fecha = '" . $fecha_reserva . "' and Hora = '" . $hora_reserva . "' and Tipo = 'Automatica' limit 1";
            $result_reserva_auto = $dbo->query($sql_reserva_auto);
            $row_reserva_auto = $dbo->fetchArray($result_reserva_auto);

            $sql_copia_reserva_auto = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)
            Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '" . $Razon . "', NOW(), NOW()
            From ReservaGeneral
            Where IDReservaGeneral = '" . $row_reserva_auto["IDReservaGeneral"] . "'");
            //borro reserva
            $sql_borra_reserva_auto = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral = '" . $row_reserva_auto["IDReservaGeneral"] . "'");

            endif;

            // SI ES DE LA PRADERA SE DEBE ELIMINAR DE SISCLUB
            if($IDClub == 16):
            require LIBDIR . "SIMWebServicePradera.inc.php";
            SIMWebServicePradera::cancelar_reserva_facturacion_potosi($IDReserva);
            endif;

            // SI LA RESERVA FUE PAGADA CON TALONERA SE REVIERTE
            if($datos_reserva[IDTipoPago] == 16):
            require_once LIBDIR . "SIMWebServiceTaloneras.inc.php";
            $ValorPagado = $datos_reserva[ValorPagado];
            SIMWebServiceTaloneras::revertir_cantidad_talonera($IDReserva,$IDSocio,$ValorPagado);
            endif;


            SIMUtil::notificar_elimina_reserva($IDReserva, $IDTipoReserva);

            //Si el elemento reservado es una persona (profesor, peluquero, masajista, etc) y esta creado como empleado en app empleados envio notificacion push
            SIMUtil::push_notifica_reserva_elimina($IDClub, $IDReserva, "Empleado");

            if ($envia_push_eliminacion == "S"):
            SIMUtil::push_notifica_reserva_elimina_socio($IDClub, $IDReserva, $Razon);
            endif;

            //Envio mensaje a lista de espera
            //SIMUtil::push_notifica_libera_reserva($IDClub,$IDReserva);

            $codigo_canje = SIMUtil::push_notifica_codigo_pago($IDReserva);
            if (!empty($codigo_canje)):
            $msg_respuesta = " Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_canje . " Lo puede consultar tambien en el modulo de Notificaciones";
            endif;

            $respuesta["message"] = "Reserva eliminada correctamente. " . $msg_respuesta . $mensaje_eliminacion;
            //$respuesta["message"] = "Delete From ReservaGeneral Where IDReservaGeneral = '".$row_reserva_auto["IDReservaGeneral"]."'";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
            endif;
            endif;

            } else {
            $respuesta["message"] = 'Error e lsocio no existeo no pertenece al club';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            }

            } else {
            $respuesta["message"] = "9." . 'Atencionfaltanparametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            }

            return $respuesta;

            }