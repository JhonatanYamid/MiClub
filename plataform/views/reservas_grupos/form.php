
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>GENERAR RESERVA GRUPO <?=strtoupper( SIMUtil::tiempo( $fecha ) )?> 
			
		</h4>

		
	</div>

			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->
					

					<form class="form-horizontal formvalida" role="form" method="post" id="frmReservaGrupo" name="frmReservaGrupo" action="<?php echo SIMUtil::lastURI()?>">
						
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Acción </label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" >
											<input type="hidden" name="IDSocio" value="" id="IDSocio" class="mandatory" title="Socio">
										</div>
                                        
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre del Grupo </label>

										<div class="col-sm-8">
                                        	<textarea rows="3" cols="50" id="Nombre" name="Nombre" class="form-control"></textarea>											
											
										</div>
                                         
								</div>

								
									
							</div>


						

						
							
							

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									<input type="hidden" name="action" value="insert">
									<input type="hidden" id="ids" name="ids" value="<?=$ids ?>">
									<input type="hidden" id="fecha" name="fecha" value="<?=$fecha ?>">
									<input type="hidden" id="idelemento" name="idelemento" value="<?=$idelemento ?>"  title="elemento">
									<input type="hidden" id="idservicio" name="idservicio" value="<?=$idservicio ?>"  title="elemento">
									<input type="hidden" id="hora" name="hora" value="<?=$hora ?>" class="mandatory" title="hora">
                                    <input type="hidden" id="tee" name="tee" value="<?=$tee ?>" class="" title="tee">
									<button class="btn btn-info btnEnviar" type="button" rel="frmReservaGrupo" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										Crear Reserva Grupo
									</button>

									
								</div>
							</div>

					</form>
				</div>
			</div>

			


		</div><!-- /.widget-main -->
		
		

<?
	include( "cmp/footer_scripts.php" );
?>
		<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

		

		

