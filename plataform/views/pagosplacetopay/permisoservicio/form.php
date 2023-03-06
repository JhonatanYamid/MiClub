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


					<?php
					if((int)SIMUser::get("club")<=0){
						echo "Seleccione un club";exit;
					}

					?>


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>">

							<div  class="form-group first ">



								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

										<div class="col-sm-8">
											<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
									</div>
								</div>



							</div>


                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicios   </label>

										<div class="col-sm-8">
                                           <?php
												  // Consulto los modulos disponibles del perfil
												  $sql_modulo_perfil=$dbo->query("SELECT * from ServicioPermiso where IDPermisoServicio = '".$frm["IDPermisoServicio"]."'");
												  while($r_permiso_servicio=$dbo->object($sql_modulo_perfil)){
													  $permiso_servicio[]=$r_permiso_servicio->IDServicio;
												  }
												  $arrayop = array();
												  // consulto los modulos
												  $query_servicios=$dbo->query("SELECT * from ServicioClub Where IDClub = '".SIMUser::get("club")."' and Activo='S'");
												  while($r=$dbo->object($query_servicios)){
														$IDServicio=$dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '".$r->IDClub."' ");
														if(!empty($r->TituloServicio))	{
															$NombreServicio=$r->TituloServicio;
														}
														else{
															$NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r->IDServicioMaestro . "'");
														}
														$arrayservicio[$NombreServicio]=$IDServicio;
												  }
												  echo SIMHTML::formCheckGroup( $arrayservicio , $permiso_servicio , "PermisoServicio[]"); ?>
										</div>
								</div>




							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                  <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="IDClub"  id="IDClub" value="<?php echo SIMUser::get("club") ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
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
