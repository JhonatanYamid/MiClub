<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}
function saber_dia($nombredia)
{
    $dias = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
    $fecha = $dias[date('N', strtotime($nombredia))];
    return $fecha;
}
$IDPerfil = $_POST["IDPerfil"];
$IDUsuario = $_POST["IDUsuario"];
$Usuario = $dbo->fetchAll('Usuario', 'IDUsuario = ' . $IDUsuario, 'array');
if ($Usuario['IDPerfil'] != 1 && $Usuario['IDPerfil'] != 0 && $Usuario['IDPerfil'] != 62) {
    $condicionJefe = " AND (S.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " OR S.DocumentoJefe2 =" . $Usuario["NumeroDocumento"] .  " OR U.DocumentoJefe = " . $Usuario['NumeroDocumento'] . " OR U.DocumentoJefe2 =" . $Usuario['NumeroDocumento'] . " ) ";
}


if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFinal"])) {
    $condicion_fecha = " and CL.FechaMovimientoEntrada  >= '" . $_POST["FechaInicio"] . " 00:00:00'  and CL.FechaMovimientoEntrada <= '" . $_POST["FechaFinal"] . " 23:59:59'";
}

if (!empty($_POST["Estado"])) {
    $condicion_estado = " AND CL.Estado='" . $_POST["Estado"] . "'";
}


if (!empty($_POST["Id"])) {
    $condicion_socio_usuario = " AND (CL.IDSocio='" . $_POST["Id"] . "' OR CL.IDUsuario='" . $_POST["Id"] . "') ";
}




$sql_reporte = "Select CL.*, U.HoraInicioLaboral, U.HoraFinalLaboral,
        S.HoraInicioLaboral as HoraInicioLaboralSocio ,S.HoraFinalLaboral as HoraFinalLaboralSocio
 From CheckinLaboralBck  CL 
 LEFT JOIN Socio S ON CL.IDSocio=S.IDSocio LEFT JOIN Usuario U on CL.IDUsuario= U.IDUsuario                                                 
					Where CL.IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . $condicionJefe . $condicion_socio_usuario . $condicion_estado .
    " Order By CL.IDCheckinLaboral DESC";

    /* echo $sql_reporte;
exit */;

/* print_r($_POST);
exit; */

$result_reporte = $dbo->query($sql_reporte);

