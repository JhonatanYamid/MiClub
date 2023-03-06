<form class="form-horizontal formvalida" role="form" method="post" id="EditVehiculo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
	
	<?php
                $action = "InsertarMascota";

                if( $_GET["IDMascota"] )
                {       
                        $EditMascota =$dbo->fetchAll("Mascota"," IDMascota = '".$_GET["IDMascota"]."' ","array");                        
                        $action = "ModificarMascota";
                        ?>
                                <input type="hidden" name="IDMascota" id="IDMascota" value="<?php echo $EditMascota["IDMascota"]?>" />
                        <?php
                }
        ?>   
    
    					
	<div  class="form-group first ">
		<div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Mascota:  </label>
			<div class="col-sm-8">
				<input id="Nombre" type="text" size="25"  name="Nombre" class="col-xs-12" title="Nombre" value="<?=$EditMascota["Nombre"] ?>">
			</div>
		</div>                      
        </div>

        <div  class="form-group first ">
		<div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Raza Mascota:  </label>
			<div class="col-sm-8">
				<input id="Raza" type="text" size="25"  name="Raza" class="col-xs-12" title="Raza" value="<?=$EditMascota["Raza"] ?>">
			</div>
		</div>
                <div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Mascota:  </label>
			<div class="col-sm-8">
				<input id="Tipo" type="text" size="25"  name="Tipo" class="col-xs-12" title="Tipo" value="<?=$EditMascota["Tipo"] ?>">
			</div>
		</div>
        </div>
        <div  class="form-group first ">
		<div  class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Edad:  </label>
			<div class="col-sm-8">
				<input id="Edad" type="text" size="25"  name="Edad" class="col-xs-12" title="Edad" value="<?=$EditMascota["Edad"] ?>">
			</div>
		</div>
        </div>

        <div  class="form-group first ">
                <div  class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Foto mascota</label>
			<div class="col-sm-8">                                            
			        <?php if ($EditMascota["Foto"]) { ?>
				        <h5>Imagen actual</h5>
				        <img src="<?php echo SOCIO_ROOT. $EditMascota["Foto"]?>" width="200">	
				        <a href="<? echo SOCIO_ROOT . $EditMascota["Foto"]?>" class="ace-icon fa fa-eye">&nbsp;</a>											
				        <a href="<? echo $script.".php?action=DelMascotaFoto&archivo=" . $EditMascota["Foto"] . "&id=" . $EditMascota["IDMascota"] . "&IDSocio=" . $EditMascota["IDSocio"]?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>											 											 
													
			        <?php } ?>
			        <br/>
			        <br/>
			        <input type="file" id="Foto" name="Foto" class="col-xs-12" title="Foto" value="<?php echo $EditMascota["Foto"];?>">											
			</div>
		</div>
                <div  class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Foto del carnet de vacuna</label>
			<div class="col-sm-8">                                            
			        <?php if ($EditMascota["FotoVacuna"]) { ?>
				        <h5>Imagen actual</h5>
				        <img src="<?php echo SOCIO_ROOT. $EditMascota["FotoVacuna"]?>" width="200">	
				        <a href="<? echo SOCIO_ROOT . $EditMascota["FotoVacuna"]?>" class="ace-icon fa fa-eye">&nbsp;</a>											
				        <a href="<? echo $script.".php?action=DelMascotaFoto&archivo=" . $EditMascota["Foto"] . "&id=" . $EditMascota["IDMascota"] . "&IDSocio=" . $EditMascota["IDSocio"] . "&campo=vacuna"?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>											 											 
													
			        <?php } ?>
			        <br/>
			        <br/>
			        <input type="file" id="FotoVacuna" name="FotoVacuna" class="col-xs-12" title="FotoVacuna" value="<?php echo $EditMascota["FotoVacuna"];?>">											
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
                <th>Foto mascota</th>
                <th>Nombre mascota</th>
                <th>Raza mascota</th>
                <th>Tipo mascota</th>                               
                <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
        <?php

        $r_datos =& $dbo->all( "Mascota" , "IDSocio = '" . $frm[$key]  ."'");

        while( $r = $dbo->object( $r_datos ) )
                {
        ?>

        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                        <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDMascota=".$r->IDMascota?>&tabsocio=mascotas" class="ace-icon glyphicon glyphicon-pencil"></a> 
                </td>
                <td>
                        <img src="<?php echo SOCIO_ROOT . $r->Foto?>" width="70px">
                </td> 
                <td>
                        <?php echo $r->Nombre; ?>
                </td>
                <td>
                        <?php echo $r->Raza; ?>
                </td>
                <td>
                        <?php echo $r->Tipo; ?>
                </td>                           
                <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="socios.php?action=EliminarMascota&id=<?php echo $r->IDMascota;?>&IDSocio=<? echo $r->IDSocio ?>&tabsocio=mascotas" ></a>
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
                                    
                                    
										
									