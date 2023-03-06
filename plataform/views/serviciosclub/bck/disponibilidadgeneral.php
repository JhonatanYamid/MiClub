      <div id="ServicioDisponibilidad">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarServicioDisponibilidad";

                  if( $_GET[IDServicioDisponibilidad] )
                  {
                          $EditServicioDisponibilidad =$dbo->fetchAll("ServicioDisponibilidad"," IDServicioDisponibilidad = '".$_GET[IDServicioDisponibilidad]."' ","array");
                          $action = "ModificaServicioDisponibilidad";
                          ?>
                          <input type="hidden" name="IDServicioDisponibilidad" id="IDServicioDisponibilidad" value="<?php echo $EditServicioDisponibilidad[IDServicioDisponibilidad]?>" />
                          <?php
                  }
                  ?>
                  <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                          <th colspan="2">CAMPOS RESERVA</th>
                  </tr>
                  <tr>
                    <td>Nombre</td>
                    <td><input type="text" name="Nombre" id="Nombre" class="input" title="Nombre" value="<?php echo $EditServicioDisponibilidad["Nombre"] ?>"></td>
                  </tr>
                  <tr>
                    <td width="26%">Dia a los que aplica</td>
                    <td width="74%">
                    <?php 
                        $array_dias=explode("|",$EditServicioDisponibilidad["IDDia"]);
                        array_pop($array_dias);
                        foreach($Dia_array as $id_dia => $dia):  ?>
                    	        <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
                    <?php endforeach; ?>
                    
                    <!--
					<select name="IDDia" id="IDDia" class="popup">
                    	<option value="">[Seleccione]</option>
                        <?php foreach($Dia_array as $id_dia => $dia):  ?>
                        	<option value="<?php echo $id_dia; ?>" <?php if($id_dia==$EditServicioDisponibilidad["IDDia"]) echo "selected"; ?>><?php echo $dia; ?></option>
                        <?php endforeach; ?>
                    </select>
                    -->
					</td>
                  </tr>
                  <tr>
                    <td class="columnafija">Hora Inicial </td>
                    <td><input type="time" name="HoraDesde" id="HoraDesde" class="input" title="Hora desde" value="<?php echo $EditServicioDisponibilidad["HoraDesde"] ?>"></td>
                  </tr>
                  <tr>
                    <td class="columnafija">Hora Final </td>
                    <td><input type="time" name="HoraHasta" id="HoraHasta" class="input" title="Hora hasta" value="<?php echo $EditServicioDisponibilidad["HoraHasta"] ?>"></td>
                  </tr>
                  <tr>
                    <td class="columnafija">Aplica a los elementos</td>
                    <td>
                    <?php  
					$array_elementos_guardados=explode("|",$EditServicioDisponibilidad["IDServicioElemento"]);   					
					$r_elemento_servicio =& $dbo->all( "ServicioElemento" , "IDServicio = '" . $frm[$key]  ."'");
					  while( $r = $dbo->object( $r_elemento_servicio ) ): ?>
				      <input type="checkbox" name="IDServicioElemento[]" id="IDServicioElemento" value="<?php echo $r->IDServicioElemento; ?>" <?php if(in_array( $r->IDServicioElemento,$array_elementos_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
			     	<?php endwhile; ?>
                    </td>
                  </tr>
                  <tr>
                          <td align="center"><input type="submit" class="submit" value="Enviar"> </td>
                  </tr>
                  </table>
                  
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
              </form>
              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th class="title" colspan="17"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>Dia</th>
                              <th>Aplica para</th>
                              <th>Hora Inicio</th>
                              <th>Hora Fin</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
							
                              $r_documento =& $dbo->all( "ServicioDisponibilidad" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDServicioDisponibilidad=".$r->IDServicioDisponibilidad.""?>&tab=disponibilidad" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php 
							  			$array_dias=explode("|",$r->IDDia);
										if(count($array_dias)>0):
											foreach($array_dias as $IDDia):
												echo $Dia_array[$IDDia] . "-"; 		
											endforeach;
										endif;
							  			
								
								?></td>
                              <td><?php 
							  			$array_elemntos=explode("|",$r->IDServicioElemento);
										if(count($array_elemntos)>0):
											foreach($array_elemntos as $IDServicioElemento):
												echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$IDServicioElemento."'") . "-"; 		
											endforeach;
										endif;
							  			
								
								?></td>
                              <td><?php echo $r->HoraDesde; ?></td>
                              <td><?php echo $r->HoraHasta; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioDisponibilidad&id=<?php echo $frm[ $key ];?>&IDServicioDisponibilidad=<? echo $r->IDServicioDisponibilidad ?>&tab=disponibilidad" class="ace-icon glyphicon glyphicon-remove"></a></td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="17"></th>
                      </tr>
              </table>



</div>
