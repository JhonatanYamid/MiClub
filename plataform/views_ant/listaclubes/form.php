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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>
									
							</div>

						

						
                            
                         
                            
                            
                            
                            
                          
                            
                          
                            
                             <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-globe green"></i>
                                 Clubes con convenio para canjes
                                </h3>
                            </div>
                            
                            
                            <div  class="form-group first ">

							  <div  class="col-xs-12 col-sm-12">
										

										<div class="col-sm-12">
                                        
                                       <?php 
												  // Consulto del listado de clubes del club
												  $sql_clubes_canje=$dbo->query("select * from DetalleClubCanje where IDClub = '".SIMUser::get("club")."' and IDCLubCanje = '".$frm[ $key ]."'");
												  while($r_club_canje=$dbo->object($sql_clubes_canje)){
													  $club_canje[]=$r_club_canje->IDClub;
												  }
												  $arrayop = array();
												  // consulto los clubes
												  $query_listaclubes=$dbo->query("Select * from ListaClubes Where Publicar = 'S' Order by Nombre");
												  while($r=$dbo->object($query_listaclubes)){
													  	$nombre_club=utf8_encode($r->Nombre);
														$arrayclubes[$nombre_club]=$r->IDListaClub;  
												  }
												  echo SIMHTML::formCheckGroup( $arrayclubes , $club_canje , "ClubCanje[]", "&nbsp;"); ?>
                                        
											
									  </div>
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
				</div>
			</div>

			


		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>