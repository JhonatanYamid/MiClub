<div id="ConfiguracionNoticias">
    <form name="frmproNoticia" id="frmproNoticia" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top"> <?php
                                                $action = "ModificaConfiguracionNoticias";
                                                $_GET[IDConfiguracionNoticias] = $dbo->getFields("ConfiguracionNoticias", "IDConfiguracionNoticias", "IDClub = '" . SIMNet::reqInt("id") . "'");

                                                if ($_GET[IDConfiguracionNoticias]) {
                                                    $EditConfiguracionNoticias = $dbo->fetchAll("ConfiguracionNoticias", " IDConfiguracionNoticias = '" . $_GET[IDConfiguracionNoticias] . "' ", "array");
                                                    $action = "ModificaConfiguracionNoticias";
                                                ?> <input type="hidden" name="IDConfiguracionNoticias" id="IDConfiguracionNoticias" value="<?php echo $EditConfiguracionNoticias[IDConfiguracionNoticias] ?>" /> <?php
                                                                                                                                                                                                                }
                                                                                                                                                                                                                    ?> <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                    <!--
            <tr>
              <td>Padre</td>
              <td>
    <select name="IDPadre" id="IDPadre">
                <option value="">[Seleccione]</option>
    <?php
    $qry_padre = $dbo->all("ConfiguracionNoticias", " IDClub = '" . $EditConfiguracionNoticias[$key] . "'");
    while ($r_pade = $dbo->object($qry_padre)) : ?>
        <option value="<?php echo $r_pade->IDConfiguracionNoticias ?>" <?php if ($r_pade->IDConfiguracionNoticias == $EditConfiguracionNoticias[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre ?></option>
                  <?php
                endwhile;
                    ?>
              </select>
              </td>
            </tr>
            -->
                                    <tr>
                                        <td colspan="2">
                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <th class="title" colspan="14" bgcolor="#CCE5C8">Configuracion Modulo Noticias 1</th>
                                                </tr>
                                                <tr>
                                                    <th>Permitir like en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteLikeNoticia1"], 'PermiteLikeNoticia1', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir comentarios en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteComentarioNoticia1"], 'PermiteComentarioNoticia1', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir Icono comentarios?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteIconoComentariosNoticias"], 'PermiteIconoComentariosNoticias', "class='input '") ?>
                                                    </th>
                                                    <th>Publicar comentarios automaticamente</th>
                                                    <th style="width: 100px;">
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PublicarComentariosAutomaticamente"], 'PublicarComentariosAutomaticamente', "class='input '") ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="title" colspan="14" bgcolor="#CCE5C8">Configuracion Modulo Noticias 2</th>
                                                </tr>
                                                <tr>
                                                    <th>Permitir like en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteLikeNoticia2"], 'PermiteLikeNoticia2', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir comentarios en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteComentarioNoticia2"], 'PermiteComentarioNoticia2', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir Icono comentarios?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteIconoComentariosNoticias2"], 'PermiteIconoComentariosNoticias2', "class='input '") ?>
                                                    </th>
                                                    <th>Publicar comentarios automaticamente</th>
                                                    <th style="width: 100px;">
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PublicarComentariosAutomaticamente2"], 'PublicarComentariosAutomaticamente2', "class='input '") ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="title" colspan="14" bgcolor="#CCE5C8">Configuracion Modulo Noticias 3</th>
                                                </tr>
                                                <tr>
                                                    <th>Permitir like en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteLikeNoticia3"], 'PermiteLikeNoticia3', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir comentarios en noticias (modulo 1)?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteComentarioNoticia3"], 'PermiteComentarioNoticia3', "class='input '") ?>
                                                    </th>
                                                    <th>Permitir Icono comentarios?</th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PermiteIconoComentariosNoticias3"], 'PermiteIconoComentariosNoticias3', "class='input '") ?>
                                                    </th>

                                                    <th>Publicar comentarios automaticamente </th>
                                                    <th style="width: 100px;">
                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditConfiguracionNoticias["PublicarComentariosAutomaticamente3"], 'PublicarComentariosAutomaticamente3', "class='input '") ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="title" colspan="14" bgcolor="#CCE5C8">Otra configuracion</th>
                                                </tr>
                                                <tr>
                                                    <th>Tipo Imagen en noticia?</th>
                                                    <th>
                                                        <select name="TipoImagenNoticias" id="TipoImagenNoticias" class="form-control">
                                                            <option value=""></option>
                                                            <option value="Expandida" <?php if ($EditConfiguracionNoticias["TipoImagenNoticias"] == "Expandida") echo "selected"; ?>>Expandida</option>
                                                            <option value="Ajustada" <?php if ($EditConfiguracionNoticias["TipoImagenNoticias"] == "Ajustada") echo "selected"; ?>>Ajustada</option>
                                                        </select>
                                                    </th>
                                                    <th>Icono like noticias (aplica para modulo1, 2 y3)</th>
                                                    <th>
                                                        <? if (!empty($EditConfiguracionNoticias["IconoLikeNoticias"])) {
                                                            echo "<img src='" . CLUB_ROOT . "$EditConfiguracionNoticias[IconoLikeNoticias]' width=55 >";
                                                        ?>
                                                            <a href="<? echo $script . " .php?action=delfoto&foto=$EditConfiguracionNoticias[IconoLikeNoticias]&campo=IconoLikeNoticias&id=" . $EditConfiguracionNoticias[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                        <?
                                                        } // END if
                                                        ?>
                                                        <input name="IconoLikeNoticias" id=IconoLikeNoticias class="col-xs-12" title="Icono Like" type="file" size="25" style="font-size: 10px">
                                                    </th>
                                                    <th>Icono unlike noticias (aplica para modulo 1, 2 y3)</th>
                                                    <th>
                                                        <? if (!empty($EditConfiguracionNoticias["IconoUnLikeNoticias"])) {
                                                            echo "<img src='" . CLUB_ROOT . "$EditConfiguracionNoticias[IconoUnLikeNoticias]' width=55 >";
                                                        ?>
                                                            <a href="<? echo $script . " .php?action=delfoto&foto=$EditConfiguracionNoticias[IconoUnLikeNoticias]&campo=IconoUnLikeNoticias&id=" . $EditConfiguracionNoticias[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                        <?
                                                        } // END if
                                                        ?>
                                                        <input name="IconoUnLikeNoticias" id=IconoUnLikeNoticias class="col-xs-12" title="Icono Unlike" type="file" size="25" style="font-size: 10px">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Icono comentarios noticias (aplica para modulo1, 2 y3)</th>
                                                    <th>
                                                        <? if (!empty($EditConfiguracionNoticias["ImagenComentarios"])) {
                                                            echo "<img src='" . CLUB_ROOT . "$EditConfiguracionNoticias[ImagenComentarios]' width=55 >";
                                                        ?>
                                                            <a href="<? echo $script . " .php?action=delfoto&foto=$EditConfiguracionNoticias[ImagenComentarios]&campo=ImagenComentarios&id=" . $EditConfiguracionNoticias[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                        <?
                                                        } // END if
                                                        ?>
                                                        <input name="ImagenComentarios" id=ImagenComentarios class="col-xs-12" title="Icono Like" type="file" size="25" style="font-size: 10px">
                                                    </th>
                                                    <th>Correo de notificación (aplica para modulo 1, 2 y 3)</th>
                                                    <th>
                                                        <input type="text" name="CorreoNotificacionComentarioNoticia" class="form-control" placeholder="Correo Notificación" value="<?php echo $EditConfiguracionNoticias["CorreoNotificacionComentarioNoticia"]; ?>">
                                                    </th>
                                                    <th>
                                                        Mostrar Fecha Publicación
                                                    </th>
                                                    <th>
                                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditConfiguracionNoticias["MostrarFecha"], 'MostrarFecha', "class='input '") ?>

                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="texto" colspan="14"></th>
                                                </tr>
                                                <tr>
                                                    <th class="texto" colspan="14"></th>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center">&nbsp;</td>
                                    </tr>
                                </table>
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center"><input type="submit" class="submit" value="Agregar"></td>
            </tr>
        </table>
    </form>
</div>