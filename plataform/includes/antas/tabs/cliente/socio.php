      <div id="Socio">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarSocio";
				  
                  if( $_GET[	IDSocio] )
                  {  		
                          $EditSocio =$dbo->fetchAll("Socio","IDSocio = '".$_GET[IDSocio]."' ","array");
                          $action = "ModificaSocio";
                          ?>
                          <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $EditSocio[IDSocio]?>" />
                          <?php
                  }
                  ?>
                  
                  
                  
                  
            <table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
			          <tr>
			            <td  class="columnafija" >Pais</td>
			            <td>
						<select name="IDPaisSocio" id="IDPaisSocio" required>
                        	<option value="">[Seleccione]</option>
                        	<?php 
							$sql_pais = $dbo->query("Select * From Pais Where Publicar = 'S'");
							while ($row_pais = $dbo->fetchArray($sql_pais)): ?>
								<option value="<?php echo $row_pais[IDPais] ?>" <?php if($row_pais[IDPais]==$EditSocio["IDPais"]) echo "selected"; ?>><?php echo $row_pais[Nombre] ?></option>
							<?php endwhile; ?>
                        </select>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Departamento</td>
			            <td>
                        	<select name="IDDepartamentoSocio" id="IDDepartamentoSocio" class="popup" required>
                            	<?php if(!empty($EditSocio["IDDepartamento"])):?>
                                  <option value="<?php echo $EditSocio["IDDepartamento"] ?>" selected><?php echo $dbo->getFields( "Departamento" , "Nombre" , "IDDepartamento = '".$EditSocio["IDDepartamento"]."'"); ?></option>
                                <?php endif; ?>
                            	
                            </select>
                        
                          </td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Ciudad</td>
			            <td>
                        	<select name="IDCiudadSocio" id="IDCiudadSocio" class="popup" required>
                            <?php if(!empty($EditSocio["IDCiudad"])):?>
                                  <option value="<?php echo $EditSocio["IDCiudad"] ?>" selected><?php echo $dbo->getFields( "Ciudad" , "Nombre" , "IDCiudad = '".$EditSocio["IDCiudad"]."'"); ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Derecho Numero</td>
			            <td><input id=NumeroDerecho type=text size=25  name=NumeroDerecho class="input mandatory " title="Accion" value="<?=$EditSocio[NumeroDerecho] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Accion</td>
			            <td><input id=Accion type=number size=25  name=Accion class="input mandatory " title="Accion" value="<?=$EditSocio[Accion] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Accion Padre</td>
			            <td><input id=AccionPadre type=number size=25  name=AccionPadre class="input" title="Accion Padre" value="<?=$EditSocio[AccionPadre] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Genero</td>
			            <td><?php echo SIMHTML::formRadioGroup( array_flip( array( "M" => "Masculino" , "F" => "Femenino") ) , $EditSocio["Genero"] , "Genero" , "title=\"Genero\"" )?></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Numero Documento</td>
			            <td><input id=NumeroDocumento type=number size=25  name=NumeroDocumento class="input mandatory " title="NumeroDocumento" value="<?=$EditSocio[NumeroDocumento] ?>" required></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" > Nombre </td><td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$EditSocio[Nombre] ?>" required> </td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Apellido</td>
			            <td><input id=Apellido type=text size=25  name=Apellido class="input mandatory " title="Apellido" value="<?=$EditSocio[Apellido] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Fecha Nacimiento</td>
			            <td><input id="FechaNacimiento" type="text" size="10" title="Fecha Nacimiento" name="FechaNacimiento" class="input calendar" value="<?php echo $EditSocio["FechaNacimiento"] ?>" readonly /></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Email</td>
			            <td><input id=Email type=email size=25  name=Email class="input mandatory " title="Email" value="<?=$EditSocio[Email] ?>" required></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Telefono</td>
			            <td><input id=Telefono type=text size=25  name=Telefono class="input " title="Telefono" value="<?=$EditSocio[Telefono] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Celular</td>
			            <td><input id=Celular type=text size=25  name=Celular class="input " title="Celular" value="<?=$EditSocio[Celular] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Nombre Beneficiario</td>
			            <td><input id=NombreBeneficiario type=text size=25  name=NombreBeneficiario class="input " title="Nombre Beneficiario" value="<?=$EditSocio[NombreBeneficiario] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Codigo Barras</td>
			            <td><? if (!empty($EditSocio[CodigoBarras])) {
                                echo "<img src='".SOCIO_ROOT."$EditSocio[CodigoBarras]'>";
                                ?>
                            <?
                            }// END if
                            ?>
                        </td>
		              </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($EditSocio[Foto])) {
					echo "<img src='".SOCIO_ROOT."$EditSocio[Foto]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=".SIMReg::get( "mod" )."&action=delfotoSocio&foto=$EditSocio[Foto]&campo=Foto&IDSocio=" . $EditSocio[IDSocio] ."&id=".SIMNet::get("id")."#Socio" ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto </td>
			  <td><input name="Foto" id=file class=""
					title="Foto" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Clave</td>
			  <td><input id=Clave type="password" size=25  name=Clave class="input mandatory " title="Clave" value="<?=$EditSocio[Clave] ?>" required></td>
			  </tr>
			<tr>
			  <td class="columnafija">&nbsp;</td>
			  <td>&nbsp;</td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Cliente'" class="submit" value="Cancelar" name="submit">
			    <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                <input type="hidden" name="ID" value="<?php echo SIMNet::get("id") ?>" />
                <input type="hidden" name="IDCliente" value="<?php echo SIMNet::get("id") ?>" />
                <input type="hidden" name="ClaveAnt" id="ClaveAnt" value="<?=$EditSocio[Clave] ?>" />
                <input type="hidden" name="EmailAnt" id="EmailAnt" value="<?=$EditSocio[Email] ?>" />
                <input type="hidden" name="NumeroDocumentoAnt" id="NumeroDocumentoAnt" value="<?=$EditSocio[NumeroDocumento] ?>" />
                </td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
                  
                  
            
  </form>
              <br />
            
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="14"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th align="left">Documento</th>
                              <th align="left">Nombre</th>
                              <th align="left">Email</th>
                              <th align="left">Accion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_socio =& $dbo->all( "Socio" , "IDCliente = '" . SIMNet::get("id")  ."'");

                              while( $r = $dbo->object( $r_socio ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&IDSocio=" . $r->IDSocio ."&id=".SIMNet::get("id")."#Socio"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><? echo $r->NumeroDocumento?></td>
                              <td><? echo $r->Nombre?></td>
                              <td><? echo $r->Email; ?></td>
                              <td><? echo $r->Accion; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaSocio&id=<?php echo SIMNet::get("id");?>&IDSocio=<? echo $r->IDSocio ?>&IDPosventa=<?php echo SIMNet::get("id_posventa") ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="14"></th>
                      </tr>
              </table>



</div>
