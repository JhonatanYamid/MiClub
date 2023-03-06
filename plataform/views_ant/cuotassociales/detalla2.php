<?php
$ConfiguracionCuotasSociales = $dbo->fetchAll('ConfiguracionCuotasSociales', 'IDClub = ' . SIMUser::get('club') . ' AND Publicar="S"', 'array');
$Categoria = $dbo->getFields('Categoria', 'Nombre', 'IDCategoria = "' . $frm['IDCategoria'] . '"');
$Parentesco = $dbo->getFields('Parentesco', 'Nombre', 'IDParentesco = "' . $frm['IDParentesco'] . '"');
function Consultar_Periodos_Pendientes()
{
    $dbo = &SIMDB::get();
    $fechaActual = date('Y-m-d');
    $fechaActual = strtotime($fechaActual);
    $dia = date('d', $fechaActual);

    if ($dia == '04') {
        $sql_socios = "SELECT *  FROM Socio WHERE IDClub = '" . SIMUser::get('club') . "' and IDSocio = '574110'";
        $q_socio = $dbo->query($sql_socios);
        $ConfiguracionCuotasSociales = $dbo->fetchAll('ConfiguracionCuotasSociales', 'IDClub = ' . SIMUser::get('club') . ' AND Publicar="S"', 'array');
        $Periodicidad = $ConfiguracionCuotasSociales['Periodicidad'];

        while ($Socios = $dbo->assoc($q_socio)) {
            $sql_HistorialCuotasSociales = "SELECT * FROM HistorialCuotasSociales WHERE IDSocio = '" . $Socios['IDSocio'] . "' ORDER BY FechaTrCr DESC LIMIT 1";
            $q_HistorialCuotasSociales = $dbo->query($sql_HistorialCuotasSociales);
            $HistorialCuotasSociales = $dbo->assoc($sql_HistorialCuotasSociales);
            if ($HistorialCuotasSociales > 0) {
            } else {
                $fechaInicioPeriodo = $Socios['FechaIngreso'];
                while ($fechaInicioPeriodo <= date('Y-m-d')) {
                    $fechaFinPeriodo = date("Y-m-d", strtotime($fechaInicioPeriodo . " + " . $Periodicidad . " month"));

                    $SaldoSocioPeriodo = Saldo($Socios);
                    $SaldoSocio = $SaldoSocioPeriodo['Saldo'];
                    $DescuentoCuotaSocio = $SaldoSocioPeriodo['DescuentoSocio'];

                    $sql_FamiliaresSocio = "SELECT * FROM Socio WHERE AccionPadre = '" . $Socios['IDSocio']  . "'";
                    $q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
                    $CuotasParientes = 0;
                    $DescuentoCuotasParientes = 0;
                    while ($Familiares = $dbo->assoc($q_FamiliaresSocio)) {
                        $SaldoParientesPeriodo = Saldo($Familiares);
                        $SaldoPorParientes = $SaldoParientesPeriodo['Saldo'];
                        $DescuentoCuota = $SaldoParientesPeriodo['DescuentoSocio'];

                        $CuotasParientes += $SaldoPorParientes;
                        $DescuentoCuotasParientes += $DescuentoCuota;
                    }

                    $SaldoTotalPeriodo = $SaldoSocio + $CuotasParientes;
                    $DescuentoTotalPeriodo = $DescuentoCuotaSocio + $DescuentoCuotasParientes;
                    $insert_HistorialCuotasSociales = "INSERT INTO HistorialCuotasSociales (IDSocio,IDClub,IDConfiguracionCuotasSociales,FechaInicioPeriodo,FechaFinPeriodo,Saldo,Descuento,UsuarioTrCr,FechaTrCr) 
                     VAlUES ('" . $Socios['IDSocio'] . "', '" . SIMUser::get('club') . "','" . $ConfiguracionCuotasSociales['IDConfiguracionCuotasSociales'] . "','" . $fechaInicioPeriodo . "', '" . $fechaFinPeriodo . "', '" . $SaldoTotalPeriodo . "', '" . $DescuentoTotalPeriodo . "', '" . SIMUser::get('Nombre') . "', NOW())";

                    $dbo->query($insert_HistorialCuotasSociales);

                    $fechaInicioPeriodo = date("Y-m-d", strtotime($fechaInicioPeriodo . " + " . $Periodicidad . " month"));
                }
            }
        }
    }
}

