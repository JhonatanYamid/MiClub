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
                    <!-- PAGE CONTENT BEGINS -->

                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NombreConfiguración', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="NombreConfig" name="NombreConfig" placeholder="<?= SIMUtil::get_traduccion('', '', 'NombreConfiguración', LANGSESSION); ?>" class="col-xs-12" title="NombreConfig" value="<?php echo $frm["NombreConfig"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajeconfirmaciónalpostularsealaoferta', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Mensaje" name="Mensaje" placeholder="<?= SIMUtil::get_traduccion('', '', 'Mensaje', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Mensajeconfirmaciónalpostularsealaoferta', LANGSESSION); ?>" value="<?php echo $frm["Mensaje"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textobotonmisofertaslaborales', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelTabMisOfertasLaborales" name="LabelTabMisOfertasLaborales" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textobotonmisofertaslaborales', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonmisofertaslaborales', LANGSESSION); ?>" value="<?php echo $frm["LabelTabMisOfertasLaborales"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textobotonofertaslaborales', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelTabOfertasLaborales" name="LabelTabOfertasLaborales" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textobotonofertaslaborales', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonofertaslaborales', LANGSESSION); ?>" value="<?php echo $frm["LabelTabOfertasLaborales"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textobotonmisaplicaciones', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelTabMisAplicaciones" name="LabelTabMisAplicaciones" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textobotonmisaplicaciones', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonmisaplicaciones', LANGSESSION); ?>" value="<?php echo $frm["LabelTabMisAplicaciones"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelAplicarParaMi" name="LabelAplicarParaMi" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparami', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparami', LANGSESSION); ?>" value="<?php echo $frm["LabelAplicarParaMi"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparauntercero', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelAplicarParaTercero" name="LabelAplicarParaTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparauntercero', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonaplicarparauntercero', LANGSESSION); ?>" value="<?php echo $frm["LabelAplicarParaTercero"]; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Encabezadoseccionaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="HeaderAplicarParaMiTexto" name="HeaderAplicarParaMiTexto" cols="10" rows="5" class="col-xs-12" title="" <?php if ($_GET["action"] != "add") echo ""; ?>><?php echo $frm["HeaderAplicarParaMiTexto"]; ?></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Piedepaginaseccionaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="FooterAplicarParaMiTexto" name="FooterAplicarParaMiTexto" cols="10" rows="5" class="col-xs-12" title="" <?php if ($_GET["action"] != "add") echo ""; ?>><?php echo $frm["FooterAplicarParaMiTexto"]; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Encabezadoseccionaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="HeaderAplicarParaTerceroTexto" name="HeaderAplicarParaTerceroTexto" cols="10" rows="5" class="col-xs-12" title="" <?php if ($_GET["action"] != "add") echo ""; ?>><?php echo $frm["HeaderAplicarParaTerceroTexto"]; ?></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Piedepaginaseccionaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="FooterAplicarParaMiTexto" name="FooterAplicarParaMiTexto" cols="10" rows="5" class="col-xs-12" title="" <?php if ($_GET["action"] != "add") echo ""; ?>><?php echo $frm["FooterAplicarParaMiTexto"]; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitiraplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteAplicarParaMi"], "PermiteAplicarParaMi", "title=\"PermiteAplicarParaMi\"") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitiraplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteAplicarParaTercero"], "PermiteAplicarParaTercero", "title=\"PermiteAplicarParaTercero\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarbotonmisofertas', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarTabMisOfertasLaborales"], "MostrarTabMisOfertasLaborales", "title=\"PermiteAplicarParaMi\"") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirpublicaroferta', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermitePublicarOferta"], "PermitePublicarOferta", "title=\"PermitePublicarOferta\"") ?>
                                </div>

                            </div>

                        </div>



                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcampotelefonoaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarTelefonoAplicarParaMi"], "MostrarTelefonoAplicarParaMi", "title=\"MostrarTelefonoAplicarParaMi\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obilgatoriotelefonoparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObilgatorioTelefonoParaMi"], "ObilgatorioTelefonoParaMi", "title=\"ObilgatorioTelefonoParaMi\"") ?>
                                </div>
                            </div>



                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcampoemailaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarEmailAplicarParaMi"], "MostrarEmailAplicarParaMi", "title=\"MostrarEmailAplicarParaMi\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatorioemailparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioEmailParaMi"], "ObligatorioEmailParaMi", "title=\"ObligatorioEmailParaMi\"") ?>
                                </div>
                            </div>

                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarcampohojadevidaaplicarparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarHojaVidaAplicarParaMi"], "MostrarHojaVidaAplicarParaMi", "title=\"MostrarHojaVidaAplicarParaMi\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriohojavidaparami', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioHojaVidaParaMi"], "ObligatorioHojaVidaParaMi", "title=\"ObligatorioHojaVidaParaMi\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcamponombreaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarNombreAplicarTercero"], "MostrarNombreAplicarTercero", "title=\"MostrarNombreAplicarTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatorionombretercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioNombreTercero"], "ObligatorioNombreTercero", "title=\"ObligatorioNombreTercero\"") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcampotelefonoaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarTelefonoAplicarTercero"], "MostrarTelefonoAplicarTercero", "title=\"MostrarTelefonoAplicarTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriotelefonotercero', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioTelefonoTercero"], "ObligatorioTelefonoTercero", "title=\"ObligatorioTelefonoTercero\"") ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarcampoemailaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarEmailAplicarTercero"], "MostrarEmailAplicarTercero", "title=\"MostrarEmailAplicarTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatorioemailtercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioEmailTercero"], "ObligatorioEmailTercero", "title=\"ObligatorioEmailTercero\"") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcampohojadevidaaplicarparatercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarHojaVidaAplicarTercero"], "MostrarHojaVidaAplicarTercero", "title=\"MostrarHojaVidaAplicarTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriohojadevidatercero', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioHojaVidaTercero"], "ObligatorioHojaVidaTercero", "title=\"ObligatorioHojaVidaTercero\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarcargoactualparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarCargoActualParaMi"], "MostrarCargoActualParaMi", "title=\"Activo\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriocargoactualparami', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioCargoActualParaMi"], "ObligatorioCargoActualParaMi", "title=\"ObligatorioCargoActualParaMi\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarcargoactualtercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarCargoActualTercero"], "MostrarCargoActualTercero", "title=\"MostrarCargoActualTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriocargoactualtercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioCargoActualTercero"], "ObligatorioCargoActualTercero", "title=\"ObligatorioCargoActualTercero\"") ?>
                                </div>
                            </div>


                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarRazonPostulacionParaMi', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarRazonPostulacionParaMi"], "MostrarRazonPostulacionParaMi", "title=\"MostrarRazonPostulacionParaMi\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriorazonpostulacionparami', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioRazonPostulacionParaMi"], "ObligatorioRazonPostulacionParaMi", "title=\"ObligatorioRazonPostulacionParaMi\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarrazonpostulaciontercero', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarRazonPostulacionTercero"], "MostrarRazonPostulacionTercero", "title=\"MostrarRazonPostulacionTercero\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatoriorazonpostulaciontercero', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioRazonPostulacionTercero"], "ObligatorioRazonPostulacionTercero", "title=\"ObligatorioRazonPostulacionTercero\"") ?>
                                </div>
                            </div>


                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Configuraciónactiva', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Activo"], "Activo", "title=\"Activo\"") ?>
                                </div>
                            </div>

                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="Version" id="ID" value="1" />
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
                    </form>
                </div>
            </div><!-- /.widget-main -->
        </div><!-- /.widget-body -->
    </div><!-- /.widget-box -->
    <?
    include("cmp/footer_scripts.php");
    ?>