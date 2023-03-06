      <div id="ServicioElemento">
          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                      <td>


                          <table id="simple-table" class="table table-striped table-bordered table-hover">
                              <tr>
                                  <td valign="top">


                                      <?php
                                        $action = "InsertarServicioElemento";

                                        if ($_GET[IDServicioElemento]) {
                                            $EditServicioElemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $_GET[IDServicioElemento] . "' ", "array");
                                            $action = "ModificaServicioElemento";

                                        ?>
                                          <input type="hidden" name="IDServicioElemento" id="IDServicioElemento" value="<?php echo $EditServicioElemento[IDServicioElemento] ?>" />
                                      <?php
                                        }
                                        ?>
                                      <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                          <tr>
                                              <th colspan="2">ELEMENTOS SERVICIO</th>
                                          </tr>
                                          <!--
                  <tr>
                    <td>Padre</td>
                    <td>
					<select name="IDPadre" id="IDPadre">
                    	<option value="">[Seleccione]</option>
					<?php
                    $qry_padre = $dbo->all("ServicioElemento", " IDServicio = '" . $frm[$key] . "' and IDPadre = 0 Order By IDServicioElemento");
                    while ($r_pade = $dbo->object($qry_padre)) : ?>
							<option value="<?php echo $r_pade->IDServicioElemento ?>" <?php if ($r_pade->IDServicioElemento == $EditServicioElemento[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre ?></option>
                        <?php
                    endwhile;
                        ?>
                    </select>
                    </td>
                  </tr>
                  -->

                                          <tr>
                                              <td width="26%">N&uacute;mero Documento </td>
                                              <td width="74%"><input id="IdentificadorElemento" type="number" size="25" title="Identificador Elemento" name="IdentificadorElemento" class="input" value="<?php echo $EditServicioElemento["IdentificadorElemento"] ?>" />
                                                  <br>Obligatorio si el elemento está presente en otro servicio (por
                                                  ejemplo masajes y peluquería)
                                              </td>
                                          </tr>
                                          <tr>
                                              <td width="26%">Validar los mismos elementos por documento, funciona para reservas en varios elementos y listar la misma información  </td>
                                              <td width="74%">
                                                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditServicioElemento["ValidarIdentificadorElemento"], 'ValidarIdentificadorElemento', "class='input'") ?>

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
                                              <td>Permite Reserva automatica</td>
                                              <td>
                                                  <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditServicioElemento["PermiteReservaAutomatica"], 'PermiteReservaAutomatica', "class='input'") ?>
                                                  <br>(si es una cancha: Esta disponible para reservala automaticamente
                                                  cuando se toma una clase?)
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Orden Reserva</td>
                                              <td><input id="OrdenReserva" type="text" size="25" title="Orden Reserva" name="OrdenReserva" class="input" value="<?php echo $EditServicioElemento["OrdenReserva"] ?>" />
                                                  <br>(si es una cancha: cuando es reserva automatica tomar en cuenta el
                                                  siguiente orden para asignar)

                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Orden Visualizaci&oacute;n</td>
                                              <td><input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input mandatory" value="<?php echo $EditServicioElemento["Orden"] ?>" /></td>
                                          </tr>
                                          <tr>
                                              <td>Colores</td>
                                              <td>Color Letra: <input name="ColorLetra" type="color" value="<?php if (empty($EditServicioElemento["ColorLetra"])) {
                                                                                                                echo "#000000";
                                                                                                            } else {
                                                                                                                echo $EditServicioElemento["ColorLetra"];
                                                                                                            }    ?>" />
                                                  Color Fondo: <input name="ColorFondo" type="color" value="<?php if (empty($EditServicioElemento["ColorFondo"])) {
                                                                                                                echo "#FFFFFF";
                                                                                                            } else {
                                                                                                                echo $EditServicioElemento["ColorFondo"];
                                                                                                            }    ?>" />
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>Foto</td>
                                              <td>
                                                  <?

                                                    if (!empty($EditServicioElemento[Foto])) {
                                                        echo "<img src='" . ELEMENTOS_ROOT . "$EditServicioElemento[Foto]' width=55 >";
                                                    ?>
                                                      <a href="<? echo $script . ".php?mod=" . SIMReg::get("mod")
                                                                    . "&action=delfotoelemento&foto=$EditServicioElemento[Foto]&campo=Foto&ids="
                                                                    . $EditServicioElemento[$key] . "&IDServicioElemento=" . $_GET[IDServicioElemento]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

                                                  <?
                                                    } // END if
                                                    ?>
                                                  <input name="Foto" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">

                                              </td>
                                          </tr>
                                          <?php
                                            $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $_GET["ids"] . "'");
                                            // Si el servicio es una clase y necesita reservar cancha
                                            $id_servicio_cancha = $dbo->getFields("ServicioMaestro", "IDServicioMaestroReservar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                                            if ($id_servicio_cancha > 0) :
                                            ?>

                                              <tr>
                                                  <td>Asignar cancha fija (opcional)</td>
                                                  <td>
                                                      <table class="table table-striped table-bordered table-hover">
                                                          <tr>
                                                              <?php
                                                                $sql_elemento_asociado = "Select * From ServicioElementoAsociado Where IDServicioElementoPrincipal  =  '" . $_GET[IDServicioElemento] . "'";
                                                                $result_elemento_asociado = $dbo->query($sql_elemento_asociado);
                                                                while ($row_elemento_asociado = $dbo->fetchArray($result_elemento_asociado)) :
                                                                    $array_elementos_guardados[] = $row_elemento_asociado["IDServicioElementoSecundario"];
                                                                    $array_datos_elementos_guardados[$row_elemento_asociado["IDServicioElementoSecundario"]] = $row_elemento_asociado;
                                                                endwhile;
                                                                // print_r($array_datos_elementos_guardados);
                                                                // Consulto el servicio del club asociado a este servicio maestro
                                                                $IDServicioCanchaClub  = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '" . $frm[IDClub] . "'");
                                                                // Valido si existe una cancha disponible en el horario de la clase
                                                                // Consulto las modalidades si aplica del elemento
                                                                $sql_elemento_asociado = $dbo->query("select * from ServicioElemento where IDServicio = '" . $IDServicioCanchaClub . "'");
                                                                while ($r = $dbo->object($sql_elemento_asociado)) :
                                                                    // print_r($r);
                                                                ?>
                                                                  <td>
                                                                      <input type="checkbox" name="IDServicioElementoAsociado[]" id="IDServicioElementoAsociado" value="<?php echo $r->IDServicioElemento; ?>" <?php if (in_array($r->IDServicioElemento, $array_elementos_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
                                                                      <br>Hora Inicio<br><input type="time" name="HoraInicio[<?php echo $r->IDServicioElemento ?>]" id="HoraInicio" class="input" title="Hora Inicio" value="<?php echo $array_datos_elementos_guardados[$r->IDServicioElemento][HoraInicio]; ?>"><br>Hora Final<br><input type="time" name="HoraFinal[<?php echo $r->IDServicioElemento ?>]" id="HoraFinal" class="input" title="Hora Final" value="<?php echo $array_datos_elementos_guardados[$r->IDServicioElemento][HoraFinal]; ?>">
                                                                  </td>
                                                              <?php

                                                                    $contador_elementos++;
                                                                    if ($contador_elementos == 4) :
                                                                        echo "</tr><tr>";
                                                                        $contador_elementos = 0;

                                                                    endif;
                                                                endwhile;
                                                                ?>
                                                          </tr>
                                                      </table>
                                                  </td>
                                              </tr>

                                              <?php if ($frm[IDClub] == 7) { ?>
                                                  <tr>
                                                      <td>Entre semana asignar canchas de: </td>
                                                      <td>
                                                          <input type="radio" name="EntreSemanaCancha" value="Corea" <?php if ($EditServicioElemento["EntreSemanaCancha"] == "Corea") echo "checked"; ?>>Corea
                                                          <input type="radio" name="EntreSemanaCancha" value="Lago" <?php if ($EditServicioElemento["EntreSemanaCancha"] == "Lago") echo "checked"; ?>>Lago
                                                      </td>
                                                  </tr>
                                                  <tr>
                                                      <td>Fin Semana asignar canchas de: </td>
                                                      <td>
                                                          <input type="radio" name="FinSemanaCancha" value="Corea" <?php if ($EditServicioElemento["FinSemanaCancha"] == "Corea") echo "checked"; ?>>Corea
                                                          <input type="radio" name="FinSemanaCancha" value="Lago" <?php if ($EditServicioElemento["FinSemanaCancha"] == "Lago") echo "checked"; ?>>Lago
                                                      </td>
                                                  </tr>
                                              <?php } ?>

                                              <tr>
                                                  <td>Valor $</td>
                                                  <td>
                                                      1 persona: <input id="Valor" type="number" size="25" title="Valor" name="Valor" class="input" value="<?php echo $EditServicioElemento["Valor"] ?>" />
                                                      2 personas: <input id="Valor2" type="number" size="25" title="Valor2" name="Valor2" class="input" value="<?php echo $EditServicioElemento["Valor2"] ?>" />
                                                  </td>
                                              </tr>
                                          <?php
                                            endif;
                                            ?>




                                          <tr>
                                              <td>Publicar</td>
                                              <td>
                                                  <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditServicioElemento["Publicar"], 'Publicar', "class='input'") ?>
                                              </td>
                                          </tr>
                                          <tr>
                                              <td align="center">&nbsp;</td>
                                          </tr>
                                      </table>
                                      <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[$key] ?>" />
                                      <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ "IDClub" ]?>" />
                                      <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

                                  </td>
                                  <td valign="top">
                                      <table>
                                          <tr>
                                              <td>
                                                  Modalidad
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>
                                                  <?php
                                                    // Consulto las modalidades si aplica del elemento
                                                    $sql_modalidad_elemento = $dbo->query("select * from ServicioElementoModalidad where IDServicioElemento = '" . $_GET[IDServicioElemento] . "'");
                                                    while ($r_modalidad_elemento = $dbo->object($sql_modalidad_elemento)) {
                                                        $modalidad[] = $r_modalidad_elemento->IDTipoModalidadEsqui;
                                                    }
                                                    $arrayop = array();
                                                    // consulto las modalidades del club
                                                    $query_modalidad = $dbo->query("Select * from TipoModalidadEsqui Where Publicar = 'S' and IDClub = '" . $frm[IDClub] . "' Order by Nombre");
                                                    while ($r = $dbo->object($query_modalidad)) {
                                                        $arraymodalidad[$r->Nombre] = $r->IDTipoModalidadEsqui;
                                                    }
                                                    echo SIMHTML::formCheckGroup($arraymodalidad, $modalidad, "ElementoModalidad[]");
                                                    ?>
                                              </td>
                                          </tr>

                                      </table>

                                      <table>
                                          <tr>
                                              <td>
                                                  Asociar Tipo Reserva
                                              </td>
                                          </tr>
                                          <tr>
                                              <td>
                                                  <?php
                                                    // Consulto los tipos de reserva si aplica del elemento
                                                    $sql_tipo_reserva = $dbo->query("select * from ServicioElementoTipoReserva where IDServicioElemento = '" . $_GET[IDServicioElemento] . "'");
                                                    while ($r_tipo_reserva = $dbo->object($sql_tipo_reserva)) {
                                                        $arraytiporeserva[] = $r_tipo_reserva->IDServicioTipoReserva;
                                                    }
                                                    $arrayop = array();
                                                    // consulto los tipo de reservas del servicio
                                                    $query_tipo_reserva_servicio = $dbo->query("Select * from ServicioTipoReserva Where IDServicio = '" . $_GET["ids"] . "' Order by Nombre");
                                                    while ($r = $dbo->object($query_tipo_reserva_servicio)) {
                                                        $arraytiporeservaservicio[$r->Nombre] = $r->IDServicioTipoReserva;
                                                    }
                                                    echo SIMHTML::formCheckGroup($arraytiporeservaservicio, $arraytiporeserva, "ElementoTipoReserva[]");
                                                    ?>
                                              </td>
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
          <table id="simple-table" class="table table-striped table-bordered table-hover">
              <tr>
                  <th class="title" colspan="15"><?php echo strtoupper("Link") . ": Listado" ?></th>
              </tr>
              <tr>
                  <th align="center" valign="middle" width="64">Editar</th>
                  <th>Elemento</th>
                  <th>Identificacion</th>
                  <th>Publicar</th>
                  <th align="center" valign="middle" width="64">Eliminar</th>
              </tr>
              <tbody id="listacontactosanunciante">
                  <?php

                    $r_documento = &$dbo->all("ServicioElemento", "IDServicio = '" . $frm[$key]  . "' order by IDServicioElemento");

                    while ($r = $dbo->object($r_documento)) {
                    ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                          <td align="center" width="64">
                              <a href="<?php echo "?mod=" . SIMReg::get("mod") . "&action=edit&ids=" . $_GET[ids] . "&IDServicioElemento=" . $r->IDServicioElemento ?>&tab=elementos" class="ace-icon glyphicon glyphicon-pencil"></a>
                          </td>
                          <td><?php echo $r->Nombre; ?></td>
                          <td><?php
                                echo $r->IdentificadorElemento;
                                //echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $r->IDPadre . "'" );

                                ?>

                          </td>
                          <td><?php echo $r->Publicar; ?></td>
                          <td align="center" width="64">
                              <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaServicioElemento&ids=<?php echo $_GET[ids]; ?>&IDServicioElemento=<? echo $r->IDServicioElemento ?>&tab=elementos"></a>
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