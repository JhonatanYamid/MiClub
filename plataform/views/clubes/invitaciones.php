<form class="form-horizontal formvalida" role="form" method="post" id="EditRegla<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

    <?php
                  $action = "InsertarRegla";

                  if( $_GET[IDRegla] )
                  {
                          $EditRegla =$dbo->fetchAll("Regla"," IDRegla = '".$_GET[IDRegla]."' ","array");
                          $action = "ModificaRegla";
                          ?>
    <input type="hidden" name="IDRegla" id="IDRegla" value="<?php echo $EditRegla[IDRegla]?>" />
    <?php
                  }
                  ?>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Regla: </label>

            <div class="col-sm-8">
                <input id=Nombre type=text size=25 name=Nombre class="col-xs-12" title="Nombre" value="<?=$EditRegla[Nombre] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion: </label>

            <div class="col-sm-8">
                <input id=Descripcion type=text size=25 name=Descripcion class="col-xs-12" title="Descripcion" value="<?=$EditRegla[Descripcion] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero maximo de invitados por d&iacute;a: </label>

            <div class="col-sm-8">
                <input id=MaximoInvitadoDia type=text size=25 name=MaximoInvitadoDia class="col-xs-12" title="Maximo Invitado Dia" value="<?=$EditRegla[MaximoInvitadoDia] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero maximo de invitados por d&iacute;a validado en App: </label>

            <div class="col-sm-8">
			<?php echo SIMHTML::formRadioGroup(  SIMResources::$sinoNum , $EditRegla["MaximoInvitadoDiaValidaApp"] , "MaximoInvitadoDiaValidaApp" , "title=\"MaximoInvitadoDiaValidaApp\"" )?>

            </div>
        </div>

    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje mostrar en App si no se valida pero debe decir información adicional: </label>

            <div class="col-sm-8">
                <input id=MensajeNoValidaApp type=text size=25 name=MensajeNoValidaApp class="col-xs-12" title="MensajeNoValidaApp" value="<?=$EditRegla[MensajeNoValidaApp] ?>">
            </div>
        </div>        
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero maximo de invitados al mes: </label>

            <div class="col-sm-8">
                <input id=MaximoInvitadoSocio type=text size=25 name=MaximoInvitadoSocio class="col-xs-12" title="Maximo Invitado Socio" value="<?=$EditRegla[MaximoInvitadoSocio] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero maximo de un mismo invitado al mes: </label>

            <div class="col-sm-8">
                <input id=MaximoRepeticionInvitado type=text size=25 name=MaximoRepeticionInvitado class="col-xs-12" title="Maximo Repeticion Invitado" value="<?=$EditRegla[MaximoRepeticionInvitado] ?>">
            </div>
        </div>

    </div>

	<div class="form-group first ">        

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero maximo de un mismo invitado al mes por socio?: </label>

            <div class="col-sm-8">
				<?php echo SIMHTML::formRadioGroup(  SIMResources::$sinoNum , $EditRegla["MaximoRepeticionInvitadoSocio"] , "MaximoRepeticionInvitadoSocio" , "title=\"MaximoRepeticionInvitadoSocio\"" )?>
            </div>
        </div>

    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Aplica para las categorias </label>

            <div class="col-sm-8">

                <div style="width:300px;">

                    <select multiple class="chosen-select form-control" name="IDCategoria[]" id="IDCategoria" data-placeholder="Selecciones valores...">
                        <?php
																		$r_valor_tabla = $dbo->all( "Categoria" , "Publicar = 'S' Order By Nombre");                    																		
																		$valores_guardados = $EditRegla[IDCategoria] ;
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;
																		
																			
					                                                  while( $r_valor = $dbo->object( $r_valor_tabla ) ){ 
																	  
																		  if(empty($valores_guardados)):
																				$seleccionar = "";
																			elseif(in_array($r_valor->IDCategoria,$array_valores_guardados)):																			
																				$seleccionar = "selected";
																			else:		
																				$seleccionar = "";	
																			endif;	
																																																																									  
																		  ?>
                        <option value="<?php echo $r_valor->IDCategoria ?>" <?php echo $seleccionar; ?>><?php echo $r_valor->Nombre; ?></option>

                        <? }	?>
                    </select>


                </div>
            </div>

        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Aplica para los parentescos </label>

            <div class="col-sm-8">

                <div style="width:300px;">

                    <select multiple class="chosen-select form-control" name="IDParentesco[]" id="IDParentesco" data-placeholder="Selecciones valores...">
                        <?php
							unset($array_valores_guardados);
							$r_valor_tabla = $dbo->all( "Parentesco" , "Publicar = 'S' Order By Nombre");                    																		
							$valores_guardados = $EditRegla[IDParentesco] ;
							if(!empty($valores_guardados)):
								$array_valores_guardados = explode("|",$valores_guardados);
							endif;
								
							while( $r_valor = $dbo->object( $r_valor_tabla ) ){ 
							
								if(empty($valores_guardados)):
									$seleccionar = "";
								elseif(in_array($r_valor->IDParentesco,$array_valores_guardados)):																			
									$seleccionar = "selected";
								else:		
									$seleccionar = "";	
								endif;	
																																																															
								?>
                        <option value="<?php echo $r_valor->IDParentesco ?>" <?php echo $seleccionar; ?>><?php echo $r_valor->Nombre; ?></option>

                        <? }	?>
                    </select>


                </div>
            </div>

        </div>



    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valida invitaciones pasadas cumplidas? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditRegla["CumplimientoInvitados"] , "CumplimientoInvitados" , "title=\"CumplimientoInvitados\"" )?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valida invitaciones futuras pendientes? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditRegla["CumplimientoInvitadosFuturas"] , "CumplimientoInvitadosFuturas" , "title=\"CumplimientoInvitadosFuturas\"" )?>
            </div>
        </div>
        

    </div>

    <div class="form-group first ">

    <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar: </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditRegla["Publicar"] , "Publicar" , "title=\"Publicar\"" )?>
            </div>
        </div>
    </div>




    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
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
        <th>Nombre Regla</th>
        <th>Descripcion</th>       
        <th>Publicar</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php
			$r_documento =& $dbo->all( "Regla" , "IDClub = '" . $frm[$key]  ."'");
			while( $r = $dbo->object( $r_documento ) )
			{
				?>
				<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
					<td align="center" width="64">
						<a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDRegla=".$r->IDRegla?>&tabclub=invitaciones" class="ace-icon glyphicon glyphicon-pencil"></a>
					</td>
					<td><?php echo $r->Nombre; ?></td>
					<td><?php echo $r->Descripcion; ?></td>           
					<td><?php echo $r->Publicar; ?></td>
					<td align="center" width="64">
						<a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaRegla&id=<?php echo $frm[$key];?>&IDRegla=<? echo $r->IDRegla ?>&tabclub=invitaciones"></a>
					</td>
				</tr>
       			 <?php
			}
				?>
    </tbody>
    <tr>
        <th class="texto" colspan="16"></th>
    </tr>
</table>