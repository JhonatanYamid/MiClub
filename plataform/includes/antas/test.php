<?php

$fecha_nacimiento = "1982-10-08";
$dia_actual = date("Y-m-d");
exit;



function set_reserva_generalV2( $IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, $Observaciones = "", $Admin = "", $Tee = "", $IDDisponibilidad = "", $Repetir = "", $Periodo = "", $RepetirFechaFinal = "", $IDTipoModalidadEsqui = "", $IDAuxiliar = "", $IDTipoReserva = "", $NumeroTurnos = "", $IDReservaGrupos, $IDBeneficiario = "", $TipoBeneficiario = "", $IDUsuarioReserva = "", $CantidadInvitadoSalon = "", $ListaAuxiliar = "", $Altitud="",$Longitud="" ) {

  $dbo = & SIMDB::get();

  $datos_servicio = $dbo->fetchAll( "Servicio", " IDServicio= '" . $IDServicio . "' ", "array" );

  $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );
  $NombreSocioReserva=$datos_socio["Nombre"]." " . $datos_socio["Apellido"];
  $AccionSocioReserva=$datos_socio["Accion"];

  if(!empty($IDBeneficiario)){
    $datos_beneficiario = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDBeneficiario . "' ", "array" );
    $NombreBenefReserva=$datos_beneficiario["Nombre"]." " . $datos_beneficiario["Apellido"];
    $AccionBenefReserva=$datos_beneficiario["Accion"];
  }


  $respuesta_sesion = SIMWebServiceApp::valida_cierre_sesion( $IDSocio );
  if ( $respuesta_sesion == 1 && empty( $Admin ) ):
    //borro el id para no mostrar mas este mensaje
    $delete_cerrar_sesion = "delete from CierreSesionSocio Where IDSocio = '" . $IDSocio . "' Limit 1";
  $dbo->query( $delete_cerrar_sesion );
  $nom_socio_validar = $NombreSocioReserva;
  $respuesta[ "message" ] = "Es usted " . $nom_socio_validar . "?  Si no es " . $nom_socio_validar . " por favor cierre sesion y vuelva a ingresar con su usuario y clave para poder tomar la reserva";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;


  if ( !empty( $IDUsuarioReserva ) ):
    //verifico si el usuario tienen permiso para hacer reservas
    $permite_funcionario_reserva = $dbo->getFields( "Usuario", "PermiteReservar", "IDUsuario = '" . $IDUsuarioReserva . "'" );
  if ( $permite_funcionario_reserva == "N" ):
    $respuesta[ "message" ] = "Lo sentimos, no tiene permiso para realizar reservas para socios";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;
  endif;



  $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
  // Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible
  if ( empty( $IDElemento ) ):
    $id_servicio_inicial = $dbo->getFields( "ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
  if ( $id_servicio_inicial == 5 ): // 5 = get_reserva_aleatoria
    $IDElemento = SIMWebService::buscar_elemento_disponible( $IDClub, $IDServicio, $Fecha, $Hora, $IDElemento );
  endif;
  endif;


  if ( ( $id_servicio_maestro == "15" || $id_servicio_maestro == "27" || $id_servicio_maestro == "28" ) && empty( $Tee ) ):
    $respuesta[ "message" ] = "Para poder reservar debe actualizar la app a la ultima version";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;


  //Valido si el socio puede reservar
  $permiso_reserva = SIMWebService::validar_permiso_reserva( $IDSocio );
  if ( $permiso_reserva == "N" || $datos_socio["IDEstadoSocio"]!=1):
    $respuesta[ "message" ] = "Lo sentimos no tiene permiso para realizar reservas";
    $respuesta[ "success" ] = false;
    $respuesta[ "response" ] = NULL;
    return $respuesta;
  endif;

  //Valido si solo permite reservar por edades
  if($datos_servicio=="ValidarEdad"){
    //$edad_socio=self::calcular_edad($datos_socio["FechaNacimiento"]);
    if($edad_socio>=$datos_socio["EdadMinima"] && $edad_socio <= $datos_socio["EdadMaxima"]){

    }
    else{
      $respuesta[ "message" ] = "Lo sentimos no tiene la edad permitida para realizar la reserva";
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
      return $respuesta;
    }
  }


  //Valido si el Beneficiario puede reservar
  if((int)$IDBeneficiario>0){
    $permiso_reserva = SIMWebService::validar_permiso_reserva( $IDBeneficiario );
    if ( $permiso_reserva == "N" ):
      $respuesta[ "message" ] = "Lo sentimos no tiene permiso para realizar reservas";
    $respuesta[ "success" ] = false;
    $respuesta[ "response" ] = NULL;
    return $respuesta;
    endif;
  }


  //Valido si el socio puede reservar	si es un canje o invitado
  $permiso_reserva = self::validar_canje_activo( $IDSocio );
  if ( $permiso_reserva == "1" ):
    $respuesta[ "message" ] = "Lo sentimos no tiene permiso para realizar reservas, las fechas estan venciadas";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;




  // Verifico si tiene sanciones
  $sancion = SIMWebServiceApp::verifica_sancion_socio( $IDClub, $IDSocio, $IDServicio, $Fecha );
  if ( $sancion && ( $IDClub == "8" || $IDClub == "15" ) ):
    $respuesta[ "message" ] = "Lo sentimos  tiene una sancion vigente.";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;

  // Si tiene invitados verifico que los invitados no tengan sanciones
  $datos_invitado = json_decode( $Invitados, true );
  if ( count( $datos_invitado ) > 0 ):
    foreach ( $datos_invitado as $detalle_datos ):
      $IDSocioInvitado = $detalle_datos[ "IDSocio" ];
  if ( !empty( $IDSocioInvitado ) ):
    $sancion = SIMWebServiceApp::verifica_sancion_socio( $IDClub, $IDSocioInvitado, $IDServicio, $Fecha );
  if ( $sancion && $IDClub == "8" ):
    $nombre_socio_sancion = utf8_encode( $dbo->getFields( "Socio", "Nombre", "IDSocio = '" . $IDSocioInvitado . "' and IDClub = '" . $IDClub . "'" ) . " " . $dbo->getFields( "Socio", "Apellido", "IDSocio = '" . $IDSocioInvitado . "' and IDClub = '" . $IDClub . "'" ) );
  $respuesta[ "message" ] = "Lo sentimos  el invitado " . $nombre_socio_sancion . " tiene una sancion vigente, la reserva no puede ser tomada";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;

  endif;
  endforeach;
  endif;


  //Si es admin valido si el auxiliar boleador esta disponible de nuevo
  if ( !empty( $Admin ) && !empty( $IDAuxiliar ) ):
      $flag_aux_disp = 0;
      $response_dispo_aux = self::get_auxiliares( $IDClub, $IDServicio, $Fecha, $Hora );
      $response_dispo_aux[ "success" ];
      if ( $response_dispo_aux[ "success" ] == 0 ):
        $flag_aux_disp = 1;
      else :
        $flag_aux_disp = 1;
          foreach ( $response_dispo_aux[ "response" ] as $datos_conf_aux ):
              foreach ( $datos_conf_aux[ "Auxiliares" ] as $datos_aux ):
                  if ( $IDAuxiliar == $datos_aux[ "IDAuxiliar" ] ):
                      $flag_aux_disp = 0;
                  endif;
              endforeach;
          endforeach;
      endif;

      if ( $flag_aux_disp == 1 ):
          $respuesta[ "message" ] = "Lo sentimos, el auxiliar no esta disponible en ese horario";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
      endif;
  endif;

  //Verifico de nuevo que la lista de auxiliares seleccionados esten disponibles
  if (  !empty( $ListaAuxiliar ) ):
      $datos_auxiliares_revisar = json_decode( $ListaAuxiliar, true );
      $response_dispo_aux = self::get_auxiliares( $IDClub, $IDServicio, $Fecha, $Hora );
      foreach ( $response_dispo_aux[ "response" ] as $datos_conf_aux ):
          foreach ( $datos_conf_aux[ "Auxiliares" ] as $datos_aux ):
                  $array_aux_disponibles[]=$datos_aux["IDAuxiliar"];
          endforeach;
      endforeach;

      if ( count( $datos_auxiliares_revisar ) > 0 ):
          foreach ( $datos_auxiliares_revisar as $key_aux => $auxiliar_seleccionado ):
              if ( !in_array($auxiliar_seleccionado["IDAuxiliar"],$array_aux_disponibles) ):
                  $respuesta[ "message" ] = "El auxiliar ".$auxiliar_seleccionado["Nombre"]." no esta disponible en ese horario";
                  $respuesta[ "success" ] = false;
                  $respuesta[ "response" ] = NULL;
                  return $respuesta;
              endif;
          endforeach;
      endif;
  endif;









  //Especial Pereira restaurante 40 por dia sin importar la hora
  if($IDServicio==5609 && $IDClub==15){
      //Verifico cuantas personas estan inscritas
      $LimiteCuposServicio=40;
      $sql_invitados="SELECT SUM(CantidadInvitadoSalon) as TotalInvitado FROM ReservaGeneral WHERE IDServicio = '".$IDServicio."' and IDClub='".$IDClub."' and Fecha = '".$Fecha."'";
      $result_invitado=$dbo->query($sql_invitados);
      $row_invitado_servicio=$dbo->fetchArray($result_invitado);
      if((int)$row_invitado_servicio["TotalInvitado"]>$LimiteCuposServicio){
        $respuesta[ "message" ] = "Lo sentimos se llegó al máximo permitido de " . $LimiteCuposServicio . " personas al dia";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
      }
      else{
        //Verifico que los seleccionados no superen el permitido
        $TotalNuevoInvitado=(int)$CantidadInvitadoSalon+(int)$row_invitado_servicio["TotalInvitado"];
        if($TotalNuevoInvitado>$LimiteCuposServicio){
          $CuposRestantes=(int)$LimiteCuposServicio-(int)$row_invitado_servicio["TotalInvitado"];
          $respuesta[ "message" ] = "Lo sentimos solo quedan " . $CuposRestantes . " cupos";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
        }
      }
  }



  //Validacion especial para Pradera en Esqui en la cual no se permite al grupo familiar tomar una reserva si alguein de su grupo eliminó una previamente
  $MinutosRestriccion = $datos_servicio["BloquearMinutosGrupo"];
  //if( ($IDClub==16) && $IDServicio==327):
  if ( ( int )$MinutosRestriccion > 0 ):

    //if($IDClub==8):
    //$minutos_restriccion = 60;
    $minutos_restriccion = ( int )$MinutosRestriccion;
  //verifico si alguien del grupo ha eliminado reserva
  $grupo_familiar = self::get_beneficiarios( $IDClub, $IDSocio );
  if ( count( $grupo_familiar[ "response" ][ "Beneficiarios" ] ) > 0 ):
    foreach ( $grupo_familiar[ "response" ][ "Beneficiarios" ] as $datos_nucleo ):
      if ( $datos_nucleo[ "TipoBeneficiario" ] == "Socio" ):
        $array_id_benef[] = $datos_nucleo[ "IDBeneficiario" ];
  endif;
  endforeach;
  endif;
  if ( count( $array_id_benef ) > 0 ):
    $condicion_benef = " or IDSocio in (" . implode( ",", $array_id_benef ) . ") ";
  endif;

  $sql_eliminada = "Select * From ReservaGeneralEliminada Where (IDSocio = '" . $IDSocio . "' " . $condicion_benef . ")  and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' order by IDReservaGeneral Desc limit 1";
  $result_eliminada = $dbo->query( $sql_eliminada );
  if ( $dbo->rows( $result_eliminada ) > 0 ):
    //verifico si ya cumplio el tiempo limite para poder intentar reservar
    $row_reserva_eliminada = $dbo->fetchArray( $result_eliminada );
  $FechaHoraEliminacion = strtotime( '+' . $minutos_restriccion . ' minute', strtotime( $row_reserva_eliminada[ "FechaTrCr" ] ) );
  $FechaHoraActual = strtotime( date( "Y-m-d H:i:s" ) );
  if ( $FechaHoraActual <= $FechaHoraEliminacion ):
    $respuesta[ "message" ] = "La reserva no puede ser tomada ya que alguien de su grupo familiar hizo una reserva y la eliminó para esta fecha, puede volver a intentar a las: " . date( "H:i:s", $FechaHoraEliminacion );
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;
  endif;
  endif;

  //Especial para atc solo dos turnos por semana
  if ( $IDClub == 26 && empty( $Admin ) ):
    if ( $IDServicio == "1490" || $IDServicio == "2106" || $IDServicio == "2109" || $IDServicio == "2110"
    || $IDServicio == "4350" || $IDServicio == "1484" || $IDServicio == "5035" || $IDServicio == "5039" || $IDServicio == "7973" ): // tenis y clase tenis hasta 2
      $ReservasPermitidaSemana = 2;
  $condicion_reserva_verif = " and Tipo <> 'Automatica'";
  elseif ( $IDServicio == "1462" ): // masajes manicure hasta 3
    $ReservasPermitidaSemana = 3;
  $condicion_reserva_verif = "";
  else : // las demas
    $ReservasPermitidaSemana = 100;
    $condicion_reserva_verif = "";
  endif;

  $fecha_hoy_semana = date( "Y-m-d" );
  $hora_hoy_semana = date( "H:i:s" );
  $year = date( 'Y', strtotime( $Fecha ) );
  $week = date( 'W', strtotime( $Fecha ) );
  $dia_reserva_atc = date("w",strtotime($Fecha)) ;
  $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
  $fecha_lunes = $fechaInicioSemana; //Lunes
  $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
  // Valido tambien los de la misma acción
  //$accion_padre = $dbo->getFields( "Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'" );
  //$accion_socio = $dbo->getFields( "Socio", "Accion", "IDSocio = '" . $IDSocio . "'" );
  $accion_padre = $datos_socio["AccionPadre"];
  $accion_socio = $datos_socio["Accion"];

  if ( empty( $accion_padre ) ): // Es titular
    $array_socio[] = $IDSocio;
  $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
  $result_nucleo = $dbo->query( $sql_nucleo );
  while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
    $array_socio[] = $row_nucleo[ "IDSocio" ];
  endwhile;
  else :
    $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
  $result_nucleo = $dbo->query( $sql_nucleo );
  while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
    $array_socio[] = $row_nucleo[ "IDSocio" ];
  endwhile;
  endif;
  if ( count( $array_socio ) > 0 ):
    $id_socio_nucleo = implode( ",", $array_socio );
  endif;


  if( (int)$dia_reserva_atc>=1 && (int)$dia_reserva_atc<=5){
    $fecha_inicio_valida = $fechaInicioSemana; //Lunes
    $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 4 day' ) ); //Viernes
    $mensaje_reserva=" entre semana";
  }
  else{
    $proximo_sabado = strtotime('next Saturday');
    $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
    $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
    $mensaje_reserva=" los fines de semana";
  }




  //Consulto las de hoy pasada la hora actual
  $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
  $total_reservas_semana = $dbo->rows( $sql_reservas_sem );

  //Consulto la de mañana en adelante
  $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
  $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

  if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
    $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por semana por accion ";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;
  endif;


  //Especial para sabana solo dos turnos por fin semana
  if ( $IDClub == 95 && empty( $Admin ) ):
    if ( $IDServicio == "15964" && $IDServicio == "16047" ): // tenis y clase tenis hasta 2

      $condicion_reserva_verif = " and Tipo <> 'Automatica'";
      $fecha_hoy_semana = date( "Y-m-d" );
      $hora_hoy_semana = date( "H:i:s" );
      $year = date( 'Y', strtotime( $Fecha ) );
      $week = date( 'W', strtotime( $Fecha ) );
      $dia_reserva_atc = date("w",strtotime($Fecha)) ;
      $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
      $fecha_lunes = $fechaInicioSemana; //Lunes
      $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
      $accion_padre = $datos_socio["AccionPadre"];
      $accion_socio = $datos_socio["Accion"];

      if ( empty( $accion_padre ) ): // Es titular
          $array_socio[] = $IDSocio;
          $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
          $result_nucleo = $dbo->query( $sql_nucleo );
          while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
            $array_socio[] = $row_nucleo[ "IDSocio" ];
          endwhile;
      else :
        $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
        $result_nucleo = $dbo->query( $sql_nucleo );
        while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
          $array_socio[] = $row_nucleo[ "IDSocio" ];
        endwhile;
      endif;

      if ( count( $array_socio ) > 0 ):
        $id_socio_nucleo = implode( ",", $array_socio );
      endif;

      if( (int)$dia_reserva_atc>=1 && (int)$dia_reserva_atc<=5){
        $fecha_inicio_valida = $fechaInicioSemana; //Lunes
        $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 4 day' ) ); //Viernes
        $mensaje_reserva=" entre semana";
        $ReservasPermitidaSemana = 2;

      }
      else{
        $proximo_sabado = strtotime('next Saturday');
        $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
        $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 7 day' ) ); //Domingo
        $mensaje_reserva=" los fines de semana";
        $ReservasPermitidaSemana = 1;
      }


      //Consulto las de hoy pasada la hora actual
      $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
      $total_reservas_semana = $dbo->rows( $sql_reservas_sem );

      //Consulto la de mañana en adelante
      $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
      $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

      if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
        $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas " . $mensaje_reserva;
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
      return $respuesta;
      endif;
  endif;
endif;

  /*
  ///Especial atc no puede tener dos turnos el mismo dia
  if ( $IDClub == 26 && empty( $Admin )) {
    if ( $IDServicio == "1490" || $IDServicio == "2109" || $IDServicio == "2110" ){
      $sql_reserva_otro="";
      $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDServicio in (1490,2109,2110) and IDEstadoReserva=1 ";
      $mensaje_cruce="Ya tiene otra reserva en el mismo dia";
      if(!empty($sql_reserva_otro)){
          $r_reserva_otro=$dbo->query($sql_reserva_otro);
          if($dbo->rows($r_reserva_otro)>=1){
            $respuesta[ "message" ] = $mensaje_cruce;
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
          }
      }
    }
  }
  ///FIN Especial atc no puede tener dos turnos el mismo dia

  ///Especial atc si reserva clase ya debe tener una cancha
  if ( $IDClub == 26 && empty( $Admin )) {
    if ( $IDServicio == "2106" || $IDServicio == "5035" || $IDServicio == "7973"){
      $sql_reserva_otro="";
      $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDServicio in (1490,2109,2110,4350) and IDEstadoReserva=1 ";
      $mensaje_cruce="Primero debe tener una reserva de cancha antes de hacer la reserva de clase";
      if(!empty($sql_reserva_otro)){
          $r_reserva_otro=$dbo->query($sql_reserva_otro);
          if($dbo->rows($r_reserva_otro)<=0){
            $respuesta[ "message" ] = $mensaje_cruce;
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
          }
      }
    }
  }
  ///Especial atc si reserva clase ya debe tener una cancha

  ///Especial atc si reserva clase o cancha no debe tener escuela
  if ( $IDClub == 26 && empty( $Admin )) {
    if ( $IDServicio == "1484" || $IDServicio == "1490" || $IDServicio == "2106" || $IDServicio == "2109" || $IDServicio == "2110" || $IDServicio == "4350" || $IDServicio == "5035"
        || $IDServicio == "2106" || $IDServicio == "5039" || $IDServicio == "7973"){
      $sql_reserva_otro="";
      $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."'  and IDServicio in (1446) and IDEstadoReserva=1 ";
      $mensaje_cruce="Ya tienen una reserva de cancha o clase, no puede inscribirse a una escuela";
      if(!empty($sql_reserva_otro)){
          $r_reserva_otro=$dbo->query($sql_reserva_otro);
          if($dbo->rows($r_reserva_otro)>0){
            $respuesta[ "message" ] = $mensaje_cruce;
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
          }
      }
    }
    elseif($IDServicio == "1446"){
      $sql_reserva_otro="";
      $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and IDServicio in (1484,1490,2106,2109,2110,5035,2106,5039,7973) and IDEstadoReserva=1 ";
      $mensaje_cruce="Ya tienen una reserva de escuela, no puede reservar una cancha o clase";
      if(!empty($sql_reserva_otro)){
          $r_reserva_otro=$dbo->query($sql_reserva_otro);
          if($dbo->rows($r_reserva_otro)>0){
            $respuesta[ "message" ] = $mensaje_cruce;
            $respuesta[ "success" ] = false;
            $respuesta[ "response" ] = NULL;
            return $respuesta;
          }
      }

    }
  }
  ///Especial atc si reserva clase ya debe tener una cancha
  */



  //Especial comercio pereira el lunes puede hacer la reserva de maximo 3 turnos por accion con un mismo Profesor despues del lunes si puede hacer las que quiera
  $dia_actual_app=date("N");
  if ( $IDClub == 48 && empty( $Admin ) && $dia_actual_app==1){
        if ( $IDServicio == "4433" || $IDServicio == "4514" || $IDServicio == "7768" ){ // tenis y clase tenis hasta 3
          $ReservasPermitidaSemana = 3;
        }
        else{
          $ReservasPermitidaSemana = 100;
        }

      $fecha_hoy_semana = date( "Y-m-d" );
      $hora_hoy_semana = date( "H:i:s" );
      $year = date( 'Y', strtotime( $Fecha ) );
      $week = date( 'W', strtotime( $Fecha ) );
      $dia_reserva_atc = date("w",strtotime($Fecha)) ;
      $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
      $fecha_lunes = $fechaInicioSemana; //Lunes
      $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
      // Valido tambien los de la misma acción
      $accion_padre = $datos_socio["AccionPadre"];
      $accion_socio = $datos_socio["Accion"];
      if ( empty( $accion_padre ) ): // Es titular
        $array_socio[] = $IDSocio;
      $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
      $result_nucleo = $dbo->query( $sql_nucleo );
      while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
      endwhile;
      else :
        $sql_nucleo = "Select IDSocio From Socio Where (AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "') and IDClub = '" . $IDClub . "' ";
      $result_nucleo = $dbo->query( $sql_nucleo );
      while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
        $array_socio[] = $row_nucleo[ "IDSocio" ];
      endwhile;
      endif;
      if ( count( $array_socio ) > 0 ):
        $id_socio_nucleo = implode( ",", $array_socio );
      endif;



        $fecha_inicio_valida = $fechaInicioSemana; //Lunes
        $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
        $mensaje_reserva=" semana";


      //Consulto las de hoy pasada la hora actual
      $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento = '".$IDElemento."' " . $condicion_reserva_verif );
      $total_reservas_semana = $dbo->rows( $sql_reservas_sem );

      //Consulto la de mañana en adelante
      $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDServicioElemento = '".$IDElemento."' " . $condicion_reserva_verif );
      $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

      if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
        $respuesta[ "message" ] = "Lo sentimos solo se permite crear el dia de hoy: " . $ReservasPermitidaSemana . " reservas por accion ";
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
      return $respuesta;
      endif;
}
  //Fin Especial Comerio pereira


  //Especial para Country solo dos turnos entre semana y 2 fin de semana
  if ( $IDClub == 44 && empty( $Admin ) ):
    if ( $IDServicio == "3941"  ): // tenis y clase tenis hasta 2
      $ReservasPermitidaSemana = 2;
      $condicion_reserva_verif = " and Tipo <> 'Automatica'";
    else : // las demas
      $ReservasPermitidaSemana = 100;
      $condicion_reserva_verif = "";
    endif;

  $fecha_hoy_semana = date( "Y-m-d" );
  $hora_hoy_semana = date( "H:i:s" );
  $year = date( 'Y', strtotime( $Fecha ) );
  $week = date( 'W', strtotime( $Fecha ) );
  $dia_reserva = date("w",strtotime($Fecha)) ;
  $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );

  $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
  if( (int)$dia_reserva>=1 && (int)$dia_reserva<=5){
    $fecha_inicio_valida = $fechaInicioSemana; //Lunes
    $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 4 day' ) ); //Viernes
    //$fecha_fin_valida = "2019-03-08";
    //$fecha_inicio_valida = "2019-03-04";
    $mensaje_reserva=" entre semana";
  }
  else{
    $proximo_sabado = strtotime('next Saturday');
    $fecha_inicio_valida = date('Y-m-d', $proximo_sabado);
    $fecha_fin_valida = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) ); //Domingo
    $mensaje_reserva=" los fines de semana";
    //si el proximo Lunes es festivo se permite 3 reservas el fin de semana
    $proximo_Lunes = strtotime('next Monday');
    $fecha_prox_lunes= date('Y-m-d', $proximo_Lunes);
    $IDFestivo = $dbo->getFields( "Festivos", "IDFestivo", "Fecha = '" . $fecha_prox_lunes . "'" );
    if(!empty($IDFestivo))
      $ReservasPermitidaSemana = 3;
  }



      //Consulto las de hoy pasada la hora actual ENTRE SEMANA
      $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
      $total_reservas_semana = $dbo->rows( $sql_reservas_sem );
      //Consulto la de mañana en adelante
      $sql_reservas_sem = $dbo->query( "SELECT * From ReservaGeneral Where ( IDSocio in (" . $IDSocio . ") and IDSocioBeneficiario = '".$IDBeneficiario."') and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_inicio_valida . "' and Fecha <= '" . $fecha_fin_valida . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
      $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

      if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
        $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas activas  " . $mensaje_reserva;
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
      endif;



  endif;

  //Especial atc para Iluminados y Exteriores los sabados, Domingos solo se puede reservar por app hasta las 7am
  if ( $IDClub == 26 && empty( $Admin ) ):
    $dia_semana_reserva = date( "w", strtotime( $Fecha ) );
    if ( date( "H:i:s" ) >= "07:00:00" &&  ( ( ( $IDServicio == "1490" || $IDServicio == "2109" ) && ( $dia_semana_reserva == "6" || $dia_semana_reserva == "0"   ))  )):
      //$respuesta[ "message" ] = "Lo sentimos solo se permiten reservar por el app hasta las 7am para este dia ";
      //$respuesta[ "success" ] = false;
      //$respuesta[ "response" ] = NULL;
      //return $respuesta;
    endif;
  endif;
  //FIN ESPECIAL atc




  //Especial Country para reservas de 6am y 7am  solo hasta las 8pm del dia anterior
  if ( ($IDClub == 44 || $IDClub == 8 ) && empty( $Admin ) ):
    $dia_manana = date('Y-m-d',time()+84600);
    $fecha_hoy_v=date("Y-m-d");
    if ( (   ( date( "H:i:s" ) >= "20:00:00"  && $dia_manana == $Fecha )  || $fecha_hoy_v == $Fecha ) &&  ($IDServicio == "3941" || $IDServicio == "36")  &&  ($Hora == '06:00:00' || $Hora == '07:00:00' )  && (!empty( $ListaAuxiliar ) && $ListaAuxiliar!="[]" )):
      $respuesta[ "message" ] = "Lo sentimos solo se permiten reservar con profesor/monitor por el app hasta antes de las 8pm para turnos de 6am y 7am ".$ListaAuxiliar;
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
      return $respuesta;
    endif;
  endif;
  //FIN ESPECIAL country

  //Especial Country Sala belleza servicios que no se pueden cruzar
  if ( ($IDClub == 44 ) && empty( $Admin ) ):
    $sql_reserva_otro="";
    switch($IDServicio){
      case "11734": //maquillaje
        //no se puede tener otra reserva en el mismo horario
        $sql_reserva_otro="SELECT IDReservageneral  FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDServicio in (3897,11732,11733,11736,12470) and IDEstadoReserva = 1";
        $mensaje_cruce="Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar maquillaje a la misma hora";
      break;
      case "11736": //tratamientos quimicos
        $sql_reserva_otro="SELECT IDReservageneral FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDServicio in (11732,11733,11734,11736,12470) and IDEstadoReserva = 1";
        $mensaje_cruce="Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar tratamiento quimicos a la misma hora";
      break;
      case "11733": // corte
      case "11732": // cepillado
        $sql_reserva_otro="SELECT IDReservageneral FROM ReservaGeneral WHERE IDSocio ='".$IDSocio."' and IDSocioBeneficiario = '".$IDSocioBeneficiario."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDServicio in (11736,11734) and IDEstadoReserva = 1 ";
        $mensaje_cruce="Ya tiene otra reserva en sala de belleza a la misma hora no es posible solicitar peinado/corte/cepillado a la misma hora";
      break;
    }


    if(!empty($sql_reserva_otro)){
        $r_reserva_otro=$dbo->query($sql_reserva_otro);
        if($dbo->rows($r_reserva_otro)>=1){
          $respuesta[ "message" ] = $mensaje_cruce;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
        }
    }
  endif;
  //FIN ESPECIAL country

  //Especial Country Sala belleza Keratina y antifrizz se reserva turno a otras personas
  if ( ($IDClub == 44 ) && $IDServicio ==11736 && ($IDTipoReserva=="1482" || $IDTipoReserva=="1483" || $IDTipoReserva=="1484") ){

  }


  //FIN ESPECIAL country



  //Especial Comercio pereira cuando se selecciona auxiliar solo permite cuando es 1 hora
  if ( ($IDClub == 48 ) ){
    if($ListaAuxiliar=="null")
      $ListaAuxiliar="";

    if ( ($IDServicio == "4514") && $IDTipoReserva != 881){
      if( (!empty( $ListaAuxiliar ) && $ListaAuxiliar!="[]")   ):
        $con_aux="S";
      elseif($IDAuxiliar!=""):
        $con_aux="S";
      endif;

      if($con_aux=="S"){
        $respuesta[ "message" ] = "Para reservas con monitor solo se pemite 1 hora no 2 horas. ";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
      }
    }
  }
  //FIN COMERCIO

  //Especial Lagartos la cancha nocturnas solo se puede hasta las 5:30pm
  if ( ($IDClub == 7 && $IDServicio==221 && date("Y-m-d")==$Fecha && $Hora>="18:00:00" && date("H:i:s")>='17:30:00' ) && empty( $Admin ) ):
      $respuesta[ "message" ] = "Lo sentimos solo se permiten reservar estas canchas antes de las 5:30pm";
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
      return $respuesta;
  endif;
  //FIN ESPECIAL Lagartos

  // Especial lagartos si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis$Fecha = "2019-04-11";
  if($IDServicio==43 || $IDServicio==221 || $IDServicio==629){
    $bloqueo_incumplida="N";
    $Hace8dias = strtotime ( '-7 day' , strtotime ( $Fecha ) ) ;
    $Hace8dias = date ( 'Y-m-d' , $Hace8dias );
    $sql_inc="SELECT IDReservaGeneral,IDSocioBeneficiario,IDSocio FROM ReservaGeneral WHERE Fecha='".$Hace8dias."' and (Cumplida = 'N' or Cumplida = 'P' ) and (IDSocio = '".$IDSocio."' or IDSocioBeneficiario = '".$IDSocio."') and IDServicio in (43, 221, 629)";
    $result_incumplidat=$dbo->query($sql_inc);
    if($dbo->rows($result_incumplidat)>0){
      $row_datos_reserva_inc=$dbo->fetchArray($result_incumplidat);
      if($row_datos_reserva_inc["IDSocioBeneficiario"]>0 && ($row_datos_reserva_inc["IDSocioBeneficiario"]==$IDSocio || $row_datos_reserva_inc["IDSocioBeneficiario"]==$IDBeneficiario))
        $bloqueo_incumplida="S";
      elseif($row_datos_reserva_inc["IDSocioBeneficiario"]<=0 && $row_datos_reserva_inc["IDSocio"]==$IDSocio)
        $bloqueo_incumplida="S";

        if($bloqueo_incumplida=="S"){
          $respuesta[ "message" ] = "Lo sentimos, el dia ".$Hace8dias." tiene una reserva incumplida, no puede tomar reservas el dia de hoy para tenis";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
        }
    }
  }
  // FIN Especial lagartos si hace 8 dias incumpli oreserva no lo deja en el dia actual para Tennis$Fecha = "2019-04-11";




  //Especial para atc solo dos turnos por semana
  if ( $IDClub == 7 && empty( $Admin ) ){
    if ( $IDServicio == "2209" ): // Retos lagartos
      $ReservasPermitidaSemana = 1;
      $condicion_reserva_verif = " and Tipo <> 'Automatica'";


        $fecha_hoy_semana = date( "Y-m-d" );
        $hora_hoy_semana = date( "H:i:s" );
        $year = date( 'Y', strtotime( $Fecha ) );
        $week = date( 'W', strtotime( $Fecha ) );
        $fechaInicioSemana = date( 'Y-m-d', strtotime( $year . 'W' . str_pad( $week, 2, '0', STR_PAD_LEFT ) ) );
        $fecha_lunes = $fechaInicioSemana; //Lunes
        $fecha_domingo = date( 'Y-m-d', strtotime( $fechaInicioSemana . ' 6 day' ) );; //Domingo
        // Valido tambien los de la misma acción
        $accion_padre = $datos_socio["AccionPadre"];
        $accion_socio = $datos_socio["Accion"];

        if ( empty( $accion_padre ) ): // Es titular
          $array_socio[] = $IDSocio;
          /*
          $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
          $result_nucleo = $dbo->query( $sql_nucleo );
          while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
            $array_socio[] = $row_nucleo[ "IDSocio" ];
          endwhile;
          */
        endif;
        if ( count( $array_socio ) > 0 ):
          $id_socio_nucleo = implode( ",", $array_socio );
        endif;

        //Consulto las de hoy pasada la hora actual
        $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDSocioBeneficiario = '".$IDBeneficiario."' and (Fecha='" . date( "Y-m-d" ) . "' and Hora >= '" . date( "H:i:s" ) . "' ) and (Fecha >= '" . $fecha_lunes . "' and Fecha <= '" . $fecha_domingo . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
        $total_reservas_semana = $dbo->rows( $sql_reservas_sem );

        //Consulto la de mañana en adelante
        $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and IDSocioBeneficiario = '".$IDBeneficiario."' and  Fecha > '" . date( "Y-m-d" ) . "'  and (Fecha >= '" . $fecha_lunes . "' and Fecha <= '" . $fecha_domingo . "') and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
        $total_reservas_semana += ( int )$dbo->rows( $sql_reservas_sem );

        if ( ( int )$total_reservas_semana >= ( int )$ReservasPermitidaSemana ):
          $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por fin de semana ";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        endif;
      endif;
}

  //Especial para clubes que solo se permite x reserva por accion por dia
  $maximo_turnos_dia = $datos_servicio["NumeroReservasPermitidaAccion"];
  if ( ( int )$maximo_turnos_dia > 0 && empty( $Admin ) ):
    unset( $array_socio );
  $condicion_reserva_verif = " and Tipo <> 'Automatica'";
  $ReservasPermitidaSemana = $maximo_turnos_dia;
  // Valido tambien los de la misma acción
  $accion_padre = $datos_socio["AccionPadre"];
  $accion_socio = $datos_socio["Accion"];

  if ( empty( $accion_padre ) ): // Es titular
    $array_socio[] = $IDSocio;
  $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
  $result_nucleo = $dbo->query( $sql_nucleo );
  while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
    $array_socio[] = $row_nucleo[ "IDSocio" ];
  endwhile;
  else :
    $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
  $result_nucleo = $dbo->query( $sql_nucleo );
  while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
    $array_socio[] = $row_nucleo[ "IDSocio" ];
  endwhile;
  endif;
  if ( count( $array_socio ) > 0 ):
    $id_socio_nucleo = implode( ",", $array_socio );
  endif;

  //$sql_reservas_sem = $dbo->query("Select * From ReservaGeneral Where IDSocio in (".$id_socio_nucleo.") and (Fecha='".$Fecha."' and Hora >= '".date("H:i:s")."' )  and IDServicio = '".$IDServicio."' and IDEstadoReserva = '1' " . $condicion_reserva_verif);
  $sql_reservas_sem = $dbo->query( "Select * From ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ") and (Fecha='" . $Fecha . "')  and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_reserva_verif );
  $total_reservas_dia_nucleo = $dbo->rows( $sql_reservas_sem );
  if ( ( int )$total_reservas_dia_nucleo >= ( int )$ReservasPermitidaSemana ):
    $respuesta[ "message" ] = "Lo sentimos solo se permiten " . $ReservasPermitidaSemana . " reservas por dia por accion ";
  $respuesta[ "success" ] = false;
  $respuesta[ "response" ] = NULL;
  return $respuesta;
  endif;

  endif;

  //Especial Guaymaral si es clase se reserva la de dentro de ocho dias automaticamente siempre y cuando este aciva la fecha
  if ( $IDClub == 8 && $IDServicio == 41 && $IDTipoReserva == "517" ):
    $RepetirFechaFinal = strtotime( '+1 week', strtotime( $Fecha ) );
  $minima_fecha = date( "Y-m" ) . "-14";
  $maxima_fecha = new DateTime();
  $maxima_fecha->modify( 'last day of this month' );
  $maxima_fecha->format( 'Y-m-d' );
  if ( ( int )date( "d" ) <= 14 && ( int )date( "d", $RepetirFechaFinal ) <= 14 ):
    $permite_repetir = "S";
  elseif ( ( int )date( "d" ) >= 15 && $RepetirFechaFinal <= strtotime( $maxima_fecha->format( 'Y-m-d' ) ) ):
    $permite_repetir = "S";
  else :
    $permite_repetir = "N";
  endif;
  if ( $permite_repetir == "S" ):
    $mensaje_especial_repetir = " Se realizó un reserva automatica para el día " . date( "Y-m-d", $RepetirFechaFinal );
  $Repetir = "S";
  $Periodo = "Semana";
  $RepetirFechaFinal = strtotime( '+8 day', strtotime( $Fecha ) );
  $RepetirFechaFinal = date( "Y-m-d", $RepetirFechaFinal );
  else :
    $mensaje_especial_repetir = " No se pudo realizar la reserva automatica en la siguiente semana ya que la fecha aun no esta disponible";
  endif;
  endif;
  //Fin validación especial

  //Especial Aeroclub cuando es crucero separa varios días
   //if ( $IDClub == 36 && $IDServicio == 3608 && ( $IDTipoReserva == 718 || $IDTipoReserva == 745 || $IDTipoReserva == 746 || $IDTipoReserva == 747 || $IDTipoReserva == 748 ) ):
    if ( $IDClub == 36 && $IDServicio == 4371 && ( $IDTipoReserva == 787 || $IDTipoReserva == 788 || $IDTipoReserva == 789 || $IDTipoReserva == 790 || $IDTipoReserva == 791 ) ):
    $datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
    $cantidad_dias_agregar = $datos_tipo_reserva[ "NumeroDias" ];
    if((int)$datos_tipo_reserva[ "NumeroDias" ]>1):
        //$IDTipoReserva = "";
        $Repetir = "S";
        $Periodo = "Dia";
        $HastaFechaFinal = strtotime( '+' . $cantidad_dias_agregar . ' day', strtotime( $Fecha ) );
        $RepetirFechaFinal = date( "Y-m-d", $HastaFechaFinal );
        // de una vez valido que el avion no haya sido reservado en los siguientes dias
        $fechaInicioVal = strtotime( $Fecha );
        $fechaFin = strtotime( $RepetirFechaFinal );
        $condicion_multiple_elemento_av = SIMWebService::verifica_elemento_otro_servicio( $IDElemento,"" );
        $cuentafecha=0;
        for ( $contador_fecha = $fechaInicioVal; $contador_fecha <= $fechaFin; $contador_fecha += 86400 ):

          if($cuentafecha>0): // valido desde la segunda fecha en adelante
            $fecha_validar.="S".$cuentafecha;
            $fecha_validar_avion=date( "Y-m-d", $contador_fecha );
            $sql_reserva_elemento_avion = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDServicioElemento in (" . $condicion_multiple_elemento_av . ") and Fecha = '" . $fecha_validar_avion . "' and (IDEstadoReserva = 1 or IDEstadoReserva=3) Limit 1 ";
            $r_reserva_elemento_avion = $dbo->query($sql_reserva_elemento_avion);
            if($dbo->rows($r_reserva_elemento_avion)>0):
              $respuesta[ "message" ] = "Lo sentimos el avion ya esta reservado el ".	$fecha_validar_avion;
              $respuesta[ "success" ] = false;
              $respuesta[ "response" ] = NULL;
              return $respuesta;
            endif;
          endif;
          $cuentafecha++;
        endfor;
    endif;
  endif;
  //Fin validación especial


  //Especial Arrayanes Colombia para golf fines de semana solo permite reservas de socios con handicap
  if ( $IDClub == 11 && $IDServicio == 122 && ( date( "w", strtotime( $Fecha ) ) == 0 || date( "w", strtotime( $Fecha ) ) == 6 ) && $Hora <= "11:20:00" ):
      $datos_socio_valida_per = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );
      $id_socio_permiso = $dbo->getFields( "SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio_valida_per[ "NumeroDocumento" ] . "' and IDClub = '" . $IDClub . "'" );
      if ( empty( $id_socio_permiso ) ):
          $respuesta[ "message" ] = "Lo sentimos no tiene permisos para reservar los fines de semana ";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
      else:
          // verifico que los invitados tambien tengan handicap
          $nuevacadena_hand = str_replace( 'Optional("', "", $Invitados );
          $nuevacadena_hand = str_replace( '")', "", $nuevacadena_hand );
          $Invitados_hand = $nuevacadena_hand;
          $datos_invitado_hand = json_decode( $Invitados_hand, true );

           if ( count( $datos_invitado_hand ) > 0 ):
              foreach ( $datos_invitado_hand as $detalle_datos ):
                  $IDSocioInvitadoHand = $detalle_datos[ "IDSocio" ];
                  if ( !empty( $IDSocioInvitadoHand ) ):
                    $datos_socio_valida_per_hand = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocioInvitadoHand . "' ", "array" );
                    $id_socio_permiso = $dbo->getFields( "SocioPermisoReserva", "IDSocioPermisoReserva", "NumeroDocumento = '" . $datos_socio_valida_per_hand[ "NumeroDocumento" ] . "' and IDClub = '" . $IDClub . "'" );
                    if ( empty( $id_socio_permiso ) ):
                      $respuesta[ "message" ] = "Lo sentimos su invitado " . utf8_decode($datos_socio_valida_per_hand["Nombre"]." ".$datos_socio_valida_per_hand["Apellido"]) . " " . "no tiene permisos para reservar los fines de semana";
                      $respuesta[ "success" ] = false;
                      $respuesta[ "response" ] = NULL;
                      return $respuesta;
                    endif;
                  endif;
                endforeach;
          endif;
      endif;
   endif;
