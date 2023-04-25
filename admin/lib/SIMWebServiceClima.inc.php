<?php
class SIMWebServiceClima
{
    public function saber_dia($nombredia)
    {
        $dias = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
        $fecha = $dias[date('N', strtotime($nombredia))];
        return $fecha;
    }
    public function traductor_estado_clima($estadoClima)
    {
        $Estados = array("clear-day" => "Soleado", "clear-night" => "Noche Despejada", "cloudy" => "Nublado", "foggy" => "Niebla", "partly-cloudy-day" => "Parcialmente Soleado", "partly-cloudy-night" => "Parcialmente Noche Nublado", "possibly-rainy-day" => "Posible Llovizna", "possibly-rainy-night" => "Posible Llovizna Noche", "possibly-sleet-day" => "Posible Agua Nieve", "possibly-sleet-night" => "Posible Agua Nieve Noche", "possibly-snow-day" => "Posible Nieve", "possibly-snow-night" => "Posible Nieve Noche", "possibly-thunderstorm-day" => "Posible Tormenta", "possibly-thunderstorm-night" => "Posible Tormenta Noche", "rainy" => "Lluvia", "sleet" => "Agua Nieve", "snow" => "Nieve", "thunderstorm" => "Tormenta", "windy" => "Vientos");
        $EstadoClima = $Estados[$estadoClima];
        return $EstadoClima;
    }

    public function get_configuracion_clima($IDClub)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $responseEstadosDelClima = array();

        $dia = date('d');
        $mesNumero = date('n');
        $FechaActual = date('Y-m-d');

        //datos del api actual del clima
        $datos_clima_dia = $dbo->fetchAll('MeteoDatosClimaDispositivo', 'IDClub ="' . $IDClub . '"', 'array');




        //header del app
        $sql = "SELECT * FROM ConfiguracionClima WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["ColorHeaderFondoClaro"] = $r["ColorHeaderFondoClaro"];
                $configuracion["ColorHeaderFondoOscuro"] = $r["ColorHeaderFondoOscuro"];
                $configuracionheader["Fecha"] = $FechaActual;

                $IconoViento = CLIMA_ROOT .  $r["IconoViento"];
                $IconoTruenos = CLIMA_ROOT .  $r["IconoTruenos"];
            }
            $configuracionheader["FechaVisual"] = "Hoy," . $dia . " de " . SIMResources::$meses[$mesNumero - 1];
            $configuracionheader["Fecha"] = $FechaActual;
            $configuracionheader["EstadoClima"] = $datos_clima_dia["Icon"]; // hay que ponerlo contra el api
            $configuracionheader["Temperatura"] = $datos_clima_dia["Air_Temperature"] . "ºC"; // hay que ponerlo contra el api

            //estados del clima
            /*     $sql_estado_clima = "SELECT Nombre,Icono FROM EstadosDelClima WHERE Publicar='S'";
            $qry_estado_clima = $dbo->query($sql_estado_clima);
            if ($dbo->rows($qry_estado_clima) > 0) {
                while ($r_estado_clima = $dbo->fetchArray($qry_estado_clima)) {
                    $estadoclima["Icono"] = CLIMA_ROOT .  $r_estado_clima["Icono"];
                    $estadoclima["Nombre"] = $r_estado_clima["Nombre"];
                    array_push($responseEstadosDelClima, $estadoclima);
                }
            } */

            $estadoclima["Icono"] = $IconoViento;
            $estadoclima["Nombre"] = "Dirección del viento " .  $datos_clima_dia["Wind_Direction"] . " cardinalidad " . $datos_clima_dia["Wind_Direction_Cardinal"];
            array_push($responseEstadosDelClima, $estadoclima);

            $estadoclima["Icono"] = $IconoTruenos;
            $estadoclima["Nombre"] = "Relámpagos ultima hora  " .  $datos_clima_dia["Lightning_Strike_Count_Last_1hr"] . " Distancia " . $datos_clima_dia["Lightning_Strike_Last_Distance_Msg"];
            array_push($responseEstadosDelClima, $estadoclima);
            $configuracionheader["EstadosClima"] = $responseEstadosDelClima;

            $configuracion["HeaderClima"] = $configuracionheader;

            //pronostico de la semana
            $responseEstadosDelClimaSemana = array();
            $sql_semana = "SELECT * FROM MeteoDatosClimaDispositivoSemana WHERE IDClub = '" . $IDClub . "' ";
            $qry_semana = $dbo->query($sql_semana);
            if ($dbo->rows($qry_semana) > 0) {

                while ($DatosSemana = $dbo->fetchArray($qry_semana)) {
                    $estadoclimasemana["TextoDia"] = substr(SIMWebServiceClima::saber_dia(date('Y') . "-" . $DatosSemana["Month_Num"] . "-" . $DatosSemana["Day_Num"]), 0, 3) . ".";
                    $estadoclimasemana["EstadoDia"] = $DatosSemana["Icon"];
                    $estadoclimasemana["TemperaturaDia"] = $DatosSemana["Air_Temp_High"] . "º";

                    array_push($responseEstadosDelClimaSemana, $estadoclimasemana);
                }
            }
            $configuracion["PronosticoSemana"] = $responseEstadosDelClimaSemana;


            //pronostico por hora
            $responseEstadosDelClimaHora = array();
            $sql_hora = "SELECT * FROM MeteoDatosClimaDispositivoHora WHERE IDClub = '" . $IDClub . "' ";
            $qry_hora = $dbo->query($sql_hora);
            if ($dbo->rows($qry_hora) > 0) {

                while ($DatosHora = $dbo->fetchArray($qry_hora)) {
                    $estadoclimahora["TextoHora"] = $DatosHora["Local_Hour"] . ":00";
                    $estadoclimahora["EstadoHora"] = $DatosHora["Icon"];
                    $estadoclimahora["TemperaturaHora"] = $DatosHora["Air_Temperature"] . "º";
                    $estadoclimahora["Porcentaje"] = "";
                    $estadoclimahora["TextoEstado"] = SIMWebServiceClima::traductor_estado_clima($DatosHora["Icon"]);
                    // $estadoclimahora["TextoEstado"] = "Hola";

                    array_push($responseEstadosDelClimaHora, $estadoclimahora);
                }
            }
            $configuracion["TiempoPorHora"] = $responseEstadosDelClimaHora;


            // array_push($response, $configuracion);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;
        } //End if
        else {
            $respuesta["message"] = "Configuracion Clima no está activo";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function


}
