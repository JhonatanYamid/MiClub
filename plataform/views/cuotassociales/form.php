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
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Usuario </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Socio" name="Socio" placeholder="Socio" class="col-xs-12 mandatory" title="Socio" value="<?php echo $frm["Nombre"]; ?>" readonly="readonly">
                                    <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $frm['IDSocio']; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="FechaTrCr"> Fecha Solicitud </label>
                                <div class="col-sm-8"><input type="text" id="FechaTrCr" name="FechaTrCr" placeholder="Fecha Solicitud" class="col-xs-12 mandatory" title="FechaTrCr" value="<?php echo $frm["FechaTrCr"]; ?>" readonly="readonly"></div>
                            </div>
                        </div>
                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-check-circle green"></i>
                                Detalle Familia
                            </h3>
                        </div>
                        <div class="form-group first ">
                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDAuxilios" id="IDAuxilios" value="<?php echo $frm["IDAuxilios"]; ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" id="FechaRevision" name="FechaRevision" placeholder="Fecha Revision" class="col-xs-12 mandatory" title="FechaRevision" value="<?php echo $DateAndTime; ?>">

                                </div>
                            </div>
                    </form>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <th valign="middle" width="64">Editar</th>
                            <th>Accion</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Parentesco</th>
                            <th>Saldo</th>
                        </tr>
                        <tbody id="listacontactosanunciante">
                            <?php

                            $sql_ReglasNegocio = "SELECT * FROM Socio WHERE IDConfiguracionCuotasSociales = '" . $frm['IDConfiguracionCuotasSociales']  . "'";
                            $q_ReglasNegocio = $dbo->query($sql_ReglasNegocio);

                            while ($r = $dbo->object($q_ReglasNegocio)) {
                            ?>
                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                    <td width="64">
                                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDDetalleConfiguracionCuotasSociales=" . $r->IDDetalleConfiguracionCuotasSociales ?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
                                    </td>
                                    <td><?php //echo SIMResources::$CriterioReglasNegocio[$r->CampoCriterio]; 
                                        ?></td>
                                    <td><?php //echo SIMResources::$ValidacionReglasNegocio[$r->Validacion]; 
                                        ?></td>
                                    <td><?php echo $r->ValorCriterio; ?></td>
                                    <td><?php echo $r->Descuento; ?></td>
                                    <td><?php echo $r->Publicar; ?></td>
                                    <td align="center" width="64">
                                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminarReglasNegocio&id=<?php echo $frm[$key]; ?>&IDDetalleConfiguracionCuotasSociales=<? echo $r->IDDetalleConfiguracionCuotasSociales ?>&tabencuesta=formulario&IDAuxilios=<?php echo $frm[$key]; ?>"></a>
                                    </td>
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
<?
include("cmp/footer_scripts.php");
?>