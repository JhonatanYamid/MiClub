<br><br>
<?= SIMUtil::get_traduccion('', '', 'OPCIONPAGOSPENDIENTES', LANGSESSION); ?>

<br><br>

<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"><?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Cliente', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Carné', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'NombreSocio', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Saldoanterior', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Totalpagos', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Totalcompras', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Cuotasostenimiento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Cobropredial', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Notascredito', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Totalapagar', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Pagueseantesde', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?></td>
                        <td>
                            '.xlsx'
                            <input type="hidden" name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1" value=",">
                        </td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'EncabezadosenlaprimeraFila', LANGSESSION); ?>?</td>
                        <td>
                            <?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="1" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="0" checked="" border="0" />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo empty($frm["IDClub"]) ?  SIMUser::get("club") : $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarpagospendientes" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"><?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'IDMovimiento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'PuntoVenta', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'producto', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ValorProducto', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>(yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'NumeroFactura', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Propina', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'TotalFactura', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Pagador', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Separadordecampo', LANGSESSION); ?></td>
                        <td>
                            <select name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1">
                                <option value="TAB">Tabulador</option>
                                <option value=",">Coma (,)</option>
                                <option value="|">Pie (|)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'EncabezadosenlaprimeraFila', LANGSESSION); ?>?</td>
                        <td>
                            <?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="1" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="0" checked="" border="0" />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimiento" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<br><br>
<?= SIMUtil::get_traduccion('', '', 'OPCION', LANGSESSION); ?> 2
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlanoExtracto" name="frmSocioPlanoExtracto" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Valor', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>(yyyy-mm-dd)</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarextracto" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>


<br><br>
<?= SIMUtil::get_traduccion('', '', 'OPCION', LANGSESSION); ?> CYB
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmMovimiento3" name="frmMovimiento3" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Apellido', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'TipoDocumento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Cuota', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Saldo', LANGSESSION); ?> 1</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Saldo', LANGSESSION); ?> 2</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION); ?> 1</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION); ?> 2</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" checked="" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarcuotasaldo" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>


<br><br>
<?= SIMUtil::get_traduccion('', '', 'CARGA', LANGSESSION); ?> DTR
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmMovimientoD" name="frmMovimientoD" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?> APP</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Campo', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Afiliado', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'TipoTurno', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Caddie', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Profesor', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ValorClase', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Luz', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'NombreInvitado', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ValorInvitado', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Retos', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Torneos', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>17</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'TotalMesActual', LANGSESSION); ?></td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" checked="" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimientodtr" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<br><br>
<?= SIMUtil::get_traduccion('', '', 'PuntosSocio', LANGSESSION); ?>
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlanoExtracto" name="frmSocioPlanoExtracto" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> (yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?> (yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Membresia', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Apellido', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'InscripcionCampaña', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Visitas', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Consumosrestaurantesydelicatessen', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Escuelasytalleresdeportivos', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Inscripciónentorneosdeportivos', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Eventossocialesycorporativos', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'TotalPuntos', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Rutaimagen', LANGSESSION); ?> </td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarpuntos" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>



<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioMovimientoCuenta" name="frmSocioMovimientoCuenta" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    <?= SIMUtil::get_traduccion('', '', 'MovimientodeCuenta(INVERMETROS)', LANGSESSION); ?>
    <br>
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"><?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nit', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Detalle', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'cheque', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>vr<?= SIMUtil::get_traduccion('', '', 'cheque', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'consignado', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ctacheq', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'tipoc', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Factura', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'debito', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'credito', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Saldo', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'basereten', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>17</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'porcretoDescuento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>18</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'cencostooValor1al15', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ivacomprasoValor16al30', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>20</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'niif', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>21</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nom_niif', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>22</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'anulada', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>23</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'registro', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>24</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'regnom', LANGSESSION); ?></td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimientocuenta" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioSaldoCartera" name="frmSocioSaldoCartera" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    <?= SIMUtil::get_traduccion('', '', 'Saldocartera(INVERMETROS)', LANGSESSION); ?>
    <br>
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>1-30</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>31-60</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>61-90</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'mas', LANGSESSION); ?> 90</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'consignado', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Juridico', LANGSESSION); ?></td>
                    </tr>


                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarsaldocartera" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>

<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioSaldoCartera" name="frmSocioSaldoCartera" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    <?= SIMUtil::get_traduccion('', '', 'Descuentos(INVERMETROS)', LANGSESSION); ?>
    <br>
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Propietario', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'AguaSerena', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'DescuentoAguaserena', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ClubHouse', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'DescuentoClubHouse', LANGSESSION); ?></td>
                    </tr>


                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Solodejarlainformacióndeestearchivo?(seborraranlosdatosantiguos)', LANGSESSION); ?></td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargardescuento" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>

<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <br>
    <?= SIMUtil::get_traduccion('', '', 'OPCIONPAGOSPENDIENTES', LANGSESSION); ?> (POLO CLUB)
    <br>
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"><?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'ModalidadPagoCredito', LANGSESSION); ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?></td>
                        <td>
                            '.xlsx'
                            <input type="hidden" name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1" value=",">
                        </td>
                    </tr>
                    <tr>
                        <td><?= SIMUtil::get_traduccion('', '', 'EncabezadosenlaprimeraFila', LANGSESSION); ?>?</td>
                        <td>
                            <?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="1" checked="" />
                            <?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
                            <input type="radio" name="IGNORELINE" value="0" />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo empty($frm["IDClub"]) ?  SIMUser::get("club") : $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarpagospendientespoloclub" />
                            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>