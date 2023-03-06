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
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Club', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <select name="IDListaClubes" id="IDListaClubes">
                                            <option value=""></option>
                                            <?php $sql_lista_club = "Select IDListaClubes,Nombre from ListaClubes Where Publicar = 'S' Order by Nombre";
                                            $query_lista_clubes = $dbo->query($sql_lista_club);
                                            while ($r = $dbo->fetchArray($query_lista_clubes)) :
                                            ?>
                                                <option value="<?php echo $r["IDListaClubes"] ?>" <?php if ($frm["IDListaClubes"] == $r["IDListaClubes"]) echo "selected";  ?>><?php echo $r["Nombre"]; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                </div>




                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaInicioCierreCanjes', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="date" id="FechaInicio" name="FechaInicio" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicioCierreCanjes', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFinCierreCanjes', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="date" id="FechaFin" name="FechaFin" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'FechaFinCierreCanjes', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>" required></div>


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