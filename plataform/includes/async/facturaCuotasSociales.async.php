<?php
include("../../procedures/general_async.php");
// SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$fechaHoy = date('m-d', strtotime(date('Y-m-d')));
$sqlSocio = "select Socio.*, IDClub,Date(FechaFacturacion) as FechaFacturacion from Socio where IDClub = 8 AND  concat(Date_format(FechaFacturacion,'%m'),'-',Date_format(FechaFacturacion,'%d')) = '" . $fechaHoy . "'";
$qSocio = $dbo->query($sqlSocio);


function Saldo($IDCategoria, $CuotaFija, $Periodicidad)
{
    $dbo = &SIMDB::get();
    $ValorCategoria = $dbo->getFields('Categoria', 'ValorPorcentaje', 'IDCategoria = "' . $IDCategoria . '" AND IDClub = ' . SIMUser::get('club') . ' AND Publicar="S"');
    $ValorCategoria = ($ValorCategoria != null) ? $ValorCategoria : 100;
    $Porcentaje = $ValorCategoria * $CuotaFija;
    $Porcentaje = $Porcentaje / 100;
    $Saldo = $Porcentaje;
    $Saldo = $Saldo * $Periodicidad;
    return $Saldo;
}

while ($Socio = $dbo->assoc($qSocio)) {
    $Club = $dbo->fetchAll('Club', 'IDClub = "' . $Socio['IDClub'] . '"', 'array');
    $sql_ConfiguracionCuotasSociales = "SELECT IDConfiguracionCuotasSociales,Periodicidad FROM ConfiguracionCuotasSociales WHERE IDClub = '" . $Socio['IDClub'] . "' AND Publicar='S' ORDER BY FechaTrCr DESC LIMIT 1";
    $q_ConfiguracionCuotasSociales = $dbo->query($sql_ConfiguracionCuotasSociales);
    $ConfiguracionCuotasSociales = $dbo->assoc($q_ConfiguracionCuotasSociales);
    $Periodicidad = $ConfiguracionCuotasSociales['Periodicidad'];

    $date = strtotime(date('Y-m-d'));
    $ano = date('Y', $date);
    $sql_FechaCuotasSociales = "SELECT FechaCuotasSociales.Cuota FROM FechaCuotasSociales WHERE IDClub = '" . SIMUser::get('club') . "'  AND FechaInicio <= '" . date('Y-m-d') . "' AND FechaFin >= '" . date('Y-m-d') . "' ORDER BY FechaInicio DESC LIMIT 1";
    $q_FechaCuotasSociales = $dbo->query($sql_FechaCuotasSociales);
    $r_FechaCuotasSociales = $dbo->assoc($q_FechaCuotasSociales);
    $CuotaFija = ($r_FechaCuotasSociales['Cuota'] > 0) ? $r_FechaCuotasSociales['Cuota'] : 0;

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * {
                margin: 0%;
                padding: 0%;
                box-sizing: border-box;
            }

            .factura {
                width: 95%;
                min-height: 600px;
                display: grid;
                grid-template-rows: 20% 20% auto;
                grid-template-columns: calc(100%/3) calc(100%/3) calc(100%/3);
                margin: auto;
                border: 2px solid #000;
                padding: 5%;
                padding-left: 10%;
                padding-right: 10%;
            }

            .titulo-factura {
                width: 70%;
                min-height: 10%;
                margin: auto;
                grid-row: 1;
                grid-column: 1 /span 2;
            }

            .titulo-factura h1,
            .titulo-factura h3 {
                text-align: center;
            }

            .logo-club {
                width: 20%;
                height: 30%;
                grid-row: 1;
                grid-column: 3;
            }

            .info-socio {
                width: 100%;
                grid-row: 2;
                grid-column: 1/span 2;
            }

            .descripcion-factura {
                width: 100%;
                grid-row: 2;
                grid-column: 3;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .info-cuota-social,
            .info-consumo,
            .saldo-abonos,
            .total-pagar {
                width: 100%;
                height: 100%;
                grid-row: 3;
                grid-column: 1/span 3;
                border: 1px solid #000;
                padding: 1%;
            }

            .info-consumo {
                grid-row: 4;
            }

            .saldo-abonos {
                grid-row: 5;
            }

            .total-pagar {
                grid-row: 6;
            }

            .col-socio,
            .col-cuota {
                width: 49%;
                display: inline-block;
                text-align: left;
            }

            .col-cuota {
                text-align: right;
            }
        </style>
    </head>

    <body>
        <main>
            <div class="factura">
                <div class="titulo-factura">
                    <h1>Detalle de cobros Trimestraless</h1>
                    <hr>
                    <h3>TRIMESTRE Abril a Junio de 2021</h3>
                </div>
                <div class="logo-club">
                    <img src="../../assets/img/logo-interno.png" alt="">
                </div>
                <div class="info-socio">
                    <label for=""><b>Socio: <?php echo $Socio['Accion']; ?></b></label> <br>
                    <label for=""><b><?php echo $Socio['Nombre'] . ' ' . $Socio['Apellido']; ?></b></label> <br>
                    <label for=""><b><?php echo $Socio['Direccion']; ?></b></label> <br>
                    <label for=""><b>Vitacura</b> 5 </label> <br>
                </div>
                <div class="descripcion-factura">
                    **<h2>CONVENIO PAC</h2>**
                </div>
                <div class="info-cuota-social">
                    <h2>CUOTAS SOCIALES</h2>
                    <br>
                    <div class="col-socio"><?php echo $Socio['Nombre'] . ' ' . $Socio['Apellido'] ?></div>
                    <div class="col-cuota">$<?php echo number_format(Saldo($Socio['IDCategoria'], $CuotaFija, $Periodicidad), 2); ?></div>
                    <br>
                    <?php
                    $sqlFamilia = "SELECT * FROM Socio where AccionPadre = '" . $Socio['Accion'] . "'";
                    $qFamilia = $dbo->query($sqlFamilia);
                    $SaldoFamilia = 0;
                    while ($Famila = $dbo->assoc($qFamilia)) {
                        $saldo = Saldo($Famila['IDCategoria'], $CuotaFija, $Periodicidad);
                        $SaldoFamilia += $saldo; ?>
                        <div class="col-socio"><?php echo $Famila['Nombre'] . ' ' . $Famila['Apellido'] ?></div>
                        <div class="col-cuota">$<?php echo number_format($saldo, 2); ?></div>
                        <br>
                    <?php   }

                    $saldoTotal = $SaldoFamilia + Saldo($Socio['IDCategoria'], $CuotaFija, $Periodicidad);
                    ?>
                    <br>
                    <br>
                    <div class="col-socio"><b>Total Cuota Social</b></div>
                    <div class="col-cuota"><b>$<?php echo number_format($saldoTotal, 2); ?></b></div>
                    <br>
                </div>
                <div class="info-consumo">
                    <h2>
                        CONSUMOS POLO/EQUITACI&Oacute;N
                    </h2>
                </div>
                <div class="saldo-abonos">
                    <h2>
                        CONSUMOS POLO/EQUITACI&Oacute;N
                    </h2>
                </div>
                <div class="total-pagar">
                    <h2>
                        CONSUMOS POLO/EQUITACI&Oacute;N
                    </h2>
                </div>
            </div>
        </main>
    </body>

    </html>
<?php } ?>