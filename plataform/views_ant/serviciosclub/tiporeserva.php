      <div id="ServicioTipoReserva">
          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                      <td>
                          <table id="simple-table" class="table table-striped table-bordered table-hover">
                              <tr>
                                  <td valign="top">
                                      <?php
                                    $action = "InsertarServicioTipoReserva";

                                    if( $_GET[IDServicioTipoReserva] )
                                    {
                                            $EditServicioTipoReserva =$dbo->fetchAll("ServicioTipoReserva"," IDServicioTipoReserva = '".$_GET[IDServicioTipoReserva]."' ","array");
                                            $action = "ModificaServicioTipoReserva";
                                            ?>
                                      <input type="hidden" name="IDServicioTipoReserva" id="IDServicioTipoReserva" value="<?php echo $EditServicioTipoReserva[IDServicioTipoReserva]?>" />
                                      <?php
                                    }
                                    ?>
                                      <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                          <?php
                                            $qry_padre = $dbo->all( "ServicioTipoReserva", " IDServicio = '".$frm[ $key ]."' and IDPadre = 0" );
                                            while ( $r_pade = $dbo->object( $qry_padre ) ): ?>
                                          <option value="<?php echo $r_pade->IDServicioTipoReserva?>" <?php if($r_pade->IDServicioTipoReserva==$EditServicioTipoReserva[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre?></option>
                                          <?php
                                            endwhile;
                                          ?>

                                          <tr>
                                              <td width="26%">Nombre </td>
                                              <td width="74%">
                                                  <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditServicioTipoReserva["Nombre"] ?>" />
                                                  <!--<textarea rows="8" cols="50" id="Nombre" name="Nombre" class="form-control mandatory"><?php echo $EditServicioTipoReserva["Nombre"] ?></textarea>-->
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Numero de turnos seguidos a separar</td>
                                              <td>
                                                  <select name="NumeroTurnos" id="NumeroTurnos" class="form-control" required>
                                                      <option value=""></option>
                                                      <?php for($i=1;$i<=60;$i++): ?>
                                                      <option value="<?php echo $i; ?>" <?php if($i==$EditServicioTipoReserva["NumeroTurnos"]) echo "selected"; ?>><?php echo $i; ?></option>
                                                      <?php endfor; ?>

                                                  </select>
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Cantidad mínima de invitados</td>
                                              <td><select name="MinimoParticipantes" id="MinimoParticipantes" class="form-control" required>
                                                      <option value=""></option>
                                                      <?php for($i=0;$i<=5;$i++): ?>
                                                      <option value="<?php echo $i; ?>" <?php if($i==$EditServicioTipoReserva["MinimoParticipantes"]) echo "selected"; ?>><?php echo $i; ?></option>
                                                      <?php endfor; ?>
                                                  </select></td>
                                          </tr>
                                          <tr>
                                              <td>Cantidad máxima de invitados</td>
                                              <td><select name="MaximoParticipantes" id="MaximoParticipantes" class="form-control" required>
                                                      <option value=""></option>
                                                      <?php for($i=0;$i<=5;$i++): ?>
                                                      <option value="<?php echo $i; ?>" <?php if($i==$EditServicioTipoReserva["MaximoParticipantes"]) echo "selected"; ?>><?php echo $i; ?></option>
                                                      <?php endfor; ?>
                                                  </select></td>
                                          </tr>
                                          <?php if ($permite_auxiliares=="S"){ ?>
                                          <tr>
                                              <td>Si el servicio es con profesor/boleador cuantos máximo se pueden agregar a la reserva?:</td>
                                              <td><select name="MaximoBoleador" id="MaximoBoleador" class="form-control" required>
                                                      <option value=""></option>
                                                      <?php for($i=0;$i<=5;$i++): ?>
                                                      <option value="<?php echo $i; ?>" <?php if($i==$EditServicioTipoReserva["MaximoBoleador"]) echo "selected"; ?>><?php echo $i; ?></option>
                                                      <?php endfor; ?>
                                                  </select></td>
                                          </tr>

                                          <tr>
                                              <td>Si el servicio es con profesor/boleador cuantos mínimo se deben agregar a la reserva?:</td>
                                              <td><select name="MinimoBoleador" id="MinimoBoleador" class="form-control" required>
                                                      <option value=""></option>
                                                      <?php for($i=0;$i<=5;$i++): ?>
                                                      <option value="<?php echo $i; ?>" <?php if($i==$EditServicioTipoReserva["MinimoBoleador"]) echo "selected"; ?>><?php echo $i; ?></option>
                                                      <?php endfor; ?>
                                                  </select></td>
                                          </tr>
                                          <?php } ?>
                                          <tr>
                                              <td width="26%">Validar Edad Tipo Reserva </td>
                                              <td>
                                                  <? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $EditServicioTipoReserva["ValidarEdad"] , 'ValidarEdad' , "class='input'" ) ?>
                                              </td>
                                          </tr>
                                          <tr>
                                              <td width="26%">Edad Minima </td>
                                              <td width="74%">
                                                  <input id="EdadMinima" type="number" size="25" title="EdadMinima" name="EdadMinima" class="input" value="<?php echo $EditServicioTipoReserva["EdadMinima"] ?>" />
                                              </td>
                                          </tr>
                                          <tr>
                                              <td width="26%">Edad Maxima </td>
                                              <td width="74%">
                                                  <input id="EdadMaxima" type="number" size="25" title="EdadMaxima" name="EdadMaxima" class="input" value="<?php echo $EditServicioTipoReserva["EdadMaxima"] ?>" />
                                              </td>
                                          </tr>
                                          <tr>
                                              <td width="26%">Despues de abierto el dia permitir reservas este tipo de reserva despues de: (ej: si el servico se abre a las 8am este tipo de reserva permitir 2 horas despues de abierto el servicio) </td>
                                              <td width="74%">
                                                <input type="number" name="TiempoDespues" id="TiempoDespues" class="col-xs-4 mandatory" title="TiempoDespues" value="<?php echo $EditServicioTipoReserva["TiempoDespues"] ?>">
                                                <select name="MedicionTiempoDespues" id="MedicionTiempoDespues" class="mandatory" title="Opcion Tiempo Despues">
                                                  <option value=""></option>
                                                  <option value="Minutos" <?php if($EditServicioTipoReserva["MedicionTiempoDespues"]=="Minutos") echo "selected";  ?>>Minutos</option>
                                                  <option value="Horas" <?php if($EditServicioTipoReserva["MedicionTiempoDespues"]=="Horas") echo "selected";  ?>>Horas</option>
                                                  <option value="Dias" <?php if($EditServicioTipoReserva["MedicionTiempoDespues"]=="Dias") echo "selected";  ?>>Dias</option>
                                                </select>
                                              </td>
                                          </tr>
                                          <tr>
                                              <td width="26%">Orden (opcional) </td>
                                              <td width="74%"><input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input" value="<?php echo $EditServicioTipoReserva["Orden"] ?>" /></td>
                                          </tr>
                                          <tr>
                                              <td>Activo</td>
                                              <td>
                                                  <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioTipoReserva["Activo"] , 'Activo' , "class='input'" ) ?>
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
                  <th align="center" valign="middle" width="64">Editar</th>
                  <th>Nombre</th>
                  <th># Turnos</th>
                  <th>Orden</th>
                  <th>Activo</th>
                  <th align="center" valign="middle" width="64">Eliminar</th>
              </tr>
              <tbody id="listacontactosanunciante">
                  <?php

                              $r_documento =& $dbo->all( "ServicioTipoReserva" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                  <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                      <td align="center" width="64">
                          <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $_GET[ids] ."&IDServicioTipoReserva=".$r->IDServicioTipoReserva?>&tab=tiporeservas" class="ace-icon glyphicon glyphicon-pencil"></a>
                      </td>
                      <td><?php echo $r->Nombre; ?></td>
                      <td><?php echo $r->NumeroTurnos; ?></td>
                      <td><?php echo $r->Orden; ?></td>
                      <td><?php echo $r->Activo; ?></td>
                      <td align="center" width="64">
                          <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioTipoReserva&ids=<?php echo $_GET[ids];?>&IDServicioTipoReserva=<? echo $r->IDServicioTipoReserva ?>&tab=tiporeservas"></a>
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
