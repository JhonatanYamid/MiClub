

<form class="form-horizontal formvalida" role="form" method="post" id="EditEquipo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

        <?php
        $action = "InsertarEquipo";

        if( $_GET[IDEquipo] )
        {
                $EditEquipo =$dbo->fetchAll("Equipo"," IDEquipo = '".$_GET[IDEquipo]."' ","array");
                $action = "ModificaEquipo";
                ?>
                <input type="hidden" name="IDEquipo" id="IDEquipo" value="<?php echo $EditEquipo[IDEquipo]?>" />
                <?php
        }
        ?>

        <div  class="form-group first ">
                
                <div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Equipo:  </label>
                        <div class="col-sm-8">
                                <select name = "IDTipoEquipo" id="IDTipoEquipo">
                                        <option value="">[Seleccione el tipo]</option>
                                        <?php
                                        foreach(SIMResources::$equiposlapradera as $id_tipo => $tipo):												
                                                ?>
                                                <option value="<?php echo $id_tipo; ?>" <?php if($id_tipo == $EditEquipo["IDTipoEquipo"]) echo "selected";  ?>><?php echo $tipo; ?></option>
                                                <?php
                                        endforeach;
                                        ?>
                                </select>
                        </div>
                </div>  

                <div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cantidad  </label>
                        <div class="col-sm-8">
                                <input type="number" id="Cantidad" name="Cantidad" placeholder="Cantidad" class="col-xs-12 mandatory" title="Cantidad" value="<?php echo $EditEquipo["Cantidad"] ?>" >
                        </div>
                </div>      
        </div>

        <div class="clearfix form-actions">
                <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID"  id="ID" title="Invitado" class="mandatory" value="<?php echo $frm[ $key ] ?>" />
                        <input type="hidden" name="IDInvitado"  id="IDInvitado" value="<?php echo $frm[ $key ] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                        <input type="submit" class="submit" value="Guardar">
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                </div>
        </div>
</form>

<br />

<table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
                <th align="center" valign="middle" width="64">Editar</th>
                <th>Equipo</th>
                <th>Cantidad</th>
                <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
                <?php
                if((int)$frm[$key]>0)
                {
                        $r_datos =& $dbo->all( "Equipo" , "IDInvitado = '" . $frm[$key]  ."'");

                        while( $r = $dbo->object( $r_datos ) )
                        {
                                ?>

                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                                        <td align="center" width="64">
                                                <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDEquipo=".$r->IDEquipo?>&tabinvitado=equipos&refiere=porteria" class="ace-icon glyphicon glyphicon-pencil"></a>                                
                                        </td>
                                        <td>                                        
                                                <?php 
                                                echo SIMResources::$equiposlapradera[$r->IDTipoEquipo];                                                
                                                ?>                                        
                                        </td>
                                        <td><?php echo $r->Cantidad; ?></td>
                                        <td align="center" width="64">
                                                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaEquipo&id=<?php echo $frm[$key];?>&IDEquipo=<? echo $r->IDEquipo ?>&tabinvitado=equipos"></a>                                
                                        </td>
                                </tr>
                                <?php
                        }
                }
                ?>
        </tbody>
        <tr>
                <th class="texto" colspan="15"></th>
        </tr>
</table>
