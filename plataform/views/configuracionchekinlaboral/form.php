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
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="Nombre"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="form-control" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>

                            </div>


                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Iconoingresoinactivo', LANGSESSION); ?> </label>
                                <input name="IconoIngresoInactivo" id=file class="" title="Icono Ingreso Inactivo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoIngresoInactivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoIngresoInactivo"] . "' width='400px' height='400px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoIngresoInactivo]&campo=IconoIngresoInactivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Iconoingresoactivo', LANGSESSION); ?> </label>
                                <input name="IconoIngresoActivo" id=file class="" title="Icono Ingreso Activo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoIngresoActivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoIngresoActivo"] . "' width='400px' height='400px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoIngresoActivo]&campo=IconoIngresoActivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Iconosalidainactivo', LANGSESSION); ?> </label>
                                <input name="IconoSalidaInactivo" id=file class="" title="Icono Salida Inactivo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoSalidaInactivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoSalidaInactivo"] . "' width='400px' height='400px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoSalidaInactivo]&campo=IconoSalidaInactivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'IconoSalidaactivo', LANGSESSION); ?> </label>
                                <input name="IconoSalidaActivo" id=file class="" title="Icono Salida Activo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoSalidaActivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoSalidaActivo"] . "' width='400px' height='400px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoSalidaActivo]&campo=IconoSalidaActivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TextoIntro"><?= SIMUtil::get_traduccion('', '', 'Textointro', LANGSESSION); ?></label>
                                <div class="col-sm-8"><input type="text" id="TextoIntro" name="TextoIntro" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textointro', LANGSESSION); ?>" class="form-control" title="Texto Intro" value="<?php echo $frm["TextoIntro"] ?>" required></div>

                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TextoConfirmacionIngreso"><?= SIMUtil::get_traduccion('', '', 'Textoconfirmacioningreso', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="TextoConfirmacionIngreso" name="TextoConfirmacionIngreso" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textoconfirmacioningreso', LANGSESSION); ?>" class="form-control" title="Longitud" value="<?php echo $frm["TextoConfirmacionIngreso"] ?>" required></div>


                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="ColorActivo"> <?= SIMUtil::get_traduccion('', '', 'Bloquearbotondeingreso/salidadependiendoelmovimiento(sidebehacerentradaeldesalidasebloquea)', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBloquearBotones"], 'PermiteBloquearBotones', "class='input mandatory'") ?>
                                </div>
                            </div>




                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="ColorActivo"> <?= SIMUtil::get_traduccion('', '', 'Coloractivo', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <input name="ColorActivo" type="color" value="<?php if (empty($frm["ColorActivo"])) {
                                                                                        echo "#FFFFFF";
                                                                                    } else {
                                                                                        echo $frm["ColorActivo"];
                                                                                    }    ?>" />
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="ColorActivo"> <?= SIMUtil::get_traduccion('', '', 'ColorInactivo', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <input name="ColorInactivo" type="color" value="<?php if (empty($frm["ColorInactivo"])) {
                                                                                        echo "#FFFFFF";
                                                                                    } else {
                                                                                        echo $frm["ColorInactivo"];
                                                                                    }    ?>" />
                                </div>
                            </div>



                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitirenviarpushdeRecordatorioparamarcaringreso', LANGSESSION); ?>? </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRecordatorioIngreso"], 'PermiteRecordatorioIngreso', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitirenviarpushdeRecordatorioparamarcarsalida', LANGSESSION); ?>? </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRecordatorioSalida"], 'PermiteRecordatorioSalida', "class='input mandatory'") ?>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="MensajeRecordatorioIngreso"><?= SIMUtil::get_traduccion('', '', 'MensajeRecordatorioIngreso', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="MensajeRecordatorioIngreso" name="MensajeRecordatorioIngreso" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'MensajeRecordatorioIngreso', LANGSESSION); ?>" value="<?php echo $frm["MensajeRecordatorioIngreso"] ?>" required></div>


                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TituloRecordatorioIngreso"><?= SIMUtil::get_traduccion('', '', 'TituloRecordatorioIngreso', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="TituloRecordatorioIngreso" name="TituloRecordatorioIngreso" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TituloRecordatorioIngreso', LANGSESSION); ?>" value="<?php echo $frm["TituloRecordatorioIngreso"] ?>" required></div>


                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="MensajeRecordatorioSalida"><?= SIMUtil::get_traduccion('', '', 'MensajeRecordatorioSalida', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="MensajeRecordatorioSalida" name="MensajeRecordatorioSalida" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'MensajeRecordatorioSalida', LANGSESSION); ?>" value="<?php echo $frm["MensajeRecordatorioSalida"] ?>" required></div>


                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TituloRecordatorioSalida"><?= SIMUtil::get_traduccion('', '', 'TituloRecordatorioSalida', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="TituloRecordatorioSalida" name="TituloRecordatorioSalida" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TituloRecordatorioSalida', LANGSESSION); ?>" value="<?php echo $frm["TituloRecordatorioSalida"] ?>" required></div>


                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteIngresarObservaciones', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteIngresarObservaciones"], 'PermiteIngresarObservaciones', "class='input mandatory'") ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TextoIngresarObservaciones"><?= SIMUtil::get_traduccion('', '', 'TextoIngresarObservaciones', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="LabelIngresarObservaciones" name="LabelIngresarObservaciones" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TextoIngresarObservaciones', LANGSESSION); ?>" value="<?php echo $frm["LabelIngresarObservaciones"] ?>" required></div>


                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMostrarHorasExtra', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMostrarHorasExtra"], 'PermiteMostrarHorasExtra', "class='input mandatory'") ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoHorasExtra', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="LabelHorasExtra" name="LabelHorasExtra" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'TextoHorasExtra', LANGSESSION); ?>" value="<?php echo $frm["LabelHorasExtra"] ?>" required></div>


                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteIngresarLugarTrabajo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteIngresarLugarTrabajo"], 'PermiteIngresarLugarTrabajo', "class='input mandatory'") ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TextoIngresarLugarTrabajo', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="LabelIngresarLugarTrabajo" name="LabelIngresarLugarTrabajo" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoIngresarLugarTrabajo', LANGSESSION); ?>" value="<?php echo $frm["LabelIngresarLugarTrabajo"] ?>" required></div>

                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObligatorioIngresarLugarTrabajo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioIngresarLugarTrabajo"], 'ObligatorioIngresarLugarTrabajo', "class='input mandatory'") ?>
                            </div>
                        </div>


                        <div class="form-group first ">

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="MinutosAntesRecordatorio"><?= SIMUtil::get_traduccion('', '', 'Minutosantesparaenviarpush(porejemplosielhorariodeingresoes8amseenviaalas7:45)', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <input type="text" id="MinutosAntesRecordatorio" name="MinutosAntesRecordatorio" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Minutosantesparaenviarpush(porejemplosielhorariodeingresoes8amseenviaalas7:45)', LANGSESSION); ?>" value="<?php echo $frm["MinutosAntesRecordatorio"] ?>" required></div>


                            </div>




                        </div>


                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-circle green"></i>
                                Checkin Despues de turno
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Registrar Turno Extra </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRegistrarTurnoExtra"], 'PermiteRegistrarTurnoExtra', "class='input mandatory'") ?>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Icono Ingreso Turno Extra Inactivo </label>
                                <input name="IconoIngresoTurnoExtraInactivo" id=file class="" title="Icono Ingreso Turno Extra Inactivo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoIngresoTurnoExtraInactivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoIngresoTurnoExtraInactivo"] . "' width='300px' height='300px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoIngresoTurnoExtraInactivo]&campo=IconoIngresoTurnoExtraInactivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Ingreso Extra Turno Activo </label>
                                <input name="IconoIngresoExtraTurnoActivo" id=file class="" title="Icono Ingreso Extra Turno Activo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoIngresoExtraTurnoActivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoIngresoExtraTurnoActivo"] . "' width='300px' height='300px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoIngresoExtraTurnoActivo]&campo=IconoIngresoExtraTurnoActivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Icono Salida Turno Extra Activo </label>
                                <input name="IconoSalidaTurnoExtraActivo" id=file class="" title="Icono Salida Turno Extra Activo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoSalidaTurnoExtraActivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoSalidaTurnoExtraActivo"] . "' width='300px' height='300px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoSalidaTurnoExtraActivo]&campo=IconoSalidaTurnoExtraActivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Salida Turno Extra Inactivo </label>
                                <input name="IconoSalidaTurnoExtraInactivo" id=file class="" title="Icono Salida TurnoExtra Inactivo" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["IconoSalidaTurnoExtraInactivo"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["IconoSalidaTurnoExtraInactivo"] . "' width='300px' height='300px'  >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoSalidaTurnoExtraInactivo]&campo=IconoSalidaTurnoExtraInactivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">
                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1">Texto Confirmacion Turno Extra Ingreso</label>
                                <div class="col-sm-8"> <input type="text" id="TextoConfirmacionTurnoExtraIngreso" name="TextoConfirmacionTurnoExtraIngreso" class="form-control" title="Texto Confirmacion Turno Extra Ingreso" value="<?php echo $frm["TextoConfirmacionTurnoExtraIngreso"] ?>"></div>


                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="form-field-1">Texto Confirmacion Turno Extra Salida</label>
                                <div class="col-sm-8"> <input type="text" id="TextoConfirmacionTurnoExtraoSalida" name="TextoConfirmacionTurnoExtraoSalida" class="form-control" title="Texto Confirmacion Turno Extra Salida" value="<?php echo $frm["TextoConfirmacionTurnoExtraoSalida"] ?>"></div>


                            </div>
                        </div>



                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
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