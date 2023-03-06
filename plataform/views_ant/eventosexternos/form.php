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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre:  </label>
										<div class="col-sm-8">
                                            <input type="text" id="Nombre" name="Nombre" placeholder="" class="col-xs-12" title="Nombre" value="<?php echo $frm["Nombre"];?>">
                                        </div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label>
										<div class="col-sm-8">
                                            <input type="date" id="Fecha" name="Fecha" placeholder="" class="col-xs-12" title="Fecha" value="<?php echo $frm["Fecha"];?>">
                                        </div>
								</div>
							</div>
						<div  class="form-group first ">
                        		<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora ingreso </label>
										<div class="col-sm-8">
                                            <input type="time" id="HoraIngreso" name="HoraIngreso" placeholder="" class="col-xs-12" title="Nombre" value="<?php echo $frm["HoraIngreso"];?>">
                                        </div>
								</div>
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora salida </label>										 
										<div class="col-sm-8">
                                            <input type="time" id="HoraSalida" name="HoraSalida" placeholder="" class="col-xs-12" title="Hora Salida" value="<?php echo $frm["HoraSalida"];?>">
										</div>
								</div>
                        </div>
						<div class="form-group first ">								

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>
										<div class="col-sm-8">
                                            <input type="text" id="Tipo" name="Tipo" placeholder="Tipo" class="col-xs-12" title="Tipo" value="<?php echo $frm["Tipo"];?>">
                                        </div>
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
