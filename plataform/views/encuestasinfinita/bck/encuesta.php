<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">



							<div  class="form-group first">

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encuesta para:  </label>

									<div class="col-sm-8">
										<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$dirigidoa ) , $frm["DirigidoA"] , "DirigidoA" , "title=\"DirigidoA\"" )?>
										
									</div>
								</div>
							</div>

							<div  class="form-group first">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre</label>

										<div class="col-sm-8">
											<input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" />
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden</label>

									<div class="col-sm-8">
										<input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input mandatory" value="<?php echo $frm["Orden"] ?>" />
									</div>
							</div>

							</div>


							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion</label>

										<div class="col-sm-8">
											<textarea rows="5" cols="50" id="Descripcion" name="Descripcion" class="input"><?php echo $frm["Descripcion"] ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Respuesta al guardar</label>

										<div class="col-sm-8">
											<textarea rows="5" cols="50" id="RespuestaGuardar" name="RespuestaGuardar" class="input"><?php echo $frm["RespuestaGuardar"] ?></textarea>
										</div>
								</div>


							</div>

							<!--Inicio Notificación -->
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificación ? </label>

									<div class="col-sm-8">
										<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , "" , "NotificarPush" , "title=\"NotificarPush\"" )?>
									</div>
								</div>
							</div>
							<!-- Fin Notificación -->


						<div  class="form-group first ">



								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio Publicacion</label>

										<div class="col-sm-8">
	                                          <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>" >
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin Publicacion</label>

										<div class="col-sm-8">
	                                            <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="Fecha Fin" value="<?php echo $frm["FechaFin"] ?>" >
										</div>
								</div>

							</div>

            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar al abrir app?</label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["SolicitarAbrirApp"] , "SolicitarAbrirApp" , "title=\"Solicitar Abrir App\"" )?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo permitir llenar la encuesta 1 vez por usuario?</label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["UnaporSocio"] , "UnaporSocio" , "title=\"UnaporSocio\"" )?>
										</div>
								</div>
							</div>



							<div  class="form-group first ">
                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Destacada </label>
										<div class="col-sm-8">
											<? if (!empty($frm[Imagen])) {
													echo "<img src='".BANNERAPP_ROOT."$frm[Imagen]' width=55 >";
													?>
												<a
													href="<? echo $script.".php?action=delfoto&foto=$frm[Imagen]&campo=Imagen&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
												<?
												}// END if
												?>
											 <input name="Imagen" id=file class="" title="Imagen" type="file" size="25" style="font-size: 10px">
										</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen listado encuesta </label>
										<div class="col-sm-8">
											<? if (!empty($frm[ImagenEncuesta])) {
													echo "<img src='".BANNERAPP_ROOT."$frm[ImagenEncuesta]' width=55 >";
													?>
												<a
													href="<? echo $script.".php?action=delfoto&foto=$frm[ImagenEncuesta]&campo=ImagenEncuesta&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
												<?
												}// END if
												?>
											 <input name="ImagenEncuesta" id=file class="" title="ImagenEncuesta" type="file" size="25" style="font-size: 10px">
										</div>
								</div>
					</div>
					<div  class="form-group first ">

						<div  class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar</label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "Publicar" , "title=\"Publicar\"" )?>
								</div>
						</div>
			</div>


					<div  class="form-group first ">

	<div  class="col-xs-12 col-sm-12">
 		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar a :  </label>

 		<div class="col-sm-8">
			<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="S" title="DirigidoA" <?php if($frm["DirigidoAGeneral"]=="S") echo "checked"; ?> />Todos los Usuarios
			<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="SE" title="DirigidoA" <?php if($frm["DirigidoAGeneral"]=="SE") echo "checked"; ?>/>Usuarios Especificos
			<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GS" title="DirigidoA" <?php if($frm["DirigidoAGeneral"]=="GS") echo "checked"; ?> />Grupo de Usuarios
			<!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="E" title="DirigidoA"/>Todos los Empleado-->
			<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="EE" title="DirigidoA" <?php if($frm["DirigidoAGeneral"]=="EE") echo "checked"; ?> />Empleados Especificos
			<!--<input type="radio" name="DirigidoAGeneral" id="DirigidoAGeneral" value="GE" title="DirigidoA"/>Grupo de Empleados-->


										 </div>
</div>

</div>


