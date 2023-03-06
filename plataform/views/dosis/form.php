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

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="NumeroDosis">Numero Dosis</label>
                                    <div class="col-sm-8"><input type="number" id="NumeroDosis" name="NumeroDosis" placeholder="" class="form-control" title="NumeroDosis" value="<?php echo $frm["NumeroDosis"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="NombreDosis">Nombre Dosis</label>
                                    <div class="col-sm-8"><input type="text" id="NombreDosis" name="NombreDosis" placeholder="" class="form-control" title="NombreDosis" value="<?php echo $frm["NombreDosis"] ?>" required></div>

                                </div>



                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelFechaCita">Label Fecha Cita</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelFechaCita" name="LabelFechaCita" placeholder="" class="form-control" title="Casa" value="<?php echo $frm["LabelFechaCita"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelFechaDosis">Label Fecha Dosis</label>
                                    <div class="col-sm-8"><input type="text" id="LabelFechaDosis" name="LabelFechaDosis" placeholder="" class="form-control" title="LabelFechaDosis" value="<?php echo $frm["LabelFechaDosis"] ?>" required></div>


                                </div>

                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelCertificadoVacuna">Label Certificado Vacuna</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelCertificadoVacuna" name="LabelCertificadoVacuna" placeholder="" class="form-control" title="LabelCertificadoVacuna" value="<?php echo $frm["LabelCertificadoVacuna"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Entidad Cita </label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioEntidadCita"], 'ObligatorioEntidadCita', "class='input mandatory'") ?></div>
                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Entidad Vacuna</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioEntidadVacuna"], 'ObligatorioEntidadVacuna', "class='input mandatory'") ?></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Entidad Dosis</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarEntidadDosis"], 'OcultarEntidadDosis', "class='input mandatory'") ?></div>
                                </div>


                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Marca Vacuna </label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioMarcaVacuna"], 'ObligatorioMarcaVacuna', "class='input mandatory'") ?></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Marca Dosis</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarMarcaDosis"], 'OcultarMarcaDosis', "class='input mandatory'") ?></div>
                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Lugar Vacuna</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioLugarVacuna"], 'ObligatorioLugarVacuna', "class='input mandatory'") ?></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Lugar Dosis</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarLugarDosis"], 'OcultarLugarDosis', "class='input mandatory'") ?></div>
                                </div>


                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Fecha Vacuna</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFechaVacuna"], 'ObligatorioFechaVacuna', "class='input mandatory'") ?></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ocultar Fecha Dosis</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarFechaDosis"], 'OcultarFechaDosis', "class='input mandatory'") ?></div>
                                </div>
                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Obligatorio Certificado Vacuna</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioCertificadoVacuna"], 'ObligatorioCertificadoVacuna', "class='input mandatory'") ?></div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ocultar Certificado Dosis</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarCertificadoDosis"], 'OcultarCertificadoDosis', "class='input mandatory'") ?></div>
                                </div>


                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Fecha Cita</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFechaCita"], 'ObligatorioFechaCita', "class='input mandatory'") ?></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activa</label>

                                    <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activa"], 'Activa', "class='input mandatory'") ?></div>
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
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
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