<?php
switch ($action) {
        /******************************************************
	/*SERVICIOS HOTEL
	/******************************************************/

    case "getconfiguracionhotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceHotel::get_configuracion_hotel($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfechasdisponibleshotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        if (empty($IDSocio)) :
            $IDSocio = SIMNet::req("IDUsuario");
        endif;
        //(SELECT FechaFin FROM `ReservaHotel` WHERE IDClub=8 and FechaFin>=now() ) UNION  (SELECT FechaInicio FROM `ReservaHotel` WHERE IDClub=8 and FechaInicio>=now() )
        $respuesta = SIMWebServiceHotel::get_fechas_disponibles_hotel($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        // Reservas hotel
    case "getmisreservashotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        if ($IDClub == 27) : //Payande
            //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisreservashotel&IDSocio=".$IDSocio);
            $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisreservashotel&IDSocio=" . $IDSocio);
            $respuesta = json_decode($datos, true);
        else :
            $respuesta = SIMWebServiceHotel::get_mis_reservas($IDSocio, $IDReserva);
        endif;

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Valida fecha
    case "getvalidafecha":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $IDSocio = SIMNet::req("IDSocio");
        $version = SIMNet::req("AppVersion");

        if ($IDClub == 27) : //Payande
            if ((int)$version < 26) {
                //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','mensajeversion','".json_encode($_GET)."','".json_encode($respuesta)."')");
                $respuesta["message"] = "Por favor descargue la ultima version del app";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
                exit;
            } else {
                //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getvalidafecha&FechaInicio=".$FechaInicio."&FechaFin=".$FechaFin);
                $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getvalidafecha&FechaInicio=" . $FechaInicio . "&FechaFin=" . $FechaFin);
                $respuesta = json_decode($datos, true);
            }
        else :
            $respuesta = SIMWebServiceHotel::get_valida_fecha($IDClub, $FechaInicio, $FechaFin, "N", $IDSocio);
        endif;
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvalidafecha','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));

        break;

    case "settipopagoreservahotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        if ($IDClub == 27) { //Payande
            $key = "P4y4nd3Reser";
            $action = "settipopagoreservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'IDReserva'   => $IDReserva,
                'IDTipoPago'   => $IDTipoPago,
                'CodigoPago'   => $CodigoPago
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            exit;
        } else {
            $respuesta = SIMWebServiceHotel::set_tipo_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago);
        }

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Invitados Hotel
    case "getmisinvitadoshotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        if ($IDClub == 27) : //Payande
            //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisinvitadoshotel&IDSocio=".$IDSocio);
            $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisinvitadoshotel&IDSocio=" . $IDSocio);
            $respuesta = json_decode($datos, true);
        else :
            $respuesta = SIMWebServiceHotel::get_mis_invitados($IDSocio);
        endif;

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Invitados Hotel
    case "setinvitadoshotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $Documento = $_POST["Documento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $MenorEdad = $_POST["MenorEdad"];

        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "setinvitadoshotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'Documento'   => $Documento,
                'Nombre'   => $Nombre,
                'Apellido'   => $Apellido,
                'Email'   => $Email
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            exit;
        else :
            $respuesta = SIMWebServiceHotel::set_invitados_hotel($IDSocio, $Documento, $Nombre, $Apellido, $Email, $MenorEdad);
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "seteditainvitadohotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDReservaHotelInvitado = $_POST["IDReservaHotelInvitado"];
        $IDSocioInvitado = $_POST["IDSocioInvitado"];
        $TipoInvitado = $_POST["TipoInvitado"];
        $Documento = $_POST["Documento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $MenorEdad = $_POST["MenorEdad"];
        $respuesta = SIMWebServiceHotel::set_edita_invitado_hotel($IDSocio, $IDReserva, $IDReservaHotelInvitado, $IDSocioInvitado, $TipoInvitado, $Documento, $Nombre, $Apellido, $Email, $MenorEdad);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setagregainvitadohotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $AcompananteSocio = $_POST["AcompananteSocio"];
        $respuesta = SIMWebServiceHotel::set_agrega_invitado_hotel($IDClub, $IDSocio, $IDReserva, $AcompananteSocio);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','set_agrega_invitado_hotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validafechapasadia":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $FechaIncio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMWebServiceHotel::get_valida_fecha_pasadia($IDClub, $IDSocio, $FechaIncio, $FechaFin);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','set_agrega_invitado_hotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteliminainvitadohotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDReserva = SIMNet::req("IDReserva");
        $IDSocio = SIMNet::req("IDSocio");
        $IDReservaHotelInvitado = SIMNet::req("IDReservaHotelInvitado");
        $IDSocioInvitado = SIMNet::req("IDSocioInvitado");
        $TipoInvitado = SIMNet::req("TipoInvitado");
        $respuesta = SIMWebServiceHotel::set_elimina_invitado_hotel($IDReserva, $IDSocio, $IDReservaHotelInvitado, $TipoInvitado, $IDSocioInvitado);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteliminainvitadoshotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreservahotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocioMiClub = $_POST["IDSocio"];
        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocioMiClub . "'");
        //Averiguo el id del socio en el sistema de payande
        $IDSocio = $_POST["IDSocio"];
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $IDHabitacion = $_POST["IDHabitacion"];
        $IDPromocion = $_POST["IDPromocion"];
        $IDTemporadaAlta = $_POST["IDTemporadaAlta"];
        $Temporada = $_POST["Temporada"];
        $CabezaReserva = $_POST["CabezaReserva"];
        $Estado = $_POST["Estado"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Ninera = $_POST["Ninera"];
        $Corral = $_POST["Corral"];
        $Observaciones = $_POST["Observaciones"];
        //$Valor = $_POST["Valor"];
        //$IVA = $_POST["IVA"];
        $NumeroPersonas = $_POST["NumeroPersonas"];
        $Adicional = $_POST["Adicional"];
        $IDInvitadoHotel = $_POST["IDInvitadoHotel"];
        //$Pagado = $_POST["Pagado"];
        //$FechaReserva = $_POST["FechaReserva"];

        $NombreDuenoReserva = $_POST["Nombre"];
        $DocumentoDuenoReserva = $_POST["Cedula"];
        $EmailDuenoReserva = $_POST["Email"];

        $Campos = $_POST["CamposReserva"];

        //Ajusto los datos que se envian al servicio de payande
        $datos_invitado = json_decode($_POST["AcompananteSocio"], true);
        $contador_invitado = 0;
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $datos_invitado_reserva) :
                $IDSocioInvitadoTurno = $datos_invitado_reserva["IDSocio"];
                $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                $Familiar = "N";
                $array_datos_acompanante[$contador_invitado]["IDSocio"] = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $datos_invitado_reserva["IDSocio"] . "'");
                $array_datos_acompanante[$contador_invitado]["Nombre"] = $NombreSocioInvitadoTurno;
                $array_datos_acompanante[$contador_invitado]["IDInvitado"] = $datos_invitado_reserva["IDInvitado"];
                //Verifico si es familiar
                if (!empty($IDSocioInvitadoTurno)) :
                    $AccionSocioInvitado = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    $array_datos_acompanante[$contador_invitado]["Nombre"]  = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitadoTurno . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    if ($AccionSocioInvitado == $AccionSocio)
                        $Familiar = "S";
                endif;
                $array_datos_acompanante[$contador_invitado]["Familiar"] = $Familiar;
                $contador_invitado++;
            endforeach;
        endif;

        $AcompananteSocio = json_encode($array_datos_acompanante);

        if ($IDClub == 27) : //Payande

            $key = "P4y4nd3Reser";
            $action = "setreserva";

            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";

            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocioMiClub,
                'IDInvitadoHotel'   => $IDInvitadoHotel,
                'IDBeneficiario'   => $IDBeneficiario,
                'AccionSocio'   => $AccionSocio,
                'IDSocio'   => $IDSocio,
                'IDHabitacion'   => $IDHabitacion,
                'IDPromocion'   => $IDPromocion,
                'IDTemporadaAlta'   => $IDTemporadaAlta,
                'Temporada'   => $Temporada,
                'CabezaReserva'   => $CabezaReserva,
                'FechaInicio'   => $FechaInicio,
                'FechaFin'   => $FechaFin,
                'Ninera'   => $Ninera,
                'Corral'   => $Corral,
                'NumeroPersonas'   => $NumeroPersonas,
                'Adicional'   => $Adicional,
                'AcompananteSocio'   => $AcompananteSocio
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);

            // close the connection, release resources used
            curl_close($ch);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($response)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
            print_r($response);
            exit;
        else :
            $AcompananteSocio = $_POST["AcompananteSocio"];
            $respuesta = SIMWebServiceHotel::set_reserva($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, "", "", $NombreDuenoReserva, $DocumentoDuenoReserva, $EmailDuenoReserva, $Observaciones, $Campos);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreservahotelv2":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocioMiClub = $_POST["IDSocio"];
        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocioMiClub . "'");
        //Averiguo el id del socio en el sistema de payande
        $IDSocio = $_POST["IDSocio"];
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $IDHabitacion = $_POST["IDHabitacion"];
        $IDPromocion = $_POST["IDPromocion"];
        $IDTemporadaAlta = $_POST["IDTemporadaAlta"];
        $Temporada = $_POST["Temporada"];
        $CabezaReserva = $_POST["CabezaReserva"];
        $Estado = $_POST["Estado"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Ninera = $_POST["Ninera"];
        $Corral = $_POST["Corral"];
        $Observaciones = $_POST["Observaciones"];
        //$Valor = $_POST["Valor"];
        //$IVA = $_POST["IVA"];
        $NumeroPersonas = $_POST["NumeroPersonas"];
        $Adicional = $_POST["Adicional"];
        $IDInvitadoHotel = $_POST["IDInvitadoHotel"];
        //$Pagado = $_POST["Pagado"];
        //$FechaReserva = $_POST["FechaReserva"];

        $NombreDuenoReserva = $_POST["Nombre"];
        $DocumentoDuenoReserva = $_POST["Cedula"];
        $EmailDuenoReserva = $_POST["Email"];

        $Campos = $_POST["CamposReserva"];
        $TipoReserva = $_POST["TipoReserva"];

        //Ajusto los datos que se envian al servicio de payande
        $datos_invitado = json_decode($_POST["AcompananteSocio"], true);
        $contador_invitado = 0;
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $datos_invitado_reserva) :
                $IDSocioInvitadoTurno = $datos_invitado_reserva["IDSocio"];
                $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                $Familiar = "N";
                $array_datos_acompanante[$contador_invitado]["IDSocio"] = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $datos_invitado_reserva["IDSocio"] . "'");
                $array_datos_acompanante[$contador_invitado]["Nombre"] = $NombreSocioInvitadoTurno;
                $array_datos_acompanante[$contador_invitado]["IDInvitado"] = $datos_invitado_reserva["IDInvitado"];
                //Verifico si es familiar
                if (!empty($IDSocioInvitadoTurno)) :
                    $AccionSocioInvitado = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    $array_datos_acompanante[$contador_invitado]["Nombre"]  = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitadoTurno . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    if ($AccionSocioInvitado == $AccionSocio)
                        $Familiar = "S";
                endif;
                $array_datos_acompanante[$contador_invitado]["Familiar"] = $Familiar;
                $contador_invitado++;
            endforeach;
        endif;

        $AcompananteSocio = json_encode($array_datos_acompanante);

        if ($IDClub == 27) : //Payande

            $key = "P4y4nd3Reser";
            $action = "setreserva";

            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";

            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocioMiClub,
                'IDInvitadoHotel'   => $IDInvitadoHotel,
                'IDBeneficiario'   => $IDBeneficiario,
                'AccionSocio'   => $AccionSocio,
                'IDSocio'   => $IDSocio,
                'IDHabitacion'   => $IDHabitacion,
                'IDPromocion'   => $IDPromocion,
                'IDTemporadaAlta'   => $IDTemporadaAlta,
                'Temporada'   => $Temporada,
                'CabezaReserva'   => $CabezaReserva,
                'FechaInicio'   => $FechaInicio,
                'FechaFin'   => $FechaFin,
                'Ninera'   => $Ninera,
                'Corral'   => $Corral,
                'NumeroPersonas'   => $NumeroPersonas,
                'Adicional'   => $Adicional,
                'AcompananteSocio'   => $AcompananteSocio
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);


            // close the connection, release resources used
            curl_close($ch);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($response)."')");
            //SIMLog::insert_app($action, $IDClub, $_POST, $response);
            print_r($response);
            exit;
        else :
            $AcompananteSocio = $_POST["AcompananteSocio"];
            $respuesta = SIMWebServiceHotel::set_reservav2($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, "", "", $NombreDuenoReserva, $DocumentoDuenoReserva, $EmailDuenoReserva, $Observaciones, $Campos, $TipoReserva);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Notificar lista de espera
    case "notificarlistaesperahotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMUtil::notificar_lista_espera_hotel($IDClub, $FechaInicio, $FechaFin);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Reserva Hotel
    case "eliminareservahotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "eliminareservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocio,
                'IDReserva'   => $IDReserva
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
            exit;
        else :
            $respuesta = SIMWebServiceHotel::elimina_reserva_hotel($IDClub, $IDSocio, $IDReserva);
        endif;

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Validar reserva con pago
    case "validapagoreservahotel":
        require(LIBDIR . "SIMWebServiceHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");

        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "validapagoreservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'IDReserva'   => $IDReserva
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
            exit;
        else :
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
            $respuesta = SIMWebServiceHotel::valida_pago_reserva_hotel($IDSocio, $IDReserva);
        endif;


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        /******************************************************
        /*FIN SERVICIOS HOTEL
        /******************************************************/
        /******************************************************
        /*SERVICIOS CASA HOTEL
        /******************************************************/

    case "getconfiguracioncasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_configuracion_casa_hotel($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmesesdisponiblescasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_meses_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getmesesdisponiblescambiarfechacasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_meses_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getmesesdisponiblesexpresscasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_meses_disponibles_casa_hotel_express($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getsemanasdisponiblescasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $TipoReserva = SIMNet::req("TipoReserva");
        $Fechas = SIMNet::req("Fechas");

        $respuesta = SIMWebServiceCasaHotel::get_semanas_disponibles_casa_hotel($IDClub, $IDSocio, $IDUsuario, $TipoReserva, $Fechas);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getsemanasdisponiblescambiarfechacasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $TipoReserva = SIMNet::req("TipoReserva");
        $Ano = SIMNet::req("Ano");
        $Mes = SIMNet::req("Mes");

        $respuesta = SIMWebServiceCasaHotel::get_semanas_disponibles_cambiar_fecha_casa_hotel($IDClub, $IDSocio, $IDUsuario, $Ano, $Mes);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getsemanasdisponiblesexpresscasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $TipoReserva = SIMNet::req("TipoReserva");
        $Ano = SIMNet::req("Ano");
        $Mes = SIMNet::req("Mes");

        $respuesta = SIMWebServiceCasaHotel::get_semanas_disponibles_express_casa_hotel($IDClub, $IDSocio, $IDUsuario, $Ano, $Mes);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setreservascasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $TipoReserva = SIMNet::req("TipoReserva");
        $Reservas = SIMNet::req("Reservas");

        $respuesta = SIMWebServiceCasaHotel::set_reservas_casa_hotel($IDClub, $IDSocio, $IDUsuario, $TipoReserva, $Reservas);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getmisreservascasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_mis_reservas_casa_hotel($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisreservasupgradecasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceCasaHotel::get_mis_reservas_upgrade_casa_hotel($IDClub, $IDSocio, $IDUsuario);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getupgradescasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDReserva = SIMNet::req("IDReserva");

        $respuesta = SIMWebServiceCasaHotel::get_upgrades_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "seteliminarreservacasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDReserva = SIMNet::req("IDReserva");

        $respuesta = SIMWebServiceCasaHotel::set_eliminar_reserva_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setupgradecasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDReserva = SIMNet::req("IDReserva");
        $IDMejora = SIMNet::req("IDMejora");

        $respuesta = SIMWebServiceCasaHotel::set_upgrade_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDReserva, $IDMejora);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setcambiofechacasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDSemana = SIMNet::req("IDSemana");

        $respuesta = SIMWebServiceCasaHotel::set_cambio_fecha_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDSemana);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setexpresscasahotel":
        require(LIBDIR . "SIMWebServiceCasaHotel.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDSemana = SIMNet::req("IDSemana");

        $respuesta = SIMWebServiceCasaHotel::set_express_casa_hotel($IDClub, $IDSocio, $IDUsuario, $IDSemana);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        /******************************************************
        /*FIN SERVICIOS HOTEL
        /******************************************************/
}///end sw
