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
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoCrearDisponibilidad', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelCrearDisponibilidad" name="LabelCrearDisponibilidad" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoCrearDisponibilidad', LANGSESSION); ?>" value="<?php echo $frm["LabelCrearDisponibilidad"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoEscribirDireccion', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelEscribirDireccion" name="LabelEscribirDireccion" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoEscribirDireccion', LANGSESSION); ?>" value="<?php echo $frm["LabelEscribirDireccion"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoCalificacion', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="TextoCalificacion" name="TextoCalificacion" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoCalificacion', LANGSESSION); ?>" value="<?php echo $frm["TextoCalificacion"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoMisViajes', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisViajes" name="LabelMisViajes" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoMisViajes', LANGSESSION); ?>" value="<?php echo $frm["LabelMisViajes"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoDesdeClub', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelDesdeClub" name="LabelDesdeClub" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoDesdeClub', LANGSESSION); ?>" value="<?php echo $frm["LabelDesdeClub"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoHaciaClub', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelHaciaClub" name="LabelHaciaClub" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoHaciaClub', LANGSESSION); ?>" value="<?php echo $frm["LabelHaciaClub"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoLLamarAlConductor', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelLLamarConductor" name="LabelLLamarConductor" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoLLamarAlConductor', LANGSESSION); ?>" value="<?php echo $frm["LabelLLamarConductor"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoTelefono', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelTelefono" name="LabelTelefono" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoTelefono', LANGSESSION); ?>" value="<?php echo $frm["LabelTelefono"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoIntroduccionMapa', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelIntroduccionMapa" name="LabelIntroduccionMapa" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoIntroduccionMapa', LANGSESSION); ?>" value="<?php echo $frm["LabelIntroduccionMapa"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoDescripcion', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelDescripcion" name="LabelDescripcion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Recorrido', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoDescripcion', LANGSESSION); ?>" value="<?php echo $frm["LabelDescripcion"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoMisPublicaciones', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisPublicaciones" name="LabelMisPublicaciones" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoMisPublicaciones', LANGSESSION); ?>" value="<?php echo $frm["LabelMisPublicaciones"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoMisSolicitudes', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisSolicitudes" name="LabelMisSolicitudes" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoMisSolicitudes', LANGSESSION); ?>" value="<?php echo $frm["LabelMisSolicitudes"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoReutilizarRuta', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelReusarRuta" name="LabelReusarRuta" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoReutilizarRuta', LANGSESSION); ?>" value="<?php echo $frm["LabelReusarRuta"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoCancelarRuta', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelCancelarRuta" name="LabelCancelarRuta" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoCancelarRuta', LANGSESSION); ?>" value="<?php echo $frm["LabelCancelarRuta"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoCancelarSolicitud', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelCancelarSolicitud" name="LabelCancelarSolicitud" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoCancelarSolicitud', LANGSESSION); ?>" value="<?php echo $frm["LabelCancelarSolicitud"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoEliminarRuta', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelEliminarRuta" name="LabelEliminarRuta" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LabelEliminarRuta', LANGSESSION); ?>" value="<?php echo $frm["LabelEliminarRuta"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Latitud" name="Latitud" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?>" value="<?php echo $frm["Latitud"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Longitud" name="Longitud" placeholder="" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?>" value="<?php echo $frm["Longitud"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoMisViajes', LANGSESSION); ?> </label>
                                    <input name="IconoMisViajes" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'IconoMisViajes', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoMisViajes"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoMisViajes"] . "' height'300px' width='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoMisViajes]&campo=IconoMisViajes&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoDesdeClub', LANGSESSION); ?> </label>
                                    <input name="IconoDesdeClub" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'IconoDesdeClub', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoDesdeClub"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoDesdeClub"] . "'height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoDesdeClub]&campo=IconoDesdeClub&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'IconoHaciaClub', LANGSESSION); ?></label>
                                    <input name="IconoHaciaClub" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'IconoHaciaClub', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoHaciaClub"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoHaciaClub"] . "' height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoHaciaClub]&campo=IconoHaciaClub&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'IconoCrearDisponibilidad', LANGSESSION); ?> </label>
                                    <input name="IconoCrearDisponibilidad" id=IconoCrearDisponibilidad class="" title="<?= SIMUtil::get_traduccion('', '', 'IconoCrearDisponibilidad', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoCrearDisponibilidad"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoCrearDisponibilidad"] . "' height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoCrearDisponibilidad]&campo=IconoCrearDisponibilidad&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'IconoEliminarRuta', LANGSESSION); ?> </label>
                                    <input name="IconoEliminarRuta" id=IconoEliminarRuta class="" title="<?= SIMUtil::get_traduccion('', '', 'IconoEliminarRuta', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoCrearDisponibilidad"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoEliminarRuta"] . "' height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoEliminarRuta]&campo=IconoEliminarRuta&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteModelo', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteModelo"], 'PermiteModelo', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteColor', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteColor"], 'PermiteColor', "class='input'") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteReusar', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteReusar"], 'PermiteReusar', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteCalificar', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteCalificar"], 'PermiteCalificar', "class='input'") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteCancelar', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteCancelar"], 'PermiteCancelar', "class='input'") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteAgregarTelefono', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteAgregarTelefono"], 'PermiteAgregarTelefono', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteAgregarValor', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteAgregarValor"], 'PermiteAgregarValor', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteDescripcion', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteDescripcion"], 'PermiteDescripcion', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitePlaca', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermitePlaca"], 'PermitePlaca', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMarca', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteMarca"], 'PermiteMarca', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteEliminarRuta', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermiteEliminarRuta"], 'PermiteEliminarRuta', "class='input'") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activo"], 'Activo', "class='input'") ?>
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