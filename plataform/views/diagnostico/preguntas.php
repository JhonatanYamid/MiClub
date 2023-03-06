<form class="form-horizontal formvalida" role="form" method="post" id="EditPregunta<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

				  <?php
                  $action = "InsertarPregunta";
                  if( $_GET["IDPreguntaDiagnostico"] )
                  {
                          $EditPregunta =$dbo->fetchAll("PreguntaDiagnostico"," IDPreguntaDiagnostico = '".$_GET["IDPreguntaDiagnostico"]."' ","array");
                          $action = "ModificaPregunta";
                          ?>
                          <input type="hidden" name="IDPreguntaDiagnostico" id="IDPreguntaDiagnostico" value="<?php echo $EditPregunta[IDPreguntaDiagnostico]?>" />
                          <?php
                  }
                  ?>



							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pregunta </label>
								  <div class="col-sm-8">
								    <input type="text" id="Nombre" name="EtiquetaCampo" placeholder="EtiquetaCampo" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditPregunta["EtiquetaCampo"]; ?>" >

                                        </div>
								</div>

									<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de respuesta </label>

										<div class="col-sm-8">
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
										</div>
								</div>

							</div>





							<div  class="form-group first ">



	                <div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden </label>

											<div class="col-sm-8">
											  <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPregunta["Orden"]; ?>" >
											</div>
									</div>

									<div  class="col-xs-12 col-sm-6">
			 							<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio </label>

			 							<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPregunta["Obligatorio"] , 'Obligatorio' , "class='input mandatory'" ) ?></div>
	 								</div>


								</div>

								<div  class="form-group first ">



									<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

													<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditPregunta["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></div>
									</div>




									</div>




								<div  class="col-xs-12 col-sm-12">
									Opciones de respuesta para cuando sea de multiple respuesta o seleccion
                        <table id="simple-table" class="table table-bordered table-hover">
												<tr>
													<td>Opcion Respuesta</td>
													<td>Terminar Encuesta?</td>
													<!--<td>Ir a pregunta</td>-->
													<td>Peso</td>
													<td>Orden</td>
												</tr>
												<?php
												$sql_opciones="SELECT * FROM DiagnosticoOpcionesRespuesta WHERE IDDiagnosticoPregunta = '".$_GET["IDPreguntaDiagnostico"]."' Order by Orden";
												$r_opciones=$dbo->query($sql_opciones);
												$contador=1;
												while($row_opciones=$dbo->fetchArray($r_opciones)){
													$array_opciones[$contador]=$row_opciones;
													$contador++;
												}
												ksort($array_opciones);
												$CantidadOpciones=15;
												for($i=1;$i<=$CantidadOpciones;$i++){
													 $valor_opciones=$array_opciones[$i];
													?>
													<tr>
														<td><textarea rows="2" cols="50"  name="Respuesta<?php echo $i; ?>"><?php echo $valor_opciones["Opcion"]; ?></textarea></td>
														<td><input type="radio" name="Terminar<?php echo $i; ?>" value="S" <?php if($valor_opciones["Terminar"]=="S") echo "checked"; ?>>Si<input type="radio" name="Terminar<?php echo $i; ?>" value="N" <?php if($valor_opciones["Terminar"]=="N") echo "checked"; ?>></td>
														<!--<td></td>-->
														<td><input type="text" name="Peso<?php echo $i; ?>" value="<?php echo $valor_opciones["Peso"]; ?>"></td>
														<td><input type="text" name="Orden<?php echo $i; ?>" value="<?php echo $valor_opciones["Orden"]; ?>"></td>
													</tr>
												<?php } ?>

											</table>
								</div>








							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                  <input type="hidden" name="ID"  id="ID" value="<?php echo $EditPregunta[ $key ] ?>" />
									<input type="hidden" name="CantidadOpciones"  id="CantidadOpciones" value="<?php echo $CantidadOpciones ?>" />
                	<input type="hidden" name="IDDiagnostico"  id="IDDiagnostico" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<input type="submit" class="submit" value="Guardar">
                                      <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditPregunta[ $key ]?>" />
                 			 <input type="hidden" name="action" id="action" value="<?php echo $action?>" />


								</div>
							</div>




					</form>










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

                              $r_documento =& $dbo->all( "PreguntaDiagnostico" , "IDDiagnostico = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDPreguntaDiagnostico=".$r->IDPreguntaDiagnostico?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->EtiquetaCampo; ?></td>
                              <td><?php echo $r->TipoCampo; ?></td>
                              <td><?php echo $r->Obligatorio; ?></td>
                              <td><?php echo $r->Orden; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                            	<a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaPregunta&id=<?php echo $frm[$key];?>&IDPreguntaDiagnostico=<? echo $r->IDPreguntaDiagnostico ?>&tabpregunta=formulario" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="16"></th>
                      </tr>
              </table>
