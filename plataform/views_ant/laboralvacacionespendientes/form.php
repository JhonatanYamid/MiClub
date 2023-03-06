<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">

                                <div class="form-group col-md-6">


                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
                                    <div class="col-sm-8">

                                        <?php
                                        $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                                        <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="" title="Socio">


                                    </div>



                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?> </label>


                                    <div class="col-sm-8">

                                        <?php
                                        $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-funcionario" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                                        <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="" title="Usuario">


                                    </div>
                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Periodo"><?= SIMUtil::get_traduccion('', '', 'Periodo', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Periodo" name="Periodo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Periodo', LANGSESSION); ?>" class="form-control" title="Periodo" value="<?php echo $frm["Periodo"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Dias"><?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="number" id="Dias" name="Dias" placeholder="" class="form-control" title="Dias" value="<?php echo $frm["Dias"] ?>" required></div>


                                </div>


                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DiasCompensatorio"><?= SIMUtil::get_traduccion('', '', 'Diascompensatorio', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="number" id="DiasCompensatorio" name="DiasCompensatorio" placeholder="" class="form-control" title="Dias Compensatorio" value="<?php echo $frm["DiasCompensatorio"] ?>" required></div>


                                </div>


                            </div>


                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                        </button>
                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>