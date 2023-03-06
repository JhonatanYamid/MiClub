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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Area </label>

										<div class="col-sm-8">
											<select name = "IDArea" id="IDArea"  >
                                        	<option value=""></option>
                                        <?php 
										$sql_area_club = string;
										$sql_area_club = "Select * From Area Where IDClub = '".SIMUser::get("club")."' order by Nombre";										
										$qry_area_club = $dbo->query($sql_area_club);
										while ($r_area = $dbo->fetchArray($qry_area_club)): ?>
											<option value="<?php echo $r_area["IDArea"]; ?>" <?php if($r_area["IDArea"]==$frm["IDArea"]) echo "selected";  ?>><?php echo $r_area["Nombre"]; ?></option>
                                        <?php
										 	endwhile;    ?>
                                        </select>    
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-8">
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
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>
                                            
                                          	<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if($_GET["action"]!= "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"]) ?>" >
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
										$sql_tipopqr_club = "Select * From TipoPqr Where IDClub = '".SIMUser::get("club")."'";
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
										  <input type="text" id="Asunto" name="Asunto" placeholder="Asunto" class="col-xs-12 mandatory" title="Asunto" value="<?php echo utf8_encode($frm["Asunto"]); ?>" <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?> >
										</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>
										 
										<div class="col-sm-8">
											<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"  <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?>><?php echo utf8_encode($frm["Descripcion"]); ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label>

										<div class="col-sm-8">
										  <input type="text" id="Fecha" name="Fecha" placeholder="Fecha" class="col-xs-12 <?php if($_GET["action"]!= "add") echo ""; else echo "calendar"; ?>" title="Fecha" value="<?php echo $frm["Fecha"] ?>" <?php if($_GET["action"]!= "add") echo "readonly='readonly'"; ?>>
										</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>
										 
										<div class="col-sm-8"><?php echo SIMHTML::formPopUp( "PqrEstado" , "Nombre" , "Nombre" , "IDPqrEstado" , $frm["IDPqrEstado"] , "[Seleccione el estado]" , "popup mandatory" , "title = \"IDTipo Archivo\"" )?></div>
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
                            
                            <div  class="form-group first">
										Agregar Respuesta									 

										<div class="col-sm-12">
											<?php
												$oCuerpo = new FCKeditor( "Cuerpo" ) ;
												$oCuerpo->BasePath = "js/fckeditor/";
												$oCuerpo->Height = 400;
												//$oCuerpo->EnterMode = "p";
												$oCuerpo->Value =  $frm["Cuerpo"];
												$oCuerpo->Create() ;
											?>		
										</div>
								
							</div>
                            
                             <div  class="form-group first">
										<div class="col-sm-12">
                                            <input type="checkbox" name="NotificarCliente" id="NotificarCliente" <?php if($frm["IDArea"]!="0"){  ?> checked="checked" <?php } ?> value="S" />
                                          <b>Notificar v&iacute;a email al Cliente la respuesta</b>
										</div>
							</div>

                            
                            

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>

									
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
																	<?php echo $detalle->Respuesta; ?>
																	

																	
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