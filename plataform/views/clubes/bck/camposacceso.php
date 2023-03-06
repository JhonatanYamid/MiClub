<div id="CampoDirectorioClub">
  <form name="frmproCampoAcceso" id="frmproCampoAcceso" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td>Correos notificar cuando no se cumpla el liímite</td>
        <td><input type="text" id="CorreoAlertaCampoAcceso" name="CorreoAlertaCampoAcceso" placeholder="Correo Alerta" class="col-xs-12 mandatory" title="Correo Alerta" value="<?php echo $frm["CorreoAlertaCampoAcceso"]; ?>" ></td>


        <td align="center">
          <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
          <input type="hidden" name="action" id="action" value="InsertCorreoAlertaAcceso" />
          <input type="submit" class="submit" value="Agregar">
        </td>

      </tr>
    </table>
  </form>

<form name="frmproCampoAcceso" id="frmproCampoAcceso" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">


  <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
              <td valign="top">


                <?php
                        $action = "InsertarPregunta";
                        if( $_GET["IDPreguntaAcceso"] )
                        {
                                $EditPregunta =$dbo->fetchAll("PreguntaAcceso"," IDPreguntaAcceso = '".$_GET["IDPreguntaAcceso"]."' ","array");
                                $action = "ModificaPregunta";
                                ?>
                                <input type="hidden" name="IDPreguntaAcceso" id="IDPreguntaAcceso" value="<?php echo $EditPregunta["IDPreguntaAcceso"]?>" />
                                <?php
                        }
                        ?>

                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                  <tr>
                    <td>Pregunta </td>
                    <td><input type="text" id="Nombre" name="EtiquetaCampo" placeholder="EtiquetaCampo" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditPregunta["EtiquetaCampo"]; ?>" ></td>


                    <td>Tipo Respuesta</td>
                    <td>
                      <select class="form-control" id="TipoCampo" name="TipoCampo">
                                            <optgroup label="Estándar">
                                            <option value="text" <?php if($EditPregunta["TipoCampo"]=="text") echo "selected"; ?>>Texto en una línea</option>
                                            <option value="textarea" <?php if($EditPregunta["TipoCampo"]=="textarea") echo "selected"; ?>>Texto en párrafo</option>
                                            <option value="radio" <?php if($EditPregunta["TipoCampo"]=="radio") echo "selected"; ?>>Múltiples opciones</option>
                                            <option value="checkbox" <?php if($EditPregunta["TipoCampo"]=="checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                            <option value="select" <?php if($EditPregunta["TipoCampo"]=="select") echo "selected"; ?>>Menú desplegable</option>
                                            <option value="number" <?php if($EditPregunta["TipoCampo"]=="number") echo "selected"; ?>>Número</option>
                                            <!--<option value="page">Page Break</option>-->
                                            </optgroup>
                                            <optgroup label="Elegantes">
                                            <option value="date" <?php if($EditPregunta["TipoCampo"]=="date") echo "selected"; ?>>Fecha</option>
                                            <option value="time" <?php if($EditPregunta["TipoCampo"]=="time") echo "selected"; ?>>Hora</option>
                                            <option value="email" <?php if($EditPregunta["TipoCampo"]=="email") echo "selected"; ?>>Correo electrónico</option>
                                            </optgroup>
                                            <optgroup label="Titulo">
                                            <option value="titulo" <?php if($EditPregunta["TipoCampo"]=="titulo") echo "selected"; ?>>Titulo</option>

                                          </select>
                    </td>
                  </tr>

                  <tr>
                    <td >Opciones de respuesta (separados por coma) </td>
                    <td ><textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPregunta["Valores"]; ?></textarea></td>
                    <td>Orden</td>
                    <td>
                    <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPregunta["Orden"]; ?>" >
                    </td>
                  </tr>

                  <tr>
                    <td >Obligatorio </td>
                    <td ><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPregunta["Obligatorio"] , 'Obligatorio' , "class='input mandatory'" ) ?></td>
                    <td>Si es numérico cual es el Límite permitido?</td>
                    <td>
                    <input type="number" id="Limite" name="Limite" placeholder="Limite" class="col-xs-12" title="Limite" value="<?php echo $EditPregunta["Limite"]; ?>" >
                    </td>
                  </tr>

                  <tr>
                    <td >Publicar </td>
                    <td ><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPregunta["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></td>
                    <td></td>
                    <td>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="4" align="center">
                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                    <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                    <input type="submit" class="submit" value="Agregar">
                   </td>

                  </tr>


                  </table>


                </td>
                <td valign="top">

                  <?php
                  //$action = "InsertarDisponibilidadElemento";
                  ?>

              </td>
            </tr>
        </table>






					</form>

</div>








              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>Tipo</th>
                              <th>Obligatorio</th>
                              <th>Orden</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "PreguntaAcceso" , "IDClub = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                              <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDPreguntaAcceso=".$r->IDPreguntaAcceso?>&tabclub=parametros&tabparametro=camposacceso" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->EtiquetaCampo; ?></td>
                              <td><?php echo $r->TipoCampo; ?></td>
                              <td><?php echo $r->Obligatorio; ?></td>
                              <td><?php echo $r->Orden; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                              <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaPregunta&id=<?php echo $frm[$key];?>&IDPreguntaAcceso=<? echo $r->IDPreguntaAcceso ?>&tabclub=parametros&tabparametro=camposacceso" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>
