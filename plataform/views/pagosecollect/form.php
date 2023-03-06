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
								
							<?php $datos_socio = $dbo->fetchAll("Socio","IDSocio = $frm[IDSocio]"); ?>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="NombreSocio" name="NombreSocio" placeholder="NombreSocio" class="col-xs-12 mandatory" title="NombreSocio" value="<?php echo $datos_socio[Nombre]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido:  </label>

										<div class="col-sm-8">
										<input type="text" id="ApellidoSocio" name="ApellidoSocio" placeholder="ApellidoSocio" class="col-xs-12 mandatory" title="ApellidoSocio" value="<?php echo $datos_socio[Apellido]; ?>" >
                                        </div>
								</div>

							</div>

                            <div  class="form-group first ">
    	                         

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Accion </label>

										<div class="col-sm-8">
											<input type="text" id="NumeroAccion" name="NumeroAccion" placeholder="NumeroAccion" class="col-xs-12 mandatory" title="NumeroAccion" value="<?php echo $datos_socio[CorreoElectronico]; ?>" >
										</div>
								</div>

							</div>

                            <div  class="form-group first ">
    	                         <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono:  </label>

										<div class="col-sm-8">
										<input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $datos_socio[Celular]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Transaccion:  </label>

										<div class="col-sm-8">
										<input type="text" id="FechaTransaccion" name="FechaTransaccion" placeholder="FechaTransaccion" class="col-xs-12 mandatory" title="FechaTransaccion" value="<?php echo $frm[BankProcessDate]; ?>" >
                                        </div>
								</div>

							</div>

                            <div  class="form-group first ">
    	                         <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Transaccion:  </label>

										<div class="col-sm-8">
										<input type="text" id="ValorTransaccion" name="ValorTransaccion" placeholder="ValorTransaccion" class="col-xs-12 mandatory" title="ValorTransaccion" value="<?php echo $frm[TransValue]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Transaccion </label>

										<div class="col-sm-8">
											<input type="text" id="NumeroTransaccion" name="NumeroTransaccion" placeholder="NumeroTransaccion" class="col-xs-12 mandatory" title="NumeroTransaccion" value="<?php echo $frm[ValorID]; ?>" >
										</div>
								</div>

							</div>

							<div  class="form-group first ">


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Autorizacion</label>

										<div class="col-sm-8">
											<input type="text" id="authorizationCode" name="authorizationCode" placeholder="authorizationCode" class="col-xs-12 mandatory" title="authorizationCode" value="<?php echo $frm[TicketId]; ?>" >
										</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Respuesta Transaccion</label>

										<div class="col-sm-8">
											<input type="text" id="errorMessage" name="errorMessage" placeholder="errorMessage" class="col-xs-12 mandatory" title="errorMessage" value="<?php echo $frm[TranState]; ?>" >
										</div>
								</div>

							</div>

							<div  class="form-group first ">


								
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Factura</label>

										<div class="col-sm-8">
											<input type="text" id="errorMessage" name="errorMessage" placeholder="errorMessage" class="col-xs-12 mandatory" title="errorMessage" value="<?php echo $frm[Factura]; ?>" >
										</div>
								</div>

							</div>


							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<!--
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
                                    -->


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
