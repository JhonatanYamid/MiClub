<div id="Categoriacaddie2">
    <form name="frmpro1" id="frmpro1" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>


                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">


                                <?php
                                $action = "InsertarCategoriaCaddie2";

                                if ($_GET[IDCategoriaCaddie]) {
                                    $EditCategoriaCaddie = $dbo->fetchAll("CategoriaCaddie2", " IDCategoriaCaddie = '" . $_GET[IDCategoriaCaddie] . "' ", "array");
                                    $action = "ModificaCategoriaCaddie2";


                                ?>
                                    <input type="hidden" name="IDCategoriaCaddie" id="IDCategoriaCaddie" value="<?php echo $EditCategoriaCaddie[IDCategoriaCaddie] ?>" />
                                <?php
                                }
                                ?>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                                    <tr>
                                        <td width="26%">Categoria </td>
                                        <td width="74%">
                                            <input id="Categoria" type="text" size="25" title="Categoria" name="Categoria" class="input mandatory" value="<?php echo $EditCategoriaCaddie["Categoria"] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>

                                    <tr>
                                        <td width="26%">Descripción </td>
                                        <td width="74%">
                                            <input id="Descripción" type="text" size="25" title="Descripción" name="Descripción" class="input" value="<?php echo $EditCategoriaCaddie["Descripción"] ?>" />
                                        </td>
                                    </tr>

                                </table>
                                <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm["IDClub"] ?>" />
                                <input type="hidden" name="Version" id="Version" value="1" />


                            </td>
                            <td valign="top">


                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td align="center"><input type="submit" class="submit" value="Agregar" /></td>
            </tr>

        </table>
    </form>


    <br />
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <th align="center" valign="middle" width="64">Editar</th>
            <th>Categoria</th>
            <th>Descricion</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = &$dbo->all("CategoriaCaddie2", "IDServicio = '" . $_GET[ids]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDCategoriaCaddie=" . $r->IDCategoriaCaddie ?>&tab=categoriacaddie2" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->Categoria; ?></td>
                    <td><?php echo $r->Descripción; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCategoriaCaddie2&ids=<?php echo $_GET["ids"]; ?>&IDCategoriaCaddie=<? echo $r->IDCategoriaCaddie ?>&tab=categoriacaddie2"></a>
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



</div>