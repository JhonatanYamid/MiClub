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

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pais', LANGSESSION); ?> </label>
                                    <div class="col-sm-7">
                                        <select name="IDPais" id="IDPais" class="form-control" required>
                                            <option value=""></option>
                                            <?php

                                            $sql_pais = "Select * From Pais WHERE Publicar = 'S' Order by Nombre";
                                            $result_pais = $dbo->query($sql_pais);
                                            while ($row_pais = $dbo->fetchArray($result_pais)) : ?>

                                                <option value="<?php echo $row_pais["IDPais"] ?>" <?php if ($frm["IDPais"] == $row_pais["IDPais"]) echo "selected";  ?>><?php echo  $row_pais["Nombre"] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Fecha"><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="date" id="Fecha" name="Fecha" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" value="<?php echo $frm["Fecha"] ?>" required></div>


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