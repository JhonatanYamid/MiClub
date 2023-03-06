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

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Aplicaparaelmódulo', LANGSESSION); ?>: </label>

                                    <div class="col-sm-8">
                                        <select name="IDModulo" id="IDModulo">
                                            <option value=""><?= SIMUtil::get_traduccion('', '', 'Seleccione', LANGSESSION); ?></option>
                                            <?php
                                            $sql_mod = "SELECT M.IDModulo, CM.TituloLateral,M.Nombre
																								From Modulo M,ClubModulo CM
																								Where M.IDModulo = CM.IDModulo and  IDClub = '" . SIMUser::get("club") . "' and Activo='S'
																								And M.Tipo = 'Noticias'";
                                            $r_mod = $dbo->query($sql_mod);
                                            while ($row_mod = $dbo->fetchArray($r_mod)) { ?>
                                                <option value="<?php echo $row_mod["IDModulo"]; ?>" <?php if ($row_mod["IDModulo"] == $frm["IDModulo"]) echo "selected"; ?>><?php if (!empty($row_mod["TituloLateral"])) echo $row_mod["TituloLateral"];
                                                                                                                                                                            else echo $row_mod["Nombre"];  ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Nombre"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="EmailNotificacionContenido"> <?= SIMUtil::get_traduccion('', '', 'EmailNotificacionContenido', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="email" id="EmailNotificacionContenido" name="EmailNotificacionContenido" placeholder="" class="form-control" title="EmailNotificacionContenido" value="<?php echo $frm["EmailNotificacionContenido"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CorreoNotificacionComentarioNoticia"><?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionComentarioNoticia', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="CorreoNotificacionComentarioNoticia" name="CorreoNotificacionComentarioNoticia" placeholder="" class="form-control" title="CorreoNotificacionComentarioNoticia" value="<?php echo $frm["CorreoNotificacionComentarioNoticia"] ?>" required></div>


                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-5 control-label no-padding-right" for="PermiteLikeNoticia1"> <?= SIMUtil::get_traduccion('', '', 'PermiteMeGustaNoticia', LANGSESSION); ?> </label>

                                    <div class="col-sm-7">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteLikeNoticia1"], "PermiteLikeNoticia1", "title=\"PermiteLikeNoticia1\"") ?>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-5 control-label no-padding-right" for="PermiteComentarioNoticia1"> <?= SIMUtil::get_traduccion('', '', 'PermiteComentarioNoticia', LANGSESSION); ?> </label>

                                    <div class="col-sm-7">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteComentarioNoticia1"], "PermiteComentarioNoticia1", "title=\"PermiteComentarioNoticia1\"") ?>
                                    </div>

                                </div>

                            </div>






                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-5 control-label no-padding-right" for="PermiteIconoComentariosNoticias"> <?= SIMUtil::get_traduccion('', '', 'PermiteIconoComentariosNoticias', LANGSESSION); ?></label>

                                    <div class="col-sm-7">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteIconoComentariosNoticias"], "PermiteIconoComentariosNoticias", "title=\"PermiteIconoComentariosNoticias\"") ?>
                                    </div>

                                </div>



                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoMeGustaNoticias', LANGSESSION); ?> </label>
                                    <input name="IconoLikeNoticias" id=IconoLikeNoticias class="" title="IconoLikeNoticias" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoLikeNoticias"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["IconoLikeNoticias"] . "'  width=300 height=300>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoLikeNoticias]&campo=IconoLikeNoticias&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group first">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'IconoNoMeGustaNoticias', LANGSESSION); ?> </label>
                                    <input name="IconoUnLikeNoticias" id=IconoUnLikeNoticias class="" title="IconoLikeNoticias" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoUnLikeNoticias"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["IconoUnLikeNoticias"] . "'  width=300 height=300 >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoUnLikeNoticias]&campo=IconoUnLikeNoticias&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ImagenComentarios', LANGSESSION); ?> </label>
                                    <input name="ImagenComentarios" id=ImagenComentarios class="" title="ImagenComentarios" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["ImagenComentarios"])) {
                                            echo "<img src='" . BANNERAPP_ROOT . $frm["ImagenComentarios"] . "'  width=300 height=300>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[ImagenComentarios]&campo=ImagenComentarios&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TipoImagenNoticias', LANGSESSION); ?> </label>
                                    <div class="select col-sm-8">
                                        <select name="TipoImagenNoticias" id="TipoImagenNoticias" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Expandida" <?php if ($frm["TipoImagenNoticias"] == "Expandida") echo "selected"; ?>>Expandida</option>
                                            <option value="Ajustada" <?php if ($frm["TipoImagenNoticias"] == "Ajustada") echo "selected"; ?>>Ajustada</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Activo"><?= SIMUtil::get_traduccion('', '', 'MostrarFechaPublicación', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formRadioGroup(SIMResources::$sinoNum, $frm["MostrarFecha"], "MostrarFecha", "title=\"MostrarFecha\"") ?>
                                    </div>

                                </div>




                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Activo"><?= SIMUtil::get_traduccion('', '', 'PermitePublicarComentariosAutomaticamente', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermitePublicarComentariosAutomaticamente"], "PermitePublicarComentariosAutomaticamente", "title=\"PermitePublicarComentariosAutomaticamente\"") ?>
                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Activo"><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Activo"], "Activo", "title=\"Activo\"") ?>
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