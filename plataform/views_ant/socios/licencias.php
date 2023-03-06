<form class="form-horizontal formvalida" role="form" method="post" id="EditLicenciaSocio<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

        <?php
        $action = "InsertarLicenciaSocio";

        if ($_GET[IDLicenciaSocio]) {
                $EditLicenciaSocio = $dbo->fetchAll("LicenciaSocio", " IDLicenciaSocio = '" . $_GET[IDLicenciaSocio] . "' ", "array");
                $action = "ModificaLicenciaSocio";
        ?>
                <input type="hidden" name="IDLicenciaSocio" id="IDLicenciaSocio" value="<?php echo $EditLicenciaSocio[IDLicenciaSocio] ?>" />
        <?php
        }
        ?>



        <div class="form-group first ">

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>: </label>
                        <div class="col-sm-8">
                                <select name="Categoria" id="Categoria" class="popup">
                                        <option value=""></option>
                                        <option value="A1" <?php if ($EditLicenciaSocio[Categoria] == "A1") echo "selected"; ?>>A1</option>
                                        <option value="A2" <?php if ($EditLicenciaSocio[Categoria] == "A2") echo "selected"; ?>>A2</option>
                                        <option value="B1" <?php if ($EditLicenciaSocio[Categoria] == "B1") echo "selected"; ?>>B1</option>
                                        <option value="B2" <?php if ($EditLicenciaSocio[Categoria] == "B2") echo "selected"; ?>>B2</option>
                                        <option value="B3" <?php if ($EditLicenciaSocio[Categoria] == "B3") echo "selected"; ?>>B3</option>
                                        <option value="C1" <?php if ($EditLicenciaSocio[Categoria] == "C1") echo "selected"; ?>>C1</option>
                                        <option value="C2" <?php if ($EditLicenciaSocio[Categoria] == "C2") echo "selected"; ?>>C2</option>
                                        <option value="C3" <?php if ($EditLicenciaSocio[Categoria] == "C3") echo "selected"; ?>>C3</option>

                                </select>
                        </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaVencimiento', LANGSESSION); ?>: </label>

                        <div class="col-sm-8">
                                <input type="text" id="FechaVencimiento" name="FechaVencimiento" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaVencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaVencimiento', LANGSESSION); ?>" value="<?php echo $EditLicenciaSocio["FechaVencimiento"] ?>">
                        </div>
                </div>

        </div>









        <div class="clearfix form-actions">
                <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />


                </div>
        </div>




</form>










<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
                <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?></th>
                <th><?= SIMUtil::get_traduccion('', '', 'FechaVencimiento', LANGSESSION); ?></th>
                <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
        </tr>
        <tbody id="listacontactosanunciante">
                <?php

                $r_datos = &$dbo->all("LicenciaSocio", "IDSocio = '" . $frm[$key]  . "'");

                while ($r = $dbo->object($r_datos)) {
                ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                <td align="center" width="64">
                                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDLicenciaSocio=" . $r->IDLicenciaSocio ?>&tabsocio=licencias" class="ace-icon glyphicon glyphicon-pencil"></a>
                                </td>
                                <td><?php echo $r->Categoria; ?></td>
                                <td><?php echo $r->FechaVencimiento; ?></td>
                                <td align="center" width="64">
                                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaLicenciaSocio&id=<?php echo $frm[$key]; ?>&IDLicenciaSocio=<? echo $r->IDLicenciaSocio ?>&tabsociodo=licencias"></a>
                                </td>
                        </tr>
                <?php
                }
                ?>
        </tbody>
        <tr>
                <th class="texto" colspan="14"></th>
        </tr>
</table>