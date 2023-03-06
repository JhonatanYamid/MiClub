<div id="ObservacionesParaReservas">
    <form name="frmObservacionesParaReservas" id="frmObservacionesParaReservas" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">

                                <?php
                                $action = "InsertarObservacionesParaReservas";

                                if ($_GET[IDObservacionesParaReservas]) {
                                    $EditObservacionesParaReservas = $dbo->fetchAll("ObservacionesParaReservas", " IDObservacionesParaReservas = '" . $_GET[IDObservacionesParaReservas] . "' ", "array");
                                    $action = "ModificaObservacionesParaReservas";


                                ?>
                                    <input type="hidden" name="IDObservacionesParaReservas" id="IDObservacionesParaReservas" value="<?php echo $EditObservacionesParaReservas[IDObservacionesParaReservas] ?>" />
                                <?php
                                }
                                ?>    
                            </td>
                        </tr>                    
                        <tr>
                            <td width="26%">Observación que quieres agregar automaticamente </td>
                            <td width="74%">
                                <input id="Obersvacion" type="text" size="25" title="Obersvacion" name="Obersvacion" class="input mandatory" value="<?php echo $EditObservacionesParaReservas["Obersvacion"] ?>" />
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
            <th>Observación</th>    

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = $dbo->all("ObservacionesParaReservas", "IDServicio = '" . $_GET[ids]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDObservacionesParaReservas=" . $r->IDObservacionesParaReservas ?>&tab=ObservacionesParaReservas" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>                    
                    <td><?php echo $r->Obersvacion; ?></td>                    

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaObservacionesParaReservas&ids=<?php echo $_GET["ids"]; ?>&IDObservacionesParaReservas=<? echo $r->IDObservacionesParaReservas ?>&tab=ObservacionesParaReservas"></a>
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