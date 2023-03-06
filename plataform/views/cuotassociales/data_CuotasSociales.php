<?php
// Consulta configuracion modulo cuotas sociales
$sql_ConfiguracionCuotasSociales = "SELECT IDConfiguracionCuotasSociales,Periodicidad,ValorUfPesos FROM ConfiguracionCuotasSociales WHERE IDClub = '" . SIMUser::get('club') . "' AND Publicar='S' ORDER BY FechaTrCr DESC LIMIT 1";
$q_ConfiguracionCuotasSociales = $dbo->query($sql_ConfiguracionCuotasSociales);
$ConfiguracionCuotasSociales = $dbo->assoc($q_ConfiguracionCuotasSociales);
$Periodicidad = $ConfiguracionCuotasSociales['Periodicidad'];
// Fin Consulta configuracion modulo cuotas sociales

// Consulta datos del TITULAR
$Categoria = $dbo->getFields('Categoria', 'Nombre', 'IDCategoria = "' . $frm['IDCategoria'] . '"');
$Parentesco = $dbo->getFields('Parentesco', 'Nombre', 'IDParentesco = "' . $frm['IDParentesco'] . '"');
// Fin Consulta datos del TITULAR

$date = strtotime(date('Y-m-d'));
$ano = date('Y', $date);
$sql_FechaCuotasSociales = "SELECT FechaCuotasSociales.Cuota FROM FechaCuotasSociales WHERE IDClub = '" . SIMUser::get('club') . "'  AND FechaInicio <= '" . date('Y-m-d') . "' AND FechaFin >= '" . date('Y-m-d') . "' ORDER BY FechaInicio DESC LIMIT 1";
$q_FechaCuotasSociales = $dbo->query($sql_FechaCuotasSociales);
$r_FechaCuotasSociales = $dbo->assoc($q_FechaCuotasSociales);
$CuotaFija = ($r_FechaCuotasSociales['Cuota'] > 0) ? $r_FechaCuotasSociales['Cuota'] : 0;

function SaldoReservas($IDSocio, $FechaInicio, $FechaFin)
{
    $dbo = &SIMDB::get();
    $sql = "SELECT rg.IDSocio,se.Nombre, se.Valor3,rg.Fecha,rg.Hora FROM ReservaGeneral rg, Socio s, ServicioElemento se WHERE rg.IDSocio=s.IDSocio and rg.IDServicioElemento=se.IDServicioElemento and rg.IDSocio = $IDSocio AND rg.IDServicio in (38802,38812) AND rg.IDClub =  " . SIMUser::get('club') . " AND rg.Fecha >= '$FechaInicio' AND rg.Fecha <= '$FechaFin' ";
    $q_reserva = $dbo->query($sql);
    $Saldo = 0;
    while ($Reserva = $dbo->assoc($q_reserva)) {
        $Saldo += $Reserva['Valor3'];
    }
    return $Saldo;
}


