	   
                    
                    <div id="timeline-1">
									<div class="row">
										<div class="col-xs-12 col-sm-10 col-sm-offset-1">
											<div class="timeline-container">
												<div class="timeline-label">
													<span class="label label-primary arrowed-in-right label-lg">
														<b>Preguntas y respuestas</b>
													</span>
												</div>
                                                                        
                                            <?      
                                            $sql_pregunta="SELECT * FROM ClasificadoPregunta WHERE IDClasificado = '".$_GET[id]."' Order By IDClasificadoPregunta Desc";
                                            $qry_pregunta=$dbo->query($sql_pregunta);
                                            while($row_pregunta=$dbo->object($qry_pregunta)){
                                                $preguntas[$row_pregunta->IDClasificadoPregunta]=$row_pregunta;	
                                            }
                                            $datos_club = $dbo->fetchAll( "Club", " IDClub = '" . SIMUser::get("club") . "' ", "array" );
                                            if( isset($preguntas) ):?>
                                                <?php foreach($preguntas as $detalle):?>
                                                                        

												<div class="timeline-items">
													<div class="timeline-item clearfix">
														<div class="timeline-info">
                                                        	
															<img alt="<?php echo $datos_club[Nombre]; ?>" src="assets/avatars/avatar2.png" />	
															
															
															<span class="label label-info label-sm"><?php echo substr($detalle->FechaPregunta,10); ?></span>
														</div>

														<div class="widget-box transparent">
															<div class="widget-header widget-header-small">
																<h5 class="widget-title smaller">
																	<a href="#" class="blue">
                                                                   			<?php
																		   
																				$nombre_cliente = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$detalle->IDSocioPregunta . "'" );
																				$apellido_cliente = $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$detalle->IDSocioPregunta . "'" );
																				echo "(socio) " .(isset($nombre_cliente) ? $nombre_cliente . " " . $apellido_cliente : '<em>N/A</em>');;
																		    
																	?>
                                                                    </a>
																	<span class="grey"><?php echo $detalle->Pregunta; ?></span>
																</h5>

																<span class="widget-toolbar no-border">
																	<i class="ace-icon fa fa-clock-o bigger-110"></i>
																	<?php echo $detalle->FechaPregunta; ?>
																</span>

																<span class="widget-toolbar">																	
																	<a href="#" data-action="collapse">
																		<i class="ace-icon fa fa-chevron-up"></i>
																	</a>
																</span>
															</div>

															<div class="widget-body">
																<div class="widget-main">
																	<?php echo utf8_encode($detalle->Respuesta); ?>
																	

																	
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