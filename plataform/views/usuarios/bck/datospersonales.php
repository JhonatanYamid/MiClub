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
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
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
								Datos Basicos
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club </label>

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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento </label>

								<div class="col-sm-8">
									<input type="number" id="NumeroDocumento" name="NumeroDocumento" placeholder="Numero Documento" class="col-xs-12 mandatory" title="Numero Documento" value="<?php echo $frm["NumeroDocumento"]; ?>">
								</div>
							</div>



						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

								<div class="col-sm-8">
									<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="Email" value="<?php echo $frm["Email"]; ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario </label>

								<div class="col-sm-8">
									<input type="text" id="User" name="User" placeholder="User" class="col-xs-12 " title="User" value="<?php echo $frm["User"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Password </label>

								<div class="col-sm-8">
									<input type="password" id="Password" name="Password" placeholder="Password" class="col-xs-12" title="Password" value="">
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Autorizado </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Autorizado"], "Autorizado", "title=\"Autorizado\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Repita Password </label>

								<div class="col-sm-8">
									<input type="password" id="RePassword" name="RePassword" placeholder="Repita Password" class="col-xs-12" title="Repita Password">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Empleado </label>

								<div class="col-sm-8">
									<input type="text" id="CodigoUsuario" name="CodigoUsuario" placeholder="Codigo Empleado" class="col-xs-12 form-control" title="Codigo Empleado" value="<?php echo $frm["CodigoUsuario"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>

								<div class="col-sm-8">
									<input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>">
								</div>
							</div>



						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Perfil </label>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permiso </label>

								<div class="col-sm-8">
									<input type="radio" name="Permiso" value="L" <?php if ($frm["Permiso"] == "L") echo "checked"; ?>> Lectura <input type="radio" name="Permiso" value="E" <?php if ($frm["Permiso"] == "E") echo "checked"; ?>> Lectura y Escritura
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<!--

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Area  </label>


                                        <div class="col-sm-8">
											 <select name = "IDArea" id="IDArea" class="form-control" >
                                        	<option value=""></option>
                                        <?php
										if (SIMUser::get("Nivel") == 0 && empty($frm["IDClub"])) :
											$condicion_club = "  1";
										else :
											if (!empty($frm["IDClub"]))
												$condicion_club = " IDClub = '" . $frm["IDClub"] . "'";
											else
												$condicion_club = " IDClub = '" . SIMUser::get("club") . "'";
										endif;

										$sql_area_lista = "Select * From Area Where $condicion_club ";
										$qry_area_lista = $dbo->query($sql_area_lista);
										while ($r_area_lista = $dbo->fetchArray($qry_area_lista)) : ?>
											<option value="<?php echo $r_area_lista["IDArea"]; ?>" <?php if ($r_area_lista["IDArea"] == $frm["IDArea"]) echo "selected";  ?>><?php echo $r_area_lista["Nombre"]; ?></option>
                                        <?php
										endwhile;    ?>
                                        </select>
										</div>

								</div>
                                 -->

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cargo </label>

								<div class="col-sm-8">
									<input type="text" id="Cargo" name="Cargo" placeholder="Cargo" class="col-xs-12 mandatory" title="Cargo" value="<?php echo $frm["Cargo"]; ?>">
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

								<div class="col-sm-8">
									<input type="text" id="Tipo" name="Tipo" placeholder="Tipo" class="col-xs-12 " title="Tipo" value="<?php echo $frm["Tipo"]; ?>">
								</div>
							</div>



						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio Contrato </label>

								<div class="col-sm-8">
									<input type="text" id="FechaInicioContrato" name="FechaInicioContrato" placeholder="Fecha Inicio Contrato" class="col-xs-12 calendar" title="Fecha Inicio Contrato" value="<?php echo $frm["FechaInicioContrato"] ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin Contrato </label>

								<div class="col-sm-8">
									<input type="text" id="FechaFinContrato" name="FechaFinContrato" placeholder="Fecha Fin Contrato" class="col-xs-12 calendar" title="Fecha Fin Contrato" value="<?php echo $frm["FechaFinContrato"] ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite crear reservas? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservar"], 'PermiteReservar', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Enviar push de reservas? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PushReserva"], 'PushReserva', "class='input mandatory'") ?>
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Si permite reservar puede reservar antes de abrir reservas en app? </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservarAntes"], 'PermiteReservarAntes', "class='input mandatory'") ?>
								</div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir editar y cambiar el due&ntilde;o de una reserva? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCambiarReserva"], 'PermiteCambiarReserva', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Activo </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>

						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Solicitar cambio de clave obligatorio cada 3 meses </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaCambioClave"], 'SolicitaCambioClave', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Sede </label>

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
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Cierre Sesion ? </label>

									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitarCierreSesion"], 'SolicitarCierreSesion', "class='input mandatory'") ?>
									</div>
								</div>




							</div>





						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Estado salud carne: </label>

								<div class="col-sm-8">
									<input type="text" id="LabelEstadoUsuario" name="LabelEstadoUsuario" placeholder="Label Estado Usuario" class="col-xs-12" title="Label Estado Usuario" value="<?php echo $frm["LabelEstadoUsuario"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar editar perfil </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaEditarPerfil"], 'SolicitaEditarPerfil', "class='input mandatory'") ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar mensaje cumpleaños </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarMensajeCumpleanos"], 'MostrarMensajeCumpleanos', "class='input mandatory'") ?>
								</div>
							</div>

						</div>

						<?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 89) : ?>
							<div class="form-group first ">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Area Soy Central </label>

									<div class="col-sm-8">
										<?php echo SIMHTML::formPopupArray(SIMResources::$areassoycentral,  $frm["IDAreaSocio"], "IDAreaSocio",  "Seleccione Area", "form-control"); ?>
									</div>
								</div>

							</div>

						<?php endif; ?>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Jefe </label>

								<div class="col-sm-8">
									<input type="text" id="NombreJefe" name="NombreJefe" placeholder="Nombre Jefe" class="col-xs-12" title="Nombre Jefe" value="<?php echo $frm["NombreJefe"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo Jefe </label>

								<div class="col-sm-8">
									<input type="text" id="CorreoJefe" name="CorreoJefe" placeholder="Correo Jefe" class="col-xs-12" title="Correo Jefe" value="<?php echo $frm["CorreoJefe"]; ?>">
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Documento Jefe </label>

								<div class="col-sm-8">
									<input type="text" id="DocumentoJefe" name="DocumentoJefe" placeholder="Documento Jefe" class="col-xs-12" title="Documento Jefe" value="<?php echo $frm["DocumentoJefe"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="NombreEspecialista"> Nombre Especialista/Aprobador </label>

								<div class="col-sm-8">
									<input type="text" id="NombreEspecialista" name="NombreEspecialista" placeholder="Nombre Especialista" class="col-xs-12" title="Nombre Especialista" value="<?php echo $frm["NombreEspecialista"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="CorreoEspecialista"> Correo Especialista/Aprobador</label>

								<div class="col-sm-8">
									<input type="text" id="CorreoEspecialista" name="CorreoEspecialista" placeholder="Correo Especialista" class="col-xs-12" title="Correo Especialista" value="<?php echo $frm["CorreoEspecialista"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="DocumentoEspecialista"> Documento Especialista/Aprobador</label>

								<div class="col-sm-8">
									<input type="text" id="DocumentoEspecialista" name="DocumentoEspecialista" placeholder="Documento Especialista" class="col-xs-12" title="Documento Especialista" value="<?php echo $frm["DocumentoEspecialista"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="DocumentoEspecialista"> Cupo para domicilio</label>

								<div class="col-sm-8">
									<input type="text" id="CupoDomicilio" name="CupoDomicilio" placeholder="Cupo Domicilio" class="col-xs-12" title="Documento Especialista" value="<?php echo $frm["CupoDomicilio"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="HoraInicioLaboral"> Hora inicio laboral</label>

								<div class="col-sm-8">
									<input type="time" id="HoraInicioLaboral" name="HoraInicioLaboral" placeholder="Hora Inicio Laboral" value="<?php echo $frm["HoraInicioLaboral"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="HoraInicioLaboral"> Hora final laboral</label>

								<div class="col-sm-8">
									<input type="time" id="HoraFinalLaboral" name="HoraFinalLaboral" placeholder="Hora Final Laboral" value="<?php echo $frm["HoraFinalLaboral"]; ?>">
								</div>
							</div>
						</div>

						
						<?php if (!empty($frm["IDUsuario"])) { ?>


							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-user green"></i>
									Datos Perfil
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
								Asignar Areas Pqr Socios
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
								Asignar Areas Pqr Funcionarios
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
								<i class="ace-icon fa fa-credit-card green"></i>
								Codigo Carn&eacute;
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Barras </label>

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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo QR </label>

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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

								<div class="col-sm-8">
									<?php if (!empty($frm[Foto])) {
										echo "<img src='" . USUARIO_ROOT . "$frm[Foto]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?php
									} // END if
									else {
									?>
										<input name="Foto" id=file class="" title="Foto" type="file" size="25" style="font-size: 10px">
									<?php } ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir al usuario el cambio de foto? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["FotoActualizadaEmpleado"], 'FotoActualizadaEmpleado', "class='input mandatory'") ?>
								</div>
							</div>
						</div>







						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-glass green"></i>
								Servicios
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
											<th>Servicio</th>
											<th>Elementos</th>
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
																<th>Activo</th>
																<th>Elemento</th>
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
								<input type="hidden" name="TipoUsuario" id="TipoUsuario" value="<?php echo $frm["TipoUsuario"] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
								</button>


							</div>
						</div>

					</form>
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->