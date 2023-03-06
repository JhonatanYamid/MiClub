<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>

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
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero Pqr </label>

										<div class="col-sm-8">
											<?php echo $frm["Numero"]; ?>
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Area </label>

										<div class="col-sm-8">
											<select name = "IDArea" id="IDArea"  >
                                        	<option value=""></option>
                                        <?php
										$sql_area_club = string;
										$sql_area_club = "Select * From Area Where IDClub = '".SIMUser::get("club")."' and Activo = 'S' order by Nombre";
										$qry_area_club = $dbo->query($sql_area_club);
										while ($r_area = $dbo->fetchArray($qry_area_club)): ?>
											<option value="<?php echo $r_area["IDArea"]; ?>" <?php if($r_area["IDArea"]==$frm["IDArea"]) echo "selected";  ?>><?php echo $r_area["Nombre"]; ?></option>
                                        <?php
										 	endwhile;    ?>
                                        </select>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-12">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-6">
										  <!--
                                          <select name = "IDSocio" id="IDSocio" <?php if($_GET["action"]!= "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
										$sql_socio_club = "Select * From Socio Where IDClub = '".SIMUser::get("club")."' Order by Apellido Asc";
										$qry_socio_club = $dbo->query($sql_socio_club);
										while ($r_socio = $dbo->fetchArray($qry_socio_club)): ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if($r_socio["IDSocio"]==$frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " .$r_socio["Nombre"]); ?></option>
										    <?php
										 	endwhile;    ?>
									      </select>
                                          -->
                                          	<?php
										$sql_socio_club = "Select * From Socio Where IDSocio = '".$frm["IDSocio"]."'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club);
										if(!empty($frm["IDSocio"])){
											$label_accion=" Accion: " . $r_socio["Accion"];
											if($frm[IDClub]==35)
												$label_accion=" Casa: " . $r_socio["Predio"];
										}
										?>

                                          	<input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if($_GET["action"]!= "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " .$r_socio["Nombre"] . $label_accion ?>" >
																						Busqueda por: Accion, Nombre, Apellido, Numero Documento
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

										<div class="col-sm-8">
											<select name = "IDTipoPqr" id="IDTipoPqr" <?php if($_GET["action"]!= "add" && SIMUser::get("IDPerfil") > 1 ) echo "disabled"; ?>>
                                        	<option value=""></option>
                                        <?php
										$sql_tipopqr_club = "Select * From TipoPqr Where IDClub = '".SIMUser::get("club")."' and Publicar = 'S'";
										$qry_tipopqr_club = $dbo->query($sql_tipopqr_club);
										while ($r_tipopqr = $dbo->fetchArray($qry_tipopqr_club)): ?>
											<option value="<?php echo $r_tipopqr["IDTipoPqr"]; ?>" <?php if($r_tipopqr["IDTipoPqr"]==$frm["IDTipoPqr"]) echo "selected";  ?>><?php echo $r_tipopqr["Nombre"]; ?></option>
                                        <?php
										 	endwhile;    ?>
                                        </select>
                                         </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Asunto </label>

										<div class="col-sm-8">
											<?php
											//para condado como no se pide asunto le pongo un espacio
											if($frm["IDClub"]==51){
												$asunto_pqr="  ";
											}else{
												$asunto_pqr=$frm["Asunto"];
											}

											?>
										  <input type="text" id="Asunto" name="Asunto" placeholder="Asunto" class="col-xs-12 mandatory" title="Asunto" value="<?php echo $asunto_pqr; ?>" <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?> >
										</div>
								</div>

							</div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

										<div class="col-sm-8">
											<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"  <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?>><?php echo $frm["Descripcion"]; ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label>

										<div class="col-sm-8">
										  <!--<input type="text" id="Fecha" name="Fecha" placeholder="Fecha" class="col-xs-12 <?php if($_GET["action"]!= "add") echo ""; else echo "calendar"; ?>" title="Fecha" value="<?php echo $frm["Fecha"] ?>" <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?>>-->
											<input type="text" id="Fecha" name="Fecha" placeholder="Fecha" class="col-xs-12 <?php if($_GET["action"]!= "add") echo ""; else echo ""; ?>" title="Fecha" value="<?php  if(!empty($frm["Fecha"])) echo $frm["Fecha"]; else echo date("Y-m-d"); ?>" readonly='readonly'>
											 <?php if($_GET["action"]!= "add"){ ?>
											<br>
											<input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 <?php if($_GET["action"]!= "add") echo ""; else echo ""; ?>" title="Telefono" value="<?php  if(!empty($r_socio["Celular"]) || !empty($r_socio["Telefono"])) echo "Telefono: " . $r_socio["Telefono"] .  " - " . $r_socio["Celular"]; else ""; ?>" readonly='readonly'>
											<?php } ?>
										</div>


								</div>

							</div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>

										<div class="col-sm-8"><?php echo SIMHTML::formPopUp( "PqrEstado" , "Nombre" , "Nombre" , "IDPqrEstado" , $frm["IDPqrEstado"] , "[Seleccione el estado]" , "form-control" , "title = \"IDTipo Archivo\"" )?></div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Archivo </label>
										<div class="col-sm-8">

											 <? if (!empty($frm[Archivo1])) { ?>
                                                <a target="_blank" href="<?php echo PQR_ROOT.$frm[Archivo1] ?>"><?php echo $frm[Archivo1]; ?></a>
                                                    <a href="<? echo $script.".php?action=delDoc&doc=$frm[Archivo1]&campo=Archivo1&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                <?
                                                }// END if
                                                ?>
                                                <input name="Archivo1" id=file class="" title="Archivo1" type="file" size="25" style="font-size: 10px">
										</div>
								</div>

							</div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Medio por la que se recibió </label>

                                    <div class="col-sm-8"><?php echo SIMHTML::formPopUp( "PqrMedio" , "Nombre" , "Nombre" , "IDPqrMedio" , $frm["IDPqrMedio"] , "[Seleccione el medio]" , "form-control" , "title = \"Tipo Medio\"" )?></div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha pr&oacute;ximo seguimiento </label>

										<div class="col-sm-8">
											<!--<input type="text" id="Fecha" name="Fecha" placeholder="Fecha" class="col-xs-12 <?php if($_GET["action"]!= "add") echo ""; else echo "calendar"; ?>" title="Fecha" value="<?php echo $frm["Fecha"] ?>" <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?>>-->
											<input type="text" id="FechaSeguimiento" name="FechaSeguimiento" placeholder="Fecha Seguimiento" class="col-xs-12 calendar" title="FechaSeguimiento" value="<?php  if(!empty($frm["FechaSeguimiento"]) && $frm["FechaSeguimiento"]!="0000-00-00") echo $frm["FechaSeguimiento"]; else echo ""; ?>" readonly='readonly'>
										</div>
								</div>



							</div>


							<?php
							// Si es el condado muestro estos datos
							if(SIMUser::get("club")=="51"){ ?>

								<div  class="form-group first ">



												<div  class="col-xs-12 col-sm-6">
																						<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Colaborador: </label>

																						<div class="col-sm-8"><?php echo $frm["NombreColaborador"] . " " . $frm["ApellidoColaborador"]; ?></div>
												</div>

								</div>

							<?php } ?>




							<div  class="form-group first ">



											<div  class="col-xs-12 col-sm-6">
																					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Creada por: </label>

																					<div class="col-sm-8"><?php echo $frm["UsuarioTrCr"]; ?></div>
											</div>

											<div  class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Calificación:(1 sobre 5) </label>

					<div class="col-sm-8"><?php echo "<strong>".$frm["Calificacion"] . "</strong> " . $frm["ComentarioCalificacion"]; ?></div>
			</div>



							</div>

								<?php if($permiso_escritura!="L"){ ?>
									<div  class="form-group first">
										Agregar Respuesta

										<div class="col-sm-12">
											<?php
												$respuesta_predeterminada=$dbo->getFields( "Club" , "RespuestaPqr" , "IDClub = '" . $frm[IDClub] . "'" );
												$respuesta_predeterminada = str_replace("[FechaActual]",date("Y-m-d"),$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[NombreSocio]",$r_socio["Apellido"] . " " .$r_socio["Nombre"],$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[FechaPqr]",substr($frm["Fecha"],0,10),$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[HoraPqr]",substr($frm["Fecha"],12),$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[TicketPqr]",$frm["Numero"],$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[AsuntoPqr]",$frm["Asunto"],$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[CasoPqr]",$frm["Descripcion"],$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[NombreUsuario]",$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .SIMUser::get("IDUsuario") . "'" ),$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[AccionSocio]",$r_socio["Accion"],$respuesta_predeterminada);
												$respuesta_predeterminada = str_replace("[CorreoUsuario]",$dbo->getFields( "Area" , "CorreoResponsable" , "IDArea = '" .$frm["IDArea"]. "'" ),$respuesta_predeterminada);


												$oCuerpo = new FCKeditor( "Cuerpo" ) ;
												$oCuerpo->BasePath = "js/fckeditor/";
												$oCuerpo->Height = 400;
												//$oCuerpo->EnterMode = "p";
												$oCuerpo->Value =  $respuesta_predeterminada;
												$oCuerpo->Create() ;
											?>
										</div>
							</div>
						<?php } ?>

						<div  class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Adjuntar archivo </label>
								<div class="col-sm-8">
										<input name="ArchivoRespuesta" id=file class="" title="ArchivoRespuesta" type="file" size="25" style="font-size: 10px">
								</div>
						</div>

                             <div  class="form-group first">
										<div class="col-sm-12">
                                            <input type="checkbox" name="NotificarCliente" id="NotificarCliente" <?php if($frm["IDArea"]!="0"){  ?> checked="checked" <?php } ?> value="S" />
                                          <b>Notificar v&iacute;a email al Cliente la respuesta</b>

																					Notificar tambien al area: <select name = "IDAreaInteres" id="IDAreaInteres"  >
										                                        	<option value=""></option>
										                                        <?php
																				$sql_area_club = string;
																				$sql_area_club = "Select * From Area Where IDClub = '".SIMUser::get("club")."' order by Nombre";
																				$qry_area_club = $dbo->query($sql_area_club);
																				while ($r_area = $dbo->fetchArray($qry_area_club)): ?>
																					<option value="<?php echo $r_area["IDArea"]; ?>"><?php echo $r_area["Nombre"]; ?></option>
										                                        <?php
																				 	endwhile;    ?>
										                                        </select>
										</div>
							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get( "title" ); ?>" />
                                    <input type="hidden" name="IDAreaAnt" id="IDAreaAnt" value="<?php echo $frm["IDArea"]; ?>" />
																		<input type="hidden" name="FechaSeguimientoAnt" id="FechaSeguimientoAnt" value="<?php echo $frm["FechaSeguimiento"]; ?>" />
                                    <input type="hidden" name="IDPqrEstadoAnt" id="IDPqrEstadoAnt" value="<?php echo $frm["IDPqrEstado"]; ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />

									<?php if($permiso_escritura!="L"){ ?>
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
									<? } ?>


								</div>
							</div>

					</form>




                    <div id="timeline-1">
									<div class="row">
										<div class="col-xs-12 col-sm-10 col-sm-offset-1">
											<div class="timeline-container">
												<div class="timeline-label">
													<span class="label label-primary arrowed-in-right label-lg">
														<b>Bit&aacute;cora de Seguimiento</b>
													</span>
												</div>

                                            <?
                                            $sql_detalle="SELECT * FROM Detalle_Pqr WHERE IDPQR = '".$_GET[id]."' Order By 	IDDetallePqr Desc";
                                            $qry_detalle=$dbo->query($sql_detalle);
                                            while($row_detalle=$dbo->object($qry_detalle)){
                                                $detalles[$row_detalle->IDDetallePqr]=$row_detalle;
                                            }
                                            $datos_club = $dbo->fetchAll( "Club", " IDClub = '" . SIMUser::get("club") . "' ", "array" );
                                            if( isset($detalles) ):?>
                                                <?php foreach($detalles as $detalle):?>


												<div class="timeline-items">
													<div class="timeline-item clearfix">
														<div class="timeline-info">
                                                        	<?php if ($detalle->IDUsuario > 0) { ?>
																	<img alt="<?php echo $datos_club[Nombre]; ?>" src="<?php echo CLUB_ROOT.$datos_club[FotoLogoApp] ?>" />
																  <?php
                                                                  } elseif($detalle->IDSocio > 0) { ?>
																	<img alt="<?php echo $datos_club[Nombre]; ?>" src="assets/avatars/avatar2.png" />
																  <?php } ?>

															<span class="label label-info label-sm"><?php echo substr($detalle->FechaTrCr,10); ?></span>
														</div>

														<div class="widget-box transparent">
															<div class="widget-header widget-header-small">
																<h5 class="widget-title smaller">
																	<a href="#" class="blue">
                                                                    <?php if ($detalle->IDUsuario > 0) {
																				$nombre_responsable =  $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$detalle->IDUsuario . "'" );
																				echo (isset($nombre_responsable) ? $nombre_responsable : '<em>N/A</em>');
																		   } elseif($detalle->IDSocio > 0) {
																				$nombre_cliente = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$detalle->IDSocio . "'" );
																				$apellido_cliente = $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$detalle->IDSocio . "'" );
																				echo "(socio) " .(isset($nombre_cliente) ? $nombre_cliente . " " . $apellido_cliente : '<em>N/A</em>');
																		   }
																	?>
                                                                    </a>
																	<span class="grey">agreg&oacute; un comentario</span>
																</h5>

																<span class="widget-toolbar no-border">
																	<i class="ace-icon fa fa-clock-o bigger-110"></i>
																	<?php echo $detalle->Fecha; ?>
																</span>

																<span class="widget-toolbar">
																	<a href="#" data-action="reload">
																		<i class="ace-icon fa fa-refresh"></i>
																	</a>

																	<a href="#" data-action="collapse">
																		<i class="ace-icon fa fa-chevron-up"></i>
																	</a>
																</span>
															</div>

															<div class="widget-body">
																<div class="widget-main">
																	<?php echo $detalle->Respuesta;
																	if(!empty($detalle->Archivo)){ ?>
																				<br><a target="_blank" href="<?php echo PQR_ROOT.$detalle->Archivo ?>"><?php echo $detalle->Archivo; ?></a>

																<?php	} ?>



																	<div class="space-6"></div>

																	<div class="widget-toolbox clearfix">

																	</div>
																</div>
															</div>
														</div>
													</div>





												</div><!-- /.timeline-items -->

                                                <?php
										endforeach;
									 endif;
									?>
											</div><!-- /.timeline-container -->


										</div>
									</div>
								</div>









				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>
