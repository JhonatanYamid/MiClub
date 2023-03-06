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
                                    <label class="col-sm-4 control-label" for="form-field-1">Quien administra?</label>
                                    <select name="AplicaPara" id="AplicaPara">
                                        <option value="S" <?php if ($frm["AplicaPara"] == "S") echo "selected";  ?>>Socio</option>
                                        <option value="U" <?php if ($frm["AplicaPara"] == "U") echo "selected";  ?>>Usuario</option>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoTitulo', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelTitulo" name="LabelTitulo" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoTitulo', LANGSESSION); ?>" value="<?php echo $frm["LabelTitulo"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoBotonIngresarPrestamo', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonIngresarPrestamo" name="LabelBotonIngresarPrestamo" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBotonIngresarPrestamo', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonIngresarPrestamo"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoBotonRegistrarDevolucion', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonRegistrarDevolucion" name="LabelBotonRegistrarDevolucion" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBotonRegistrarDevolucion', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonRegistrarDevolucion"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoTituloBuscador', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelTituloBuscador" name="LabelTituloBuscador" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoTituloBuscador', LANGSESSION); ?>" value="<?php echo $frm["LabelTituloBuscador"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoBuscador', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelBuscador" name="LabelBuscador" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBuscador', LANGSESSION); ?>" value="<?php echo $frm["LabelBuscador"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoTituloCantidad', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelTituloCantidad" name="LabelTituloCantidad" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoTituloCantidad', LANGSESSION); ?>" value="<?php echo $frm["LabelTituloCantidad"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoTituloLugarEntrega', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelTituloLugarEntrega" name="LabelTituloLugarEntrega" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoTituloLugarEntrega', LANGSESSION); ?>" value="<?php echo $frm["LabelTituloLugarEntrega"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoBuscadorObjetos', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelBuscadorObjetos" name="LabelBuscadorObjetos" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBuscadorObjetos', LANGSESSION); ?>" value="<?php echo $frm["LabelBuscadorObjetos"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoProductosPrestados', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelProductosPrestados" name="LabelProductosPrestados" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoProductosPrestados', LANGSESSION); ?>" value="<?php echo $frm["LabelProductosPrestados"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoProductosEntregados', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelProductosEntregados" name="LabelProductosEntregados" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoProductosEntregados', LANGSESSION); ?>" value="<?php echo $frm["LabelProductosEntregados"] ?>" required></div>

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