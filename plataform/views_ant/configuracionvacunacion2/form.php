<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
<div class="widget-box transparent" id="recent-box">
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Nombre Configuración
                                </label>
                                <div class="col-sm-8">
                                    <input id=Nombre type=text size=25 name=Nombre class="input mandatory" title="Orden" value="<?= $frm["Nombre"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?></div>
                            </div>

                            <!--  <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Diagnostico para: </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>

                                </div>
                            </div> -->
                        </div>




                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-paper-plane green"></i>
                                Configuracion estas vacunado
                            </h3>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Mostrar boton de Esta vacunado?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarEstaVacunado"], 'MostrarEstaVacunado', "class='input mandatory'") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label esta vacunado
                                </label>

                                <div class="col-sm-8">
                                    <input id=LabelEstaVacunado type=text size=25 name=LabelEstaVacunado class="input" title="LabelEstaVacunado" value="<?= $frm["LabelEstaVacunado"] ?>">
                                </div>

                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label respuesta "SI" para Esta Vacunado
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelSi type=text size=25 name=LabelSi class="input" title="Label Si" value="<?= $frm["LabelSi"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label respuesta "NO" para Esta Vacunado
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelNo type=text size=25 name=LabelNo class="input" title="Label Si" value="<?= $frm["LabelNo"] ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label confirmación respuesta "SI"
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelSiConfirmacion type=text size=25 name=LabelSiConfirmacion class="input" title="Label Si" value="<?= $frm["LabelSiConfirmacion"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label confirmación respuesta "NO"
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelNoConfirmacion type=text size=25 name=LabelNoConfirmacion class="input" title="Label Si" value="<?= $frm["LabelNoConfirmacion"] ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label no quiero confirmacion
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelNoQuieroConfirmacion type=text size=25 name=LabelNoQuieroConfirmacion class="input" title="LabelNoQuieroConfirmacion" value="<?= $frm["LabelNoQuieroConfirmacion"] ?>">
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Permite no quiero vacunar
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteNoQuieroVacunar"], 'PermiteNoQuieroVacunar', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label no quiero vacunar
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelNoQuieroVacunar type=text size=25 name=LabelNoQuieroVacunar class="input" title="LabelNoQuieroVacunar" value="<?= $frm["LabelNoQuieroVacunar"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Solicitar pedir terminos y condiciones?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PreguntarTerminos"], 'PreguntarTerminos', "class='input mandatory'") ?>
                                </div>
                            </div>

                        </div>


                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-paper-plane green"></i>
                                Configuracion Citas
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Mostrar boton registrar cita vacuna?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarRegistrarCitaVacuna"], 'MostrarRegistrarCitaVacuna', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label boton registrar cita vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelRegistrarCitaVacuna type=text size=25 name=LabelRegistrarCitaVacuna class="input" title="LabelRegistrarCitaVacuna" value="<?= $frm["LabelRegistrarCitaVacuna"] ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta entidad que vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelEntidadVacuna type=text size=25 name=LabelEntidadVacuna class="input" title="Label Entidad Vacuna" value="<?= $frm["LabelEntidadVacuna"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label confirma registrar cita para vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelConfirmaRegistraCita type=text size=25 name=LabelConfirmaRegistraCita class="input" title="Label Fecha Cita Vacuna" value="<?= $frm["LabelConfirmaRegistraCita"] ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">
                            <!--  <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta primera cita para vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelFechaPrimeraCita type=text size=25 name=LabelFechaPrimeraCita class="input" title="Label Fecha Cita Vacuna" value="<?= $frm["LabelFechaPrimeraCita"] ?>">
                                </div>
                            </div> -->
                            <!-- <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta segunda cita para vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelFechaSegundaCita type=text size=25 name=LabelFechaSegundaCita class="input" title="Label Fecha Cita Vacuna" value="<?= $frm["LabelFechaSegundaCita"] ?>">
                                </div>
                            </div> -->
                        </div>

                        <div class="form-group first ">
                            <!--  <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta tercera cita para vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelFechaTerceraCita type=text size=25 name=LabelFechaTerceraCita class="input" title="Label Fecha Cita Vacuna" value="<?= $frm["LabelFechaTerceraCita"] ?>">
                                </div>
                            </div> -->
                        </div>

                        <div class="form-group first ">
                            <!--   <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Obligatorio entidad cita?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioEntidadCita"], 'ObligatorioEntidadCita', "class='input mandatory'") ?>
                                </div>
                            </div> -->

                            <!--  <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Obligatorio fecha primera cita?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFechaPrimeraCita"], 'ObligatorioFechaPrimeraCita', "class='input mandatory'") ?>
                                </div>
                            </div> -->
                        </div>

                        <div class="form-group first ">
                            <!--   <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Obligatorio fecha segunda cita?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFechaSegundaCita"], 'ObligatorioFechaSegundaCita', "class='input mandatory'") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Obligatorio fecha tercera cita?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioFechaTerceraCita"], 'ObligatorioFechaTerceraCita', "class='input mandatory'") ?>
                                </div>
                            </div> -->
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-paper-plane green"></i>
                                Configuracion Registro de vacuna
                            </h3>
                        </div>




                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Mostrar boton registrar vacuna?
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarRegistrarVacuna"], 'MostrarRegistrarVacuna', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label boton registrar vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelRegistrarVacuna type=text size=25 name=LabelRegistrarVacuna class="input" title="Label Registrar Cita Vacuna" value="<?= $frm["LabelRegistrarVacuna"] ?>">
                                </div>
                            </div>

                        </div>



                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta entidad que vacunó
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelEntidadVacuno type=text size=25 name=LabelEntidadVacuno class="input" title="Label Entidad Vacuno" value="<?= $frm["LabelEntidadVacuno"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta marca de vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelMarcaVacuna type=text size=25 name=LabelMarcaVacuna class="input" title="Label Marca Vacuna" value="<?= $frm["LabelMarcaVacuna"] ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label pregunta lugar de vacunación
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelLugarVacunacion type=text size=25 name=LabelLugarVacunacion class="input" title="Label Lugar Vacunacion" value="<?= $frm["LabelLugarVacunacion"] ?>">
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label confirma registrar vacuna
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelConfirmaRegistraVacuna type=text size=25 name=LabelConfirmaRegistraVacuna class="input" title="Label Fecha Cita Vacuna" value="<?= $frm["LabelConfirmaRegistraVacuna"] ?>">
                                </div>
                            </div>
                        </div>


                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-paper-plane green"></i>
                                Configuracion carnet del gobierno
                            </h3>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Mostrar Registrar Carne Gobierno
                                </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarRegistrarCarneGobierno"], 'MostrarRegistrarCarneGobierno', "class='input mandatory'") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Label Registrar Carne Gobierno
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelRegistrarCarneGobierno type=text size=25 name=LabelRegistrarCarneGobierno class="input" title="LabelRegistrarCarneGobierno" value="<?= $frm["LabelRegistrarCarneGobierno"] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Intro Registrar Carne Gobierno
                                </label>
                                <div class="col-sm-8">
                                    <input id=IntroRegistrarCarneGobierno type=text size=25 name=IntroRegistrarCarneGobierno class="input" title="IntroRegistrarCarneGobierno" value="<?= $frm["IntroRegistrarCarneGobierno"] ?>">
                                </div>
                            </div>
                        </div>



                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-paper-plane green"></i>
                                Otros datos
                            </h3>
                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Email Notificación
                                </label>
                                <div class="col-sm-8">
                                    <input id=EmailNotificacion type=text size=25 name=EmailNotificacion class="input" title="EmailNotificacion" value="<?= $frm["EmailNotificacion"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                    Tipo campo entidad?
                                </label>
                                <div class="col-sm-8">
                                    <select name="TipoCampoEntidad" id="TipoCampoEntidad" class="form-control">
                                        <option value=""></option>
                                        <option value="Texto" <?php if ($frm["TipoCampoEntidad"] == "Texto") echo "selected"; ?>>Texto</option>
                                        <option value="Seleccion" <?php if ($frm["TipoCampoEntidad"] == "Seleccion") echo "selected"; ?>>Seleccion</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                Texto Ver Certificado Digital
                                </label>
                                <div class="col-sm-8">
                                    <input id=TextoVerCertificadoDigital type=text size=25 name=TextoVerCertificadoDigital class="input" title="TextoVerCertificadoDigital" value="<?= $frm["TextoVerCertificadoDigital"] ?>">
                                </div>
                            </div>

                            <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Ejemplo Pdf Certificado </label>
										 <input name="ImagenEjemploPdfCertificado" id=file class="" title="ImagenEjemploPdfCertificado" type="file" size="25" style="font-size: 10px">
										<div class="col-sm-8">
											<? if (!empty($frm["ImagenEjemploPdfCertificado"])) {
												echo "<img src='".VACUNA_ROOT.$frm["ImagenEjemploPdfCertificado"]."' width=300 height=500 >";
												?>
                                              <a href="<? echo $script.".php?action=delfoto&foto=$frm[ImagenEjemploPdfCertificado]&campo=ImagenEjemploPdfCertificado&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											  <?
											}// END if
											?>
										</div>
								</div>

                           
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                Texto Ejemplo Pdf Certificado
                                </label>
                                <div class="col-sm-8">
                                    <input id=TextoEjemploPdfCertificado type=text size=25 name=TextoEjemploPdfCertificado class="input" title="TextoEjemploPdfCertificado" value="<?= $frm["TextoEjemploPdfCertificado"] ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                Texto para indicar que puede ver y diligenciar el carne del nucelo familiar
                                </label>
                                <div class="col-sm-8">
                                    <input id=LabelTextoCarnetFamilia type=text size=25 name=LabelTextoCarnetFamilia class="input" title="LabelTextoCarnetFamilia" value="<?= $frm["LabelTextoCarnetFamilia"] ?>">
                                </div>
                            </div>
                            
                        </div>




                        <div class="form-group first ">

                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">
                                Terminos y Condiciones
                            </label>
                            <div class="col-sm-12">
                                <?php
                                $oCuerpo = new FCKeditor("TerminosHtmlEstaVacunado");
                                $oCuerpo->BasePath = "js/fckeditor/";
                                $oCuerpo->Height = 400;
                                $oCuerpo->Value =  $frm["TerminosHtmlEstaVacunado"];
                                $oCuerpo->Create();
                                ?>
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