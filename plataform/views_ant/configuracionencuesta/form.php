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
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'AplicaParaCualModuloDeEncuestas', LANGSESSION); ?>:</label>

                                    <div class="col-sm-8">
                                        <select name="IDModulo" id="IDModulo">
                                            <option value="0"><?= SIMUtil::get_traduccion('', '', 'Encuesta', LANGSESSION); ?></option>
                                            <?php $sqlEncuesta = "SELECT IDModulo FROM Modulo WHERE Tipo = 'Encuesta'";

                                            $queryEncuesta = $dbo->query($sqlEncuesta);

                                            while ($DatosEncuesta = $dbo->fetchArray($queryEncuesta)) {
                                                $ArrayidModuloEncuesta[] = $DatosEncuesta["IDModulo"];
                                            }
                                            $DatosArrayEncuesta = implode(",", $ArrayidModuloEncuesta);
                                            $IDClub = SIMUser::get("club");
                                            $sql1Encuesta = "SELECT TituloLateral,IDModulo FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayEncuesta) and Activo = 'S'";
                                            $query1Encuesta = $dbo->query($sql1Encuesta);
                                            while ($DatosEncuesta = $dbo->fetchArray($query1Encuesta)) {
                                            ?>
                                                <option value="<?php echo $DatosEncuesta["IDModulo"]; ?>" <?php if ($frm["IDModulo"] == $DatosEncuesta["IDModulo"]) echo "selected"; ?>><?php echo $DatosEncuesta["TituloLateral"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMostrarHistorial', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarHistorial"], 'PermiteMostrarHistorial', "class='input mandatory'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoMostrarHistorial', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelMostrarHistorial" name="LabelMostrarHistorial" placeholder="" class="form-control" title="Vehiculos" value="<?php echo $frm["LabelMostrarHistorial"] ?>" required></div>


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