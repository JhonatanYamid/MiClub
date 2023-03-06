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
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Descripcion"> Descripcion</label>
                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-12">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias Domicilios</label>

                                    <div class="col-sm-8">
                                        <?php
                                        if (!empty($frm["Dias"])) :
                                            $array_dias = explode("|", $frm["Dias"]);
                                        endif;
                                        array_pop($array_dias);
                                        foreach ($Dia_array as $id_dia => $dia) :  ?>
                                            <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if (in_array($id_dia, $array_dias) && $dia != "") echo "checked"; ?>><?php echo $dia; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Periodicidaddd</label>

                                <div class="col-sm-8">
                                    <select class="form-control" id="Tipo" name="Periocidad">
                                        <optgroup label="Periocidad">
                                            <?php
                                            var_dump(SIMResources::$PeriodicidadAuxilios);
                                            $html = "";
                                            foreach (SIMResources::$PeriodicidadAuxilios as $indice => $valor) {
                                                $selected = ($frm["Periocidad"] == $valor) ? "selected" : "";
                                                $html = '<option value="' . $indice . '"' . $selected . '>' . $valor . '</option>';
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group first ">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono </label>
                                    <input name="Icono" id="Icono" class="" title="Icono" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["Icono"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["Icono"] . "' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>


                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo SIMReg::get("IDModulo"); ?>" />
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
