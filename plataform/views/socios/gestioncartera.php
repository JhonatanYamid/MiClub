<form class="form-horizontal formvalida" role="form" method="post" id="InsertGestionCartera<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

        <?php
        $action = "InsertGestionCartera";
        ?>


        <div class="form-group first ">
                <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'observacion', LANGSESSION); ?>: </label>
                        <div class="col-sm-8">
                                <textarea id="Observacion" type="text" size="25" name="Observacion" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'observacion', LANGSESSION); ?>"><?= $editSocioAusente["Observacion"] ?></textarea>    
                        </div>
                </div>
        </div>
        <div class="clearfix form-actions">
                <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                        <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $_GET['id'] ?>" />

                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                        <input type="hidden" name="FechaRegistro" id="FechaRegistro" value="<?php echo date("Y-m-d") ?>" />
                        <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
                </div>
        </div>

</form>


<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
                <th><?= SIMUtil::get_traduccion('', '', 'observacion', LANGSESSION); ?></th>
                <th>Fecha Registro</th>
                <th>Usuario</th>

        </tr>
        <tbody id="listacontactosanunciante">
                <?php
                $r_datos = &$dbo->all("GestionCartera", "IDClub = ". SIMUser::get("club") ." AND IDSocio = ".$_GET['id']);
                while ($r = $dbo->object($r_datos)) {
                ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                <td>
                                        <?php echo $r->Observacion; ?>
                                </td>
                                <td>
                                        <?php echo $r->FechaRegistro; ?>
                                </td>
                                <td>
                                        <?php echo $r->UsuarioTrEd; ?>
                                </td>
                        </tr>
                <?php
                }
                ?>
        </tbody>
        <tr>
                <th class="texto" colspan="15"></th>
        </tr>
</table>