function Saldo($row)
{
    $dbo = &SIMDB::get();
    $ConfiguracionCuotasSociales = $dbo->fetchAll('ConfiguracionCuotasSociales', 'IDClub = ' . SIMUser::get('club') . ' AND Publicar="S"', 'array');
    $sql_Reglas_Negocio = "SELECT CampoCriterio,Validacion,ValorCriterio,Descuento FROM DetalleConfiguracionCuotasSociales WHERE IDConfiguracionCuotasSociales = '" . $ConfiguracionCuotasSociales['IDConfiguracionCuotasSociales'] . "' AND  Publicar ='S'";
    $q_Reglas_Negocio = $dbo->query($sql_Reglas_Negocio);
    $Descuento = 0;
    while ($r_Reglas_Negocio = $dbo->assoc($q_Reglas_Negocio)) {
        $keysSocio = array_keys($frm);
        $keysReglas = array_keys($r_Reglas_Negocio);
        $Periodicidad = $r_Reglas_Negocio['Periodicidad'];
        if ($keysReglas[0] == 'CampoCriterio') {
            switch ($r_Reglas_Negocio['CampoCriterio']) {
                case 'EstadoCivil':
                    if ($frm[$r_Reglas_Negocio['CampoCriterio']] == $r_Reglas_Negocio['ValorCriterio']) {
                        $Descuento += $r_Reglas_Negocio['Descuento'];
                    }
                    break;
                case 'Edad':

                    $EdadSocio = SIMUtil::Calcular_Edad($row['FechaNacimiento']);
                    switch ($r_Reglas_Negocio['Validacion']) {
                        case '>':
                            if ($EdadSocio > $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case '>=':
                            if ($EdadSocio >= $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case '<':
                            $EdadSocio;
                            if ($EdadSocio < $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case '<=':
                            if ($EdadSocio <= $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case '==':
                            if ($EdadSocio == $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case '!=':
                            if ($EdadSocio != $r_Reglas_Negocio['ValorCriterio']) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        case 'Rango':
                            $parametros = explode('-', $r_Reglas_Negocio['ValorCriterio']);
                            if ($EdadSocio >= $parametros[0] && $EdadSocio <= $parametros[1]) {
                                $Descuento += $r_Reglas_Negocio['Descuento'];
                            }
                            break;
                        default:
                            //0
                            break;
                    }

                    break;
                case 'IDCategoria':
                    $IDCategoria = $dbo->getFields('Categoria', 'IDCategoria', 'Nombre = "' . $r_Reglas_Negocio['ValorCriterio'] . '"');

                    if ($row[$r_Reglas_Negocio['CampoCriterio']] == $IDCategoria) {
                        $Descuento += $r_Reglas_Negocio['Descuento'];
                    }

                    break;
                case 'TipoSocio':
                    if ($row[$r_Reglas_Negocio['CampoCriterio']] == $r_Reglas_Negocio['ValorCriterio']) {
                        $Descuento += $r_Reglas_Negocio['Descuento'];
                    }

                    break;
                case 'IDParentesco':
                    $IDParentesco = $dbo->getFields('Parentesco', 'IDParentesco', 'Nombre = "' . $r_Reglas_Negocio['ValorCriterio'] . '"');
                    if ($row[$r_Reglas_Negocio['CampoCriterio']] == $IDParentesco) {
                        $Descuento += $r_Reglas_Negocio['Descuento'];
                    }

                    break;

                default:
                    // 0
                    break;
            }
        }
    }
    $CuotaFija = $ConfiguracionCuotasSociales['CuotaFija'];
    $Periodicidad = $ConfiguracionCuotasSociales['Periodicidad'];
    $Porcentaje_Descuento = $Descuento * $CuotaFija;
    $Porcentaje_Descuento = $Porcentaje_Descuento / 100;
    $Porcentaje_Descuento = $Porcentaje_Descuento;
    $Saldo = $CuotaFija - $Porcentaje_Descuento;
    $Saldo = $Saldo * $Periodicidad;
    return $Detalle = array('Saldo' => $Saldo, 'DescuentoSocio' => $Porcentaje_Descuento);
}

// var_dump(Consultar_Periodos_Pendientes());
// die();
$sql_FamiliaresSocio = "SELECT * FROM Socio WHERE AccionPadre = '" . $frm['IDSocio']  . "'";
$q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
$SaldoParientes = 0;
while ($result = $dbo->assoc($q_FamiliaresSocio)) {
    $DetalleDescuentos = Saldo($result);
    $SaldoPariente = $DetalleDescuentos['Saldo'];
    $SaldoParientes += $SaldoPariente;
}

$DetalleSaldo = Saldo($frm);
$Saldo = $DetalleSaldo['Saldo'];
$DescuentoSocio = $DetalleSaldo['DescuentoSocio'];

$SaldoTotal = $Saldo + $SaldoParientes;

?>
<style>
    a {
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis !important;
    }
</style>

<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>DETALLAR <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Tipo Socio </label>
                                <div class="col-sm-8">
                                    <input type="text" id="TipoSocio" name="TipoSocio" placeholder="TipoSocio" class="col-xs-12 mandatory" title="TipoSocio" value="<?php echo $frm["TipoSocio"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Accion </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Accion" name="Accion" placeholder="Accion" class="col-xs-12 mandatory" title="Accion" value="<?php echo $frm["Accion"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Nombre </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" readonly="readonly">
                                    <!-- <input type="text" id="Usuario" name="Usuario" placeholder="Usuario" class="col-xs-12 mandatory" title="Usuario" value="<?php echo $rowUser["Nombre"]; ?>" readonly="readonly"> -->
                                    <!-- <input type="hidden" id="IDUsuario" name="IDUsuario" value="<?php echo $IDUser = $rowUser["IDUsuario"] > 0 ? $rowUser["IDUsuario"] : 0; ?>"> -->
                                    <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $frm['IDSocio']; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Apellido </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="Apellido" value="<?php echo $frm["Apellido"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Numero de Documento </label>
                                <div class="col-sm-8">
                                    <input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="NumeroDocumento" class="col-xs-12 mandatory" title="NumeroDocumento" value="<?php echo $frm["NumeroDocumento"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Edad </label>
                                <?php $Edad = SIMUtil::Calcular_Edad($frm['FechaNacimiento']); ?>
                                <div class="col-sm-8">
                                    <input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="Apellido" value="<?php echo $Edad; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Categoria </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Categoria" name="Categoria" placeholder="Categoria" class="col-xs-12 mandatory" title="Categoria" value="<?php echo $Categoria; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Parentesco </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Parentesco" name="Parentesco" placeholder="Parentesco" class="col-xs-12 mandatory" title="Parentesco" value="<?php echo $Parentesco; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Estado Civil </label>
                                <div class="col-sm-8">
                                    <input type="text" id="EstadoCivil" name="EstadoCivil" placeholder="EstadoCivil" class="col-xs-12 mandatory" title="EstadoCivil" value="<?php echo $frm['EstadoCivil']; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="FechaTrCr"> Fecha Ingreso </label>
                                <div class="col-sm-8"><input type="text" id="FechaTrCr" name="FechaTrCr" placeholder="Fecha Solicitud" class="col-xs-12 mandatory" title="FechaTrCr" value="<?php echo $frm["FechaIngreso"]; ?>" readonly="readonly"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Periodicidad </label>
                                <div class="col-sm-8">
                                    <input type="text" id="" name="Periodicidad" placeholder="Periodicidad" class="col-xs-12 mandatory" title="Periodicidad" value="<?php echo $ConfiguracionCuotasSociales["Periodicidad"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Periodo Actual </label>
                                <div class="col-sm-8">
                                    <input type="text" id="PeriodoActual" name="PeriodoActual" placeholder="PeriodoActual" class="col-xs-12 mandatory" title="PeriodoActual" value="<?php echo $frm["Apellido"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Cuota Fija </label>
                                <div class="col-sm-8">
                                    <input type="text" id="CuotaFija" name="CuotaFija" placeholder="CuotaFija" class="col-xs-12 mandatory" title="CuotaFija" value="<?php echo $ConfiguracionCuotasSociales["CuotaFija"]; ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Total Descuento </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 mandatory" title="Saldo" value="$ <?php echo number_format($DescuentoSocio, 2); ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Cuota Socio </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 mandatory" title="Saldo" value="$ <?php echo number_format($Saldo, 2); ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Saldo Total </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 mandatory" title="Saldo" value="$ <?php echo number_format($SaldoTotal, 2); ?>" readonly="readonly">
                                </div>
                            </div>



                        </div>
                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDAuxilios" id="IDAuxilios" value="<?php echo $frm["IDAuxilios"]; ?>" />
                                    <!-- <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button> -->
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" id="FechaRevision" name="FechaRevision" placeholder="Fecha Revision" class="col-xs-12 mandatory" title="FechaRevision" value="<?php echo $DateAndTime; ?>">

                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="widget-header widget-header-large">
                        <h3 class="widget-title grey lighter">
                            <i class="ace-icon fa fa-check-circle green"></i>
                            Detalle Familia
                        </h3>
                    </div>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <th align="center" valign="middle" width="64">Editar</th>
                            <th>TipoSocio</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Categoria</th>
                            <th>Parentesco</th>
                            <th>Estado Civil</th>
                            <th>Descuento</th>
                            <th>Saldo</th>
                        </tr>
                        <tbody id="listacontactosanunciante">
                            <?php
                            $q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
                            while ($r = $dbo->assoc($q_FamiliaresSocio)) {
                                $DetalleSaldoPariente = Saldo($r);
                                $SaldoPorPariente = $DetalleSaldoPariente['Saldo'];
                                $DescuentoPorPariente = $DetalleSaldoPariente['DescuentoSocio'];
                            ?>
                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                    <td><?php echo $r['Accion']; ?></td>
                                    <td><?php echo $r['TipoSocio']; ?></td>
                                    <td><?php echo $r['Nombre'] . ' ' . $r['Apellido']; ?></td>
                                    <td><?php echo SIMUtil::Calcular_Edad($r['FechaNacimiento']); ?></td>
                                    <td><?php echo $dbo->getFields('Categoria', 'Nombre', 'IDCategoria ="' . $r['IDCategoria'] . '"'); ?></td>
                                    <td><?php echo $dbo->getFields('Parentesco', 'Nombre', 'IDParentesco ="' . $r['IDParentesco'] . '"'); ?></td>
                                    <td><?php echo $r['EstadoCivil']; ?></td>
                                    <td>$<?php echo number_format($DescuentoPorPariente, 2); ?></td>
                                    <td>$<?php echo number_format($SaldoPorPariente, 2); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <tr>
                            <th class="texto" colspan="16"></th>
                        </tr>
                    </table>


                    <div class="widget-header widget-header-large">
                        <h3 class="widget-title grey lighter">
                            <i class="ace-icon fa fa-check-circle green"></i>
                            Historial Periodicidad
                        </h3>
                    </div>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                        </tr>
                        <tbody id="listacontactosanunciante">
                            <?php
                            $sql_HistorialCuotasSociales = "SELECT * FROM HistorialCuotasSociales WHERE IDSocio = '" . $frm['IDSocio'] . "' AND IDClub = '" . SIMUser::get('club') . "' ORDER BY IDHistorialCuotasSociales DESC LIMIT 10";
                            $q_HistorialCuotasSociales = $dbo->query($sql_HistorialCuotasSociales);
                            while ($r = $dbo->assoc($q_HistorialCuotasSociales)) {
                                if ($r['Saldo'] == 'Pendiente') {
                                    $ColorEstado = '#f70914';
                                } else {
                                    $ColorEstado = '#4caf50';
                                }
                            ?>
                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                    <td><?php echo $r['FechaInicioPeriodo']; ?></td>
                                    <td><?php echo $r['FechaFinPeriodo']; ?></td>
                                    <td>$<?php echo $r['Saldo']; ?></td>
                                    <td style="color:<?php echo $ColorEstado; ?>"><?php echo $r['Estado']; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <tr>
                            <th class="texto" colspan="16"></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<script>
    function TipoRechazo(i) {
        if (i == 2) {
            document.getElementById('TipoRechazo').style.display = 'block'
        } else {
            document.getElementById('TipoRechazo').style.display = 'none'
        }
    }
    var estado = document.getElementById('IDEstado').value
    window.load = TipoRechazo(estado);
</script>

<?php
include("cmp/footer_scripts.php");
