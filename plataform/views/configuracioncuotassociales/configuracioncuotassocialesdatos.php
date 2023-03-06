<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cuota Fija </label>
                                <div class="col-sm-8"><input type="text" id="CuotaFija" name="CuotaFija" placeholder="Cuota Fija" class="col-xs-12 mandatory" title="Cuota Fija" value="<?php echo $frm["CuotaFija"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descuento PAC </label>
                                <div class="col-sm-8"><input type="text" id="DescuentoPac" name="DescuentoPac" placeholder="Descuento PAC" class="col-xs-12 mandatory" title="Descuento PAC" value="<?php echo $frm["DescuentoPac"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Periodicidad </label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="Tipo" name="Periodicidad">
                                        <optgroup label="Periodicidad">
                                            <option value="1" <?php if ($frm["Periodicidad"] == "1") echo "selected"; ?>>Mensual</option>
                                            <option value="3" <?php if ($frm["Periodicidad"] == "3") echo "selected"; ?>>Trimestral</option>
                                            <option value="6" <?php if ($frm["Periodicidad"] == "6") echo "selected"; ?>>Semestral</option>
                                            <option value="12" <?php if ($frm["Periodicidad"] == "12") echo "selected"; ?>>Anual</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor UF en Pesos </label>
                                <div class="col-sm-8"><input type="text" id="ValorUfPesos" name="ValorUfPesos" placeholder="Valor UF en Pesos" class="col-xs-12 mandatory" title="Valor UF en Pesos" value="<?php echo $frm["ValorUfPesos"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
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