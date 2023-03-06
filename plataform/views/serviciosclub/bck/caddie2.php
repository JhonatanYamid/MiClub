<div id="caddie2">
    <form name="frmcaddie2" id="frmcaddie2" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">
                                
                                    <?php
                                    $action = "InsertarCaddie2";

                                    if ($_GET[IDCaddie]) {
                                        $EditCaddie = $dbo->fetchAll("Caddie2", " IDCaddie = '" . $_GET[IDCaddie] . "' ", "array");
                                        $action = "ModificaCaddie2";


                                    ?>
                                        <input type="hidden" name="IDCaddie" id="IDCaddie" value="<?php echo $EditCaddie[IDCaddie] ?>" />
                                    <?php
                                    }
                                    ?>                                

                                    <tr>
                                        <td width="26%">Elementos</td>
                                        <td>
                                            <select name="AccionInvitadoUsuario" id="AccionInvitadoUsuario">
                                                <option value=""></option>
                                                <?php
                                                $sql_elementos = "SELECT * FROM ServicioElemento WHERE IDServicio='" . $_GET[ids] . "'";
                                                $result_elementos = $dbo->query($sql_elementos);

                                                while ($row_elementos = $dbo->fetchArray($result_elementos)) :

                                                ?>
                                                    <option value="<?php echo $row_elementos[IDServicioElemento] ?>"><?php echo  $row_elementos[Nombre] ?></option>


                                                <?php endwhile; ?>
                                            </select>
                                            <br>
                                            <a id="agregar_caddie2" href="#">Agregar</a> | <a id="borrar_empleado" href="#">Borrar</a>
                                            <br>
                                            <select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8" multiple>
                                                <?php
                                                $item = 1;
                                                $array_invitados = explode("|||", $EditCaddie["IDElemento"]);
                                                // print_r($array_invitados);


                                                foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                                    if (!empty($datos_invitado)) {
                                                        //$array_datos_invitados = explode("-", $datos_invitado);
                                                        $item--;
                                                        $IDSocioInvitacion = $datos_invitado;
                                                        if ($IDSocioInvitacion > 0) :
                                                            $nombre_socio = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $IDSocioInvitacion . "'");
                                                            echo $nombre_socio;
                                                ?>
                                                            <option value="<?php echo  $IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                                <?php
                                                        endif;
                                                    }
                                                endforeach; ?>
                                            </select>
                                            <input type="hidden" name="IDElemento" id="IDElemento" value="">

                                        </td>                                       

                                    </tr>

                                    <tr>
                                        <td width="26%">Categoria caddie </td>

                                        <td width="74%">
                                            <select name="IDCategoriaCaddie" id="IDCategoriaCaddie" required>
                                                <option value=""></option>
                                                <?php
                                                $sql_categoriacaddie2 = "SELECT * FROM CategoriaCaddie2 WHERE IDServicio='" . $_GET[ids] . "'";
                                                $result_categorias = $dbo->query($sql_categoriacaddie2);
                                                while ($row_categorias = $dbo->fetchArray($result_categorias)) :

                                                ?>
                                                    <option value="<? echo $row_categorias[IDCategoriaCaddie] ?>" <?php if ($EditCaddie["IDCategoriaCaddie"] == $row_categorias[IDCategoriaCaddie]) echo "selected"; ?>><?php echo $row_categorias[Categoria] ?></option>

                                                <?php endwhile; ?>

                                            </select>
                                        </td>                                        
                                    </tr>
                                   
                                    <tr>
                                        <td width="26%">Nombre </td>
                                        <td width="74%">
                                            <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditCaddie["Nombre"] ?>" />
                                        </td>                                        
                                    </tr>                                   
                                    <tr>
                                        <td width="26%">Precio </td>
                                        <td width="74%">
                                            <input id="Precio" type="text" size="25" title="Precio" name="Precio" class="input mandatory" value="<?php echo $EditCaddie["Precio"] ?>" />
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>Docuemento</td>
                                        <td> <input id="DocuementoCaddie" type="text" size="25" title="DocuementoCaddie" name="DocuementoCaddie" class="input mandatory" value="<?php echo $EditCaddie["DocuementoCaddie"] ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td>Activo</td>
                                        <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditCaddie["Activo"], 'Activo', "class='input'") ?></td>
                                    </tr>                                   
                                    <tr>
                                        <td>Descripcion</td>
                                        <td> <textarea name="Descripcion" id="Descripcion" cols="30" rows="10"><?php echo $EditCaddie["Descripcion"] ?></textarea></td>
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
            <th>Nombre</th>
            <th>Precio</th>
            <th>Activo</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = $dbo->all("Caddie2", "IDServicio = '" . $_GET[ids]  . "'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDCaddie=" . $r->IDCaddie ?>&tab=caddie" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->Nombre; ?></td>                   
                    <td>$<?php echo $r->Precio; ?></td>
                    <td><?php echo $r->Activo; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCaddie2&ids=<?php echo $_GET["ids"]; ?>&IDCaddie=<? echo $r->IDCaddie ?>&tab=caddie"></a>
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