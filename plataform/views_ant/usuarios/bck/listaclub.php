<form class="form-horizontal formvalida" role="form" method="post" id="EditListaClub<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th>Identificador Club</th>
                              <th>Nombre Club</th>
                              <th align="center" valign="middle" width="64">Seleccionar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

												$sql_lista="SELECT IDClub FROM UsuarioClub WHERE IDUsuario = '".$frm[ "IDUsuario" ]."' ";
												$r_lista=$dbo->query($sql_lista);
												while($row_lista=$dbo->fetchArray($r_lista)){
													$array_selecc[]=$row_lista["IDClub"];
												}

                              $r_datos =& $dbo->all( "Club" , "IDClub  > 1 Order by Nombre");
                              while( $r = $dbo->object( $r_datos ) ) { ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td><?php echo $r->IDClub; ?></td>
                              <td><?php echo $r->Nombre; ?></td>
															<td><input type="checkbox" value="<?php echo $r->IDClub; ?>" name="ListaClub[]" <?php if(in_array($r->IDClub,$array_selecc)) echo "checked"; ?>></td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="15"></th>
                      </tr>
              </table>

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
																		<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
																		 <input type="hidden" name="IDUsuario"  id="IDUsuario" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="actualizaclub" />
									<input type="submit" class="submit" value="Guardar">
																		<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />



								</div>
							</div>
</form>
