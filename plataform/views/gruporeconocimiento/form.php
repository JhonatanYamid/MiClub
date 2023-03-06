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




					<form class="form-horizontal formvalida" role="form" method="post" id="frmgruposocio" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Grupo</label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

										<div class="col-sm-8">
										  <input type="text" id="Descripcion" name="Descripcion" placeholder="Descripcion" class="col-xs-12" title="Descripcion" value="<?php echo $frm["Descripcion"]; ?>" >
										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-8">
											<input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho" >
												<br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
	<br>

                                        	<select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8"  multiple style="height:300px" >
                                        	<?php

																					$sql_persona="SELECT S.* FROM GrupoReconocimientoSocio GRS, Socio S WHERE GRS.IDSocio=S.IDSocio and IDGrupoReconocimiento='".$frm[ $key ]."' ";
																					$r_persona=$dbo->query($sql_persona);
                                        	while($row_persona = $dbo->fetchArray($r_persona)): ?>
																						<option value="<?php echo "socio-".$row_persona["IDSocio"]; ?>"><?php echo $row_persona["Nombre"]. " ". $row_persona["Apellido"]; ?></option>
                                          <?php
																					endwhile;
																					?>
                                        </select>
                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">

										</div>
								</div>

								<!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Subir Archivo (Accion) </label>

										<div class="col-sm-8"><input name="file" type="file" size="20" class="form-control" /></div>
								</div>
							-->

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
									<button class="btn btn-info btnEnviar" type="button" rel="frmgruposocio" >
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