<div id="SocioGrupo" class="form-group first " style="<?php if($frm["DirigidoAGeneral"]=="GS") echo ""; else echo "display:none"; ?> " >
	<div  class="col-xs-12 col-sm-6">
		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seleccione el Grupo:  </label>

		<div class="col-sm-8">
			<select name="IDGrupoSocio" id="IDGrupoSocio" class="form-control">
				<option value="">Seleccion Grupo</option>
				<?php
					$sql_grupos="Select * From GrupoSocio Where IDClub = '".SIMUser::get("club")."'";
					$result_grupos = $dbo->query($sql_grupos);
				while($row_grupos = $dbo->fetchArray($result_grupos )): ?>
					<option value="<?php echo $row_grupos["IDGrupoSocio"]; ?>" <?php if($frm["IDGrupoSocio"]==$row_grupos["IDGrupoSocio"]) echo "selected";  ?>><?php echo $row_grupos["Nombre"]; ?></option>
				<?php endwhile; ?>
			</select>
			<a href="gruposocio.php?action=add">Crear Grupo</a>

			<br>
			<a id="agregar_invitadoGrupo" href="#">Agregar</a> | <a id="borrar_invitadoGrupo" href="#">Borrar</a>
			<br>
			<select name="SocioInvitado[]" id="SocioInvitadoGrupo" class="col-xs-8"  multiple >
				<?php
				$item=1;
				$array_invitados = explode("|||",$frm["SeleccionGrupo"]);
				foreach($array_invitados as $id_invitado => $datos_invitado):
					if(!empty($datos_invitado)){
						$array_datos_invitados=explode("-",$datos_invitado);
						$item--;
						$IDSocioInvitacion=$array_datos_invitados[1];
						if($IDSocioInvitacion > 0):
						$nombre_socio = utf8_encode($dbo->getFields( "GrupoSocio" , "Nombre" , "IDGrupoSocio = '".$IDSocioInvitacion."'" ));
						?>
						<option value="<?php echo "grupo-".$IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
						<?php
						endif;
					}
				endforeach;?>
			</select>
			<input type="hidden" name="SeleccionGrupo" id="SeleccionGrupo" value="">
		</div>
	</div>
</div>

<div id="SocioEspecifico" class="form-group first " style="<?php if($frm["DirigidoAGeneral"]=="SE") echo ""; else echo "display:none"; ?> ">
	<div  class="col-xs-12 col-sm-6">
		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuarios: </label>
		<div class="col-sm-8">
			<input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho" >
			<br>
			<a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
			<br>
			<select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8"  multiple >
				<?php
				$item=1;
				$array_invitados=explode("|||",$frm["InvitadoSeleccion"]);
				foreach($array_invitados as $id_invitado => $datos_invitado):
					if(!empty($datos_invitado)){
						$array_datos_invitados=explode("-",$datos_invitado);
						$item--;
						$IDSocioInvitacion=$array_datos_invitados[1];
						if($IDSocioInvitacion > 0):
						$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocioInvitacion."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocioInvitacion."'" ));
						?>
						<option value="<?php echo "socio-".$IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
						<?php
						endif;
					}
				endforeach;?>
			</select>
			<input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
		</div>
	</div>
</div>

<div id="EmpleadoEspecifico" class="form-group first " style="<?php if($frm["DirigidoAGeneral"]=="EE") echo ""; else echo "display:none"; ?> ">
	<div  class="col-xs-12 col-sm-6">
		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Empleados: </label>
		<div class="col-sm-8">
			<input type="text" id="AccionInvitadoUsuario" name="AccionInvitadoUsuario" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-funcionarioEncuestas" title="número de derecho" >
			<br>
			<a id="agregar_empleado" href="#">Agregar</a> | <a id="borrar_empleado" href="#">Borrar</a>
			<br>
			<select name="SocioInvitadoUsuario[]" id="SocioInvitadoUsuario" class="col-xs-8"  multiple >
				<?php
				$item=1;
				$array_invitados=explode("|||",$frm["UsuarioSeleccion"]);
				foreach($array_invitados as $id_invitado => $datos_invitado):
					if(!empty($datos_invitado)){
						$array_datos_invitados=explode("-",$datos_invitado);
						$item--;
						$IDSocioInvitacion=$array_datos_invitados[1];
						if($IDSocioInvitacion > 0):
						$nombre_socio = utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$IDSocioInvitacion."'" ) . "  " . $dbo->getFields( "Usuario" , "Apellido" , "IDUsuario = '".$IDSocioInvitacion."'" ));
						?>
						<option value="<?php echo "usuario-".$IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
						<?php
						endif;
					}
				endforeach;?>
			</select>
			<input type="hidden" name="UsuarioSeleccion" id="UsuarioSeleccion" value="">
		</div>
	</div>
</div>











							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									 <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get( "title" ); ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
								  <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>


								</div>
							</div>

					</form>