function Consultar_Periodos_Pendientes($id, $IDConfiguracionCuotasSociales, $Periodicidad)
{
    $dbo = &SIMDB::get();
    $fechaHoy = date('Y-m-d');
    $sql_socios = "SELECT * FROM Socio WHERE IDClub = '" . SIMUser::get('club') . "' AND IDSocio = '" . $id . "'";
    $q_socio = $dbo->query($sql_socios);
    while ($Socios = $dbo->assoc($q_socio)) {
        $sql_HistorialCuotasSociales = "SELECT * FROM HistorialCuotasSociales WHERE IDSocio = '" . $Socios['IDSocio'] . "' ORDER BY FechaFinPeriodo DESC LIMIT 1";
        $q_HistorialCuotasSociales = $dbo->query($sql_HistorialCuotasSociales);
        $HistorialCuotasSociales = $dbo->assoc($q_HistorialCuotasSociales);
        $num_HistorialCuotasSociales = $dbo->rows($q_HistorialCuotasSociales);

        $FechaIngresoClub = ($Socios['FechaIngresoClub'] != '0000-00-00') ? $Socios['FechaIngresoClub'] : $fechaHoy;

        $fechaInicioPeriodo = ($num_HistorialCuotasSociales != '' && $num_HistorialCuotasSociales > 0) ? $HistorialCuotasSociales['FechaFinPeriodo'] : $FechaIngresoClub;


        if ($fechaInicioPeriodo < $fechaHoy) {

            while ($fechaInicioPeriodo <= $fechaHoy) {
                $fechaFinPeriodo = date("Y-m-d", strtotime($fechaInicioPeriodo . " + " . $Periodicidad . " month"));
                $FechaCuotasSociales = $dbo->fetchAll('FechaCuotasSociales', 'FechaInicio <= "' . $fechaInicioPeriodo . '" AND FechaFin >= "' . $fechaInicioPeriodo . '" AND Publicar = "S"', 'array');
                if ($FechaCuotasSociales) {
                    $SaldoSocioPeriodo = Saldo($Socios, $FechaCuotasSociales['Cuota'], $Periodicidad);
                    $SaldoReservaSocio = SaldoReservas($id, $fechaInicioPeriodo, $fechaFinPeriodo);
                    $SaldoSocio = $SaldoSocioPeriodo;
                    $sql_FamiliaresSocio = "SELECT IDSocio,IDCategoria FROM Socio WHERE AccionPadre = '" . $Socios['Accion']  . "'";
                    $q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
                    $CuotasParientes = 0;
                    $SaldoReservas = 0;
                    while ($Familiares = $dbo->assoc($q_FamiliaresSocio)) {
                        $SaldoParientesPeriodo = Saldo($Familiares, $FechaCuotasSociales['Cuota'], $Periodicidad);
                        $SaldoPorParientes = $SaldoParientesPeriodo;
                        $CuotasParientes += $SaldoPorParientes;

                        $SaldoReservas += SaldoReservas($Familiares['IDSocio'], $fechaInicioPeriodo, $fechaFinPeriodo);
                    }
                    $SaldoTotalPeriodo = $SaldoSocio + $CuotasParientes;
                    $SaldoTotalReservas = $SaldoReservaSocio + $SaldoReservas;


                    $sql_Reservas = "SELECT * FROM ReservaGeneral WHERE ";

                    $insert_HistorialCuotasSociales = "INSERT INTO HistorialCuotasSociales (IDSocio,IDClub,IDConfiguracionCuotasSociales,Periodicidad,IDFechaCuotasSociales,FechaCuota,Cuota,IDCategoria,FechaInicioPeriodo,FechaFinPeriodo,Saldo,SaldoReservas,Estado,UsuarioTrCr,FechaTrCr) 
                         VAlUES ('" . $Socios['IDSocio'] . "', '" . SIMUser::get('club') . "','" . $IDConfiguracionCuotasSociales . "','" . $Periodicidad . "','" . $FechaCuotasSociales['IDFechaCuotasSociales'] . "','" . $FechaCuotasSociales['Fecha'] . "','" . $FechaCuotasSociales['Cuota'] . "','" . $Socios['IDCategoria'] . "','" . $fechaInicioPeriodo . "', '" . $fechaFinPeriodo . "', '" . $SaldoTotalPeriodo . "','$SaldoTotalReservas' ,'Pendiente', '" . SIMUser::get('Nombre') . "', NOW())";

                    $dbo->query($insert_HistorialCuotasSociales);
                }

                $fechaInicioPeriodo = date("Y-m-d", strtotime($fechaInicioPeriodo . " + " . $Periodicidad . " month"));
            }
        }
    }
}
function Saldo($row, $CuotaFija, $Periodicidad)
{
    $dbo = &SIMDB::get();
    $ValorCategoria = $dbo->getFields('Categoria', 'ValorPorcentaje', 'IDCategoria = "' . $row['IDCategoria'] . '" AND IDClub = ' . SIMUser::get('club') . ' AND Publicar="S"');
    $ValorCategoria = ($ValorCategoria != null) ? $ValorCategoria : 100;
    $Porcentaje = $ValorCategoria * $CuotaFija;
    $Porcentaje = $Porcentaje / 100;
    $Saldo = number_format($Porcentaje, 2);
    $Saldo = $Saldo * $Periodicidad;
    return $Saldo;
}
function SaldoLocker($Valor, $Fecha, $FechaFin)
{
    $FechaInicio = date('Y-m-d h:i:s', strtotime($Fecha));
    $FechaFin = date('Y-m-d h:i:s', strtotime($FechaFin));

    $FechaInicio = strtotime($FechaInicio);
    $FechaFin = strtotime($FechaFin);

    $Diff = abs($FechaFin - $FechaInicio) / (60 * 60 * 24);
    // $Diff = ($Diff < 0.99) ? 1 : $Diff;
    $Diff = ceil($Diff);

    $ValorReserva = $Diff * $Valor;
    return $ValorReserva;
}
function SaldoParking($Valor, $Fecha, $FechaFin)
{
    $FechaInicio = date('Y-m-d h:i:s', strtotime($Fecha));
    $FechaFin = date('Y-m-d h:i:s', strtotime($FechaFin));

    $FechaInicio = strtotime($FechaInicio);
    $FechaFin = strtotime($FechaFin);

    $Diff = abs($FechaFin - $FechaInicio) / (60 * 60 * 24);
    // $Diff = ($Diff < 0.99) ? 1 : $Diff;
    $Diff = ceil($Diff);

    $ValorReserva = $Diff * $Valor;
    return $ValorReserva;
}
Consultar_Periodos_Pendientes($frm['IDSocio'], $ConfiguracionCuotasSociales['IDConfiguracionCuotasSociales'], $Periodicidad);



// Capturamos los datos del periodo actual
$sql_HistorialCuotasSociales = "SELECT FechaInicioPeriodo,FechaFinPeriodo,SaldoReservas FROM HistorialCuotasSociales WHERE IDSocio = " . $frm['IDSocio'] . " ORDER BY FechaFinPeriodo DESC LIMIT 1";
$q_HistorialCuotasSociales = $dbo->query($sql_HistorialCuotasSociales);
$HistorialCuotasSociales = $dbo->assoc($q_HistorialCuotasSociales);
$SaldoReservas = $HistorialCuotasSociales['SaldoReservas'];
$PeriodoActual = $HistorialCuotasSociales['FechaInicioPeriodo'] . " / " . $HistorialCuotasSociales['FechaFinPeriodo'];
// Fin Capturamos los datos del periodo actual

$sql_FamiliaresSocio = "SELECT * FROM Socio WHERE AccionPadre = '" . $frm['Accion']  . "'";
$q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
$SaldoParientes = 0;
while ($result = $dbo->assoc($q_FamiliaresSocio)) {
    $SaldoPariente = Saldo($result, $CuotaFija, $Periodicidad);
    $SaldoParientes += $SaldoPariente;
}
$Saldo = Saldo($frm, $CuotaFija, $Periodicidad);
$SaldoCuotaSocial = $Saldo + $SaldoParientes;
$SaldoTotal = $Saldo + $SaldoParientes + $SaldoReservas;
