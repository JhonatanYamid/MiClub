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

                            <?php if ($_GET["action"] == "add") { ?>

                                <div class="form-group first ">

                                    <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?>" value="" required=false>
                                            <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">

                                        </div>
                                    </div>

                                </div>
                            <?php  } ?>

                            <div class="form-group first ">
                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Nombre"><?= SIMUtil::get_traduccion('', '', 'NombreDelCanino', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input name="Nombre" id="Nombre" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'NombreDelCanino', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NombreDelCanino', LANGSESSION); ?>" value="<?php echo $frm["Nombre"] ?>" required /></div>
                                    </div>



                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Tipo"><?= SIMUtil::get_traduccion('', '', 'Tamaño', LANGSESSION); ?></label>
                                        <div class="col-sm-8">
                                            <select class="form-control mandatory" name="Tipo" id="Tipo" title="<?= SIMUtil::get_traduccion('', '', 'Tamaño', LANGSESSION); ?>" class="mandatory">
                                                <option value=""><?= SIMUtil::get_traduccion('', '', 'Tamaño', LANGSESSION); ?></option>
                                                <option value="Grande" <?php if ($frm["Tipo"] == "Grande") echo "selected"; ?>>Grande</option>
                                                <option value="Mediano" <?php if ($frm["Tipo"] == "Mediano") echo "selected"; ?>>Mediano</option>
                                                <option value="Pequeño" <?php if ($frm["Tipo"] == "Pequeño") echo "selected"; ?>>Pequeño</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Raza"><?= SIMUtil::get_traduccion('', '', 'Raza', LANGSESSION); ?></label>
                                        <div class="col-sm-8">
                                            <div class="col-sm-8"> <input name="Raza" id="Raza" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'Raza', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Raza', LANGSESSION); ?>" value="<?php echo $frm["Raza"] ?>" required /></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Celular"><?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?></label>
                                        <div class="col-sm-8"> <input name="Celular" id="Celular" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?>" value="<?php echo $frm["Celular"] ?>" required /></div>
                                    </div>

                                </div>

                                <div class="form-group first ">

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="FechaDeIngreso"><?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input name="FechaDeIngreso" id="FechaDeIngreso" type="date" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?>" value="<?php echo $frm["FechaDeIngreso"] ?>" required /></div>
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="FechaFin"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?></label>
                                        <div class="col-sm-8"> <input name="FechaFin" id="FechaFin" type="date" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>" required /></div>
                                    </div>

                                </div>




                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Cedula"><?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input name="Cedula" id="Cedula" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?>" value="<?php echo $frm["Cedula"] ?>" required /></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="Descripcion"><?= SIMUtil::get_traduccion('', '', 'DescripcionDeLaMascota', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input name="Descripcion" id="Descripcion" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'DescripcionDeLaMascota', LANGSESSION); ?>" class="form-control mandatory" value="<?php echo $frm["Descripcion"] ?>" title="<?= SIMUtil::get_traduccion('', '', 'DescripcionDeLaMascota', LANGSESSION); ?>" /></div>
                                    </div>
                                </div>

                                <div class="form-group first ">

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="AQuienPertenece"><?= SIMUtil::get_traduccion('', '', 'AQuienPertenece', LANGSESSION); ?></label>
                                        <div class="col-sm-8">
                                            <select class="form-control mandatory" name="AQuienPertenece" id="AQuienPertenece" title="<?= SIMUtil::get_traduccion('', '', 'AQuienPertenece', LANGSESSION); ?>">
                                                <option value=""><?= SIMUtil::get_traduccion('', '', 'AQuienPertenece', LANGSESSION); ?></option>
                                                <option value="Socio" <?php if ($frm["AQuienPertenece"] == "Socio") echo "selected"; ?>>Socio</option>
                                                <option value="Invitado" <?php if ($frm["AQuienPertenece"] == "Invitado") echo "selected"; ?>>Invitado</option>

                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="NombreSocioInvitado"><?= SIMUtil::get_traduccion('', '', 'Nombre(SociooInvitado)', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input name="NombreSocioInvitado" id="NombreSocioInvitado" type="text" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombre(SociooInvitado)', LANGSESSION); ?>" class="form-control mandatory" value="<?php echo $frm["NombreSocioInvitado"] ?>" title="<?= SIMUtil::get_traduccion('', '', 'Nombre(SociooInvitado)', LANGSESSION); ?>" /></div>
                                    </div>
                                </div>

                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>
                                        <div class="col-sm-8">
                                            <input name="Foto" id="Foto" class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                            <div class="col-sm-8">
                                                <? if (!empty($frm["Foto"])) {
                                                    echo "<img src='" . BANNERAPP_ROOT . $frm["Foto"] . "' width=300 height=300>";
                                                ?>

                                                <?
                                                } // END if
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CarnéVacunación', LANGSESSION); ?> </label>
                                        <div class="col-sm-8">
                                            <input name="FotoVacuna" id="FotoVacuna" class="" title="<?= SIMUtil::get_traduccion('', '', 'CarnéVacunación', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                            <div class="col-sm-8">
                                                <? if (!empty($frm["FotoVacuna"])) {
                                                    echo "<img src='" . BANNERAPP_ROOT . $frm["FotoVacuna"] . "' width=300 height=300 >";
                                                ?>
                                                <?
                                                } // END if
                                                ?>
                                            </div>
                                        </div>
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