//Fin validación especial

//Validacion especial Polo solo 1 turno por elemento en practicas
if($IDClub==37 && $IDServicio=="3575"){
  $id_reserva_general_soc=(int)$dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDSocio = '" . IDSocio . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 1 and IDServicioElemento = '".$IDElemento."'" );
  if($id_reserva_general_soc>0){
    $respuesta[ "message" ] = "Ya tiene una reserva en esta hora por favor verifique Mis Reservas";
    $respuesta[ "success" ] = false;
    $respuesta[ "response" ] = NULL;
    return $respuesta;
  }

}


//Especial para cuando el campo de repetir se pregunta en la reserva
$array_Campos = $Campos;
$array_Campos = json_decode( $Campos, true );
if ( count( $array_Campos ) > 0 ):
  foreach ( $array_Campos as $id_valor_campo => $valor_campo ):
    if($valor_campo[ "IDCampo" ]==31 && (int)$valor_campo[ "Valor" ]>0){
      $Repetir = "S";
      $Periodo = "Dia";
      $dias_repetir=$valor_campo[ "Valor" ]-1;
      $RepetirFechaFinal = strtotime( '+'.$dias_repetir.' day', strtotime( $Fecha ) );
      $RepetirFechaFinal = date( "Y-m-d", $RepetirFechaFinal );
    }
