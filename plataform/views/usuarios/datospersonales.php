<script type="text/javascript">
	function marcar(source, namecss) {
		checkboxes = document.getElementsByClassName(namecss); //obtenemos todos los controles del tipo Input
		for (i = 0; i < checkboxes.length; i++) //recoremos todos los controles
		{
			if (checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked = source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>


<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-users green"></i>
								<?= SIMUtil::get_traduccion('', '', 'datosbasicos', LANGSESSION); ?>
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Club', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDClub" id="IDClub" class="form-control">
										<option value=""></option>
										<?php
										if (SIMUser::get("Nivel") == 0) :
											$condicion_club = "  1";
										else :
											$condicion_club = " IDClub = '" . SIMUser::get("club") . "'";
										endif;

										$sql_club_lista = "Select * From Club Where $condicion_club ";
										$qry_club_lista = $dbo->query($sql_club_lista);
										while ($r_club_lista = $dbo->fetchArray($qry_club_lista)) : ?>
											<option value="<?php echo $r_club_lista["IDClub"]; ?>" <?php if ($r_club_lista["IDClub"] == $frm["IDClub"]) echo "selected";  ?>><?php echo $r_club_lista["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>

									<?php //echo SIMHTML::formPopUp( "Club" , "Nombre" , "Nombre" , "IDClub" , $frm["IDClub"] , "[Seleccione el Club]" , "popup mandatory" , "title = \"Club\"" )
									?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="number" id="NumeroDocumento" name="NumeroDocumento" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>" value="<?php echo $frm["NumeroDocumento"]; ?>">
								</div>
							</div>



						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Email" name="Email" placeholder="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" value="<?php echo $frm["Email"]; ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="User" name="User" placeholder="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" value="<?php echo $frm["User"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="password" id="Password" name="Password" placeholder="<?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?>" value="">
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Autorizado', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Autorizado"], "Autorizado", "title=\"Autorizado\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?></label>

								<div class="col-sm-8">
									<input type="password" id="RePassword" name="RePassword" placeholder="<?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CodigoEmpleado', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CodigoUsuario" name="CodigoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'CodigoEmpleado', LANGSESSION); ?>" class="col-xs-12 form-control" title="<?= SIMUtil::get_traduccion('', '', 'CodigoEmpleado', LANGSESSION); ?>" value="<?php echo $frm["CodigoUsuario"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Telefono" name="Telefono" placeholder="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" value="<?php echo $frm["Telefono"]; ?>">
								</div>
							</div>



						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Perfil', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Perfil", "Nombre", "Nombre", "IDPerfil", $frm["IDPerfil"], "[Seleccione el Perfil]", "form-control mandatory", "title = \"Perfil\"") ?>
								</div>


								<!--

											<div class="col-sm-8">
												<select name = "IDPerfil" id="IDPerfil" class="form-control" >
													<option value="">[Selecciona el Perfil]</option>
												<?php
												if (SIMUser::get("Nivel") == 0) :
													$condicion_perfil = "  1";
												else :
													$condicion_perfil = " IDClub = '" . SIMUser::get("club") . "'";
												endif;

												$sql_perfil_lista = "SELECT * FROM Perfil WHERE " . $condicion_perfil;
												$qry_perfil_lista = $dbo->query($sql_perfil_lista);
												while ($r_perfil_lista = $dbo->fetchArray($qry_perfil_lista)) : ?>
													<option value="<?php echo $r_perfil_lista["IDPerfil"]; ?>" <?php if ($r_perfil_lista["IDPefil"] == $frm["IDPerfil"]) echo "selected";  ?>><?php echo $r_perfil_lista["Nombre"]; ?></option>
													<?php
												endwhile; ?>
												</select>
											</div> -->
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permisos', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="radio" name="Permiso" value="L" <?php if ($frm["Permiso"] == "L") echo "checked"; ?>><?= SIMUtil::get_traduccion('', '', 'Lectura', LANGSESSION); ?> <input type="radio" name="Permiso" value="E" <?php if ($frm["Permiso"] == "E") echo "checked"; ?>> <?= SIMUtil::get_traduccion('', '', 'LecturayEscritura', LANGSESSION); ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoUsuario', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select name="TipoUsuario" id="TipoUsuario" class="form-control">
										<option value=""><?= SIMUtil::get_traduccion('', '', '[Seleccioneunaopción]', LANGSESSION); ?></option>
										<?php
										$TipoUsuario = "SELECT * FROM TipoUsuario WHERE 1";
										$qry = $dbo->query($TipoUsuario);
										while ($row = $dbo->fetchArray($qry)) :
											$selected = ($frm['TipoUsuario'] == $row['Nombre']) ? 'selected' : '';
										?>
											<option value="<?php echo $row['Nombre']; ?>" <?= $selected; ?>><?php echo $row['Nombre']; ?></option>
										<?php endwhile; ?>
									</select>
								</div>
							</div>



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Area </label>


								<div class="col-sm-8">
									<select name="IDAreaUsuario" id="IDAreaUsuario" class="form-control">
										<option value=""></option>
										<?php
										$condicion_club = " IDClub = '" . SIMUser::get("club") . "' AND Activo='S'";
										$sql_area_lista = "Select Nombre,Activo,IDAreaUsuario From AreaUsuario Where $condicion_club ";
										$qry_area_lista = $dbo->query($sql_area_lista);
										while ($r_area_lista = $dbo->fetchArray($qry_area_lista)) : ?>
											<option value="<?php echo $r_area_lista["IDAreaUsuario"]; ?>" <?php if ($r_area_lista["IDAreaUsuario"] == $frm["IDAreaUsuario"]) echo "selected";  ?>><?php echo $r_area_lista["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
								</div>

							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Cargo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Cargo" name="Cargo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Cargo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Cargo', LANGSESSION); ?>" value="<?php echo $frm["Cargo"]; ?>">
								</div>
							</div>
						</div>



						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Area empresa', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="AreaEmpresa" name="AreaEmpresa" placeholder="<?= SIMUtil::get_traduccion('', '', 'Area empresa', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Area empresa', LANGSESSION); ?>" value="<?php echo $frm["AreaEmpresa"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Tipo" name="Tipo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>" value="<?php echo $frm["Tipo"]; ?>">
								</div>
							</div>

						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicioContrato', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaInicioContrato" name="FechaInicioContrato" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicioContrato', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicioContrato', LANGSESSION); ?>" value="<?php echo $frm["FechaInicioContrato"] ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFinContrato', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaFinContrato" name="FechaFinContrato" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFinContrato', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFinContrato', LANGSESSION); ?>" value="<?php echo $frm["FechaFinContrato"] ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fecha de Nacimiento', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha de Nacimiento', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'Fecha de Nacimiento', LANGSESSION); ?>" value="<?php echo $frm["FechaNacimiento"] ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitecrearreservas', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservar"], 'PermiteReservar', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Enviarpushdereservas', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PushReserva"], 'PushReserva', "class='input mandatory'") ?>
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Sipermitereservarpuedereservarantesdeabrirreservasenapp', LANGSESSION); ?>? </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservarAntes"], 'PermiteReservarAntes', "class='input mandatory'") ?>
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Permitireditarycambiareldueñodeunareserva', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCambiarReserva"], 'PermiteCambiarReserva', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>

						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Solicitarcambiodeclaveobligatoriocada3meses', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaCambioClave"], 'SolicitaCambioClave', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php
									// Consulto las sedes disponibles del usuario
									$array_id_sede = explode("|", $frm["IDCursoSede"]);
									foreach ($array_id_sede as $id_sede_usuario)
										if (!empty($id_sede_usuario))
											$usuario_sede[] = $id_sede_usuario;


									$sql_area_usuario = $dbo->query("select * from UsuarioArea where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_area_usuario = $dbo->object($sql_area_usuario)) {
										$usuario_area[] = $r_area_usuario->IDArea;
									}
									$arrayop = array();
									// consulto los modulos
									$query_sede = $dbo->query("SELECT * FROM CursoSede Where IDClub = '" . $frm["IDClub"] . "' Order by Nombre");
									while ($r = $dbo->object($query_sede)) {
										$nombre_sede = utf8_encode($r->Nombre);
										$arraysedes[$nombre_sede] = $r->IDCursoSede;
									}
									echo SIMHTML::formCheckGroup($arraysedes, $usuario_sede, "SedeUsuario[]", "&nbsp;");
									?>
								</div>
							</div>

							<div class="form-group first ">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Solicitar Cierre Sesion ?', LANGSESSION); ?> </label>

									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitarCierreSesion"], 'SolicitarCierreSesion', "class='input mandatory'") ?>
									</div>
								</div>




							</div>





						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Label Estado salud carné', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<input type="text" id="LabelEstadoUsuario" name="LabelEstadoUsuario" placeholder="Label Estado Usuario" class="col-xs-12" title="Label Estado Usuario" value="<?php echo $frm["LabelEstadoUsuario"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Solicitar Editar Perfil', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaEditarPerfil"], 'SolicitaEditarPerfil', "class='input mandatory'") ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Mostrarmensajecumpleaños', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarMensajeCumpleanos"], 'MostrarMensajeCumpleanos', "class='input mandatory'") ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 89) : ?>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Area Soy Central', LANGSESSION); ?> </label>

									<div class="col-sm-8">
										<?php echo SIMHTML::formPopupArray(SIMResources::$areassoycentral,  $frm["IDAreaSocio"], "IDAreaSocio",  "Seleccione Area", "form-control"); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Aprobar Vacaciones', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AprobarVacaciones"], 'AprobarVacaciones', "class='input mandatory'") ?>
								</div>
							</div>
						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombre Jefe', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="NombreJefe" name="NombreJefe" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombre Jefe', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Nombre Jefe', LANGSESSION); ?>" value="<?php echo $frm["NombreJefe"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Correo Jefe', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CorreoJefe" name="CorreoJefe" placeholder=" <?= SIMUtil::get_traduccion('', '', 'Correo Jefe', LANGSESSION); ?>" class="col-xs-12" title=" <?= SIMUtil::get_traduccion('', '', 'Correo Jefe', LANGSESSION); ?>" value="<?php echo $frm["CorreoJefe"]; ?>">
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Documento Jefe', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="DocumentoJefe" name="DocumentoJefe" placeholder="<?= SIMUtil::get_traduccion('', '', 'Documento Jefe', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Documento Jefe', LANGSESSION); ?>" value="<?php echo $frm["DocumentoJefe"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="NombreEspecialista"> <?= SIMUtil::get_traduccion('', '', 'NombreEspecialista/Aprobador', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="NombreEspecialista" name="NombreEspecialista" placeholder="<?= SIMUtil::get_traduccion('', '', 'NombreEspecialista/Aprobador', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'NombreEspecialista/Aprobador', LANGSESSION); ?>" value="<?php echo $frm["NombreEspecialista"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="CorreoEspecialista"> <?= SIMUtil::get_traduccion('', '', 'CorreoEspecialista/Aprobador', LANGSESSION); ?></label>

								<div class="col-sm-8">
									<input type="text" id="CorreoEspecialista" name="CorreoEspecialista" placeholder="<?= SIMUtil::get_traduccion('', '', 'CorreoEspecialista/Aprobador', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'CorreoEspecialista/Aprobador', LANGSESSION); ?>" value="<?php echo $frm["CorreoEspecialista"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="DocumentoEspecialista"> <?= SIMUtil::get_traduccion('', '', 'DocumentoEspecialista/Aprobador', LANGSESSION); ?></label>

								<div class="col-sm-8">
									<input type="text" id="DocumentoEspecialista" name="DocumentoEspecialista" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoEspecialista/Aprobador', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'DocumentoEspecialista/Aprobador', LANGSESSION); ?>" value="<?php echo $frm["DocumentoEspecialista"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="HoraInicioLaboral"> <?= SIMUtil::get_traduccion('', '', 'Hora Inicio Laboral', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="time" id="HoraInicioLaboral" name="HoraInicioLaboral" placeholder="<?= SIMUtil::get_traduccion('', '', 'Hora Inicio Laboral', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Hora Inicio Laboral', LANGSESSION); ?>" value="<?php echo $frm["HoraInicioLaboral"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="HoraFinalLaboral"> <?= SIMUtil::get_traduccion('', '', 'Hora final laboral', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="time" id="HoraFinalLaboral" name="HoraFinalLaboral" placeholder="<?= SIMUtil::get_traduccion('', '', 'Hora final laboral', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Hora final laboral', LANGSESSION); ?>" value="<?php echo $frm["HoraFinalLaboral"]; ?>">
								</div>
							</div>
						</div>
						<?php if (!empty($frm["IDUsuario"])) { ?>


							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-user green"></i>
									<?= SIMUtil::get_traduccion('', '', 'DatosPerfil', LANGSESSION); ?>
								</h3>
							</div>

							<?php
							//Consulto los campos dinamicos
							$sql_campos = "SELECT SCES.Valor,CED.Nombre FROM CampoEditarUsuario CED, UsuarioCampoEditarUsuario SCES
													 WHERE SCES.IDCampoEditarUsuario=CED.IDCampoEditarusuario AND SCES.IDUsuario='" . $frm["IDUsuario"] . "'
													 Group by SCES.IDCampoEditarUsuario
													 Order by CED.Orden";
							$r_campos = $dbo->query($sql_campos);
							while ($r = $dbo->object($r_campos)) {
								$array_preguntas[] = $r->Nombre;
								$array_respuesta[] = $r->Valor;
							}
							?>

							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<tr>

									<?php foreach ($array_preguntas as $key_pregunta => $value_pregunta) {   ?>
										<th><?php echo $value_pregunta; ?></th>
									<?php } ?>
								</tr>
								<tbody id="listacontactosanunciante">
									<tr>
										<?php foreach ($array_respuesta as $key_respuesta => $value_respuesta) {   ?>
											<td><?php echo $value_respuesta; ?></td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
						<?php } ?>




						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								<?= SIMUtil::get_traduccion('', '', 'AsignarAreasPqrSocios', LANGSESSION); ?>
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto las areas disponibles del usuario
									$sql_area_usuario = $dbo->query("select * from UsuarioArea where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_area_usuario = $dbo->object($sql_area_usuario)) {
										$usuario_area[] = $r_area_usuario->IDArea;
									}
									$arrayop = array();
									// consulto los modulos
									$query_area = $dbo->query("Select * from Area Where IDClub = '" . $frm["IDClub"] . "' Order by Nombre");
									while ($r = $dbo->object($query_area)) {
										$nombre_area = $r->Nombre;
										$arrayareas[$nombre_area] = $r->IDArea;
									}
									echo SIMHTML::formCheckGroup($arrayareas, $usuario_area, "AreaUsuario[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>


						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								<?= SIMUtil::get_traduccion('', '', 'AsignarAreasPqrFuncionarios', LANGSESSION); ?>
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto las areas disponibles del usuario
									$sql_area_usuario_func = $dbo->query("select * from UsuarioAreaFuncionario where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_area_usuario_func = $dbo->object($sql_area_usuario_func)) {
										$usuario_area_func[] = $r_area_usuario_func->IDArea;
									}
									$arrayop = array();
									// consulto los modulos
									$query_area_func = $dbo->query("Select * from AreaFuncionario Where IDClub = '" . $frm["IDClub"] . "' Order by Nombre");
									while ($r_func = $dbo->object($query_area_func)) {
										$nombre_area = $r_func->Nombre;
										$arrayareasfunc[$nombre_area] = $r_func->IDArea;
									}
									echo SIMHTML::formCheckGroup($arrayareasfunc, $usuario_area_func, "AreaUsuarioFuncionario[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								Asignar Carreras del triatlon
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto los restaurantes disponibles del usuario
									$sql_usuario_carrera = $dbo->query("select * from UsuarioCarrera where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_usuario_carrera = $dbo->object($sql_usuario_carrera)) {
										$usuario_carrera[] = $r_usuario_carrera->IDCarrera;
									}
									$arrayop = array();
									// consulto los restaurantes del club
									$query_usuario_carrera = $dbo->query("Select * from Carrera Where IDClub = '" . $frm["IDClub"] . "' AND Activo='S' Order by Nombre");
									while ($r_carrera = $dbo->object($query_usuario_carrera)) {
										$nombre_restaurante = $r_carrera->Nombre;
										$arraycarrera[$nombre_restaurante] = $r_carrera->IDCarrera;
									}
									echo SIMHTML::formCheckGroup($arraycarrera, $usuario_carrera, "UsuarioCarrera[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>



						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								Asignar Restaurantes Domicilio 1
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto los restaurantes disponibles del usuario
									$sql_usuario_restaurante = $dbo->query("select * from UsuarioRestaurante where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_usuario_restaurante = $dbo->object($sql_usuario_restaurante)) {
										$usuario_restaurante[] = $r_usuario_restaurante->IDRestauranteDomicilio;
									}
									$arrayop = array();
									// consulto los restaurantes del club
									$query_usuario_restaurante = $dbo->query("Select * from RestauranteDomicilio Where IDClub = '" . $frm["IDClub"] . "'  Order by Nombre");
									while ($r_restaurante = $dbo->object($query_usuario_restaurante)) {
										$nombre_restaurante = $r_restaurante->Nombre;
										$arrayrestaurante[$nombre_restaurante] = $r_restaurante->IDRestauranteDomicilio;
									}
									echo SIMHTML::formCheckGroup($arrayrestaurante, $usuario_restaurante, "UsuarioRestaurante[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>


						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								Asignar Restaurantes Domicilio 2
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto los restaurantes disponibles del usuario
									$sql_usuario_restaurante2 = $dbo->query("select * from UsuarioRestaurante2 where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_usuario_restaurante2 = $dbo->object($sql_usuario_restaurante2)) {
										$usuario_restaurante2[] = $r_usuario_restaurante2->IDRestauranteDomicilio;
									}
									$arrayop = array();
									// consulto los restaurantes del club
									$query_usuario_restaurante2 = $dbo->query("Select * from RestauranteDomicilio2 Where IDClub = '" . $frm["IDClub"] . "'  Order by Nombre");
									while ($r_restaurante2 = $dbo->object($query_usuario_restaurante2)) {
										$nombre_restaurante2 = $r_restaurante2->Nombre;
										$arrayrestaurante2[$nombre_restaurante2] = $r_restaurante2->IDRestauranteDomicilio;
									}
									echo SIMHTML::formCheckGroup($arrayrestaurante2, $usuario_restaurante2, "UsuarioRestaurante2[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>



						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								Asignar Restaurantes Domicilio 3
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto los restaurantes disponibles del usuario
									$sql_usuario_restaurante3 = $dbo->query("select * from UsuarioRestaurante3 where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_usuario_restaurante3 = $dbo->object($sql_usuario_restaurante3)) {
										$usuario_restaurante3[] = $r_usuario_restaurante3->IDRestauranteDomicilio;
									}
									$arrayop = array();
									// consulto los restaurantes del club
									$query_usuario_restaurante3 = $dbo->query("Select * from RestauranteDomicilio3 Where IDClub = '" . $frm["IDClub"] . "'  Order by Nombre");
									while ($r_restaurante3 = $dbo->object($query_usuario_restaurante3)) {
										$nombre_restaurante3 = $r_restaurante3->Nombre;
										$arrayrestaurante3[$nombre_restaurante3] = $r_restaurante3->IDRestauranteDomicilio;
									}
									echo SIMHTML::formCheckGroup($arrayrestaurante3, $usuario_restaurante3, "UsuarioRestaurante3[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-globe green"></i>
								Asignar Restaurantes Domicilio 4
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php
									// Consulto los restaurantes disponibles del usuario
									$sql_usuario_restaurante4 = $dbo->query("select * from UsuarioRestaurante4 where IDUsuario = '" . $frm["IDUsuario"] . "'");
									while ($r_usuario_restaurante4 = $dbo->object($sql_usuario_restaurante4)) {
										$usuario_restaurante4[] = $r_usuario_restaurante4->IDRestauranteDomicilio;
									}
									$arrayop = array();
									// consulto los restaurantes del club
									$query_usuario_restaurante4 = $dbo->query("Select * from RestauranteDomicilio4 Where IDClub = '" . $frm["IDClub"] . "'  Order by Nombre");
									while ($r_restaurante4 = $dbo->object($query_usuario_restaurante4)) {
										$nombre_restaurante4 = $r_restaurante4->Nombre;
										$arrayrestaurante4[$nombre_restaurante4] = $r_restaurante4->IDRestauranteDomicilio;
									}
									echo SIMHTML::formCheckGroup($arrayrestaurante4, $usuario_restaurante4, "UsuarioRestaurante4[]", "&nbsp;"); ?>


								</div>
							</div>
						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-credit-card green"></i>
								<?= SIMUtil::get_traduccion('', '', 'CodigoCarne', LANGSESSION); ?>
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'CodigoBarras', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<? if (!empty($frm[CodigoBarras])) {
										echo "<img src='" . USUARIO_ROOT . "$frm[CodigoBarras]'>";
									?>
									<?
									} // END if
									?>


								</div>
							</div>


						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'CodigoQR', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm[CodigoQR])) {
										echo "<img src='" . USUARIO_ROOT . "qr/$frm[CodigoQR]'>";
									} // END if
									?>
									<?
									//echo SIMUtil::generar_qr($frm[IDSocio],$frm[NumeroDocumento]);
									?>


								</div>
							</div>


						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-camera green"></i>
								Foto
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php if (!empty($frm[Foto])) {
										echo "<img src='" . USUARIO_ROOT . "$frm[Foto]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?php
									} // END if
									else {
									?>
										<input name="Foto" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
									<?php } ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitir al usuario el cambio de foto?', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["FotoActualizadaEmpleado"], 'FotoActualizadaEmpleado', "class='input mandatory'") ?>
								</div>
							</div>
						</div>







						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-glass green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Servicios', LANGSESSION); ?>
							</h3>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-12">


								<div class="col-sm-12">

									<?php

									// Consulto los servicios disponibles al usuario
									$sql_servicio = $dbo->query("select * from UsuarioServicio where IDUsuario = '" . $frm[IDUsuario] . "'");
									while ($r_servicio = $dbo->object($sql_servicio)) {
										$id_serviciomaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_servicio->IDServicio . "'");
										$servicio_usuario[] = $id_serviciomaestro;
									}

									// Consulto los elementos disponibles al usuario
									unset($servicio_elemento_usuario);
									$sql_elemento = $dbo->query("select * from  UsuarioServicioElemento where IDUsuario = '" . $frm[IDUsuario] . "'");
									while ($r_elemento = $dbo->object($sql_elemento)) {
										$servicio_elemento_usuario[] = $r_elemento->IDServicioElemento;
									}

									// Consulto los elementos disponibles al usuario
									unset($servicio_auxiliar);
									$sql_aux = $dbo->query("select * from  UsuarioAuxiliar where IDUsuario = '" . $frm[IDUsuario] . "'");
									while ($r_aux = $dbo->object($sql_aux)) {
										$servicio_auxiliar[] = $r_aux->IDAuxiliar;
									}

									/*
									  $arrayop = array();
									  // consulto los servicios maestros
									  $query_servicios=$dbo->query("Select * from Servicio Where IDClub = '".$frm[IDClub]."' Order by IDServicio");
									  while($r=$dbo->object($query_servicios)){
											$nombre_servicio=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $r->IDServicioMaestro."'");
											$arrayservicio[$nombre_servicio]=$r->IDServicio;
									  }
									  */


									// echo SIMHTML::formCheckGroup( $arrayservicio , $servicio , "UsuarioServicio[]") 
									?>

									<table id="simple-table" class="table table-striped table-bordered table-hover">
										<tr>
											<th><?= SIMUtil::get_traduccion('', '', 'Servicios', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Elementos', LANGSESSION); ?></th>
										</tr>
										<tbody id="listacontactosanunciante">
											<?php

											$servicio_club = "Select SM.*
												  					From ServicioMaestro SM
																	Where SM.IDServicioMaestro in (Select IDServicioMaestro From ServicioClub Where IDClub = '" . $frm["IDClub"] . "' and Activo = 'S')";
											$r_servicioclub = $dbo->query($servicio_club);
											while ($r = $dbo->object($r_servicioclub)) {
											?>

												<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
													<td aling="center">
														<input type="checkbox" name="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" id="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" <?php if (in_array($r->IDServicioMaestro, $servicio_usuario)) echo "checked"; ?>>

														<?php
														$nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $frm["IDClub"] . "' and IDServicioMaestro = '" . $r->IDServicioMaestro . "'");
														if (empty($nombre_servicio_personalizado))
															$nombre_servicio_personalizado = $r->Nombre;
														else
															$nombre_servicio_personalizado = $nombre_servicio_personalizado;

														//echo $nombre_servicio_personalizado . " " . $r->Descripcion;
														echo $nombre_servicio_personalizado;

														?>
													</td>
													<td><br>

														<table id="simple-table" class="table table-striped table-bordered table-hover">
															<tr>
																<th><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?></th>
																<th><?= SIMUtil::get_traduccion('', '', 'Elementos', LANGSESSION); ?></th>
															</tr>
															<tr bgcolor="#EBEBEB">
																<td>
																	<input type="checkbox" onclick="marcar(this,'<?php echo "clase" . $r->IDServicioMaestro; ?>');" /> Marcar/Desmarcar Todos
																</td>
																<td>Marcar /Desmarcar Todo</td>
															</tr>
															<tbody id="listacontactosanunciante">
																<?php
																$id_servicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '" . $frm["IDClub"] . "'");
																$query_elemento = "Select SE.* from ServicioElemento SE  Where SE.Publicar = 'S' and SE.IDServicio = '" . $id_servicio . "' Order by Nombre";
																$r_servicioelementoclub = $dbo->query($query_elemento);
																while ($r_elemento = $dbo->object($r_servicioelementoclub)) {
																?>

																	<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
																		<td aling="center">
																			<input type="checkbox" class="clase<?php echo $r->IDServicioMaestro; ?>" name="IDServicioElemento<?php echo $r_elemento->IDServicioElemento; ?>" id="IDServicioElemento<?php echo $r_elemento->IDServicioMaestro; ?>" <?php if (in_array($r_elemento->IDServicioElemento, $servicio_elemento_usuario)) echo "checked"; ?>>
																		</td>
																		<td><?php echo $r_elemento->Nombre . " " . $r_elemento->Descripcion; ?>
																			<br>
																		</td>
																	</tr>
																<?php
																}
																?>

																<?php
																//Auxiliares del servicio
																$query_aux = "Select A.* from Auxiliar A  Where A.IDServicio = '" . $id_servicio . "' Order by Nombre";
																$r_aux = $dbo->query($query_aux);
																if ($dbo->rows($r_aux) > 0) {
																?>
																	<tr>
																		<th>Auxiliares Servicio</th>
																		<th>&nbsp;</th>
																	</tr <?php
																			//Auxiliares del servicio
																			$query_aux = "Select A.* from Auxiliar A  Where A.IDServicio = '" . $id_servicio . "' Order by Nombre";
																			$r_aux = $dbo->query($query_aux);
																			while ($row_aux = $dbo->object($r_aux)) {	?> <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
																	<td aling="center">
																		<input type="checkbox" class="clase<?php echo $r->IDServicioMaestro; ?>" name="IDAuxiliar<?php echo $row_aux->IDAuxiliar; ?>" id="IDAuxiliar<?php echo $row_aux->IDAuxiliar; ?>" <?php if (in_array($row_aux->IDAuxiliar, $servicio_auxiliar)) echo "checked"; ?>>
																	</td>
																	<td><?php echo $row_aux->Nombre; ?>
																		<br>
																	</td>
												</tr>
										<?php
																			}
																		}
										?>


										</tbody>
										<tr>
											<th class="texto" colspan="12"></th>
										</tr>
									</table>




									</td>
									</tr>
								<?php
											}
								?>
								</tbody>
								<tr>
									<th class="texto" colspan="12"></th>
								</tr>
								</table>






								</div>
							</div>
						</div>



						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="Nivel" id="Nivel" value="<?php echo $frm["Nivel"] ?>" />
								<!-- <input type="hidden" name="TipoUsuario" id="TipoUsuario" value="<?php echo $frm["TipoUsuario"] ?>" /> -->
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>


							</div>
						</div>

					</form>
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->