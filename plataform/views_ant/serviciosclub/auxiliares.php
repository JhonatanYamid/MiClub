      <div id="Auxiliar">
<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarAuxiliar";

                  if( $_GET[IDAuxiliar] )
                  {
                          $EditAuxiliar =$dbo->fetchAll("Auxiliar"," IDAuxiliar = '".$_GET[IDAuxiliar]."' ","array");
                          $action = "ModificaAuxiliar";
                          ?>
                          <input type="hidden" name="IDAuxiliar" id="IDAuxiliar" value="<?php echo $EditAuxiliar[IDAuxiliar]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                          <th colspan="4">Auxiliares (Boleadores)</th>
                  </tr>
                  <!--
                  <tr>
                    <td>Padre</td>
                    <td>
					<select name="IDPadre" id="IDPadre">
                    	<option value="">[Seleccione]</option>
					<?php
						$qry_padre = $dbo->all( "Auxiliar", " IDServicio = '".$frm[ $key ]."' and IDPadre = 0" );
						while ( $r_pade = $dbo->object( $qry_padre ) ): ?>
							<option value="<?php echo $r_pade->IDAuxiliar?>" <?php if($r_pade->IDAuxiliar==$EditAuxiliar[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre?></option>
                        <?php
						endwhile;
					?>
                    </select>
                    </td>
                  </tr>
                  -->

                  <tr>
                    <td width="26%">Documento</td>
                    <td width="74%"><input id="NumeroDocumento" type="text" size="25" title="Numero Documento" name="NumeroDocumento" class="input mandatory" value="<?php echo $EditAuxiliar["NumeroDocumento"] ?>" /></td>
                    <td width="74%">Nombre</td>
                    <td width="74%"><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditAuxiliar["Nombre"] ?>" /></td>
                  </tr>
                  <tr>
                    <td>Telefono</td>
                    <td><input id="Telefono" type="text" size="25" title="Telefono" name="Telefono" class="input mandatory" value="<?php echo $EditAuxiliar["Telefono"] ?>" /></td>
                    <td>Activo</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditAuxiliar["Activo"] , 'Activo' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                    <td>Orden</td>
                    <td><input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input" value="<?php echo $EditAuxiliar["Orden"] ?>" /></td>
                    <td>Foto</td>
                    <td>
                      <?php
                      if (!empty($EditAuxiliar["Foto"])) {
                         echo "<img src='".ELEMENTOS_ROOT."$EditAuxiliar[Foto]' width=55 >";
                         ?>
                            <a href="<? echo $script.".php?mod=" . SIMReg::get( "mod" ) . "&action=delfotoauxiliar&foto=$EditAuxiliar[Foto]&campo=Foto&ids=".$EditAuxiliar[$key]."&IDAuxiliar=".$_GET["IDAuxiliar"]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                       <?php }// END if  ?>
                       <input name="Foto" id=file class="col-xs-12"	title="Foto" type="file" size="25" style="font-size: 10px">
                    </td>
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
                  //$action = "InsertarDisponibilidadElemento";
                  ?>
                  <!--
                  <table id="simple-table" class="table table-striped table-bordered table-hover">
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

              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th>Dia</th>
                              <th>Hora Inicio</th>
                              <th>Hora Fin</th>
                              <th>Repeticion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
	                          $r_dispo_elemento =& $dbo->all( "ElementoDisponibilidad" , "IDAuxiliar = '" . $_GET["IDAuxiliar"]  ."'");
                              while( $r = $dbo->object( $r_dispo_elemento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                        <td><?php echo $Dia_array[$r->IDDia]; ?></td>
                        <td><?php echo $r->HoraDesde; ?></td>
                        <td><?php echo $r->HoraHasta; ?></td>
                        <td><?php echo $r->Repeticion; ?></td>
                              <td align="center" width="64">
                                <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaDisponibilidadElemento&id=<?php echo $frm[ $key ];?>&IDElementoDisponibilidad=<? echo $r->IDElementoDisponibilidad ?>&IDAuxiliar=<?php echo $EditAuxiliar[IDAuxiliar]?>&tab=auxiliares" class="ace-icon glyphicon glyphicon-remove"></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="15"></th>
                      </tr>
              </table>
              -->


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
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th class="title" colspan="15"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Documento</th>
                              <th>Nombre</th>
                              <th>Activo</th>
                              <th>Orden</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "Auxiliar" , "IDServicio = '" . $frm[$key]  ."' Order by Orden");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $_GET[ids] ."&IDAuxiliar=".$r->IDAuxiliar?>&tab=auxiliares" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->NumeroDocumento; ?></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Activo; ?></td>
                              <td><?php echo $r->Orden; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaAuxiliar&ids=<?php echo $_GET["ids"];?>&IDAuxiliar=<? echo $r->IDAuxiliar ?>&tab=auxiliares" ></a>                                </td>
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
