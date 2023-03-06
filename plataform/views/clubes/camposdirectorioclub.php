      <div id="CampoDirectorioClub">
              <form name="frmproCampoDirectorioSocio" id="frmproCampoDirectorioSocio" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
                      <table id="simple-table" class="table table-striped table-bordered table-hover">
                              <tr>
                                      <td>


                                              <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                      <tr>
                                                              <td valign="top">


                                                                      <?php
                                                                        $action = "InsertarCampoDirectorioClub";

                                                                        if ($_GET[IDCampoDirectorioClub]) {
                                                                                $EditCampoDirectorioClub = $dbo->fetchAll("CampoDirectorioClub", " IDCampoDirectorioClub = '" . $_GET[IDCampoDirectorioClub] . "' ", "array");
                                                                                $action = "ModificarCampoDirectorioClub";
                                                                        ?>
                                                                              <input type="hidden" name="IDCampoDirectorioClub" id="IDCampoDirectorioClub" value="<?php echo $EditCampoDirectorioClub[IDCampoDirectorioClub] ?>" />
                                                                      <?php
                                                                        }
                                                                        ?>
                                                                      <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                                                              <!--
                  <tr>
                    <td>Padre</td>
                    <td>
					<select name="IDPadre" id="IDPadre">
                    	<option value="">[Seleccione]</option>
					<?php
                                        $qry_padre = $dbo->all("CampoDirectorioClub", " IDClub = '" . $frm[$key] . "'");
                                        while ($r_pade = $dbo->object($qry_padre)) : ?>
							<option value="<?php echo $r_pade->IDCampoDirectorioClub ?>" <?php if ($r_pade->IDCampoDirectorioClub == $EditCampoDirectorioClub[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre ?></option>
                        <?php
                                        endwhile;
                        ?>
                    </select>
                    </td>
                  </tr>
                  -->

                                                                              <tr>
                                                                                      <td width="26%">Nombre </td>
                                                                                      <td width="74%"><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditCampoDirectorioClub["Nombre"] ?>" /></td>
                                                                              </tr>
                                                                              <tr>
                                                                                      <td><br></td>
                                                                              </tr>

                                                                              <tr>
                                                                                      <td width="26%">Tipo: </td>
                                                                                      <td>

                                                                                              <select name="TipoCampo" id="TipoCampo" required>
                                                                                                      <option value=""></option>
                                                                                                      <option value="texto" <?php if ($EditCampoDirectorioClub["TipoCampo"] == "texto") echo "selected"; ?>>Texto</option>
                                                                                                      <option value="telefono" <?php if ($EditCampoDirectorioClub["TipoCampo"] == "telefono") echo "selected"; ?>>Telefono</option>
                                                                                                      <option value="email" <?php if ($EditCampoDirectorioClub["TipoCampo"] == "email") echo "selected"; ?>>Email</option>
                                                                                                      <option value="whatsapp" <?php if ($EditCampoDirectorioClub["TipoCampo"] == "whatsapp") echo "selected"; ?>>whatsapp</option>
                                                                                              </select>
                                                                                      </td>
                                                                              </tr>
                                                                              <tr>
                                                                                      <td><br></td>
                                                                              </tr>
                                                                              <tr>
                                                                                      <td>Descripcion</td>
                                                                                      <td>
                                                                                              <textarea name="Descripcion" id="Descripcion" cols="40" rows="5"><?php echo $EditCampoDirectorioClub["Descripcion"] ?></textarea>
                                                                                      </td>
                                                                              </tr>
                                                                              <tr>
                                                                                      <td>Publicar</td>
                                                                                      <td><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditCampoDirectorioClub["Publicar"], 'Publicar', "class='input'") ?></td>
                                                                              </tr>
                                                                              <tr>
                                                                                      <td align="center">&nbsp;</td>
                                                                              </tr>
                                                                      </table>
                                                                      <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                                                                      <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

                                                              </td>
                                                              <td valign="top">

                                                                      <?php
                                                                        //$action = "InsertarDisponibilidadElemento";
                                                                        ?>

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
                              <th>Descripcion</th>
                              <th>Publicar</th>
                              <th>TipoCampo</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                              <?php

                                $r_documento = &$dbo->all("CampoDirectorioClub", "IDClub = '" . $frm[$key]  . "'");

                                while ($r = $dbo->object($r_documento)) {
                                ?>

                                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                              <td align="center" width="64">
                                                      <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDCampoDirectorioClub=" . $r->IDCampoDirectorioClub ?>&tabclub=parametros&tabparametro=camposdirectorioclub" class="ace-icon glyphicon glyphicon-pencil"></a>
                                              </td>
                                              <td><?php echo $r->Nombre; ?></td>
                                              <td><?php echo $r->Descripcion; ?></td>
                                              <td><?php echo $r->Publicar; ?></td>
                                              <td><?php echo $r->TipoCampo; ?></td>
                                              <td align="center" width="64">
                                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCampoDirectorioClub&id=<?php echo $frm[$key]; ?>&IDCampoDirectorioClub=<? echo $r->IDCampoDirectorioClub ?>&tabclub=parametros&tabparametro=camposdirectorioclub"></a>
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