endforeach;
endif;

//Validacion especial Farallones solo se puede 1 depilación por hora
/*
if($IDClub==29 && $IDServicio=="1772"){
  $id_reserva_general_soc=(int)$dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 1 and IDServicioTipoReserva = '618' " );
  if($id_reserva_general_soc>0){
    $respuesta[ "message" ] = "Lo sentimos ya existe una reserva de depilacion a esta hora y solo es posible 1 por hora, intente con otra hora";
    $respuesta[ "success" ] = false;
    $respuesta[ "response" ] = NULL;
    return $respuesta;
  }
}
*/


  //Valido si la reserva pide un tipo (sencillo, doble, 2 turnos, etc) y si esta vacio le asigno alguno
  $tipo_reserva_servicio = "Select * From ServicioTipoReserva Where IDServicio = '" . $IDServicio . "' and Activo = 'S' Order by Orden Desc";
  $result_reserva_servicio = $dbo->query( $tipo_reserva_servicio );
  if ( count( $dbo->rows( $result_reserva_servicio ) > 0 ) && empty( $IDTipoReserva ) ):
    //le asigno algun tipo ya que es obligatorio
    while ( $row_reserva_servicio = $dbo->fetchArray( $result_reserva_servicio ) ):
      $IDTipoReserva = $row_reserva_servicio[ "IDServicioTipoReserva" ];
  endwhile;
  endif;



  //Validacion del formato de hora, el app puede enviar con a.m o p.m
  $Hora = SIMWebService::validar_formato_hora( $Hora );

  //if ( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) && $Hora != "00:00:00" ) {
    if ( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora )  ) {

    //verifico que el socio exista y pertenezca al club
    $id_socio = $datos_socio["IDSocio"];

      if ( !empty( $id_socio ) ) {


        // Obtener la disponibilidad utilizada al consultar la reserva
        $id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada( $IDServicio, $Fecha, $IDElemento, $Hora );


        //Valido que no se pueda tomar varios turnos seguidos
        $PermiteReservaSeguida = $dbo->getFields( "Disponibilidad", "PermiteReservaSeguida", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
        $PermiteReservaSeguidaNucleo = $dbo->getFields( "Disponibilidad", "PermiteReservaSeguidaNucleo", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
        if ( $PermiteReservaSeguida != "S" )
          $flag_turno_seguido = SIMWebService::validar_turnos_seguidos( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario, $TipoBeneficiario, $PermiteReservaSeguidaNucleo );
        else
          $flag_turno_seguido = 0;






        //Especial para medellin dobles no se permite los sabados
        if ( $IDClub == 20 && ( date( "N", strtotime( $Fecha ) ) == "6" ) && ( $IDTipoReserva == 96 || $IDTipoReserva == 94 ) ):
          $respuesta[ "message" ] = "Lo sentimos dobles no se puede los Sabados";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        endif;


        if ( ( $IDClub == "9" || $IDClub == "8" ) && empty( $Admin ) && $IDServicio == "89" ):
          //Valido regla especial en Esqui si tiene dos turnos seguidos no permite reservar mas si no solo deja las configuradas (Caso especial Mesa de Yeguas)
          $regla_no_cumplida = SIMWebService::validar_regla_turnos( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva );
        if ( $regla_no_cumplida > 0 ):
          switch ( $regla_no_cumplida ):
          case "1":
        $mensaje_regla_no_cumplida = "Lo sentimos, ya tomo dos turnos seguidos, no puede reservas mas turnos en este dia ";
        break;
        case "2":
          $mensaje_regla_no_cumplida = "Lo sentimos, ya reservo un turno, no puede tomar turnos seguidos en este dia";
          break;
          endswitch;
          $respuesta[ "message" ] = $mensaje_regla_no_cumplida;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          endif;

          if (
            ( ( $IDClub == "900" || $IDClub == "800" ) && empty( $Admin ) && ( $IDServicio == "240" || $IDServicio == "479" || $IDServicio == "89" ) ) ||
            ( ( $IDClub == "20" ) && empty( $Admin ) && ( $IDServicio == "571" ) && date( "N", strtotime( $Fecha ) ) == 6 )
          ):
            //Valido regla especial tenis my solo dos turnos por accion en un mismo dia en Temporada alta
            //Valido regla especial tenis MEdellin solo dos turnos por accion en un mismo dia los sabados
            $regla_no_cumplida_tenis = SIMWebService::validar_regla_turnos_tenis( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva );
          if ( ( int )$regla_no_cumplida_tenis > 0 ):
            $mensaje_regla_no_cumplida_tenis = "Lo sentimos, solo se puede tomar dos turnos por accion en el dia";
          $respuesta[ "message" ] = $mensaje_regla_no_cumplida_tenis;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          endif;



          //Especial para Lagartos en clases natación solo permite tomar reservas antes de las 8am el mismo dia
          /*
          if ( ( $IDClub == "7" || $IDClub == "800" ) && empty( $Admin ) && $IDServicio == "94" ):
            if ( date( "Y-m-d" ) == $Fecha && strtotime( date( "Y-m-d H:i:s" ) ) >= strtotime( date( "Y-m-d 08:00:00" ) ) ):
              $respuesta[ "message" ] = "Lo sentimos, solo se puede reservar clases para hoy hasta antes de las 8 a.m.";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          endif;
          */



          //$rand_seg2 = rand(1,2);
          //sleep($rand_seg2);




          //Valido que se tengan los invitados minimos y maximos para reservar
          // elimino la pabra optional segun bug detectado en una actualizacion en ios
          $nuevacadena = str_replace( 'Optional("', "", $Invitados );
          $nuevacadena = str_replace( '")', "", $nuevacadena );
          $Invitados = $nuevacadena;
          $datos_invitado = json_decode( $Invitados, true );

          // Si el numero de turnos es mayor a 1 multiplico el minimo de la disponibilidad * el numero de turnos para saber el minimo y validar
          if ( ( int )$NumeroTurnos > 1 ):
            $MinimoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad", "MinimoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $MinimoInvitados = ( int )( $MinimoInvitadosDisponibilidad * $NumeroTurnos ) - 1; // Le resto 1 para que cuente al socio que hace la reserva
          else :
            $MinimoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad", "MinimoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $MinimoInvitados = ( int )$MinimoInvitadosDisponibilidad - 1;
          endif;

          if ( ( int )$NumeroTurnos > 1 ):
            $MaximoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $MaximoInvitados = $MaximoInvitadosDisponibilidad * $NumeroTurnos;
          else :
            $MaximoInvitados = $dbo->getFields( "Disponibilidad", "MaximoInvitados", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          endif;


          // Si es desde el administrador permito agregar los invitados
          if ( !empty( $Admin )  ){
            $MaximoInvitados=100;
          }


          $cantidad_invitado = json_decode( $Invitados, true );








          // Si agrega un aux por ejemplo boleador lo cuento como invitado
          if ( !empty( $IDAuxiliar ) ):


            $IDAuxiliar = $IDAuxiliar . ",";
          $cantidad_auxiliar = 1;
          //Verifico si el auxiliar esta disponible en esta fecha hora
          $id_reserva_aux = "";
          if ( ( $IDClub == "8" || $IDClub == "10" ) && ( $IDServicio == "316" || $IDServicio == "317" ) ):
            $id_reserva_aux = SIMWebServiceApp::validar_disponibilidad_auxiliar( $IDAuxiliar, $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub );
          $mensaje_auxiliar_no_dispo = "La masajista seleccionada no esta disponible en esta fecha/hora, por favor seleccione otra";
          else :
            //Pongo un tiempo de espera por si ingresan varios al mismo tiempo
            $id_reserva_aux = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );
          $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora";
          endif;
          if ( !empty( $id_reserva_aux ) ):
            $respuesta[ "message" ] = $mensaje_auxiliar_no_dispo;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          else :
            $cantidad_auxiliar = 0;
          endif;

          //Cuando se puede escoger multiples auxiliares
          $datos_auxiliares = json_decode( $ListaAuxiliar, true );
          if ( count( $datos_auxiliares ) > 0 ):
            $cantidad_auxiliar = count( $datos_auxiliares );
          //$ArrayAuxiliar = implode(",",$datos_auxiliares);
          foreach ( $datos_auxiliares as $key_aux => $auxiliar_seleccionado ):
            $array_id_auxiliar[] = $auxiliar_seleccionado[ "IDAuxiliar" ];


          endforeach;
          if ( count( $array_id_auxiliar ) > 0 ):
            $IDAuxiliar = implode( ",", $array_id_auxiliar ) . ",";
          endif;
          endif;



          if ( !empty( $IDAuxiliar ) ):
            //Actualizo la reserva separada con el dato del auxiliar
            $update_reserva = "Update ReservaGeneral Set IDAuxiliar = '" . $IDAuxiliar . "', IDSocioBeneficiario='".$IDBeneficiario."' Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "' and Hora = '" . $Hora . "' and IDServicio = '" . $IDServicio . "' and IDSocio = '" . $IDSocio . "' and IDEstadoreserva = '3' ";
            $dbo->query( $update_reserva );
          endif;



          if ( ( count( $datos_invitado ) + $cantidad_auxiliar ) >= ( int )$MinimoInvitados ):
            if ( ( count( $datos_invitado ) + $cantidad_auxiliar ) <= ( int )$MaximoInvitados ):
              //if (1==1):




              // Si es Admin si puede reservas turnos seguidos
              if ( !empty( $Admin ) ):
                $flag_turno_seguido = 0;
          endif;




          if ( $flag_turno_seguido == 0 ):
            $fecha_disponible = 0;
          //Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
          $array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio( $IDClub, $IDServicio );
          foreach ( $array_disponibilidad[ "response" ][ 0 ][ "Fechas" ] as $id_fecha => $datos_fecha ):
            if ( $datos_fecha[ "Fecha" ] == $Fecha && $datos_fecha[ "Activo" ] == "S" ):
              $fecha_disponible = 1;
          endif;
          endforeach;

          // Si es Admin si puede reservas cualquier fecha
          //Ene 27 El rancho solicitan que ellos no puedan tomar turnos si no esta activo el dia
          if ( !empty( $Admin ) && $IDClub != "12" ):
            $fecha_disponible = 1;
          endif;

          if ( !empty( $IDBeneficiario ) && !empty( $TipoBeneficiario ) ):
            if ( $TipoBeneficiario == "Invitado" )
              $IDInvitadoBeneficiario = $IDBeneficiario;
            elseif ( $TipoBeneficiario == "Socio" )
              $IDSocioBeneficiario = $IDBeneficiario;
          endif;


          //Si el numero maximo de invitados esta en 0 no dejo que la reserva sea a un invitado seleccionado por beneficiarios
          if ( $MaximoInvitados <= 0 && ( int )$IDInvitadoBeneficiario > 0 ):
            $respuesta[ "message" ] = "Lo sentimos el dia de hoy no es posible tomar reserva a nombre de un invitado";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;


          if ( $fecha_disponible == 1 ):

            if ( $IDClub == 1 || $IDClub == 23 || $IDClub == 16 || $IDClub == 44):
              //Para Guaymaral si deja tomar otro turno asi tenga reserva de cancha automatica
              $condicion_automatica = " and Tipo <> 'Automatica' ";
          endif;

          //Verifico que el socio no tenga mas de x reservas en el mismo dia dependiendo la conf de disponibilidad
          //Para Mess Yeguas en temporada alta no pemite asi se la haya realizado a un invitado o benef para los demas clubes si lo permite
          //Para Medellin si es sabado no permite mas de dos reservas asi sea para beneficiario
          if ( $IDClub == "900" || $IDClub == 800 ):
            $sql_reservas_dia = $dbo->query( "Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and Tipo <> 'Automatica'" );
          else :
            $sql_reservas_dia = $dbo->query( "Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDSocioBeneficiario = '" . ( int )$IDSocioBeneficiario . "' and IDEstadoReserva = '1' " . $condicion_automatica );
          endif;


          $total_reserva_socio = ( int )$dbo->rows( $sql_reservas_dia );

          //Valido que el beneficiario al que se esta tomando la reserva ya no tenga otra reserva y asi cumplir lo de maximo de reservas por dia tambien
          $condicion_beneficiario = "";
          if ( !empty( $IDBeneficiario ) ):

            if ( !empty( $IDSocioBeneficiario ) ):
              $sql_reservas_dia_benef = $dbo->query( "Select * From ReservaGeneral Where IDSocio = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica );
              $total_reserva_socio += ( int )$dbo->rows( $sql_reservas_dia_benef );

              $sql_reservas_dia_benef = $dbo->query( "Select * From ReservaGeneral Where IDSocio <> '" . $IDSocio . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica );
              $total_reserva_socio += ( int )$dbo->rows( $sql_reservas_dia_benef );
          endif;

          //Invitado
          if ( !empty( $IDInvitadoBeneficiario ) ):
            $sql_reservas_dia_benef = $dbo->query( "Select * From ReservaGeneral Where IDInvitadoBeneficiario = '" . $IDInvitadoBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' " . $condicion_automatica );
          $total_reserva_socio += ( int )$dbo->rows( $sql_reservas_dia_benef );
          endif;


          endif;
          //Fin Validacion


          //Valido si en la configuracion permite a un socio tomar otro turno dspues que cumpla el que tiene en el dia solo aplica si esta en el dia actual
          $PermiteReservaDespuesdeprimerturno = $dbo->getFields( "Disponibilidad", "PermiteReservaCumplirTurno", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          if ( $PermiteReservaDespuesdeprimerturno == "S" && $Fecha == date( "Y-m-d" ) && $total_reserva_socio >= $MaximoReservaSocioServicio ):
            $TiempoDespues = ( int )$dbo->getFields( "Disponibilidad", "TiempoDespues", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $MedicionTiempoDespues = $dbo->getFields( "Disponibilidad", "MedicionTiempoDespues", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $IntervaloTurno = $dbo->getFields( "Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $id_disponibilidad . "'" );

          switch ( $MedicionTiempoDespues ):
        case "Dias":
          $minutos_posterior_turno = ( 60 * 24 ) * $TiempoDespues;
          break;
        case "Horas":
          $minutos_posterior_turno = 60 * $TiempoDespues;
          break;
        case "Minutos":
          $minutos_posterior_turno = $TiempoDespues;
          break;
        default:
          $minutos_posterior_turno = 0;
          endswitch;

          //Le sumo el intervalo del turno para calcular la siguiente hora que puede reservar despues de finalizar el turno
          $minutos_posterior_turno += ( int )$IntervaloTurno;

          //Consulto cual es la utima que reserva que tiene en el dia para calcula con esa hora
          if ( !empty( $IDBeneficiario && !empty( $IDSocioBeneficiario )) ){
            // es para un beneficiario
              $sql_reserva_dia_hora = "Select * From ReservaGeneral Where IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' Order by Hora Desc Limit 1";
          }
          else{
              $sql_reserva_dia_hora = "Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and IDEstadoReserva = '1' and IDSocioBeneficiario = '" . ( int )$IDBeneficiario . "' Order by Hora Desc Limit 1";
          }

          $result_reserva_dia_hora = $dbo->query( $sql_reserva_dia_hora );
          $row_reserva_dia_hora = $dbo->fetchArray( $result_reserva_dia_hora );
          $ultimo_turno_dia = $Fecha . " " . $row_reserva_dia_hora[ "Hora" ];
          $hora_actual_peticion = date( 'Y-m-d H:i:s' );
          $hora_volver_reservar = strtotime( '+' . $minutos_posterior_turno . ' minute', strtotime( $ultimo_turno_dia ) );
          //echo "Puede reservar a las " . date("Y-m-d H:i:s",$hora_volver_reservar);
          if ( strtotime( $hora_actual_peticion ) >= $hora_volver_reservar ):
            $total_reserva_socio = 0;
          else :
            $mensaje_opcion_reserva = "Puede volver a reservar despues de: " . $TiempoDespues . " " . $MedicionTiempoDespues . " de cumplir la reserva del dia";
          endif;
          endif;




          // Si es Admin si puede reservar mas de un turno por dia
          if ( !empty( $Admin ) ):
            if ( $IDClub != "12" && $IDClub != "8" ): //para el Rincon no permite asi sea admin
              //verifico que si pueda reservar mientras no sea la misma hora en el mismo servicio
              $sql_reservas_dia_hora = $dbo->query( "Select * From ReservaGeneral Where IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and IDServicio = '" . $IDServicio . "' and Hora = '" . $Hora . "' and IDServicioElemento <> '" . $IDElemento . "' and  IDSocioBeneficiario = '" . ( int )$IDBeneficiario . "' and IDEstadoReserva = '1' and IDServicioElemento = '" . $IDElemento . "'" );
          $total_reserva_socio_hora = ( int )$dbo->rows( $sql_reservas_dia_hora );
          if ( $total_reserva_socio_hora > 0 ):
            $total_reserva_socio = 100;
          else :
            $total_reserva_socio = 0;
          endif;
          endif;
          //$total_reserva_socio = 0;
          $UsuarioCreacion = "Starter";
          else :
            if ( !empty( $IDUsuarioReserva ) )
              $UsuarioCreacion = "Empleado";
            else
              $UsuarioCreacion = "Socio";
          endif;

          //Consulto el parametro en disponibilidad de cuantas veces puede reervar el socio el mismo servicio en el mismo dia
          $MaximoReservaSocioServicio = $dbo->getFields( "Disponibilidad", "MaximoReservaDia", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          //if($total_reserva_socio<1):

          //Especial Bogota tenis si es fin semana y reserva cancha despues de 12:15
          if ( $IDClub == 72):
            $dia_semana_reserva = date( "w", strtotime( $Fecha ) );
            if (  $Hora >= "12:15:00" &&  ( ( ( $IDServicio == "8649" ) && ( $dia_semana_reserva == "6" || $dia_semana_reserva == "0"   ))  )):
              $MaximoReservaSocioServicio=2;
            endif;
          endif;
          //FIN BTCC



          if ( $total_reserva_socio < $MaximoReservaSocioServicio ){



            if ( $Repetir == "S" ):

              //Consulto el limite de reservas que se pueda hacer para calcular la fecha final
              $MedicionRepeticion = $dbo->getFields( "Disponibilidad", "MedicionRepeticion", "IDDisponibilidad = '" . $IDDisponibilidad . "'" );
          switch ( $MedicionRepeticion ):
        case "Dia":
          $periodo_sumar = "day";
          $dato_sumar=1;
          break;
        case "Semana":
          $periodo_sumar = "week";
          $dato_sumar=1;
          break;
        case "Quincenal":
          $periodo_sumar = "day";
          $dato_sumar=14;
        break;
        case "Mes":
          $periodo_sumar = "month";
          $dato_sumar=1;
          break;
        default:
          $periodo_sumar = "day";
          $dato_sumar=1;
          endswitch;

          //periodo a sumar dependiendo de lo que el socio escoja en el app
          switch ( $Periodo ):
        case "Dia":
          $periodo_sumar_app = "day";
          $dato_sumar=1;
          break;
        case "Semana":
          $periodo_sumar_app = "week";
          $dato_sumar=1;
          break;
          case "Quincenal":
            $periodo_sumar_app = "day";
            $dato_sumar=14;
          break;
        case "Mes":
          $periodo_sumar_app = "month";
          $dato_sumar=1;
          break;
        default:
          $periodo_sumar_app = "day";
          $dato_sumar=1;
          endswitch;


          $numero_repeticion = $dbo->getFields( "Disponibilidad", "NumeroRepeticion", "IDDisponibilidad = '" . $IDDisponibilidad . "'" );
          // Este sirve para establecer el limite deacuerdo al admin en el parametro limite de repeticion
          //$fechaFin = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;

          //Toma la fecha final de lo que seleccione el usuario en el app
          if ( !empty( $RepetirFechaFinal ) ):
            //$fechaFin = strtotime( $RepetirFechaFinal );
            $fechaFin = strtotime( '+1 day', strtotime( $RepetirFechaFinal ) );
          else :
            $fechaFin = strtotime( $Fecha );
          endif;
          else :
            $numero_repeticion = 1;
            $fechaFin = strtotime( $Fecha );
            $periodo_sumar = "day";
            $periodo_sumar_app =  "day";
            $dato_sumar=1;
          endif;


          $fechaInicio = strtotime( $Fecha );
          //$fechaFin=strtotime($fecha_fin_reserva );
          //echo date("Y-m-d",$fechaFin);
          //exit;


          if ( !empty( $IDBeneficiario ) && !empty( $TipoBeneficiario ) ):
            if ( $TipoBeneficiario == "Invitado" )
              $IDInvitadoBeneficiario = $IDBeneficiario;
            elseif ( $TipoBeneficiario == "Socio" )
              $IDSocioBeneficiario = $IDBeneficiario;
          endif;



          for ( $contador_fecha = $fechaInicio; $contador_fecha <= $fechaFin; $contador_fecha += 86400 ):


            $flag_reserva_cancha_clase = 0;
          // verifico que todavia este disponible la reserva

          if ( !empty( $Tee ) ):
            $condicion_tee = " and Tee = '" . $Tee . "'";
          endif;

          // Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia
          $cupo_total = "S"; // ya no hay cupos
          $cupos_disponibilidad = $dbo->getFields( "Disponibilidad", "Cupos", "IDDisponibilidad = '" . $id_disponibilidad . "'" );
          $datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral[ "IDDisponibilidad" ] . "' ", "array" );
          if ( ( int )$cupos_disponibilidad > 1 ):
            //Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
            $cupos_reservados = self::valida_cupos_disponibles( $IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial );
          //Valido si todavia existe cupo en esta hora
          if ( $cupos_reservados <= $datos_disponibilidad[ "Cupos" ] ):
            $cupo_total = "N"; // aun hay cupos disponibles
          endif;
          $numero_inscripcion = rand( 0, 999999 );
          else :
            $numero_inscripcion = 0;
          endif;




          $id_reserva_disponible = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee " );
          if ( empty( $id_reserva_disponible ) || $cupo_total == "N" ):
            $datos_invitado = json_decode( $Invitados, true );

          //Verifico que el socio no este como invitado en el mismo servicio en otra hora
          if ( $Fecha == date( "Y-m-d" ) ):
            $hora_actual_sistema_valida = date( "H:i:s" );
          else :
            $hora_actual_sistema_valida = "01:00:00";
          endif;

          $sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '" . $IDSocio . "') and RG.IDClub = '" . $IDClub . "' and RG.Fecha = '" . $Fecha . "' and RG.Hora >='" . $hora_actual_sistema_valida . "' and RG.IDServicio = '" . $IDServicio . "' ORDER BY IDReservaGeneralInvitado Desc ";
          $qry_socio_grupo = $dbo->query( $sql_socio_grupo );
          if ( $dbo->rows( $qry_socio_grupo ) > 0 ):
            $nombre_socio_invitado = utf8_encode($dbo->getFields( "Socio", "Nombre", "IDSocio = '" . $IDSocio . "'" ) . " " . $dbo->getFields( "Socio", "Apellido", "IDSocio = '" . $IDSocio . "'" ));
          $respuesta[ "message" ] = $nombre_socio_invitado . ", ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          exit;
          endif;


          // Si es golf verifico que los invitado no este en mas de un grupo el mismo dia
          if ( count( $datos_invitado ) > 0 ):
            foreach ( $datos_invitado as $detalle_datos ):
              $IDSocioInvitado = $detalle_datos[ "IDSocio" ];
          $NombreSocioInvitado = $detalle_datos[ "Nombre" ];
          if ( !empty( $IDSocioInvitado ) ):
            $respuesta_valida_invitado = SIMWebService::verificar_socio_grupo( $IDClub, $IDSocioInvitado, $Fecha, $IDServicio );
          if ( $respuesta_valida_invitado == 1 ):
            $nombre_socio_invitado = $dbo->getFields( "Socio", "Nombre", "IDSocio = '" . $IDSocioInvitado . "'" ) . " " . $dbo->getFields( "Socio", "Apellido", "IDSocio = '" . $IDSocioInvitado . "'" );
          $respuesta[ "message" ] = "El invitado: " . utf8_encode( $nombre_socio_invitado ) . ", solo puede estar en un grupo por dia";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          exit;
          endif;
          endif;
          endforeach;
          endif;


          // Si el servicio es una clase y necesita reservar cancha
          $id_servicio_cancha = $dbo->getFields( "ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
          if ( $id_servicio_cancha > 0 ):
            // Consulto el servicio del club asociado a este servicio maestro
            $IDServicioCanchaClub = $dbo->getFields( "Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $IDClub . "'" );
          // Valido si existe una cancha disponible en el horario de la clase
          $IDElemento_cancha = SIMWebService::buscar_elemento_disponible( $IDClub, $IDServicioCanchaClub, $Fecha, $Hora, $IDElemento, $IDTipoReserva );
          if ( empty( $IDElemento_cancha ) ):
            $respuesta[ "message" ] = "Lo sentimos no hay una cancha disponible para tomar la clase en este horario ";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          else :
            $flag_reserva_cancha_clase = 1;
          endif;
          endif;



          /*
          //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
          //Si el servicio maestro tiene definido permitir turnos seguidos cuando los invitados sean mas de X personas
          $numero_para_reservar_turnos = $dbo->getFields( "ServicioMaestro" , "InvitadoTurnos" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
          if( ( (int)$numero_para_reservar_turnos>0 ) && count($datos_invitado)>=$numero_para_reservar_turnos):
          //if($id_servicio_maestro==14): //Tennis
            $cantidad_turnos = 1; // Para validar los siguientes X turnos esten disponibles
            $array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_disponible($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );
            if(count($array_hora_siguiente_turno_diponible)!=$cantidad_turnos):
              $respuesta["message"] = "Se necesitan ".$cantidad_turnos." turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otro opcion.";
              $respuesta["success"] = false;
              $respuesta["response"] = NULL;
              return $respuesta;
            else:
              $flag_separa_siguiente_turno=1;
            endif;
          endif;
          */


          //Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
          if ( !empty( $IDTipoReserva ) ):
            $datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
          $cantidad_turnos = $datos_tipo_reserva[ "NumeroTurnos" ];
          $cantidad_minima_participantes = $datos_tipo_reserva[ "MinimoParticipantes" ];

          //Consulto cuantos turnos automaticos se deben separar , pj en salones despues de una reserva se toma un turno mas para que se pueda realizar el aseo
          $TurnoMantenimiento = ( int ) $datos_servicio["TurnoMantenimiento"];
          $cantidad_turnos += $TurnoMantenimiento;

          if ( ( count( $datos_invitado ) + $cantidad_auxiliar ) >= ( int )$cantidad_minima_participantes ):
            // valido que no vengas mas de los participantes que es necesario
            if ( ( count( $datos_invitado ) + $cantidad_auxiliar ) >= ( int )$cantidad_minima_participantes ):
              if ( ( ( int )$cantidad_turnos > 1 ) ):
                //$cantidad_turnos-=1; // Quito uno para que no cuente la reserva primera

                // Separo las reservas
          $array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_sin_reserva( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );
          if ( ( count( $array_hora_siguiente_turno_diponible ) != ( int )( $cantidad_turnos - 1 ) || !is_array( $array_hora_siguiente_turno_diponible )) && ( int )$cupos_disponibilidad <= 1):
            $respuesta[ "message" ] = "Se necesitan " . $cantidad_turnos . ". turnos mas seguidos y el siguiente turno no esta disponible. Por favor seleccione otra opcion! " .$cupos_disponibilidad;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          else :
            $flag_separa_siguiente_turno = 1;
          endif;
          endif;
          else :
            $respuesta[ "message" ] = "Lo sentimos, el maximo numero de invitados debe ser de " . $cantidad_minima_participantes;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          else :
            $respuesta[ "message" ] = "Lo sentimos, el minimo numero de invitados debe ser de: " . $cantidad_minima_participantes;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          endif;


          //Si turnos es mayor a 1 verifico que los siguientes turnos esten disponibles y los reservo
          if ( ( int )$NumeroTurnos > 1 ):

            if ( $id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30 ): //Golf
              $array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, $Tee, "Reserva","" );
            else :
              $array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos );
          endif;

          //Cuando es por grupos busco aleatoriamente de los invitados los socios que quedaran como cabeza de grupo
          $array_cabeza_grupo = SIMWebService::busca_cabeza_grupo( $Invitados, $NumeroTurnos, $IDSocio );

          if ( count( $array_disponible ) != $NumeroTurnos ):
            $respuesta[ "message" ] = "Se necesitan: " . $NumeroTurnos . " turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otra opcion.";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          else :
            $contador_turno = 0;
          // separo los siguientes turnos disponibles menos el primero que se realiza en el siguiente proceso
          foreach ( $array_disponible as $key_hora => $dato_hora ):
            if ( $contador_turno > 0 ):
              $socios_cabeza = 0;
          $contador_socio_cabeza_real = 0;
          $IDSocioCabeza = $array_cabeza_grupo[ ( $contador_turno - 1 ) ];
          if ( empty( $IDSocioCabeza ) )
            $IDSocioCabeza = $IDSocio;

          // Registro los socios cabeza como ingresados para que no queden como invitados
          foreach ( $array_cabeza_grupo as $id_socio_cabeza => $datos_socio_cabeza ):
            $array_invitado_agregado[] = $datos_socio_cabeza;
          if ( $IDSocio <> $datos_socio_cabeza ):
            $contador_socio_cabeza_real++;
          endif;
          endforeach;


          $sql_inserta_reserva_turno = $dbo->query( "INSERT Into ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Tee, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,UsuarioTrCr, FechaTrCr)
                                                    Values ('" . $IDClub . "','" . $IDSocioCabeza . "', '" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "',
                                                      '" . $dato_hora . "','" . $Tee . "', '" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','".$Altitud."','".$Longitud."','".$NombreSocioReserva."','".$AccionSocioReserva."','".$NombreBenefReserva."','".$AccionBenefReserva."','WebService Automatica',NOW())" );

          if ( !$sql_inserta_reserva_turno ):
            $respuesta[ "message" ] = "No se pudo realizar la reserva, intente de nuevo (m1)";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          $id_reserva_general_turno = $dbo->lastID();


          //Inserto los invitados
          $datos_invitado_turno = json_decode( $Invitados, true );
          //Reparto los jugadores por turnos
          $total_invitados_turno = count( $datos_invitado_turno );

          if ( $contador_socio_cabeza_real >= 1 )
            $socios_cabeza = $contador_socio_cabeza_real + 1;
          else
            $socios_cabeza = 0;

          $invitados_por_turno = ( ( int )( ( $total_invitados_turno + 1 ) - $socios_cabeza ) / $NumeroTurnos );



          $contador_invitado_agregado = 1;
          if ( count( $datos_invitado_turno ) > 0 ):
            foreach ( $datos_invitado_turno as $detalle_datos_turno ):
              $IDSocioInvitadoTurno = $detalle_datos_turno[ "IDSocio" ];
          $NombreSocioInvitadoTurno = $detalle_datos_turno[ "Nombre" ];
          // Guardo los invitados socios o externos
          $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
          if ( !in_array( $datos_invitado_actual, $array_invitado_agregado ) ):
            if ( $contador_invitado_agregado <= ( int )$invitados_por_turno ):
              $sql_inserta_invitado_turno = $dbo->query( "Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
                                                            Values ('" . $id_reserva_general_turno . "','" . $IDSocioInvitadoTurno . "', '" . $NombreSocioInvitadoTurno . "')" );
          $array_invitado_agregado[] = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
          //Envio push al invitado para notificarle si es un invitado socio
          if ( !empty( $IDSocioInvitadoTurno ) ) {
            SIMUtil::push_socio_invitado( $IDClub, $id_reserva_general_turno, $IDSocioInvitadoTurno );
          }
          endif;
          else :
            $contador_invitado_agregado = 0;
          endif;
          $contador_invitado_agregado++;
          endforeach;
          endif;
          // Borro la reserva separada
          $sql_inserta_reserva = $dbo->query( "Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $dato_hora . "' and IDEstadoReserva  = 3" );
          endif;


          $contador_turno++;
          endforeach;
          //$respuesta["message"] = "Guardado";
          //$respuesta["success"] = true;
          //$respuesta["response"] = $id_reserva_general;
          //return $respuesta;
          endif;
          endif;






          // Si se va a reservas mas turnos seguidos y la validacion fue exitosa borro las separacion hechas
          if ( $flag_separa_siguiente_turno == 1 && count( $array_hora_siguiente_turno_diponible ) > 0 ):
            foreach ( $array_hora_siguiente_turno_diponible as $Hora_siguiente ):
              // Borro la reserva separada
              $sql_inserta_reserva = $dbo->query( "Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and IDEstadoReserva  = 3" );
          // Borro la reserva separada automaticas
          $sql_inserta_reserva_aut = $dbo->query( "Delete From ReservaGeneralAutomatica Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and IDEstadoReserva  = 3" );

          endforeach;
          endif;


          //Vuelvo a validar que nadie haya tomado la reserva nuevamente por los casos de milisegundos, para esto creo un espera de x segundos
          $suma_rand = rand( 0, 1 );
          $rand_seg = rand( 1, 1 ) + $suma_rand;
          sleep( $rand_seg );

          $id_reserva_disponible2 = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' $condicion_tee " );
          if ( !empty( $id_reserva_disponible2 ) && ( ( int )$cupos_disponibilidad <= 1 || empty( $cupos_disponibilidad ) ) ):
            $respuesta[ "message" ] = "Lo sentimos, la reserva ya fue tomada";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          if ( !empty( $IDAuxiliar ) ):
            $suma_rand = rand( 0, 2 );
          $rand_seg = rand( 1, 1 ) + $suma_rand;
          sleep( $rand_seg );
          //Verifico de nuevo que el auxiliar boleador no esté reservado

          $id_reserva_aux = "";
          $sql_aux = "Select *
                            From ReservaGeneral
                            Where IDAuxiliar = '" . $IDAuxiliar . "'
                              and Fecha = '" . $Fecha . "'
                              and Hora = '" . $Hora . "'
                              and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)";
          $r_aux = $dbo->query( $sql_aux );
          while ( $row_aux = $dbo->fetchArray( $r_aux ) ):
            if ( $row_aux[ "IDSocio" ] == $IDSocio && $row_aux[ "IDSocioBeneficiario" ] == ( int )$IDBeneficiario ):
              $id_reserva_aux = "";
            else :
              $id_reserva_aux = "1";
          endif;
          endwhile;


          /*$id_reserva_aux = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and (IDSocio <> '".$IDSocio."' and IDSocioBeneficiario <> '".$IDSocioBeneficiario."') and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );
           */
          /*
          $respuesta["message"] = $sql_aux;
          $respuesta["success"] = false;
          $respuesta["response"] = NULL;
          return $respuesta;
          */



          $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora";
          if ( !empty( $id_reserva_aux ) ):
            $respuesta[ "message" ] = $mensaje_auxiliar_no_dispo;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          //Cuando viene con array
          foreach ( $array_id_auxiliar as $id_auxiliar_valida ):
            if ( ( int )$id_auxiliar_valida >= 0 ):
              $id_auxiliar_valida = $id_auxiliar_valida . ",";
          $id_reserva_aux = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDAuxiliar like '" . $id_auxiliar_valida . "%' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDSocio <> '" . $IDSocio . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );
          $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora.";
          if ( !empty( $id_reserva_aux ) ):
            $respuesta[ "message" ] = $mensaje_auxiliar_no_dispo;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          endif;
          endforeach;

          endif;


          //Verifico que el elemnto no hay sido reservado a esta misma hora en otro servicio
          $condicion_multiple_elemento = SIMWebService::verifica_elemento_otro_servicio( $IDElemento,"" );
          //Si el elemento ya tiene otra reserva en otro servicio marco esta como ya revarda asi tenga cupos disponibles
          $array_otro_elemento = explode( ",", $condicion_multiple_elemento );
          //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
          $suma_rand = rand( 0, 1 );
          $rand_seg = rand( 1, 1 ) + $suma_rand;
          sleep( $rand_seg );
          if ( count( $array_otro_elemento ) > 1 ): //Si es mas de 1 quiere decir que el elemento esta en mas de un servicio y hago la verificacion
            foreach ( $array_otro_elemento as $id_elemento_multiple ):
              if ( $id_elemento_multiple != $IDElemento && !empty( $id_elemento_multiple ) ):
                $sql_reserva_elemento_multp = "SELECT * FROM ReservaGeneral WHERE IDServicioElemento in (" . $id_elemento_multiple . ") and Fecha = '" . $Fecha . "' and (IDEstadoReserva = 1 ) and Hora = '" . $Hora . "' ";
          $qry_reserva_elemento_mult = $dbo->query( $sql_reserva_elemento_multp );
          if ( $dbo->rows( $qry_reserva_elemento_mult ) > 0 && $cupos_disponibilidad <= 1):
            $respuesta[ "message" ] = "La persona o elemento seleccionado ya fue reservado en otro servicio en esta misma fecha/hora";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;
          endif;
          endforeach;
          endif;


          //Guardo la reserva maestra
          $sql_inserta_reserva = $dbo->query( "INSERT INTO ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
                                                Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, UsuarioTrCr, FechaTrCr)
                                          Values ('" . $IDClub . "','" . $IDSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "',
                                          '" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','".$Altitud."' , '".$Longitud."','".$NombreSocioReserva."',
                                          '".$AccionSocioReserva."','".$NombreBenefReserva."','".$AccionBenefReserva."','" . $UsuarioCreacion . "',NOW())" );

          if ( !$sql_inserta_reserva ):
            $respuesta[ "message" ] = "No se pudo realizar la reserva, intente de nuevo (m2)";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;


          $id_reserva_general = $dbo->lastID();




          $datos_invitado = json_decode( $Invitados, true );
          if ( count( $datos_invitado ) > 0 ):
            foreach ( $datos_invitado as $detalle_datos ):
              $IDSocioInvitado = $detalle_datos[ "IDSocio" ];
          $NombreSocioInvitado = $detalle_datos[ "Nombre" ];
          $datos_invitado_actual = $IDSocioInvitado . "-" . $NombreSocioInvitado;
          if ( !in_array( $datos_invitado_actual, $array_invitado_agregado ) ):
              // Guardo los invitados socios o externos
              $sql_inserta_invitado = $dbo->query( "INSERT Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
                                                  Values ('" . $id_reserva_general . "','" . $IDSocioInvitado . "', '" . $NombreSocioInvitado . "')" );


              //// Para las reservas del polo de practicas los invitado se crean como socio invitado y se les crea la reserva
              if($IDClub==37 && $IDServicio == 3575){
                $sql_reserva_invitado="INSERT INTO ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, UsuarioTrCr, FechaTrCr)
                                                Values ('" . $IDClub . "','" . $IDNuevoSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "',
                                                '" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','".$NombreSocioReserva."','".$AccionSocioReserva."','".$NombreBenefReserva."','".$AccionBenefReserva."','InvitadoPolo',NOW())";

                if(empty( $IDSocioInvitado )){
                  $sql_inserta_invitado_socio = $dbo->query( "Insert Into Socio (IDClub, IDCategoria, IDEstadoSocio, Accion, Nombre, NumeroDocumento, Email, Clave, TipoSocio, FechaInicioInvitado, FechaFinInvitado, ObservacionEspecial)
                                                    Values ('" . $IDClub . "','43', '1','999999991','".$NombreSocioInvitado."','','invitadoreserva','','Invitado','".$Fecha."','".$Fecha."','Creado Automaticamente por practica de polo')" );
                  $IDNuevoSocio=$dbo->lastID();

                }else{
                  $IDNuevoSocio=$IDSocioInvitado;
                }
                $numero_inscripcion_inv = rand( 0, 999999 );
                //Crear la reserva para el invitado
                $sql_reserva_invitado="INSERT INTO ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, UsuarioTrCr, FechaTrCr)
                                        Values ('" . $IDClub . "','" . $IDNuevoSocio . "','" . $IDNuevoSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "',
                                        '" . $CantidadInvitadoSalon . "','" . $numero_inscripcion_inv . "','".$NombreSocioInvitado."','','".$NombreBenefReserva."','".$AccionBenefReserva."','InvitadoPolo',NOW())";
                $dbo->query($sql_reserva_invitado);

              }


              //Envio push al invitado para notificarle si es un invitado socio
              if ( !empty( $IDSocioInvitado ) ) {
                SIMUtil::push_socio_invitado( $IDClub, $id_reserva_general, $IDSocioInvitado );
              }

          endif;
          endforeach;
          endif;


          $array_Campos = $Campos;
          $array_Campos = json_decode( $Campos, true );

          if ( count( $array_Campos ) > 0 ):
            foreach ( $array_Campos as $id_valor_campo => $valor_campo ):
              // Guardo los campos personalizados
              $sql_inserta_campo = $dbo->query( "INSERT Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor)
                                                Values ('" . $id_reserva_general . "','" . $valor_campo[ "IDCampo" ] . "', '" . $valor_campo[ "Valor" ] . "')" );
          endforeach;
          endif;





          // Si se va a reservas mas turnos seguidos y la validacion fue exitosa ingreso las demas reservas
          if ( $flag_separa_siguiente_turno == 1 && count( $array_hora_siguiente_turno_diponible ) > 0 ):
            foreach ( $array_hora_siguiente_turno_diponible as $Hora_siguiente ):

              $validacion_auxiliar = 0;
          if ( !empty( $IDAuxiliar ) ):

            if ( !empty( $IDAuxiliar ) && count( $array_id_auxiliar ) <= 0 ):
              $array_id_auxiliar = explode( ",", $IDAuxiliar );
          endif;

          //Cuando viene con array
          foreach ( $array_id_auxiliar as $id_auxiliar_valida ):
            if ( ( int )$id_auxiliar_valida >= 0 ):
              $id_auxiliar_valida = $id_auxiliar_valida . ",";
          $id_reserva_aux = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDAuxiliar like '" . $id_auxiliar_valida . "%' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );
          $mensaje_auxiliar_no_dispo = "Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora: " . $Fecha . "/" . $Hora_siguiente;
          if ( !empty( $id_reserva_aux ) ):
            $validacion_auxiliar = 1;
          $borra_reserva_primera = $dbo->query( "Delete From ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "'" );
          $respuesta[ "message" ] = $mensaje_auxiliar_no_dispo;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          endif;
          endforeach;
          endif;





          //Vuelvo a validar que nadie haya tomado la reserva nuevamente por los casos de milisegundos
          $id_reserva_disponible3 = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora_siguiente . "'" );
          if ( empty( $id_reserva_disponible3 ) ):
            //Guardo la reserva si nadie la ha tomado
            $sql_inserta_reserva_duplicar = $dbo->query( "INSERT INTO ReservaGeneral (IDClub, IDSocio,IDUsuarioReserva,  IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, NumeroInscripcion,Tipo, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,UsuarioTrCr, FechaTrCr)
                                                      Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora_siguiente . "','" . $Observaciones . "'
                                                        ,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion . "','Automatica','".$NombreSocioReserva."','".$AccionSocioReserva."','".$NombreBenefReserva."','".$AccionBenefReserva."','" . $UsuarioCreacion . "',NOW())" );

          if ( !$sql_inserta_reserva_duplicar ):
            $respuesta[ "message" ] = "No se pudo realizar la reserva, intente de nuevo (m3)";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          $id_reserva_general_duplicar = $dbo->lastID();
          // Duplico los invitados de la reserva padre
          $sql_invitado_duplicar = $dbo->query( "Insert into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) Select '" . $id_reserva_general_duplicar . "', IDSocio, Nombre From ReservaGeneralInvitado Where IDReservaGeneral = '" . $id_reserva_general . "'" );
          // Guardar relacion de reservas automaticas
          $sql_reserva_automatica = $dbo->query( "Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
                                            Values ('" . $id_reserva_general . "','" . $id_reserva_general_duplicar . "','" . $IDClub . "','" . $IDSocio . "','" . $IDElemento . "','" . $Fecha . "','" . $Hora_siguiente . "','1')" );
          endif;

          endforeach;
          endif;


          // SI el servicio es una clase y se solicta reservar una cancha realizo la reserva
          if ( $flag_reserva_cancha_clase == 1 ):
            //Verifico de nuevo que la cancha este disponible por que por milesimas de seg puede queadr 2 al tiempo

            //duermo la ejecucion por lo meno x seg, esto para evitar reservas multiples por causa de milisegundos
            $suma_rand = rand( 0, 1 );
          $rand_seg = rand( 1, 1 ) + $suma_rand;
          sleep( $rand_seg );

          $id_reserva_disponible = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicioCanchaClub . "' and IDServicioElemento = '" . $IDElemento_cancha . "' and IDEstadoReserva in (1) and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "'" );

          if ( empty( $id_reserva_disponible ) ):
            // Obtener la disponibilidad utilizada al consultar la reserva
            $id_disponibilidad_cancha = SIMWebService::obtener_disponibilidad_utilizada( $IDServicioCanchaClub, $Fecha, $IDElemento_cancha );
          $sql_inserta_reserva_cancha = $dbo->query( "INSERT INTO ReservaGeneral (IDClub, IDSocio, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, NumeroInscripcion, Tipo, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario,UsuarioTrCr, FechaTrCr)
                                Values ('" . $IDClub . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicioCanchaClub . "','" . $IDElemento_cancha . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "','" . $IDTipoReserva . "','" . $Fecha . "', '" . $Hora . "','" . $Observaciones . "'
                                  ,'" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $numero_inscripcion . "','Automatica','".$NombreSocioReserva."','".$AccionSocioReserva."','".$NombreBenefReserva."','".$AccionBenefReserva."','" . $UsuarioCreacion . "',NOW())" );


          if ( !$sql_inserta_reserva_cancha ):
            $respuesta[ "message" ] = "La reserva solicitada ya fue o esta siendo tomada (m5)";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;




          $id_reserva_cancha = $dbo->lastID();

          // Guardar relacion de reservas automaticas
          $sql_reserva_automatica = $dbo->query( "Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva)
                                      Values ('" . $id_reserva_general . "','" . $id_reserva_cancha . "','" . $IDClub . "','" . $IDSocio . "','" . $IDElemento_cancha . "','" . $Fecha . "','" . $Hora . "','1')" );
          else :
            //No se pudo tomar por que la cancha ya fue reservada por alguien mas en el mismo segundo
            //Borro la reserva asociadas
            $borra_reserva_primera = $dbo->query( "Delete From ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "'" );
          $respuesta[ "message" ] = "Lo sentimos, no hay una cancha disponible para tomar la clase en este horario.";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          return $respuesta;
          endif;

          endif;


          //Especial Country Sala belleza Keratina y antifrizz se reserva turno a otras personas
          if ( ($IDClub == 44 ) && $IDServicio ==11736 && ($IDTipoReserva=="1482" || $IDTipoReserva=="1483" || $IDTipoReserva=="1484") ){
            $mensaje_otra_persona="Se necesita otra profesional para completar la reserva pero no esta disponible, intente en otro horario";
            $reserva_aut_otra=self::reserva_otra_elemento($IDClub,$IDSocio,$IDServicio,$IDTipoReserva,$Fecha,$Hora,$id_reserva_general);
            if(!$reserva_aut_otra){
              //Borro reserva maestra
              $borra_reserva_primera = $dbo->query( "DELETE FROM ReservaGeneral Where IDReservaGeneral = '" . $id_reserva_general . "' Limit 1" );
              $respuesta[ "message" ] = $reserva_aut_otra;
              $respuesta[ "success" ] = false;
              $respuesta[ "response" ] = NULL;
              return $respuesta;

            }
          }
          //FIN ESPECIAL country


          // Borro la reserva separada Padre
          //$sql_inserta_reserva = $dbo->query( "Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3" );
            $sql_inserta_reserva = $dbo->query( "Delete From ReservaGeneral Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3" );
          // Borro la reserva separada automaticas
          $sql_inserta_reserva_aut = $dbo->query( "Delete From ReservaGeneralAutomatica Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDServicioElemento	 = '" . $IDElemento . "' and Fecha = '" . $Fecha . "' and Hora = '" . $Hora . "' and IDEstadoReserva  = 3" );




          SIMUtil::notificar_nueva_reserva( $id_reserva_general, $IDTipoReserva );

          //Si el elemento reservado es una persona (profesor, peluquero, masajista, etc) y esta creado como empleado en app empleados envio notificacion push
          SIMUtil::push_notifica_reserva( $IDClub, $id_reserva_general, "Empleado" );


          //Datos reserva
          $datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $IDClub . "' ", "array" );
          $response_reserva = array();
          $datos_reserva[ "IDReserva" ] = ( int )$id_reserva_general;
          //Calculo el valor de la reserva
          $valor_inicial_reserva = ( int ) $datos_servicio["ValorReserva"];
          $TurnosSeparar = ( int )$dbo->getFields( "ServicioTipoReserva", "NumeroTurnos", "IDServicioTipoReserva = '" . $IDTipoReserva . "'" );
          $consul = "ServicioTipoReserva " . " NumeroTurnos " . "IDServicioTipoReserva = '" . $IDTipoReserva . "'";
          if ( $TurnosSeparar > 1 ):
            $ValorReserva = ( int )$valor_inicial_reserva * $TurnosSeparar;
          else :
            $ValorReserva = ( int )$valor_inicial_reserva;
          endif;

          if ( $IDClub == 28 ): // Valor Especial Liga de tenis
            $ValorReserva = SIMUtil::calcular_tarifa( $IDClub, $IDSocio, $IDServicio, $Fecha, $Hora, $IDElemento, $id_reserva_general, $IDTipoReserva );
          endif;



          $datos_reserva[ "ValorReserva" ] = $ValorReserva;
          //array_push($response_reserva, $datos_reserva);
          //Produccion
          $llave_encripcion = $datos_club[ "ApiKey" ]; //llave de encripciÛn que se usa para generar la fima
          //Produccion
          $usuarioId = $datos_club[ "MerchantId" ]; //c0digo inicio del cliente

          $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
          $iva = 0; //impuestos calculados de la transacciÛn
          $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
          $valor = $datos_reserva[ "ValorReserva" ] + ( ( $datos_reserva[ "ValorReserva" ] * $ArrayParametro[ "Iva" ] ) / 100 ); //el valor ; //el valor total
          $moneda = "COP"; //la moneda con la que se realiza la compra
          $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
          $descripcion = "Pago Reserva Mi Club"; //descripciÛn de la transacciÛn
          $url_respuesta = URLROOT . "respuesta_transaccion.php"; //Esta es la p·gina a la que se direccionar· al final del pago
          $url_confirmacion = URLROOT . "confirmacion_pagos.php";
          $emailSocio = $datos_socio["CorreoElectronico"];
          if ( filter_var( trim( $emailSocio ), FILTER_VALIDATE_EMAIL ) )
            $emailComprador = $emailSocio;
          else
            $emailComprador = "";

          $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
          $firma = md5( $firma_cadena ); //creaciÛn de la firma con la cadena previamente hecha
          $extra1 = $id_reserva_general;



          $datos_reserva[ "Action" ] = $datos_club[ "URL_PAYU" ];

          $response_parametros = array();
          $datos_post[ "llave" ] = "moneda";
          $datos_post[ "valor" ] = ( string )$moneda;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "ref";
          $datos_post[ "valor" ] = $refVenta;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "llave";
          $datos_post[ "valor" ] = $llave_encripcion;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "userid";
          $datos_post[ "valor" ] = $usuarioId;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "usuarioId";
          $datos_post[ "valor" ] = $usuarioId;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "accountId";
          $datos_post[ "valor" ] = ( string )$datos_club[ "AccountId" ];
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "descripcion";
          $datos_post[ "valor" ] = $descripcion;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "extra1";
          $datos_post[ "valor" ] = ( string )$extra1;
          array_push( $response_parametros, $datos_post );


          $datos_post[ "llave" ] = "extra2";
          $datos_post[ "valor" ] = $IDClub;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "refVenta";
          $datos_post[ "valor" ] = $refVenta;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "valor";
          $datos_post[ "valor" ] = $ValorReserva;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "iva";
          $datos_post[ "valor" ] = "0";
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "baseDevolucionIva";
          $datos_post[ "valor" ] = "0";
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "firma";
          $datos_post[ "valor" ] = $firma;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "emailComprador";
          $datos_post[ "valor" ] = $emailComprador;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "prueba";
          $datos_post[ "valor" ] = ( string )$datos_club[ "IsTest" ];
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "url_respuesta";
          $datos_post[ "valor" ] = ( string )$url_respuesta;
          array_push( $response_parametros, $datos_post );

          $datos_post[ "llave" ] = "url_confirmacion";
          $datos_post[ "valor" ] = ( string )$url_confirmacion;
          array_push( $response_parametros, $datos_post );

          $datos_reserva[ "ParametrosPost" ] = $response_parametros;

          /*
          $datos_reserva["Action"] = $datos_club["URL_PAYU"];
          $datos_reserva["moneda"] = (string)$moneda;
          $datos_reserva["ref"] = $refVenta;
          $datos_reserva["llave"] = $llave_encripcion;
          $datos_reserva["userid"] = $usuarioId;
          $datos_reserva["usuarioId"] = $usuarioId;
          $datos_reserva["accountId"] = (string)$datos_club["AccountId"];
          $datos_reserva["descripcion"] = $descripcion;
          $datos_reserva["extra1"] = (string)$extra1;
          $datos_reserva["extra2"] = $IDClub;
          $datos_reserva["refVenta"] = $refVenta;
          $datos_reserva["valor"] =  $datos_reserva["ValorReserva"];
          $datos_reserva["iva"] = "0";
          $datos_reserva["baseDevolucionIva"] = "0";
          $datos_reserva["firma"] = $firma;
          $datos_reserva["emailComprador"] = $emailComprador;
          $datos_reserva["prueba"] = (string)$datos_club["IsTest"];
          $datos_reserva["url_respuesta"] = (string)$url_respuesta;
          $datos_reserva["url_confirmacion"] = (string)$url_confirmacion;
          */

          $mensaje_guardar = $datos_servicio["MensajeReservaGuardada"];
          if(!empty($mensaje_guardar))
            $mensaje_guardado=$mensaje_guardar;
          else
            $mensaje_guardado="Guardado";

          //Para aeroclub avion si es 8 dias antes no esta sujeta a verificación y es menos días si aparece mensaje
          if($IDClub==36):
              $mensaje_guardado="Reservado con exito";
          endif;
          if ( $IDServicio == "3608" || $IDServicio == "4371" || $IDServicio == "3608" || $IDServicio == "3609"  ):
            $fecha_reser = $Fecha;
            $nuevafecha = strtotime( '-8 day', strtotime( $fecha_reser ) );
            $nuevafecha = date( 'Y-m-j', $nuevafecha );
            $fecha_hoy_reser = date( "Y-m-d" );
            if ( strtotime( $nuevafecha ) <= strtotime( $fecha_hoy_reser ) ):
              $mensaje_especial_repetir = "RESERVA EN PROCESO Como su solicitud esta siendo procesada con menos de 8 días de antelación, queda sujeta a disponibilidad. Pronto le confirmaremos";
              $mensaje_guardado="";
            else:
              $mensaje_guardado="Reservado con exito";
            endif;
          endif;





          $respuesta[ "message" ] = $mensaje_guardado . $mensaje_especial_repetir;
          $respuesta[ "success" ] = true;
          $respuesta[ "response" ] = $datos_reserva;
          else :
            $respuesta[ "message" ] = "Lo sentimos la reserva ya fue tomada.";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;

          endif;



          //$contador_fecha = strtotime ( '+1 '.$periodo_sumar ,  $contador_fecha  ) ;
          //$contador_fecha = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;
          $contador_fecha = strtotime( '+'.$dato_sumar.' ' . $periodo_sumar_app, strtotime( $Fecha ) );
          $Fecha = date( "Y-m-d", $contador_fecha );




          endfor;
          }
          else {
            $respuesta[ "message" ] = "Lo sentimos solo se permite " . $MaximoReservaSocioServicio . " reserva por dia. " . $mensaje_opcion_reserva;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
        }


          else :
            $respuesta[ "message" ] = "Lo sentimos fecha no disponible";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          endif;
          else :
            $respuesta[ "message" ] = "Lo sentimos no se puede reservar turnos seguidos, debe haber un laspso de por lo menos 1 hora ";
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          endif;

          else :
            $respuesta[ "message" ] = "Lo sentimos, el maximo numero de invitados para poder reservar es: " . $MaximoInvitados;
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          endif;

          else :
            $respuesta[ "message" ] = "Lo sentimos el minimo de personas para reservas es de : " . ($MinimoInvitados+1);
          $respuesta[ "success" ] = false;
          $respuesta[ "response" ] = NULL;
          endif;

      } else {
      $respuesta[ "message" ] = "Error el socio no existe o no pertenece al club";
      $respuesta[ "success" ] = false;
      $respuesta[ "response" ] = NULL;
    }

  } else {
    $respuesta[ "message" ] = "21. Atencion faltan parametros";
    $respuesta[ "success" ] = false;
    $respuesta[ "response" ] = NULL;
  }

  return $respuesta;

}
?>
