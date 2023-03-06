
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-comments-o orange"></i>ENVIAR NOTIFICACIÓN SOCIOS CON RESERVA PARA <?=strtoupper( SIMUtil::tiempo( date( "Y-m-d" ) ) )?>
			
		</h4>

		
	</div>

			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->

					<form class="form-horizontal formvalida" role="form" method="post" id="frmServicioNotificacion" name="frmServicioNotificacion" action="<?php echo SIMUtil::lastURI()?>">
						
							<div  class="form-group first ">
                            
                            	<div  class="col-sm-12">
										
                                        
                                        <label  for="Mensaje"> Fecha </label><br>
										<div class="col-sm-8">
											<input type="text" id="FechaReserva" name="FechaReserva" placeholder="Fecha Reserva" class="col-xs-12 calendar" title="Fecha Reserva" value="<?php echo date("Y-m-d") ?>" >
										</div>									
										
								</div>

								<div  class="col-sm-12">
									<label  for="Mensaje"> Notificacion para las reservas de cual elemento </label><br>
										<div class="col-sm-8">
											<div class="col-sm-8"><?php echo SIMHTML::formPopUp( "ServicioElemento" , "Nombre" , "Nombre" , "IDServicioElemento" , $frm["IDElemento"] , "[Seleccione el elemento]" , "form-control" , "title = \"IDTipo Archivo\"", "AND IDServicio ='".$_GET['ids']."'" )?></div>
										</div>
								</div>


								<div  class="col-sm-12">
										
                                        
                                        <label  for="Mensaje"> Mensaje </label>
										<textarea id="Mensaje" name="Mensaje" class="input form-control"></textarea>											
										<span class="help-inline col-xs-12 col-sm-12">
											<span class="middle">Una vez haga clic en "Enviar Notificación Socios" A los socios con reserva para la fecha seleccionada se les enviará un mensaje a su celular</span>
										</span>
								</div>		

								
									
							</div>


							

						
							
							

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									<input type="hidden" name="action" value="insert">
									<input type="hidden" id="ids" name="ids" value="<?=$ids ?>">
									<button class="btn btn-info btnEnviar" type="button" rel="frmServicioNotificacion" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										Enviar Notificación Socios
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

		

		

