<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Postulado </label>
										<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>
										<div class="col-sm-8"><input type="text" id="Tipo" name="Tipo" placeholder="Socio Nuevo" class="col-xs-12 mandatory" title="Tipo" value="<?php echo $frm["Tipo"];?>"></div>
								</div>
							</div>

							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vuelta </label>
										<div class="col-sm-8"><input type="text" id="Vuelta" name="Vuelta" placeholder="Primera Vuelta, Segunda Vuelta" class="col-xs-12" title="Vuelta" value="<?php echo $frm["Vuelta"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Resumen </label>
										<div class="col-sm-8">
											<textarea id="Resumen" name="Resumen" cols="10" rows="5" class="col-xs-12 mandatory" title="Resumen"><?php echo $frm["Resumen"]; ?></textarea>
											</div>
								</div>
							</div>



							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto Postulado </label>
										 <input name="Imagen" id=file class="" title="Imagen" type="file" size="25" style="font-size: 10px">
										<div class="col-sm-8">
											<? if (!empty($frm["Imagen"])) {
												echo "<img src='".SOCIO_ROOT.$frm["Imagen"]."' width=40 height=40 >";
												?>
                                              <a href="<? echo $script.".php?action=delfoto&foto=$frm[Imagen]&campo=Imagen&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											  <?
											}// END if
											?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf adjunto </label>

										<div class="col-sm-8">
											<?php
											$ruta_adjunto1file = string;
                                            if($frm["Adjunto1File"])
                                            {

                                                if(strstr(strtolower($frm["Adjunto1File"]),"http://"))
                                                    $ruta_adjunto1file = $frm["Adjunto1File"];
                                                else
                                                    $ruta_adjunto1file = SOCIO_ROOT.$frm["Adjunto1File"];
                                                ?>
                                                <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto1File"] ?></a>
                                                <a href="<? echo $script.".php?action=DelDocNot&cam=Adjunto1File&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                            <?php
                                            }
                                            else
                                            {
                                            ?>
                                            <input type="file" name="Adjunto1File" id="Adjunto1File" class="popup" title="Noticia Documento">
                                            <?php
                                            }
                                            ?>
										</div>
								</div>


							</div>


							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

										<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "Publicar" , "title=\"Publicar\"" )?></div>
								</div>

							</div>

							<div class="widget-header widget-header-large">
                    <h3 class="widget-title grey lighter">
                        <i class="ace-icon fa fa-glass green"></i>
                    Núcleo familiar
                    </h3>
              </div>
							<div  class="form-group first ">
														<div  class="col-xs-12 col-sm-12">
						                          <table id="simple-table" class="table table-striped table-bordered table-hover">
						                                          <tr>
						                                            <th>Nombre</th>
			                                                  <th>Patentesco</th>
			                                                  <th>Foto</th>

						                                          </tr>
						                                          <tbody id="listacontactosanunciante">

																												<?php for($i=1;$i<=6;$i++){ ?>

																													<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
								                                            <td aling="center" >
																																<input type="text" id="NombreBeneficiario<?php echo $i; ?>" name="NombreBeneficiario<?php echo $i; ?>" placeholder="Nombre Beneficiario <?php echo $i; ?>" class="col-xs-12 " title="NombreBeneficiario<?php echo $i; ?>" value="<?php echo $frm["NombreBeneficiario".$i];?>">
								                                            </td>
								                                             <td>
																															 	<input type="text" id="ParentescoBeneficiario<?php echo $i; ?>" name="ParentescoBeneficiario<?php echo $i; ?>" placeholder="Parentesco Beneficiario <?php echo $i; ?>" class="col-xs-12 " title="ParentescoBeneficiario<?php echo $i; ?>" value="<?php echo $frm["ParentescoBeneficiario".$i];?>">
								                                              </td>
								                                                  <td>
																																		<input name="ImagenBeneficiario<?php echo $i; ?>" id=file class="" title="Imagen" type="file" size="25" style="font-size: 10px">

																							 											<? if (!empty($frm["ImagenBeneficiario".$i])) {
																							 												echo "<img src='".SOCIO_ROOT.$frm["ImagenBeneficiario".$i]."' width=40 height=40>";
																							 												?>
																							                         <a href="<? echo $script.".php?action=delfoto&foto=".$frm[ImagenBeneficiario.$i]."&campo=ImagenBeneficiario".$i."&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
																							 											  <?
																							 											}// END if
																							 											?>
																																	</td>
								                                            </tr>

																												<?php } ?>


						                                          </tbody>
						                                  </table>
							  </div>
							</div>

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
								</div>
							</div>
                           </form>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>
