<?php
include("data_CuotasSociales.php")
?>

<style>
    .first {
        margin-bottom: 1%;
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
                    <div class="tabbable" id="myTABS" role="tablist">
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="<?php if (empty($_GET['tabcuotassociales'])) echo "active"; ?>">
                                <a data-toggle="tab" href="#home">
                                    <i class="green ace-icon fa fa-home bigger-120"></i>
                                    Cuotas Sociales
                                </a>
                            </li>

                            <li class="<?php if ($_GET['tabcuotassociales'] == "locker") echo "active"; ?>">
                                <a data-toggle="tab" href="#locker">
                                    <i class="green ace-icon fa fa-check-circle bigger-120"></i>
                                    Lockers
                                </a>
                            </li>
                            <li class="<?php if ($_GET['tabcuotassociales'] == "parking") echo "active"; ?>">
                                <a data-toggle="tab" href="#parking">
                                    <i class="green ace-icon fa fa-check-circle bigger-120"></i>
                                    Parqueaderos
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade <?php if (empty($_GET['tabcuotassociales'])) echo "in active"; ?> ">
                                <?php //include_once("cuotassociales.php"); 
                                ?>
                                <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                    <div class="form-group first ">

                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Tipo socio </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="TipoSocio" name="TipoSocio" placeholder="TipoSocio" class="col-xs-12 " title="TipoSocio" value="<?php echo $frm["TipoSocio"]; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Accion </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Accion" name="Accion" placeholder="Accion" class="col-xs-12 " title="Accion" value="<?php echo $frm["Accion"]; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Nombre </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 " title="Nombre" value="<?php echo $frm["Nombre"]; ?>" readonly="readonly">
                                                <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $frm['IDSocio']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Apellido </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 " title="Apellido" value="<?php echo $frm["Apellido"]; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Numero de documento </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="NumeroDocumento" class="col-xs-12 " title="NumeroDocumento" value="<?php echo $frm["NumeroDocumento"]; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Edad </label>
                                            <?php $Edad = SIMUtil::Calcular_Edad($frm['FechaNacimiento']); ?>
                                            <div class="col-sm-8">
                                                <input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 " title="Apellido" value="<?php echo $Edad; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Categoria </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Categoria" name="Categoria" placeholder="Categoria" class="col-xs-12 " title="Categoria" value="<?php echo $Categoria; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="FechaTrCr"> Fecha ingreso </label>
                                            <div class="col-sm-8"><input type="text" id="FechaIngresoClub" name="FechaIngresoClub" placeholder="Fecha Ingreso" class="col-xs-12 " title="FechaIngresoClub" value="<?php echo $frm["FechaIngresoClub"]; ?>" readonly="readonly"></div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Periodicidad </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="" name="Periodicidad" placeholder="Periodicidad" class="col-xs-12 " title="Periodicidad" value="<?php echo SIMResources::$PeriodicidadCuotasSociales[$ConfiguracionCuotasSociales['Periodicidad']]; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Periodo actual </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="PeriodoActual" name="PeriodoActual" placeholder="PeriodoActual" class="col-xs-12 " title="PeriodoActual" value="<?php echo $PeriodoActual; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for=""> Cuota socio </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 " title="Saldo" value="$<?php echo $Saldo; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for=""> Saldo reservas </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="SaldoReservas" name="SaldoReservas" placeholder="Saldo Reservas" class="col-xs-12 " title="SaldoReservas" value="$<?php echo $SaldoReservas; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for=""> Saldo cuota social </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 " title="Saldo" value="$<?php echo $SaldoCuotaSocial; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 first">
                                            <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Saldo total </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Saldo" name="Saldo" placeholder="Saldo" class="col-xs-12 " title="Saldo" value="$<?php echo $SaldoTotal; ?>" readonly="readonly">
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
                                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                                <input type="hidden" id="FechaRevision" name="FechaRevision" value="<?php echo $DateAndTime; ?>">
                                                <input type="hidden" id="ValorUfPesos" name="ValorUfPesos" value="<?php echo $ConfiguracionCuotasSociales['ValorUfPesos']; ?>">

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
                                        <th>Acci&oacute;n Titular</th>
                                        <th>N&uacute;mero Documento</th>
                                        <th>TipoSocio</th>
                                        <th>Nombre</th>
                                        <th>Edad</th>
                                        <th>Categoria</th>
                                        <th>Parentesco</th>
                                        <th>Saldo cuota social</th>
                                        <th>Saldo reservas</th>
                                        <th>Saldo total</th>
                                    </tr>
                                    <tbody id="listacontactosanunciante">
                                        <?php
                                        $q_FamiliaresSocio = $dbo->query($sql_FamiliaresSocio);
                                        while ($r = $dbo->assoc($q_FamiliaresSocio)) {
                                            $SaldoPariente = Saldo($r, $CuotaFija, $Periodicidad);
                                            $SaldoPorPariente = $SaldoPariente;
                                            $SaldoReservaPorPariente = SaldoReservas($r['IDSocio'], $HistorialCuotasSociales['FechaInicioPeriodo'], $HistorialCuotasSociales['FechaFinPeriodo']);

                                        ?>
                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                                <td><?php echo $r['AccionPadre']; ?></td>
                                                <td><?php echo $r['NumeroDocumento']; ?></td>
                                                <td><?php echo $r['TipoSocio']; ?></td>
                                                <td><?php echo $r['Nombre'] . ' ' . $r['Apellido']; ?></td>
                                                <td><?php echo SIMUtil::Calcular_Edad($r['FechaNacimiento']); ?></td>
                                                <td><?php echo $dbo->getFields('Categoria', 'Nombre', 'IDCategoria ="' . $r['IDCategoria'] . '"'); ?></td>
                                                <td><?php echo $dbo->getFields('Parentesco', 'Nombre', 'IDParentesco ="' . $r['IDParentesco'] . '"'); ?></td>
                                                <td>$<?php echo $SaldoPorPariente; ?></td>
                                                <td>$<?php echo $SaldoReservaPorPariente; ?></td>
                                                <td>$<?php echo $SaldoPorPariente + $SaldoReservaPorPariente; ?></td>
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
                                        $sql_HistorialCuotasSociales = "SELECT * FROM HistorialCuotasSociales WHERE IDSocio = '" . $frm['IDSocio'] . "' AND IDClub = '" . SIMUser::get('club') . "' ORDER BY IDHistorialCuotasSociales DESC";
                                        $q_HistorialCuotasSociales = $dbo->query($sql_HistorialCuotasSociales);
                                        $htmlBottonPago = "";
                                        while ($r = $dbo->assoc($q_HistorialCuotasSociales)) {

                                            if ($r['Estado'] == 'Pendiente') {
                                                $ColorEstado = '#f70914';
                                                $htmlBottonPago = '<span class="input-group-btn">
                                                            <button type="button" class="btn btn-primary btn-sm boton-modal" data-toggle="modal" data-target="#PagoModal" data-id="' . $r['IDHistorialCuotasSociales'] . '" data-cuota="' . $r['Saldo'] . '">
                                                                Pagar
                                                            </button>
                                                        </span>';
                                            } else {
                                                $ColorEstado = '#4caf50';
                                                $htmlBottonPago = "";
                                            }
                                        ?>
                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                                <td><?php echo $r['FechaInicioPeriodo']; ?></td>
                                                <td><?php echo $r['FechaFinPeriodo']; ?></td>
                                                <td>$<?php echo $r['Saldo']; ?></td>
                                                <td style="display:flex;aling-items:center;justify-content:center;color:<?php echo $ColorEstado; ?>"><?php echo $r['Estado'] . '&nbsp;&nbsp' . $htmlBottonPago; ?></td>
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
                            <div id="locker" class="tab-pane fade <?= ($_GET['tabcuotassociales'] == 'locker') ? "in active" : ''; ?> ">
                                <?php
                                include("locker.php")
                                ?>
                            </div>
                            <div id="parking" class="tab-pane fade <?php if ($_GET['tabcuotassociales'] == 'parking') echo "in active"; ?> ">
                                <?php
                                include_once("parking.php");
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<?php
include("cmp/footer_scripts.php");
?>

<!-- Modal -->
<div class="modal fade" id="PagoModal" tabindex="-1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content col-sm-12">
            <div class="modal-header">
                <h5 class="modal-title" id="PagoModalLabel">Pagar Cuota Social</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formularioPago" method="post" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 payForm">
                            <div class="col-sm-12">
                                <label for="" class="col-sm-8">Saldo: <b>UF <label class="Saldo"></label> / <b>$<label class="SaldoPesos"></label></b></label>
                                <div class="form-group col-sm-2">
                                    <button type="button" class="btn btn-info btn-xs addMetodoPago">Agregar Metodo Pago</button>
                                </div>
                            </div>
                            <input type="hidden" name="" id="contRow" value="0">
                            <div class="form-group first rowPay">
                                <div class="col-xs-12 col-sm-6 first">
                                    <label class="col-sm-3 control-label no-padding-right" for="Usuario"> M&eacute;todo </label>
                                    <div class="col-sm-9">
                                        <select name="MetodoPago0" id="MetodoPago0" class="col-xs-12 MetodoPago" title="TipoSocio" required>
                                            <option value="">[ Seleccione m&eacute;todo ]</option>
                                            <?php
                                            foreach (SIMResources::$MetodoPago as $indice => $valor) {
                                            ?>
                                                <option value="<?php echo $indice; ?>"><?php echo $valor; ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <input type="hidden" name="pay" id="pay" value="">
                                        <input type="hidden" name="saldo" id="saldo" value="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 first">
                                    <label class="col-sm-3 control-label no-padding-right" for="Usuario"> Monto </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="MontoPago0" id="MontoPago0" class="col-xs-12 MontoPago" placeholder="MontoPago" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary reload" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btnPago">Pagar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.btn-sm').click(function() {
        var id = $(this).attr('data-id');
        $('#pay').val(id);
        var saldo = $(this).attr('data-cuota');
        var ValorUfPesos = $('#ValorUfPesos').val();
        var ValorPesos = saldo * ValorUfPesos;
        ValorPesos = ValorPesos.toFixed(2);
        $('#saldo').val(saldo);

        $('.Saldo').html(saldo).css({
            'color': 'red'
        });
        $('.SaldoPesos').html(ValorPesos).css({
            'color': 'red'
        });
    });
    $('.addMetodoPago').click(function() {
        var cont = $('#contRow').val();
        cont++;
        var html = '<div class="form-group first rowPay">' + '<div class="col-xs-12 col-sm-6 first">' +
            '<label class="col-sm-3 control-label no-padding-right" for="Usuario"> M&eacute;todo </label>' +
            '<div class="col-sm-9">' +
            '<select name="MetodoPago' + cont + '" id="MetodoPago' + cont + '" class="col-xs-12 MetodoPago" title="TipoSocio" required>' +
            '<option value="">[ Seleccione m&eacute;todo ]</option>' +
            <?php
            foreach (SIMResources::$MetodoPago as $indice => $valor) {
            ?> '<option value="<?php echo $indice; ?>"><?php echo $valor; ?></option>' +
            <?php }
            ?> '.</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-xs-12 col-sm-6 first">' +
            '<label class="col-sm-3 control-label no-padding-right" for="Usuario"> Monto </label>' +
            '<div class="col-sm-9">' +
            '<input type="text" name="MontoPago' + cont + '" id="MontoPago' + cont + '" class="col-xs-12 MontoPago" placeholder="MontoPago" value="">' +
            '</div>' +
            '</div>' +
            '</div>';
        $('.payForm').append(html);
        $('#contRow').val(cont);

    });
    $('.btnPago').click(function() {
        if ($('#MetodoPago0').val() == '') {
            alert('Metodo de pago es necesario.');
            return false;
        }
        if ($('#MontoPago0').val() == '') {
            alert('Monto es necesario.');
            return false;
        }
        if ($('#MetodoPago0').val() != '' && $('#MontoPago0').val() != '') {
            var id = $('#pay').val();
            var saldo = $('#saldo').val();


            var valores = '[';
            $('.rowPay').each(function(e) {
                var metodo = $('#MetodoPago' + e).val();
                var monto = $('#MontoPago' + e).val();
                valores += '{"metodo":"' + metodo + '","monto":"' + monto + '"}';
                e++;
            });
            valores += ']';
            valores = valores.replace(/}{/gi, '},{');
            valores = jQuery.parseJSON(valores);
            $.post('includes/async/pagar_cuota_social.async.php', {
                'id': id,
                'saldo': saldo,
                'data': valores
            }).done(function(response) {
                saldo = response.saldo.toString();
                var ValorUfPesos = $('#ValorUfPesos').val();
                var ValorPesos = response.saldo * ValorUfPesos;
                ValorPesos = ValorPesos.toFixed(2);
                alert(response.mensaje);
                $('.Saldo').html(saldo);
                $('#saldo').val(response.saldo);
                $('.SaldoPesos').html(ValorPesos);
                if (response.saldo == 0) {
                    location.href = "cuotassociales.php?action=detallar&id=<?php echo $frm['IDSocio'] ?>";
                }
            });
            return false;
        }
    });
    $('.reload').click(function() {
        location.href = "cuotassociales.php?action=detallar&id=<?php echo $frm['IDSocio'] ?>";
    });
</script>