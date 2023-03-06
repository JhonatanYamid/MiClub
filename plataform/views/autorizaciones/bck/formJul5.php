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
					

					<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento</label>

										<div class="col-sm-8">                                        
                                        <input id="NumeroDocumento" type="text" size="25" title="Numero Documento" name="NumeroDocumento" class="input mandatory autocomplete-ajax_invitado" value="<?php echo $frm["NumeroDocumento"] ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>  />                                                                               
										<input type="hidden" name="IDSocioInvitado" value="<?php echo $frm["NumeroDocumento"] ?>" id="IDSocioInvitado" title="Numero Documento">
	
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio</label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if($newmode=="updateingreso") echo "readonly"; ?> value="<?php echo utf8_decode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$frm["IDSocio"]."'" )) . " ".utf8_decode($dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$frm["IDSocio"]."'" )) ?>" >
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"] ?>" id="IDSocio" class="mandatory" title="Socio">
										</div>
								</div>
									
							</div>
						
							
							<div  class="form-group first">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre</label>

										<div class="col-sm-8">
										  <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?> />
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Ingreso</label>

										<div class="col-sm-8">
										  <input type="text" id="FechaIngreso" name="FechaIngreso" placeholder="Fecha Ingreso" class="col-xs-12 <?php if($newmode!="updateingreso") echo "calendar"; ?> " title="Fecha Ingreso" value="<?php echo $frm["FechaIngreso"] ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>>
										</div>
								</div>
									
							</div>
                            
                            
                            <?php if($newmode=="updateingreso"): ?>
                            <div  class="form-group first">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones</label>

										<div class="col-sm-8">
										  <textarea id="Observaciones" rows="4"  title="Observaciones" name="Observaciones" class="form-control" /><?php echo $frm["Observaciones"] ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha/Hora Ingreso</label>

										<div class="col-sm-8">
										  <input type="text" id="FechaIngresoClub" name="FechaIngresoClub" placeholder="Fecha Ingreso Club" class="col-xs-12" title="Fecha Ingreso Club" value="<?php if($newmode=="updateingreso"): echo date("Y-m-d H:i:s"); else: echo ""; endif; ?>" readonly >
										</div>
								</div>
									
							</div>
                            <?php endif; ?>
                            



							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									 <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm" >
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

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>