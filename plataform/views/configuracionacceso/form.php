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
										<label class="col-sm-4 control-label no-padding-right" for="IntroduccionBuscador"> Introduccion Buscador </label>
										<div class="col-sm-8"><input type="text" id="IntroduccionBuscador" name="IntroduccionBuscador" placeholder="Introduccion Buscador" class="col-xs-12 mandatory" title="IntroduccionBuscador" value="<?php echo $frm["IntroduccionBuscador"];?>"></div>
															
							</div>
							<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="LabelBuscador"> Label Buscador </label>
										<div class="col-sm-8">
											<input id="LabelBuscador" name="LabelBuscador" cols="10" rows="5" class="col-xs-12 mandatory" title="Label Buscador" value="<?php echo $frm["LabelBuscador"];?>">
										</div>
								</div>
						</div>
						<div  class="form-group first ">
								                      		
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="PintarEstadoSalidaEntrada"> Pintar Estado Salida Entrada </label>
										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PintarEstadoSalidaEntrada"] , 'PintarEstadoSalidaEntrada' , "class='input mandatory'" ) ?>	
										</div>								
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="PermiteRegistrarSinEstado-field-1"> Permite Registrar Sin Estado </label>
										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteRegistrarSinEstado"] , 'PermiteRegistrarSinEstado' , "class='input mandatory'" ) ?>	
										</div>								
								</div>
                        </div>
						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="TipoBoton"> Tipo Botón </label>
										<div class="col-sm-8">
											<input type="text" id="TipoBoton" name="TipoBoton" placeholder="Tipo Botón" class="col-xs-12 mandatory" title="Tipo Botón" value="<?php echo $frm["TipoBoton"];?>">
										</div>															
							</div>
							<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="LabelRegistroObjeto"> Label Registro Objeto </label>
										<div class="col-sm-8">
											<input type="text" id="LabelRegistroObjeto" name="LabelRegistroObjeto" placeholder="Label Registro Objeto" class="col-xs-12 mandatory" title="Label Registor Objeto" value="<?php echo $frm["LabelRegistroObjeto"];?>">
										</div>										
							</div>							
						</div>
						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="LabelCampo1RegistroObjeto"> Label Campo1 Registro Objeto </label>
										<div class="col-sm-8">
											<input type="text" id="LabelCampo1RegistroObjeto" name="LabelCampo1RegistroObjeto" placeholder="Label Campo1 Registro Objeto" class="col-xs-12 mandatory" title="Label Campo1 Registro Objeto" value="<?php echo $frm["LabelCampo1RegistroObjeto"];?>">
										</div>															
							</div>
							<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="LabelCampo2RegistroObjeto"> Label Campo2 Registro Objeto </label>
										<div class="col-sm-8">
											<input type="text" id="LabelCampo2RegistroObjeto" name="LabelCampo2RegistroObjeto" placeholder="Label Campo2 Registro Objeto" class="col-xs-12 mandatory" title="Label Campo2 Registro Objeto" value="<?php echo $frm["LabelCampo2RegistroObjeto"];?>">
										</div>										
							</div>							
						</div>
						<div  class="form-group first ">								                      		
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="PermiteRegistroObjetos"> Permite Registro Objetos </label>
										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteRegistroObjetos"] , 'PermiteRegistroObjetos' , "class='input mandatory'" ) ?>	
										</div>								
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="PermiteInvitacionPortero"> Permite Invitacion Portero </label>
										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteInvitacionPortero"] , 'PermiteInvitacionPortero' , "class='input mandatory'" ) ?>	
										</div>								
								</div>
                        </div>
						<div  class="form-group first ">								                      		
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="Activo"> Activo </label>
										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Activo"] , 'Activo' , "class='input mandatory'" ) ?>	
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
