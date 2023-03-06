      <div id="ServicioElemento">
<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">      
      <table border="0" width="100%">
      	<tr>
      		<td>
      
      
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
        	<tr>
            	<td valign="top">
               

                  <?php
                  $action = "InsertarServicioElemento";

                  if( $_GET[IDServicioElemento] )
                  {
                          $EditServicioElemento =$dbo->fetchAll("ServicioElemento"," IDServicioElemento = '".$_GET[IDServicioElemento]."' ","array");
                          $action = "ModificaServicioElemento";
                          ?>
                          <input type="hidden" name="IDServicioElemento" id="IDServicioElemento" value="<?php echo $EditServicioElemento[IDServicioElemento]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="2">ELEMENTOS SERVICIO</th>
                  </tr>
                  <tr>
                    <td>Padre</td>
                    <td>
					<select name="IDPadre" id="IDPadre">
                    	<option value="">[Seleccione]</option>
					<?php 
						$qry_padre = $dbo->all( "ServicioElemento", " IDServicio = '".$frm[ $key ]."' and IDPadre = 0" );
						while ( $r_pade = $dbo->object( $qry_padre ) ): ?>
							<option value="<?php echo $r_pade->IDServicioElemento?>" <?php if($r_pade->IDServicioElemento==$EditServicioElemento[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre?></option>
                        <?php	
						endwhile;
					?>
                    </select>
                    </td>
                  </tr>
                  <tr>
                    <td width="26%">Nombre Elemento</td>
                    <td width="74%"><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditServicioElemento["Nombre"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Descripcion</td>
                    <td>
                    <textarea name="Descripcion" id="Descripcion" cols="40" rows="5"><?php echo $EditServicioElemento["Descripcion"] ?></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td>Publicar</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioElemento["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                          <td align="center">&nbsp;</td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
             
                </td>
                <td valign="top">

                  <?php
                  $action = "InsertarDisponibilidadElemento";
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="40%" class="adminform">
                  <tr>
                          <th colspan="2">AGREGAR DISPONIBILIDAD ELEMENTO</th>
                  </tr>
                  <tr>
                    <td width="26%">Dia</td>
                    <td width="74%">
					<select name="IDDia" id="IDDia" class="popup">
                    	<option value="">[Seleccione]</option>
                        <?php foreach($Dia_array as $id_dia => $dia):  ?>
                   	  <option value="<?php echo $id_dia; ?>" <?php if($id_dia==$EditServicioDisponibilidadElemento["IDDia"]) echo "selected"; ?>><?php echo $dia; ?></option>
                        <?php endforeach; ?>
                    </select>
					</td>
                  </tr>
                  <tr>
                    <td >Hora Inicial </td>
                    <td><input type="time" name="HoraDesde" id="HoraDesde" class="input" title="Hora desde" value="<?php echo $EditServicioDisponibilidadElemento["HoraDesde"] ?>" size="10" style="width:150px;"></td>
                  </tr>
                  <tr>
                    <td>Hora Final </td>
                    <td><input type="time" name="HoraHasta" id="HoraHasta" class="input" title="Hora hasta" value="<?php echo $EditServicioDisponibilidadElemento["HoraHasta"] ?>" size="10" style="width:150px;"></td>
                  </tr>
                  <tr>
                    <td>Repetir</td>
                    <td>
                    <select name="Repeticion" id="Repeticion" class="popup">
                    	<option value="">[Seleccione]</option>
                        <option value="Dia">Cada Dia</option>
                        <option value="Semana">Cada Semana</option>
                        <option value="Mes">Cada Mes</option>
                        <option value="Year">Cada A&ntilde;o</option>
                    </select>
                    </td>
                  </tr>
                  <tr>
                          <td align="center">&nbsp;</td>
                  </tr>
                  </table>
                  
              <br>
              
              <table class="adminlist" width="100%">
                      <tr>
                              <th>Dia</th>
                              <th>Hora Inicio</th>
                              <th>Hora Fin</th>
                              <th>Repeticion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
	                          $r_dispo_elemento =& $dbo->all( "ElementoDisponibilidad" , "IDServicioElemento = '" . $_GET["IDServicioElemento"]  ."'");
                              while( $r = $dbo->object( $r_dispo_elemento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                        <td><?php echo $Dia_array[$r->IDDia]; ?></td>
                        <td><?php echo $r->HoraDesde; ?></td>
                        <td><?php echo $r->HoraHasta; ?></td>
                        <td><?php echo $r->Repeticion; ?></td>
                              <td align="center" width="64">
                                <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaDisponibilidadElemento&id=<?php echo $frm[ $key ];?>&IDElementoDisponibilidad=<? echo $r->IDElementoDisponibilidad ?>&IDServicioElemento=<?php echo $EditServicioElemento[IDServicioElemento]?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="15"></th>
                      </tr>
              </table>
              
               
                
              </td>
            </tr>
        </table>
        
        </td>
        </tr>
        <tr>
        	<td align="center"><input type="submit" class="submit" value="Agregar"></td>
        </tr>
        
        </table>
</form>

          
              <br />
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="15"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Elemento</th>
                              <th>Padre</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "ServicioElemento" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&id=" . $_GET[id] ."&IDServicioElemento=".$r->IDServicioElemento."#ServicioElemento"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $r->IDPadre . "'" ); ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a class="confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioElemento&id=<?php echo $frm[ $key ];?>&IDServicioElemento=<? echo $r->IDServicioElemento ?>"><img src='images/trash.png' border='0' /></a>                                </td>
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
