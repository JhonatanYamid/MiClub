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

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Directorio </label>

										<div class="col-sm-8">
										  <input type="text" id="Directorio" name="Directorio" placeholder="Directorio" class="col-xs-12 mandatory" title="Directorio" value="<?php echo $frm["Directorio"]; ?>" >
										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Link </label>

										<div class="col-sm-8">
											<input type="text" id="Link" name="Link" placeholder="Link" class="col-xs-12 mandatory" title="Link" value="<?php echo $frm["Link"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Identificador Modulo </label>

										<div class="col-sm-8">
                                        <input type="text" id="IdentificadorModulo" name="IdentificadorModulo" placeholder="Identificador Modulo" class="col-xs-12 mandatory" title="Identificador Modulo" value="<?php echo $frm["IdentificadorModulo"]; ?>" >
                                        </div>
								</div>

							</div>

            <div  class="form-group first ">

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

									<div class="col-sm-8"><?
									$tipomodulo = array( "App" => "App" , "WebView" => "WebView","Encuesta" => "Encuesta","Auxilios" => "Auxilios","Noticias" => "Noticias","Documentos"=>"Documentos");
									echo SIMHTML::formradiogroup( array_flip( $tipomodulo ) , $frm["Tipo"] , 'Tipo' , "class='input mandatory'" ) ?></div>
							</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Control Navegación </label>

										<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["MostrarControlNavegacion"] , 'MostrarControlNavegacion' , "class='input mandatory'" ) ?></div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Header </label>

										<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["MostrarHeader"] , 'MostrarHeader' , "class='input mandatory'" ) ?></div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Header</label>

										<div class="col-sm-8"><input type="text" id="MensajeWebView" name="MensajeWebView" placeholder="MensajeWebView" class="col-xs-12" title="Url" value="<?php echo $frm["MensajeWebView"]; ?>" ></div>

								</div>



								</div>

								<div  class="form-group first ">
									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url</label>
											<div class="col-sm-8"><input type="text" id="Url" name="Url" placeholder="Url" class="col-xs-12" title="Url" value="<?php echo $frm["Url"]; ?>" ></div>
									</div>

										<div  class="col-xs-12 col-sm-6">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Link Externo </label>
												<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["LinkExterno"] , 'LinkExterno' , "class='input mandatory'" ) ?></div>
										</div>
									</div>

									<div  class="form-group first ">

										<div  class="col-xs-12 col-sm-6">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostra Circulo rojo de notificaciones (cuando sea de submodulos y modulo de notificaciones sea uno de los módulos hijos) </label>

												<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["MostrarBadgeNotificaciones"] , 'MostrarBadgeNotificaciones' , "class='input mandatory'" ) ?></div>
										</div>


											<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Si el módulo es de noticias mostra secciones:  </label>

													<div class="col-sm-8">
														<?php
														$tiposeccion = array( "SeccionGrid" => "Grilla de seccion (iconos)" , "SeccionHeader" => "Secciones en encbezado");
														echo SIMHTML::formradiogroup( array_flip( $tiposeccion ) , $frm["TipoSeccion"] , 'TipoSeccion' , "class='input mandatory'" );
														?>
													</div>
											</div>

										</div>

										<div  class="form-group first ">



												<div  class="col-xs-12 col-sm-6">
														<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

														<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></div>
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
