<div id="porcentajeabono">
    <form name="frmporcentajeabono" id="frmporcentajeabono" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">

                                <?php
                                $action = "InsertarPorcentajeAbono";

                                if ($_GET[IDPorcentajeAbono]) {
                                    $EditPorcentajeAbono = $dbo->fetchAll("PorcentajeAbono", " IDPorcentajeAbono = '" . $_GET[IDPorcentajeAbono] . "' ", "array");
                                    $action = "ModificaPorcentajeAbono";


                                ?>
                                    <input type="hidden" name="IDPorcentajeAbono" id="IDPorcentajeAbono" value="<?php echo $EditPorcentajeAbono[IDPorcentajeAbono] ?>" />
                                <?php
                                }
                                ?>



                        <tr>
                            <td width="26%">Porcentaje </td>

                            <td width="74%">
                                <input id="Porcentaje" type="number" size="25" title="Porcentaje" name="Porcentaje" class="input mandatory" value="<?php echo $EditPorcentajeAbono["Porcentaje"] ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td width="26%">Nombre </td>
                            <td width="74%">
                                <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditPorcentajeAbono["Nombre"] ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Activo </td>
                            <td width="74%">
                                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditPorcentajeAbono["Activo"], 'Activo', "class='input'") ?>

                            </td>
                        </tr>




                        <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $_GET[ids] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
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

            <th>Porcentaje</th>
            <th>Nombre</th>
            <th>Activo</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = $dbo->all("PorcentajeAbono", "IDServicio = '" . $_GET[ids]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDPorcentajeAbono=" . $r->IDPorcentajeAbono ?>&tab=porcentajeabono" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->Porcentaje; ?></td>
                    <td><?php echo $r->Nombre; ?></td>
                    <td><?php echo $r->Activo; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPorcentajeAbono&ids=<?php echo $_GET["ids"]; ?>&IDPorcentajeAbono=<? echo $r->IDPorcentajeAbono ?>&tab=porcentajeabono"></a>
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