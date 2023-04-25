<?php

class SIMWebServicePanico
{
  public function set_informacion_panico($IDClub, $IDSocio, $IDUsuario, $Latitud, $Longitud)
  {
    $dbo = &SIMDB::get();


    //CORREO PANICO EN LA CONFIGURACION DE CADA CLUB
    $correopanico = $dbo->getFields("ConfiguracionClub", "CorreoPanico", "IDClub = '" . $IDClub . "'");


    if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

      if (!empty($IDSocio)) :
        $datos_persona = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Identificador = $datos_persona["IDSocio"];
      else :
        $datos_persona = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
        $Identificador = $datos_persona["IDUsuario"];
      endif;

      if (!empty($Identificador)) {

        $sql_panico = "INSERT INTO LogPanico (IDClub,IDSocio, IDUsuario, Latitud, Longitud, UsuarioTrCr, FechaTrCr)
                               Values ('" . $IDClub . "','" . $IDSocio . "','" . $IDUsuario . "','" . $Latitud . "','" . $Longitud . "','APP',NOW())";
        $dbo->query($sql_panico);
        $id_panico = $dbo->lastID();

        if (!empty($correopanico)) {

          self::notificar_panico($IDClub, $id_panico, $datos_persona);
        }

        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Solictudenviada', LANG);
        $respuesta["success"] = true;
        $respuesta["response"] = null;
      } else {
        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelsocionoexisteonopertenecealclub', LANG) . $Identificador . "-" . $IDSocio;
        $respuesta["success"] = false;
        $respuesta["response"] = null;
      }
    } else {
      $respuesta["message"] = "P1." .  SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
      $respuesta["success"] = false;
      $respuesta["response"] = null;
    }

    return $respuesta;
  }

  public function notificar_panico($IDClub, $id_panico, $datos_persona)
  {

    $dbo = &SIMDB::get();

    $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
    $correopanico = $dbo->getFields("ConfiguracionClub", "CorreoPanico", "IDClub = '" . $IDClub . "'");
    $empleados = $dbo->getFields("ConfiguracionClub", "UsuarioSeleccion", "IDClub='" . $IDClub . "'");
    $r_panico = $dbo->fetchAll("LogPanico", " IDLogPanico = '" . $id_panico . "' ", "array");
    $correo = $correopanico;
    $latitud = $r_panico["Latitud"];
    $longitud = $r_panico["Longitud"];
    //correo para el socio
    if ($r_panico["IDSocio"] > 0) {
      $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r_panico["IDSocio"] . "' ", "array");


      $Mensaje = "El socio con los siguientes datos pulso el boton de panico " . "<br><br>" .
        "<b>" . "Nombre:" . "</b>" .  $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]  . "<br><br>" .
        "<b>" . "Predio:" . "</b>" . $datos_socio["Predio"] . "<br><br>" .
        "<b>" . "Accion:" . "</b>" . $datos_socio["Accion"] . "<br><br>" .
        "<b>" . "Telefono:" . "</b>" . $datos_socio["Telefono"] . "<br><br>" .
        "<b>" . "Celular:" . "</b>" . $datos_socio["Celular"] . "<br><br>" .
        "<b>" . "Latitud Y Longitud:" . "</b>" . "(" . $latitud  . $longitud . ")" . "<br><br>" .
        "<b>" .  "URL:" . "</b>" . "<a href= 'https://www.google.com.co/maps/place/" . $latitud . ","  . $longitud . "'>" . "Ubicacion en Google Maps" . "</a>" . "<br><br>";

      // se crea el mensaje para enviar push a los empleados
      $MensajePush = "El socio con los siguientes datos pulso el boton de panico Nombre:" . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Predio:" . $datos_socio["Predio"] .
        " Accion:" . $datos_socio["Accion"] . " Telefono:" . $datos_socio["Telefono"] . " Celular:" . $datos_socio["Celular"] . " Latitud:" . $latitud . " Longitud:" . $longitud;
    } else { //correo para el usuario
      $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $r_panico["IDUsuario"] . "' ", "array");
      $Mensaje = "El usuario con los siguientes datos pulso el boton de panico " . "<br><br>" .
        "<b>" . "Nombre:" . "</b>" .  $datos_usuario["Nombre"]   . "<br><br>" .
        "<b>" . "Telefono:" . "</b>" . $datos_usuario["Telefono"] . "<br><br>" .
        "<b>" . "Latitud Y Longitud:" . "</b>" . "(" . $latitud  . $longitud . ")" . "<br><br>" .
        "<b>" .  "URL:" . "</b>" . "<a href= 'https://www.google.com.co/maps/place/" . $latitud . ","  . $longitud . "'>" . "Ubicacion en Google Maps" . "</a>" . "<br><br>";

      // se crea el mensaje para enviar push a los empleados
      $MensajePush = "El usuario con los siguientes datos pulso el boton de panico Nombre:" . $datos_usuario["Nombre"]  .
        " Telefono:" . $datos_usuario["Telefono"]  . " Latitud:" . $latitud . " Longitud:" . $longitud;
    }


    //correo
    $Asunto = "Urgente Panico";
    SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);

    //echo $empleados;


    //se recorre los ids de los empleados para enviar un push a cada uno.
    $datosempleados = explode("|||", $empleados);
    for ($i = 0; $i < count($datosempleados); $i++) {

      //echo $datosempleados[$i] . "-" . $IDClub . "-" . $Mensaje . "------";
      SIMUtil::enviar_notificacion_push_general_funcionario($IDClub, $datosempleados[$i], $MensajePush);
    }
  }
}