$nombre = "chekinLaboral_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";

    $html .= "<th>NOMBRE </th>";
    $html .= "<th>DOCUMENTO </th>";
    $html .= "<th>CARGO </th>";
    $html .= "<th>BPF </th>";
    $html .= "<th>BT/SV</th>";
    $html .= "<th>Empresa</th>";
    $html .= "<th>HORA ENTRADA ESTABLECIDA</th>";
    $html .= "<th>HORA SALIDA ESTABLECIDA</th>";

    $html .= "<th>MARCACION ENTRADA</th>";
    $html .= "<th>DIA ENTRADA</th>";
    $html .= "<th>FECHA ENTRADA</th>";
    $html .= "<th>HORA ENTRADA</th>";
    $html .= "<th>EXTRAS ANTES DE ENTRADA</th>";
    $html .= "<th>EXTRAS DESPUES DE SALIDA</th>";
    $html .= "<th>TOTAL EXTRAS DIURNAS</th>";
    $html .= "<th>TOTAL EXTRAS NOCTURNAS</th>";
    $html .= "<th>TOTAL EXTRAS DIURNAS SABADO</th>";
    $html .= "<th>TOTAL EXTRAS NOCTURNAS SABADO</th>";
    $html .= "<th>TOTAL EXTRAS DIURNAS DOMINGO</th>";
    $html .= "<th>TOTAL EXTRAS NOCTURNAS DOMINGO</th>";
    $html .= "<th>DIURNAS</th>";
    $html .= "<th>NOCTURNAS</th>";




    $html .= "<th>MARCACION SALIDA</th>";
    $html .= "<th>DIA SALIDA</th>";
    $html .= "<th>FECHA SALIDA</th>";
    $html .= "<th>HORA SALIDA</th>";



    $html .= "<th>ESTADO</th>";
    $html .= "<th>COMENTARIO REVISION</th>";
    $html .= "<th>COMENTARIO ENTRADA</th>";
    $html .= "<th>COMENTARIO SALIDA</th>";
    $html .= "<th>FECHA CAMBIO ESTADO</th>";
    $html .= "<th>REGISTRO</th>";
    $html .= "<th>PERSONA QUE APROBO</th>";
    $html .= "<th>PERSONA QUE MODIFICO</th>";









    $html .= "</tr>";

    while ($Datos = $dbo->fetchArray($result_reporte)) {

        //persona que aprobo
        if (!empty($Datos["PersonaQueAprobo"])) {
            $persona = explode(" ", $Datos["PersonaQueAprobo"]);
            $personaqueaprobo = $persona[1];
        } else {
            $personaqueaprobo = "";
        }


        //persona que modifico
        if (!empty($Datos["UsuarioTrEd"])) {

            $personaquemodifico = $Datos["UsuarioTrEd"];
        } else {
            $personaquemodifico = "";
        }



        /*  $arr_horas_despues_de_turno = 0;
        //saco las extras despues del turno
        if ($Datos["FechaMovimientoEntradaDespuesDelTurno"] > 0 && $Datos["FechaMovimientoSalidaDespuesDelTurno"] > 0) {
            $arr_horas_despues_de_turno = SIMUtil::tiempo_laboral_despues_de_turno($Datos["FechaMovimientoEntradaDespuesDelTurno"], $Datos["FechaMovimientoSalidaDespuesDelTurno"]);
        } */


        //tomo la fecha del movimiento donde el socio o usuario registran entrada 
        $separarFechaEntrada = (explode(" ", $Datos["FechaMovimientoEntrada"]));
        $horaEntrada = new DateTime($separarFechaEntrada[1]);

        //tomo la fecha del movimiento donde el socio o usuario registran salida
        $separarFechaSalida = (explode(" ", $Datos["FechaMovimientoSalida"]));
        $horaSalida = new DateTime($separarFechaSalida[1]);

        //usuario
        if ($Datos["IDUsuario"] > 0) {
            //saco horas extras de los usuarios-- hora de entrada
            $fechaEntradaEstablecidaUsuario = new DateTime($Datos["HoraEntradaEstablecida"]);
            $ExtraUsuarioEntrada = $horaEntrada->diff($fechaEntradaEstablecidaUsuario);
            /*  $ExtraUsuarioEntrada = $ExtraUsuarioEntrada->format('%H:%I:%S');
            $ExtraUsuarioEntrada = (date('H:i:s', strtotime($ExtraUsuarioEntrada)) > date('H:i:s', strtotime('00:15:00'))) ? $ExtraUsuarioEntrada : 0; */

            //saco horas extras de los usuarios-- hora de salida
            $fechaSalidaEstablecidaUsuario = new DateTime($Datos["HoraSalidaEstablecida"]);
            $ExtraUsuarioSalida = $horaSalida->diff($fechaSalidaEstablecidaUsuario);
            /* $ExtraUsuarioSalida = $ExtraUsuarioSalida->format('%H:%I:%S');
            $ExtraUsuarioSalida = (date('H:i:s', strtotime($ExtraUsuarioSalida)) > date('H:i:s', strtotime('00:15:00'))) ? $ExtraUsuarioSalida : 0; */
        }


        //socio
        if ($Datos["IDSocio"] > 0) {
            //saco horas extras de los socios-- hora de entrada
            $fechaEntradaEstablecidaSocio = new DateTime($Datos["HoraEntradaEstablecida"]);
            $ExtraSocioEntrada = $horaEntrada->diff($fechaEntradaEstablecidaSocio);
            /*    $ExtraSocioEntrada = $ExtraSocioEntrada->format('%H:%I:%S');
            $ExtraSocioEntrada = (date('H:i:s', strtotime($ExtraSocioEntrada)) > date('H:i:s', strtotime('00:15:00'))) ? $ExtraSocioEntrada : 0; */

            //saco horas extras de los socios-- hora de salida
            $fechaSalidaEstablecidaSocio = new DateTime($Datos["HoraSalidaEstablecida"]);
            $ExtraSocioSalida = $horaSalida->diff($fechaSalidaEstablecidaSocio);
            /*   $ExtraSocioSalida = $ExtraSocioSalida->format('%H:%I:%S');
            $ExtraSocioSalida = (date('H:i:s', strtotime($ExtraSocioSalida)) > date('H:i:s', strtotime('00:15:00'))) ? $ExtraSocioSalida : 0; */
        }
        //dia de la semana
        $Dia = saber_dia($separarFechaEntrada[0]);

        //hago la consulta para saber si el dia que marco es festivo

        $sql_dia_festivo = "SELECT Fecha FROM Festivos WHERE Fecha='" . $separarFechaEntrada[0] . "' AND IDPais='1'";
        $query = $dbo->query($sql_dia_festivo);
        $Dia_festivos = $dbo->fetchArray($query);
        $DiaFestivo = $Dia_festivos;

        unset($array_datos_seguimiento);
        $html .= "<tr>";

        // nombre,Numero documento,hora Inicio laboral, hora fin laboral socio-usario
        if ($Datos["IDSocio"] > 0) {
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Cargo", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Bpf", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Bt", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Empresa", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["HoraEntradaEstablecida"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["HoraSalidaEstablecida"]) . "</td>";
        } else if ($Datos["IDUsuario"] > 0) {
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "NumeroDocumento", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "Cargo", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "Bpf", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "Bt", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Usuario", "Empresa", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["HoraEntradaEstablecida"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["HoraSalidaEstablecida"]) . "</td>";
        }

        //DATOS ENTRADA LABORAL

        // $html .= "<td>" . remplaza_tildes($Datos["LatitudEntrada"]) . "</td>";
        //$html .= "<td>" . remplaza_tildes($Datos["LongitudEntrada"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Entrada"]) . "</td>";
        $html .= "<td>" . saber_dia($separarFechaEntrada[0]) . "</td>";
        $html .= "<td>" . remplaza_tildes(($separarFechaEntrada[0])) . "</td>";
        $html .= "<td>" . remplaza_tildes(($separarFechaEntrada[1])) . "</td>";



        //extras usuario entrada
        if ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes" && $Datos["IDUsuario"] > 0) {
            if (!empty($Datos["Entrada"]) && $Datos["IDUsuario"] > 0 && ($horaEntrada <  $fechaEntradaEstablecidaUsuario)) {
                $horaUsuarioEntrada = $ExtraUsuarioEntrada->format('%H');
                $minutosUsuarioEntrada = $ExtraUsuarioEntrada->format('%I');
                $totalHorasEntradaUsuario = ((60 * $horaUsuarioEntrada) + $minutosUsuarioEntrada) / 60;
                $totalHorasEntradaUsuario = str_replace(".", ",", $totalHorasEntradaUsuario);

                //si las extras son mayores de 15 minutos
                if ((float)$totalHorasEntradaUsuario > 0.25) {
                    $totalHorasEntradaUsuario = $totalHorasEntradaUsuario;
                } else {
                    $totalHorasEntradaUsuario = 0;
                }
                // $html .= "<td>" . remplaza_tildes($ExtraUsuarioEntrada->format('%h,%I')) . "</td>";
                $html .= "<td>" . $totalHorasEntradaUsuario . "</td>";
            } else 
            if (!empty($Datos["Entrada"]) && $Datos["IDUsuario"] > 0 && ($horaEntrada  >=  $fechaEntradaEstablecidaUsuario)) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            } else
            if (empty($Datos["Entrada"]) && $Datos["IDUsuario"] > 0) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            }
        }


        //extras socios entrada
        if ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes" && $Datos["IDSocio"] > 0) {
            if (!empty($Datos["Entrada"]) && $Datos["IDSocio"] > 0 && ($horaEntrada <  $fechaEntradaEstablecidaSocio)) {

                $horaSocioEntrada = $ExtraSocioEntrada->format('%H');
                $minutosSocioEntrada = $ExtraSocioEntrada->format('%I');
                $totalHorasEntradaSocio = ((60 * $horaSocioEntrada) + $minutosSocioEntrada) / 60;

                //si las extras son mayores de 15 minutos
                if ((float)$totalHorasEntradaSocio > 0.25) {
                    $totalHorasEntradaSocio = $totalHorasEntradaSocio;
                } else {
                    $totalHorasEntradaSocio = 0;
                }
                // $totalHorasEntradaSocio = str_replace(".", ",", $totalHorasEntradaSocio);
                //$html .= "<td>" . remplaza_tildes($ExtraSocioEntrada->format('%h,%I')) . "</td>";
                $html .= "<td>" . $totalHorasEntradaSocio . "</td>";
            } else 
        if (!empty($Datos["Entrada"]) && $Datos["IDSocio"] > 0 && ($horaEntrada >=  $fechaEntradaEstablecidaSocio)) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            } else
        if (empty($Datos["Entrada"]) && $Datos["IDSocio"] > 0) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            }
        } else if ($Datos["IDSocio"] > 0 && ($Dia == "Sabado" || $Dia == "Domingo" || !empty($DiaFestivo["Fecha"]))) {
            $html .= "<td>" . " " . "</td>";
        }




        //extras usuario salida
        if ($Datos["IDUsuario"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes")) {
            if (!empty($Datos["Salida"]) && $Datos["IDUsuario"] > 0 && ($horaSalida >   $fechaSalidaEstablecidaUsuario)) {
                $horaUsuarioSalida = $ExtraUsuarioSalida->format('%h');
                $minutosUsuarioSalida = $ExtraUsuarioSalida->format('%I');
                $totalHorasSalidaUsuario = ((60 * $horaUsuarioSalida) + $minutosUsuarioSalida) / 60;


                //$totalHorasSalidaUsuario = str_replace(".", ",", $totalHorasSalidaUsuario);
                // $html .= "<td>" . remplaza_tildes($ExtraUsuarioSalida->format('%h,%I')) . "</td>";
                $html .= "<td>" . $totalHorasSalidaUsuario . "</td>";
            } else 
                    if (!empty($Datos["Salida"]) && $Datos["IDUsuario"] > 0 && ($horaSalida <=   $fechaSalidaEstablecidaUsuario)) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            } else 
                    if (empty($Datos["Salida"]) && $Datos["IDUsuario"] > 0) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            }
        } else if ($Datos["IDUsuario"] > 0 && ($Dia == "Sabado" || $Dia == "Domingo" || !empty($DiaFestivo["Fecha"]))) {
            $html .= "<td>" . " " . "</td>";
        }


        //extras socios salida
        if ($Datos["IDSocio"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes")) {
            if (!empty($Datos["Salida"]) && $Datos["IDSocio"] > 0 && ($horaSalida >   $fechaSalidaEstablecidaSocio)) {
                $horaSocioSalida = $ExtraSocioSalida->format('%h');
                $minutosSocioSalida = $ExtraSocioSalida->format('%I');
                $totalHorasSalidaSocio = ((60 * $horaSocioSalida) + $minutosSocioSalida) / 60;


                // $totalHorasSalidaSocio = str_replace(".", ",", $totalHorasSalidaSocio);
                //$html .= "<td>" . remplaza_tildes($ExtraSocioSalida->format('%h,%I')) . "</td>";
                $html .= "<td>" . $totalHorasSalidaSocio . "</td>";
            } else 
                     if (!empty($Datos["Salida"]) && $Datos["IDSocio"] > 0 && ($horaSalida <=   $fechaSalidaEstablecidaSocio)) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            } else
                    if (empty($Datos["Salida"]) && $Datos["IDSocio"] > 0) {
                $html .= "<td>" . remplaza_tildes(('0,0')) . "</td>";
            }
        } else if ($Datos["IDSocio"] > 0 && ($Dia == "Sabado" || $Dia == "Domingo")) {
            $html .= "<td>" . "   " . "</td>";
        }


        if ($Datos["IDSocio"] > 0) {

            //extras totales diurnas socio
            if ($Datos["IDSocio"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes" && empty($DiaFestivo["Fecha"])) && !empty($Datos["HoraInicioLaboralSocio"]) && !empty($Datos["HoraFinalLaboralSocio"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"])) {

                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                $TotalExtrasDiurnas = $TotalExtras[0];
                $TotalExtrasNocturnas = $TotalExtras[1];
                //total extras diurnas
                // $html .= "<td>" . $TotalExtras[0] . "</td>";
                $html .= "<td>" .  $TotalExtrasDiurnas . "</td>";

                //total extras nocturnas
                // $html .= "<td>" . $TotalExtras[1] . "</td>";
                $html .= "<td>" . $TotalExtrasNocturnas . "</td>";
            } else  if ($Datos["IDSocio"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes" && empty($DiaFestivo["Fecha"])) && empty($Datos["HoraInicioLaboralSocio"]) || empty($Datos["HoraFinalLaboralSocio"]) || empty($Datos["FechaMovimientoEntrada"]) || empty($Datos["FechaMovimientoSalida"])) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "   " . "</td>";
            } else if ($Datos["IDSocio"] > 0 && ($Dia == "Sabado" || $Dia == "Domingo" || !empty($DiaFestivo["Fecha"]))) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "   " . "</td>";
            }

            //total extras sabado socio
            if ($Datos["IDSocio"] > 0 && ($Dia == "Sabado" && empty($DiaFestivo["Fecha"])) && !empty($Datos["HoraInicioLaboralSocio"]) && !empty($Datos["HoraFinalLaboralSocio"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"])) {
                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                // $TotalExtrasDiurnasSabado = $TotalExtras[2] + $arr_horas_despues_de_turno[2];
                $TotalExtrasDiurnasSabado = $TotalExtras[2];
                $TotalExtrasNocturnasSabado = $TotalExtras[3];
                //diurnas sabado
                // $html .= "<td>" . $TotalExtras[2] . "</td>";
                $html .= "<td>" . $TotalExtrasDiurnasSabado . "</td>";

                //nocturnas sabado
                //$html .= "<td>" . $TotalExtras[3] . "</td>";
                $html .= "<td>" . $TotalExtrasNocturnasSabado . "</td>";
            } else if ($Datos["IDSocio"] > 0 && ($Dia == "Sabado" && empty($DiaFestivo["Fecha"])) && empty($Datos["HoraInicioLaboralSocio"]) || empty($Datos["HoraFinalLaboralSocio"]) || empty($Datos["FechaMovimientoEntrada"]) || empty($Datos["FechaMovimientoSalida"])) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "   " . "</td>";
            } else  if ($Datos["IDSocio"] > 0 && ($Dia != "Sabado") || !empty($DiaFestivo["Fecha"])) {
                $html .= "<td>" . "   " . "</td>";
                $html .= "<td>" . "  " . "</td>";
            }

            //total extras domingo socio
            if ($Datos["IDSocio"] > 0 && ($Dia == "Domingo" || !empty($DiaFestivo["Fecha"])) && !empty($Datos["HoraInicioLaboralSocio"]) && !empty($Datos["HoraFinalLaboralSocio"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"])) {
                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                $TotalExtrasDiurnasDomingo = $TotalExtras[4];
                $TotalExtrasNocturnasDomingo = $TotalExtras[5];

                //diurnas domingo
                //$html .= "<td>" . $TotalExtras[4] . "</td>";
                $html .= "<td>" . $TotalExtrasDiurnasDomingo . "</td>";

                //nocturnas domingo
                // $html .= "<td>" . $TotalExtras[5] . "</td>";
                $html .= "<td>" . $TotalExtrasNocturnasDomingo . "</td>";
            } else if ($Datos["IDSocio"] > 0 && ($Dia == "Domingo") && empty($Datos["HoraInicioLaboralSocio"]) || empty($Datos["HoraFinalLaboralSocio"]) || empty($Datos["FechaMovimientoEntrada"])) {
                $html .= "<td>" . "   " . "</td>";
                $html .= "<td>" . "   " . "</td>";
            } else if ($Datos["IDSocio"] > 0 && ($Dia != "Domingo" || empty($DiaFestivo["Fecha"]))) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "   " . "</td>";
            }
        }

        if ($Datos["IDUsuario"] > 0) {
            //extras totales diurnas usuario pendiente
            if ($Datos["IDUsuario"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes") && !empty($Datos["HoraInicioLaboral"]) && !empty($Datos["HoraFinalLaboral"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"]) &&  (int)$IDFestivoCol <= 0) {
                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                $TotalExtrasDiurnas = $TotalExtras[0];
                $TotalExtrasNocturnas = $TotalExtras[1];

                //total extras diurnas
                $html .= "<td>" .  $TotalExtrasDiurnas . "</td>";

                //total extras nocturnas
                $html .= "<td>" . $TotalExtrasNocturnas . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia == "Lunes" || $Dia == "Martes" || $Dia == "Miercoles" || $Dia == "Jueves" || $Dia == "Viernes") && empty($Datos["HoraInicioLaboral"]) || empty($Datos["HoraFinalLaboral"]) || empty($Datos["FechaMovimientoEntrada"]) || empty($Datos["FechaMovimientoSalida"])) {
                $html .= "<td>" . " " . "</td>";
                $html .= "<td>" . "  espacio 18 " . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia == "Sabado" || $Dia == "Domingo")) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "  " . "</td>";
            }



            //total extras sabado usuario
            if ($Datos["IDUsuario"] > 0 && ($Dia == "Sabado") && !empty($Datos["HoraInicioLaboral"]) && !empty($Datos["HoraFinalLaboral"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"]) &&  (int)$IDFestivoCol <= 0) {
                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                $TotalExtrasDiurnasSabado = $TotalExtras[2];
                $TotalExtrasNocturnasSabado = $TotalExtras[3];

                //diurnas sabado
                $html .= "<td>" .  $TotalExtrasDiurnasSabado . "</td>";

                //nocturnas sabado usuario
                $html .= "<td>" .   $TotalExtrasNocturnasSabado . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia == "Sabado") && empty($Datos["HoraInicioLaboral"]) || empty($Datos["HoraFinalLaboral"]) || empty($Datos["FechaMovimientoEntrada"]) || empty($Datos["FechaMovimientoSalida"])) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "  " . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia != "Sabado")) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . " " . "</td>";
            }

            //total extras domingo usuario
            if ($Datos["IDUsuario"] > 0 && ($Dia == "Domingo")  && !empty($Datos["HoraInicioLaboral"]) && !empty($Datos["HoraFinalLaboral"]) && !empty($Datos["FechaMovimientoEntrada"]) && !empty($Datos["FechaMovimientoSalida"]) ||  (int)$IDFestivoCol > 0) {
                $TotalExtras = SIMUtil::Tipo_tiempo_laboral($Datos["HoraEntradaEstablecida"], $Datos["HoraSalidaEstablecida"], $Datos["FechaMovimientoEntrada"], $Datos["FechaMovimientoSalida"]);
                $TotalExtrasDiurnasDomingo = $TotalExtras[4];
                $TotalExtrasNocturnasDomingo = $TotalExtras[5];
                //diurnas domingo
                $html .= "<td>" .  $TotalExtrasDiurnasDomingo . "</td>";

                //nocturnas domingo usuario
                $html .= "<td>" . $TotalExtrasNocturnasDomingo . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia == "Domingo")  && empty($Datos["HoraInicioLaboral"]) || empty($Datos["HoraFinalLaboral"]) || empty($Datos["FechaMovimientoEntrada"]) || empty($Datos["FechaMovimientoSalida"])) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . " " . "</td>";
            } else if ($Datos["IDUsuario"] > 0 && ($Dia != "Domingo")) {
                $html .= "<td>" . "  " . "</td>";
                $html .= "<td>" . "  " . "</td>";
            }
        }

        //CAMPOS DIURNAS NOCTURNAS
        $Diurnas = $TotalExtrasDiurnas . $TotalExtrasDiurnasSabado . $TotalExtrasDiurnasDomingo;
        $Nocturnas = $TotalExtrasNocturnas . $TotalExtrasNocturnasSabado . $TotalExtrasNocturnasDomingo;
        $Diurnas = !empty($Diurnas) ? $Diurnas : "0";
        $Nocturnas = !empty($Nocturnas) ? $Nocturnas : "0";
        $html .= "<td>" . $Diurnas   . "</td>";
        $html .= "<td>" . $Nocturnas . "</td>";



        //DATOS SALIDA LABORAL
        if ($Datos['Salida'] != "") {
            // $html .= "<td>" . remplaza_tildes($Datos["LatitudSalida"]) . "</td>";
            //$html .= "<td>" . remplaza_tildes($Datos["LongitudSalida"]) . "</td>";
            $html .= "<td>" . remplaza_tildes($Datos["Salida"]) . "</td>";
            $html .= "<td>" . saber_dia($separarFechaSalida[0]) . "</td>";
            $html .= "<td>" . remplaza_tildes(($separarFechaSalida[0])) . "</td>";
            $html .= "<td>" . remplaza_tildes(($separarFechaSalida[1])) . "</td>";
        } else {
            $html .= "<td>" . "  "  . "</td>";
            $html .= "<td>" . "  "  . "</td>";
            $html .= "<td>" . "  "  . "</td>";
            $html .= "<td>" . "  "  . "</td>";
        }

        if (!empty($Datos["Estado"])) {
            $html .= "<td>" . remplaza_tildes(SIMResources::$estado_checkin_laboral[$Datos["Estado"]]) . "</td>";
        } else {
            $html .= "<td>" . " "  . "</td>";
        }

        if (!empty($Datos["ComentarioRevision"])) {
            $html .= "<td>" . remplaza_tildes($Datos["ComentarioRevision"]) . "</td>";
        } else {
            $html .= "<td>" . " "  . "</td>";
        }


        if (!empty($Datos["ObservacionEntrada"])) {
            $html .= "<td>" . remplaza_tildes($Datos["ObservacionEntrada"]) . "</td>";
        } else {
            $html .= "<td>" . " "  . "</td>";
        }

        if (!empty($Datos["ObservacionSalida"])) {
            $html .= "<td>" . remplaza_tildes($Datos["ObservacionSalida"]) . "</td>";
        } else {
            $html .= "<td>" . " "  . "</td>";
        }

        if (!empty($Datos["FechaCambioEstado"])) {
            $html .= "<td>" . remplaza_tildes($Datos["FechaCambioEstado"]) . "</td>";
        } else {
            $html .= "<td>" . " "  . "</td>";
        }
        $html .= "<td>" . remplaza_tildes($Datos["UsuarioTrCr"]) . "</td>";
        $html .= "<td>" .  $personaqueaprobo . "</td>";
        $html .= "<td>" .  $personaquemodifico . "</td>";
        $html .= "</tr>";
    }





    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <?php
        echo $html;
        ?>
    </body>

    </html>
<?php
    exit();
}
?>