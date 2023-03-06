<form class="form-horizontal formvalida" role="form" method="post" id="EditVehiculo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
	
	<?php
                $action = "InsertarSocioAusente";

                if( $_GET["IDSocioAusente"] )
                {       
                        $editSocioAusente =$dbo->fetchAll("SocioAusente"," IDSocioAusente = '".$_GET["IDSocioAusente"]."' ","array");                        
                        $action = "ModificarSocioAusente";
                        ?>
                                <input type="hidden" name="IDSocioAusente" id="" value="<?php echo $editSocioAusente["IDSocioAusente"]?>" />
                        <?php
                }
        ?>   
    
    					
	<div  class="form-group first ">
		<div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio:  </label>
			<div class="col-sm-8">
				<input id="FechaInicio" type="text" size="25"  name="FechaInicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?=$editSocioAusente["FechaInicio"] ?>">
			</div>
		</div>
                <div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin:  </label>
			<div class="col-sm-8">
				<input id="FechaFin" type="text" size="25"  name="FechaFin" class="col-xs-12 calendar" title="Fecha Fin" value="<?=$editSocioAusente["FechaFin"] ?>">
			</div>
		</div>                       
        </div>

        <div  class="form-group first ">
		<div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observación:  </label>
			<div class="col-sm-8">
				<textarea id="Observacion" type="text" size="25"  name="Observacion" class="col-xs-12" title="Observación"><?=$editSocioAusente["Observacion"] ?></textarea>
			</div>
		</div>                
        </div>             

	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
                        <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
                        <input type="hidden" name="IDSocio"  id="IDSocio" value="<?php echo $frm[ $key ] ?>" />			
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
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Observación</th>                                              
                <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
        <?php

        $r_datos =& $dbo->all( "SocioAusente" , "IDSocio = '" . $frm[$key]  ."'");

        while( $r = $dbo->object( $r_datos ) )
                {
        ?>

        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                        <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDSocioAusente=".$r->IDSocioAusente?>&tabsocio=ausente" class="ace-icon glyphicon glyphicon-pencil"></a> 
                </td>                
                <td>
                        <?php echo $r->FechaInicio; ?>
                </td>
                <td>
                        <?php echo $r->FechaFin; ?>
                </td>
                <td>
                        <?php echo $r->Observacion; ?>
                </td>                           
                <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="socios.php?action=EliminarSocioAusente&id=<?php echo $r->IDSocioAusente;?>&IDSocio=<? echo $r->IDSocio ?>&tabsocio=mascotas" ></a>
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
                                    
                                    
										
									