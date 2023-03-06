      <div id="ParametroAcceso">
        <?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
        ?>
        <form name="frmproAcceso" id="frmproAcceso" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
          <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
              <td>


                <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                    <td valign="top">
                      <?php
                      $action = "ModificaParametroAcceso";
                      $_GET[IDParametroAcceso] = $dbo->getFields("ParametroAcceso", "IDParametroAcceso", "IDClub = '" . SIMNet::reqInt("id") . "'");

                      if ($_GET[IDParametroAcceso]) {
                        $EditParametroAcceso = $dbo->fetchAll("ParametroAcceso", " IDParametroAcceso = '" . $_GET[IDParametroAcceso] . "' ", "array");
                        $action = "ModificaParametroAcceso";
                      ?>
                        <input type="hidden" name="IDParametroAcceso" id="IDParametroAcceso" value="<?php echo $EditParametroAcceso[IDParametroAcceso] ?>" />
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
          $qry_padre = $dbo->all("ParametroAcceso", " IDClub = '" . $frm[$key] . "'");
          while ($r_pade = $dbo->object($qry_padre)) : ?>
							<option value="<?php echo $r_pade->IDParametroAcceso ?>" <?php if ($r_pade->IDParametroAcceso == $EditParametroAcceso[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre ?></option>
                        <?php
                      endwhile;
                        ?>
                    </select>
                    </td>
                  </tr>
                  -->

                        <tr>
                          <td colspan="2">

                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                              <tr>
                                <th class="title" colspan="14">Configuracion</th>
                              </tr>
                              <tr>
                                <th>Activo</th>
                                <th>Modulo</th>
                                <th>Icono</th>
                                <th>Nombre</th>
                              </tr>
                              <tbody id="listacontactosanunciante">

                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                  <td aling="center">
                                    <input type="checkbox" name="GrupoFamiliar" id="GrupoFamiliar" <?php if ($EditParametroAcceso["GrupoFamiliar"] == "S") echo "checked"; ?> value="S">
                                  </td>
                                  <td>Registro Grupo Familiar</td>
                                  <td>
                                    <? if (!empty($EditParametroAcceso["IconoFamiliar"])) {
                                      echo "<img src='" . CLUB_ROOT . $EditParametroAcceso[IconoFamiliar] . "' width=55 >";
                                    ?>
                                      <a href="<? echo $script . ".php?action=delfotoacceso&foto=$EditParametroAcceso[IconoFamiliar]&campo=IconoFamiliar"; ?>&id=<?php echo $frm[IDClub]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                    <input name="IconoFamiliar" id=IconoFamiliar class="col-xs-12" title="Icono Familiar" type="file" size="25" style="font-size: 10px">
                                  </td>
                                  <td><input id=NombreFamiliar type=text size=25 name=NombreFamiliar class="col-xs-12" title="Nombre Familiar" value="<?= $EditParametroAcceso["NombreFamiliar"]; ?>"></td>
                                </tr>
                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                  <td aling="center">
                                    <input type="checkbox" name="Invitado" id="Invitado" <?php if ($EditParametroAcceso["Invitado"] == "S") echo "checked"; ?> value="S">

                                  </td>
                                  <td>Registro Invitado individual</td>
                                  <td><? if (!empty($EditParametroAcceso["IconoIndividual"])) {
                                        echo "<img src='" . CLUB_ROOT . "$EditParametroAcceso[IconoIndividual]' width=55 >";
                                      ?>
                                      <a href="<? echo $script . ".php?action=delfotoacceso&foto=$EditParametroAcceso[IconoIndividual]&campo=IconoIndividual"; ?>&id=<?php echo $frm[IDClub]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                      } // END if
                                    ?>
                                    <input name="IconoIndividual" id=IconoIndividual class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                  <td><input id=NombreIndividual type=text size=25 name=NombreIndividual class="col-xs-12" title="Nombre Individual" value="<?= $EditParametroAcceso["NombreIndividual"]; ?>"></td>
                                </tr>


                              </tbody>
                              <tr>
                                <th class="texto" colspan="14"></th>
                              </tr>
                              <tr>
                                <th class="texto" colspan="14"></th>
                              </tr>
                            </table>




                          </td>
                        </tr>

                        <tr>
                          <td width="26%">Tipo Invitado</td>
                          <td width="74%">
                            <?php
                            // $array_tipo_invitado = explode("|", $EditParametroAcceso["TipoInvitado"]);
                            // foreach (SIMResources::$tipoinvitado as $tipo_invitado) : 
                            ?>
                            <!--<input type="checkbox" name="TipoInvitado[]" value="<?php //echo $tipo_invitado; 
                                                                                    ?>" <?php //if (in_array($tipo_invitado, $array_tipo_invitado)) echo "checked"; 
                                                                                        ?>><?php //echo $tipo_invitado; 
                                                                                            ?>-->
                            <?php //endforeach; 
                            ?>
                            <?php
                            $TipoInvitado = $dbo->fetchAll('TipoInvitado', "Publicar = 'S'", "array");
                            $array_tipo_invitado = explode("|", $EditParametroAcceso["TipoInvitado"]);
                            foreach ($TipoInvitado as $tipo_invitado) : ?>
                              <input type="checkbox" name="TipoInvitado[]" value="<?php echo $tipo_invitado['Nombre']; ?>" <?php if (in_array($tipo_invitado['Nombre'], $array_tipo_invitado)) echo "checked"; ?>><?php echo $tipo_invitado['Nombre']; ?>
                            <?php endforeach; ?>

                        </tr>
                        <tr>
                          <td>Tipo Autorizacion</td>
                          <td>
                            <?php
                            $array_tipo_autorizacion = explode("|", $EditParametroAcceso["TipoAutorizacion"]);
                            foreach (SIMResources::$tipoautorizacion as $tipo_autorizacion) : ?>
                              <input type="checkbox" name="TipoAutorizacion[]" value="<?php echo $tipo_autorizacion; ?>" <?php if (in_array($tipo_autorizacion, $array_tipo_autorizacion)) echo "checked"; ?>><?php echo $tipo_autorizacion; ?>
                            <?php endforeach; ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Texto Menor de Edad</td>
                          <td><span class="col-sm-8">
                              <input id=TextoMenorEdad type=text size=25 name=TextoMenorEdad class="col-xs-12" title="TextoMenorEdad" value="<?= $EditParametroAcceso[TextoMenorEdad] ?>">
                            </span></td>
                        </tr>
                        <tr>
                          <td colspan="2" bgcolor="#D7E8D6">GEOREFERENCIACION INVITADOS </td>
                        </tr>
                        <tr>
                          <td>Permitir Invitaciones solo por georeferenciacion?</td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["Georeferenciacion"], "Georeferenciacion", "title=\"Georeferenciacion\"") ?>

                            <div id="div_geo" <?php if ($EditParametroAcceso["Georeferenciacion"] == "N" || empty($EditParametroAcceso["Georeferenciacion"])) echo "style='display:none'";  ?>>

                              Latitud <input id=Latitud type=text size=25 name=Latitud class="input" title="Latitud" value="<?= $EditParametroAcceso[Latitud] ?>">
                              Longitud <input id=Longitud type=text size=25 name=Longitud class="input" title="Longitud" value="<?= $EditParametroAcceso[Longitud] ?>">
                              Rango <input id=Rango type=text size=25 name=Rango class="input" title="Rango" value="<?= $EditParametroAcceso[Rango] ?>">(mts)
                              Mensaje Fuera Rango <input id=MensajeFueraRango type=text size=25 name=MensajeFueraRango class="input" title="Mensaje Fuera Rango" value="<?= $EditParametroAcceso[MensajeFueraRango] ?>">

                            </div>


                          </td>

                        </tr>
                        <tr>
                          <td colspan="2" bgcolor="#D7E8D6">GEOREFERENCIACION CONTRATISTAS</td>
                        </tr>
                        <tr>
                          <td>Permitir Invitaciones solo por georeferenciacion para contratistas?</td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["GeoreferenciacionContratista"], "GeoreferenciacionContratista", "title=\"Georeferenciacion Contratista\"") ?>
                            <div id="div_geo_contratista" <?php if ($EditParametroAcceso["GeoreferenciacionContratista"] == "N" || empty($EditParametroAcceso["GeoreferenciacionContratista"])) echo "style='display:none'";  ?>> Latitud
                              <input id=LatitudContratista type=text size=25 name=LatitudContratista class="input" title="Latitud Contratista" value="<?= $EditParametroAcceso[LatitudContratista] ?>">
                              Longitud
                              <input id=LongitudContratista type=text size=25 name=LongitudContratista class="input" title="Longitud Contratista" value="<?= $EditParametroAcceso[LongitudContratista] ?>">
                              Rango
                              <input id=RangoContratista type=text size=25 name=RangoContratista class="input" title="Rango Contratista" value="<?= $EditParametroAcceso[RangoContratista] ?>">
                              (mts)
                              Mensaje Fuera Rango
                              <input id=MensajeFueraRangoContratista type=text size=25 name=MensajeFueraRangoContratista class="input" title="Mensaje Fuera Rango Contratista" value="<?= $EditParametroAcceso[MensajeFueraRangoContratista] ?>">
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td>Mostrar Terminos Contratista</td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["MostrarTerminosContratista"], "MostrarTerminosContratista", "title=\"MostrarTerminosContratista\"") ?>
                        </tr>

                        <tr>
                          <td>Label Terminos Contratista</td>
                          <td><input id="LabelTerminosContratista" type="text" size=25 width="50%" name="LabelTerminosContratista" class="col-xs-12" title="LabelTerminosContratista" value="<?= $EditParametroAcceso["LabelTerminosContratista"]; ?>"></td>
                        </tr>

                        <tr>
                          <td>Mostrar Hora Inicio y Fin </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["MostrarHoraInicioFinContratista"], "MostrarHoraInicioFinContratista", "title=\"MostrarHoraInicioFinContratista\"") ?>
                        </tr>

                        <tr>
                          <td>Permite Cargar Excel Invitado </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["PermiteCargarExcelInvitado"], "PermiteCargarExcelInvitado", "title=\"PermiteCargarExcelInvitado\"") ?>
                        </tr>

                        <tr>
                          <td>Texto Boton Cargar Excel Invitado</td>
                          <td><input id="TextoBotonCargarExcelInvitado" type="text" size=25 width="50%" name="TextoBotonCargarExcelInvitado" class="col-xs-12" title="TextoBotonCargarExcelInvitado" value="<?= $EditParametroAcceso["TextoBotonCargarExcelInvitado"]; ?>"></td>
                        </tr>

                        <tr>
                          <td>Permite Cargar Excel Contratista </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["PermiteCargarExcelContratista"], "PermiteCargarExcelContratista", "title=\"PermiteCargarExcelContratista\"") ?>
                        </tr>

                        <tr>
                          <td>Texto Boton Cargar Excel Contratista</td>
                          <td><input id="TextoBotonCargarExcelContratista" type="text" size=25 width="50%" name="TextoBotonCargarExcelContratista" class="col-xs-12" title="TextoBotonCargarExcelContratista" value="<?= $EditParametroAcceso["TextoBotonCargarExcelContratista"]; ?>"></td>
                        </tr>

                        <tr>
                          <td>Permite Invitacion Individual Rango Fechas </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["PermiteInvitacionIndividualRangoFechas"], "PermiteInvitacionIndividualRangoFechas", "title=\"PermiteInvitacionIndividualRangoFechas\"") ?>
                        </tr>

                        <tr>
                          <td>Permite Invitacion Contratista Rango Fechas </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["PermiteInvitacionContratistaRangoFechas"], "PermiteInvitacionContratistaRangoFechas", "title=\"PermiteInvitacionContratistaRangoFechas\"") ?>
                        </tr>

                        <tr>
                          <td>Permite Enviar Codigo Qr </td>
                          <td><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $EditParametroAcceso["PermiteEnviarCodigoQr"], "PermiteEnviarCodigoQr", "title=\"PermiteEnviarCodigoQr\"") ?>
                        </tr>


                        <tr>
                          <td>Texto Seleccion Dias Semana</td>
                          <td><input id="LabelSeleccionDiasSemana" type="text" size=25 width="50%" name="LabelSeleccionDiasSemana" class="col-xs-12" title="LabelSeleccionDiasSemana" value="<?= $EditParametroAcceso["LabelSeleccionDiasSemana"]; ?>"></td>

                        </tr>


                        <tr>
                          <td>
                            <br>
                          </td>
                        </tr>

                        <tr>
                          <td>Texto Dias Seleccionados</td>
                          <td><input id="LabelDiasSeleccionados" type="text" size=25 width="50%" name="LabelDiasSeleccionados" class="col-xs-12" title="LabelDiasSeleccionados" value="<?= $EditParametroAcceso["LabelDiasSeleccionados"]; ?>"></td>
                        </tr>

                        <tr>
                          <td>
                            <br>
                          </td>
                        </tr>

                        <tr>
                          <td>Texto Nombre Invitado</td>
                          <td><input id="LabelNombreInvitado" type="text" size=25 width="50%" name="LabelNombreInvitado" class="col-xs-12" title="Texto Nombre Invitado" value="<?= $EditParametroAcceso["LabelNombreInvitado"]; ?>"></td>
                        </tr>

                        <tr>
                          <td>
                            <br>
                          </td>
                        </tr>

                        <tr>
                          <td>Texto Numero Identificacion</td>
                          <td><input id="LabelNumeroIdentificacion" type="text" size=25 width="50%" name="LabelNumeroIdentificacion" class="col-xs-12" title="Texto Numero Identificacion" value="<?= $EditParametroAcceso["LabelNumeroIdentificacion"]; ?>"></td>
                        </tr>

                        <tr>
                          <td align="center">&nbsp;</td>
                        </tr>




                      </table>

                      <div class="form-group first">
                        Terminos Html Contratista
                        <div class="col-sm-12">
                          <?php
                          $oCuerpoQr = new FCKeditor("TerminosHtmlContratista");
                          $oCuerpoQr->BasePath = "js/fckeditor/";
                          $oCuerpoQr->Height = 200;
                          //$oCuerpo->EnterMode = "p";
                          $oCuerpoQr->Value =  $EditParametroAcceso["TerminosHtmlContratista"];
                          $oCuerpoQr->Create();
                          ?>
                        </div>
                      </div>
                      <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                      <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

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

      </div>