<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?> <div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Reconocimiento </label>
                                <input name="ImagenHuella" id=file class="" title="ImagenHuella" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm["ImagenHuella"])) {
                                        echo "<img src='" . CLUB_ROOT . $frm["ImagenHuella"] . "' >";
                                    ?>
                                        <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ImagenHuella]&campo=ImagenHuella&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>
                            <?php if (SIMUser::get("club") != 188) { ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Cultura de seguridad y salud en el trabajo </label>
                                    <input name="ImagenSeguridad" id=file class="" title="ImagenSeguridad" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["ImagenSeguridad"])) {
                                            echo "<img src='" . CLUB_ROOT . $frm["ImagenSeguridad"] . "' >";
                                        ?>
                                            <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ImagenSeguridad]&campo=ImagenSeguridad&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion Programa Reconocimiento </label>
                                <div class="col-sm-8"> <?php
                                                        $oCuerpo = new FCKeditor("DescripcionHuella");
                                                        $oCuerpo->BasePath = "js/fckeditor/";
                                                        $oCuerpo->Height = 400;
                                                        //$oCuerpo->EnterMode = "p";
                                                        $oCuerpo->Value =  $frm["DescripcionHuella"];
                                                        $oCuerpo->Create();
                                                        ?> </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion Programa Cultura de seguridad </label>
                                <div class="col-sm-8"> <?php
                                                        $oCuerpo = new FCKeditor("DescripcionSeguridad");
                                                        $oCuerpo->BasePath = "js/fckeditor/";
                                                        $oCuerpo->Height = 400;
                                                        //$oCuerpo->EnterMode = "p";
                                                        $oCuerpo->Value =  $frm["DescripcionSeguridad"];
                                                        $oCuerpo->Create();
                                                        ?> </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo Modulo Reconocimiento </label>
                                <div class="col-sm-8">
                                    <input type="text" id="NombreHuella" name="NombreHuella" placeholder="Nombre Huella" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["NombreHuella"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo Modulo Seguridad </label>
                                <div class="col-sm-8">
                                    <input type="text" id="NombreSeguridad" name="NombreSeguridad" placeholder="Nombre Seguridad" class="col-xs-12 mandatory" title="NombreSeguridad" value="<?php echo $frm["NombreSeguridad"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Reconocer </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelReconocer" name="LabelReconocer" placeholder="Label Reconocer" class="col-xs-12 mandatory" title="Label Reconocer" value="<?php echo $frm["LabelReconocer"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Mis Reconocimientos </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelMisReconocimientos" name="LabelMisReconocimientos" placeholder="Label Mis Reconocimientos" class="col-xs-12 mandatory" title="Label Mis Reconocimientos" value="<?php echo $frm["LabelMisReconocimientos"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Buscador Compañero </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBuscadorCompanero" name="LabelBuscadorCompanero" placeholder="Label LabelBuscador Compañero" class="col-xs-12 mandatory" title="Label Buscador" value="<?php echo $frm["LabelBuscadorCompanero"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Reconocer individual </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelReconocerIndividual" name="LabelReconocerIndividual" placeholder="Label Individual" class="col-xs-12 mandatory" title="Label Individual" value="<?php echo $frm["LabelReconocerIndividual"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Reconocer Grupal </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelReconocerGrupal" name="LabelReconocerGrupal" placeholder="Label Reconocer Grupal" class="col-xs-12 mandatory" title="Label Reconocer Grupal" value="<?php echo $frm["LabelReconocerGrupal"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton historial reconocimiento </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBotonHistorial" name="LabelBotonHistorial" placeholder="Label Boton historial" class="col-xs-12 mandatory" title="Label Boton Historial" value="<?php echo $frm["LabelBotonHistorial"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Reconocer Seguridad </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelReconocerSeguridad" name="LabelReconocerSeguridad" placeholder="Label Reconocer Seguridad" class="col-xs-12 mandatory" title="Label Reconocer Seguridad" value="<?php echo $frm["LabelReconocerSeguridad"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton pantalla principal que va a los detalles </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBotonDetalle" name="LabelBotonDetalle" placeholder="Texto boton pantalla principal que va a los detalles" class="col-xs-12 mandatory" title="Texto boton pantalla principal que va a los detalles" value="<?php echo $frm["LabelBotonDetalle"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton enviar el reconocimiento</label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelBotonEnviarReconocimiento" name="LabelBotonEnviarReconocimiento" placeholder="Texto boton enviar el reconocimiento" class="col-xs-12 mandatory" title="Texto boton enviar el reconocimiento" value="<?php echo $frm["LabelBotonEnviarReconocimiento"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto pregunta por defecto (¿Qué conductas te inspiraron y dejaron huella?) </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelRazonReconocer" name="LabelRazonReconocer" class="col-xs-12 mandatory" title="Texto pregunta por defecto" value="<?php echo $frm["LabelRazonReconocer"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ocultar Agregar Grupos Por Persona </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarAgregarGruposPorPersona"], 'OcultarAgregarGruposPorPersona', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Agregar Grupo Por Persona </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelAgregarGrupoPorPersona" name="LabelAgregarGrupoPorPersona" class="col-xs-12 mandatory" title="Texto Agregar Grupo Por Persona" value="<?php echo $frm["LabelAgregarGrupoPorPersona"]; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Agregar Grupos Previos </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAgregarGruposPrevios"], 'PermiteAgregarGruposPrevios', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Agregar Grupos Previos </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelAgregarGruposPrevios" name="LabelAgregarGruposPrevios" class="col-xs-12 mandatory" title="Texto Agregar Grupos Previos" value="<?php echo $frm["LabelAgregarGruposPrevios"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Filtro Grupal </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteFiltroGrupal"], 'PermiteFiltroGrupal', "class='input mandatory'") ?>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Filtro Grupal </label>
                                <div class="col-sm-8">
                                    <input type="text" id="LabelFiltroGrupal" name="LabelFiltroGrupal" class="col-xs-12 mandatory" title="Texto Filtro Grupal" value="<?php echo $frm["LabelFiltroGrupal"]; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite enviar correo a la persona que se le hace el reconocimiento? </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEnviarCorreoPersonaReconocida"], 'PermiteEnviarCorreoPersonaReconocida', "class='input mandatory'") ?>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> </button>
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