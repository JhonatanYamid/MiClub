<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa fa-users green"></i>
			Datos Personales
		</h3>
	</div>



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

			<div class="col-sm-8">
				<select name="IDTipoInvitado" id="IDTipoInvitado" title="Tipo Invitado" class="form-control mandatory">
					<option value=""></option>
					<?php
					$sql_tipoinv_club = "Select * From TipoInvitado Where IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S'";
					$qry_tipoinv_club = $dbo->query($sql_tipoinv_club);
					while ($r_tipoinv = $dbo->fetchArray($qry_tipoinv_club)) : ?>
						<option value="<?php echo $r_tipoinv["IDTipoInvitado"]; ?>" <?php if ($r_tipoinv["IDTipoInvitado"] == $frm["IDTipoInvitado"]) echo "selected";  ?>><?php echo $r_tipoinv["Nombre"]; ?></option>
					<?php
					endwhile;    ?>
				</select>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clasificacion </label>

			<div class="col-sm-8">
				<select name="IDClasificacionInvitado" id="IDClasificacionInvitado" class="form-control">
					<option value="">Clasificacion</option>
					<?php
					if (!empty($frm["IDTipoInvitado"])) :
						$sql_clasifinv_club = "Select * From ClasificacionInvitado Where IDTipoInvitado = '" . $frm["IDTipoInvitado"] . "'";
						$qry_clasifinv_club = $dbo->query($sql_clasifinv_club);
						while ($r_clasifinv = $dbo->fetchArray($qry_clasifinv_club)) : ?>
							<option value="<?php echo $r_clasifinv["IDClasificacionInvitado"]; ?>" <?php if ($r_clasifinv["IDClasificacionInvitado"] == $frm["IDClasificacionInvitado"]) echo "selected";  ?>><?php echo $r_clasifinv["Nombre"]; ?></option>
					<?php
						endwhile;
					endif;
					?>
				</select>
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cargo </label>

			<div class="col-sm-8">
				<input type="text" id="Cargo" name="Cargo" placeholder="Cargo" class="col-xs-12 " title="Cargo" value="<?php echo $frm["Cargo"]; ?>">
			</div>
		</div>



	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Documento </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formPopUp("TipoDocumento", "Nombre", "Nombre", "IDTipoDocumento", $frm["IDTipoDocumento"], "[Seleccione tipo documento]", "form-control mandatory", "title = \"Tipo Documento\"") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento </label>

			<div class="col-sm-8">
				<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Numero Documento" class="col-xs-12 mandatory" title="Numero Documento" value="<?php echo $frm["NumeroDocumento"]; ?>">
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
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido </label>

			<div class="col-sm-8">
				<input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="Apellido" value="<?php echo $frm["Apellido"]; ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion </label>

			<div class="col-sm-8">
				<input type="text" id="Direccion" name="Direccion" placeholder="Direccion" class="col-xs-12 " title="Direccion" value="<?php echo $frm["Direccion"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciudad Residencia </label>

			<div class="col-sm-8">
				<input type="text" id="CiudadResidencia" name="CiudadResidencia" placeholder="CiudadResidencia" class="col-xs-12" title="Ciudad Residencia" value="<?php echo $frm["CiudadResidencia"]; ?>">
			</div>
		</div>



	</div>



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono1 </label>

			<div class="col-sm-8">
				<input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 " title="Telefono" value="<?php echo $frm["Telefono"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono 2 </label>

			<div class="col-sm-8">
				<input type="text" id="Telefono2" name="Telefono2" placeholder="Telefono2" class="col-xs-12" title="Telefono2" value="<?php echo $frm["Telefono2"]; ?>">
			</div>
		</div>



	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email 1 </label>

			<div class="col-sm-8">
				<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 " title="Email" value="<?php echo $frm["Email"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Predio al que se dirige </label>

			<div class="col-sm-8">
				<input type="text" id="Predio" name="Predio" placeholder="Predio" class="col-xs-12 " title="Predio" value="<?php echo $frm["Predio"]; ?>">
			</div>
		</div>



	</div>

	<div class="form-group first ">


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Predio al que se dirige </label>

			<div class="col-sm-8">
				<input type="text" id="Predio2" name="Predio2" placeholder="Predio2" class="col-xs-12 " title="Predio2" value="<?php echo $frm["Predio2"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Predio al que se dirige </label>

			<div class="col-sm-8">
				<input type="text" id="Predio3" name="Predio3" placeholder="Predio3" class="col-xs-12 " title="Predio3" value="<?php echo $frm["Predio3"]; ?>">
			</div>
		</div>



	</div>



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo </label>

			<div class="col-sm-8">
				<input type="text" id="Codigo" name="Codigo" placeholder="Codigo" class="col-xs-12" title="Codigo" value="<?php echo $frm["Codigo"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio Contrato </label>

			<div class="col-sm-8">
				<input type="text" id="FechaContrato" name="FechaContrato" placeholder="Fecha Inicio Contrato" class="col-xs-12 calendar" title="Fecha Inicio Contrato" value="<?php echo $frm["FechaContrato"] ?>">
			</div>
		</div>



	</div>

	<!--
                          <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i>
                                   Licencia Conducci&oacute;n
                                </h3>
                            </div>


                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Licencia de Conduccion </label>

										<div class="col-sm-8">
                                        <input type="text" id="LicenciaConduccion" name="LicenciaConduccion" placeholder="LicenciaConduccion" class="col-xs-12" title="LicenciaConduccion" value="<?php echo $frm["LicenciaConduccion"]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Expedicion </label>

										<div class="col-sm-8">
                                            <input type="text" id="FechaExpedicion" name="FechaExpedicion" placeholder="Fecha Expedicion" class="col-xs-12 calendar" title="Fecha Expedicion" value="<?php echo $frm["FechaExpedicion"] ?>" >
										</div>
								</div>



							</div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> FechaVencimiento </label>

										<div class="col-sm-8">
                                        <input type="text" id="FechaVencimiento" name="FechaVencimiento" placeholder="Fecha Vencimiento" class="col-xs-12 calendar" title="Fecha Vencimiento" value="<?php echo $frm["FechaVencimiento"] ?>" >
                                        </div>
								</div>



							</div>

                            -->

	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa fa-bell green"></i>
			Datos Emergencia
		</h3>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="NombreEmergencia" name="NombreEmergencia" placeholder="NombreEmergencia" class="col-xs-12" title="NombreEmergencia" value="<?php echo $frm["NombreEmergencia"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="ApellidoEmergencia" name="ApellidoEmergencia" placeholder="Apellido Emergencia" class="col-xs-12" title="Apellido Emergencia" value="<?php echo $frm["ApellidoEmergencia"]; ?>">
			</div>
		</div>

	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="NumeroDocumentoEmergencia" name="NumeroDocumentoEmergencia" placeholder="Numero Documento Emergencia" class="col-xs-12" title="NumeroDocumentoEmergencia" value="<?php echo $frm["NumeroDocumentoEmergencia"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="DireccionEmergencia" name="DireccionEmergencia" placeholder="Direccion Emergencia" class="col-xs-12" title="Direccion Emergencia" value="<?php echo $frm["DireccionEmergencia"]; ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="TelefonoEmergencia" name="TelefonoEmergencia" placeholder="Telefono Emergencia" class="col-xs-12" title="Telefono Emergencia" value="<?php echo $frm["TelefonoEmergencia"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Emergencia </label>

			<div class="col-sm-8">
				<input type="text" id="EmailEmergencia" name="EmailEmergencia" placeholder="Email Emergencia" class="col-xs-12" title="Email Emergencia" value="<?php echo $frm["EmailEmergencia"]; ?>">
			</div>
		</div>

	</div>


	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa fa-plus-circle green"></i>
			Seguridad Social
		</h3>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ARL </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formPopUp("Arl", "Nombre", "Nombre", "IDArl", $frm["IDArl"], "[Seleccione]", "form-control", "title = \"ARL\"") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Vencimiento ARL </label>

			<div class="col-sm-8">
				<input type="text" id="FechaVencimientoArl" name="FechaVencimientoArl" placeholder="Fecha Vencimiento Arl" class="col-xs-12 calendar" title="Fecha Vencimiento Arl" value="<?php echo $frm["FechaVencimientoArl"] ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> AFP </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formPopUp("Afp", "Nombre", "Nombre", "IDAfp", $frm["IDAfp"], "[Seleccione]", "form-control", "title = \"AFP\"") ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> EPS </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formPopUp("Eps", "Nombre", "Nombre", "IDEps", $frm["IDEps"], "[Seleccione]", "form-control", "title = \"EPS\"") ?>
			</div>
		</div>

	</div>


	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Vencimiento SOS </label>

			<div class="col-sm-8">
				<input type="text" id="FechaVencimientoSOS" name="FechaVencimientoSOS" placeholder="FechaVencimientoSOS" class="col-xs-12 calendar" title="Fecha Vencimiento Arl" value="<?php echo $frm["FechaVencimientoSOS"] ?>">
			</div>
		</div>

	</div>



	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa  fa-comments green"></i>
			Observaciones
		</h3>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion General </label>

			<div class="col-sm-8">
				<textarea id="ObservacionGeneral" name="ObservacionGeneral" cols="10" rows="5" class="col-xs-12 " title="ObservacionGeneral"><?php echo $frm["ObservacionGeneral"]; ?></textarea>
			</div>
		</div>

		<!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion Especial </label>

										<div class="col-sm-8">
                                        <textarea id="ObservacionEspecial" name="ObservacionEspecial" cols="10" rows="5" class="col-xs-12 " title="ObservacionEspecial"><?php echo $frm["ObservacionEspecial"]; ?></textarea>
                                        </div>
								</div>
                                -->

	</div>

	<!-- covid -19 -->



	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa fa-info-circle green"></i>
			Covid - 19
		</h3>
	</div>

	<div class="form-group first ">
		<script>
			/* 	function mostrar(dato) {
								console.log(dato)
  								if (dato == "S") {
    							document.getElementById("primeradosis").style.visibility = "visible";
    							document.getElementById("segundadosis").style.visibility = "visible";
    							document.getElementById("marca").style.visibility = "visible";
								document.getElementById("certificado").style.visibility = "visible";
  								}
  							if (dato == "N") {
								document.getElementById("primeradosis").style.visibility = "hidden";
    							document.getElementById("segundadosis").style.visibility = "hidden";
    							document.getElementById("marca").style.visibility = "hidden";
								document.getElementById("certificado").style.visibility = "visible";
  							}

							} */

			function mostrar(dato) {

				if (dato == "S") {
					document.getElementById("primeradosis").style.display = "block";
					document.getElementById("segundadosis").style.display = "block";
					document.getElementById("terceradosis").style.display = "block";
					document.getElementById("pdfterceradosis").style.display = "block";
					document.getElementById("marca").style.display = "block";
					document.getElementById("certificado").style.display = "block";
					document.getElementById("certificadoterceradosis").style.display = "block";
				}
				if (dato == "N") {
					document.getElementById("primeradosis").style.display = "none";
					document.getElementById("segundadosis").style.display = "none";
					document.getElementById("terceradosis").style.display = "none";
					document.getElementById("pdfterceradosis").style.display = "none";
					document.getElementById("marca").style.display = "none";
					document.getElementById("certificado").style.display = "none";
					document.getElementById("certificadoterceradosis").style.display = "none";
				}

			}
		</script>
		<?php
		$dbo = &SIMDB::get();
		$query = $dbo->query("SELECT V.* FROM Invitado I LEFT JOIN Vacuna V ON I.IDInvitado=V.IDInvitado WHERE I.IDInvitado=" . $_GET['id']);
		$frm_vacuna = $dbo->fetch($query);
		$query = $dbo->query("SELECT * FROM VacunaMarca");
		$marcaVacunas = $dbo->fetch($query);

		//Dato list entidad vacuna
		$query = $dbo->query("SELECT IDVacunaEntidad, Nombre FROM VacunaEntidad WHERE IDClub=" . SIMUser::get("club"));
		$entidadVacunas = $dbo->fetch($query);


		if (isset($entidadVacunas["IDVacunaEntidad"])) {
			$entidadVacunas = [$entidadVacunas];
		}
		//Dato list marca vacuna
		$query = $dbo->query("SELECT * FROM VacunaMarca");
		$marcaVacunas = $dbo->fetch($query);



		if (empty($marcaVacunas[0]["IDVacunaMarca"])) {
			$marcaVacunas = [$marcaVacunas];
		}

		if (empty($frm_vacuna['IDVacuna'])) {
			$frm_vacuna['Vacunado'] = 'N';
		}

		if ($frm_vacuna["FechaPrimeraDosis"] === "0000-00-00") {
			$frm_vacuna["FechaPrimeraDosis"] = "";
		}

		if ($frm_vacuna["FechaSegundaDosis"] === "0000-00-00") {
			$frm_vacuna["FechaSegundaDosis"] = "";
		}
		if ($frm_vacuna["FechaTerceraDosis"] === "0000-00-00") {
			$frm_vacuna["FechaTerceraDosis"] = "";
		}


		if ($frm_vacuna['Vacunado'] == 'N') {

			$checked_no = 'checked';
		}

		if ($frm_vacuna['Vacunado'] == 'S') {
			$checked_si = 'checked';
		}
		?>
		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado de vacunación? </label>
				<div class="col-sm-8">


					<input type="radio" name="Vacunado" value="S" id="Vacunado" <?php echo $checked_si ?> onchange="mostrar(this.value); ">SI
					<input type="radio" name="Vacunado" value="N" id="Vacunado" <?php echo $checked_no ?> onchange="mostrar(this.value); ">NO

				</div>
			</div>


		</div>






		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6" id="marca">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Marca</label>
				<div class="col-sm-8">
					<select name="IDVacunaMarca" id="IDVacunaMarca" class="form-control">
						<option value="">[Seleccione Marca vacuna]</option>

						<?php foreach ($marcaVacunas as $value) { ?>
							<option <?php if ($frm_vacuna['IDVacunaMarca'] == $value["IDVacunaMarca"]) {
										echo " selected ";
									} ?>value="<?php echo $value["IDVacunaMarca"] ?>"><?php echo $value["Nombre"] ?></option>
							<!-- <option value="<?php echo $value["IDVacunaMarca"] ?>"><?php echo $value["Nombre"] ?></option> -->
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-xs-12 col-sm-6" id="segundadosis">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha segunda dosis</label>
				<div class="col-sm-8">
					<input type="text" id="FechaSegundaDosis" name="FechaSegundaDosis" placeholder="Fecha Segunda Dosis" class="col-xs-12 calendar" title="Fecha Segunda Dosis" value="<?php echo $frm_vacuna["FechaSegundaDosis"]; ?>">
				</div>
			</div>

		</div>
		<!-- 	<div  class="form-group first ">								
								
								
                        </div>
					 -->

		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6" id="primeradosis">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha primera dosis</label>
				<div class="col-sm-8">
					<input type="text" id="FechaPrimeraDosis" name="FechaPrimeraDosis" placeholder="Fecha primera dosis" class="col-xs-12 calendar" title="FechaPrimeraDosis" value="<?php echo $frm_vacuna["FechaPrimeraDosis"]; ?>">
				</div>
			</div>

			<div class="col-xs-12 col-sm-6" id="certificado">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Certificado/Prueba covid</label>
				<div class="col-sm-8">
					<?php if ($frm_vacuna["ImagenPrimeraDosis"]) { ?>
						<h5>Imagen actual</h5>
						<img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" width="200">
						<a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
						<a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenPrimeraDosis"] . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

					<?php } ?>
					<br />
					<br />
					<input type="file" id="ImagenPrimeraDosis" name="ImagenPrimeraDosis" class="col-xs-12" title="ImagenPrimeraDosis" value="<?php echo $frm_vacuna["ImagenPrimeraDosis"]; ?>">
				</div>
			</div>

		</div>


		<div class="form-group first ">
			<div class="col-xs-12 col-sm-6" id="terceradosis">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha tercera dosis</label>
				<div class="col-sm-8">
					<input type="text" id="FechaTerceraDosis" name="FechaTerceraDosis" placeholder="Fecha tercera dosis" class="col-xs-12 calendar" title="Fecha Tercera Dosis" value="<?php echo $frm_vacuna["FechaTerceraDosis"]; ?>">
				</div>
			</div>
			<div class="col-xs-12 col-sm-6" id="certificadoterceradosis">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Refuerzo Vacuna</label>
				<div class="col-sm-8">
					<?php if ($frm_vacuna["ImagenTerceraDosis"]) { ?>
						<h5>Imagen actual</h5>
						<img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenTerceraDosis"] ?>" width="200">
						<a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenTerceraDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
						<a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenTerceraDosis"] . "&num_img=Tercera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

					<?php } ?>
					<br />
					<br />
					<input type="file" id="ImagenTerceraDosis" name="ImagenTerceraDosis" class="col-xs-12" title="ImagenTerceraDosis" value="<?php echo $frm_vacuna["ImagenTerceraDosis"]; ?>">
				</div>
			</div>
		</div>

		<div class="form-group first " id="pdfterceradosis">
			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Pdf refuerzo Vacuna</label>
				<div class="col-sm-8">
					<?php if ($frm_vacuna["PdfTerceraDosis"]) { ?>
						<iframe src="<?= VACUNA_ROOT . $frm_vacuna["PdfTerceraDosis"] ?>" style="width:100%; height:300px;" frameborder="0"></iframe>
						<a href="<? echo $script . ".php?action=del-pdf&archivo=" . $frm_vacuna["PdfTerceraDosis"] . "&num_img=Tercera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
					<?php } ?>
					<input type="file" name="PdfTerceraDosis" id="PdfTerceraDosis" class="" title="PdfTerceraDosis" value="<?php echo $frm_vacuna["PdfTerceraDosis"]; ?>">
				</div>
			</div>

		</div>









	</div>


	<!-- fin covid 19 -->

	<div class="widget-header widget-header-large">
		<h3 class="widget-title grey lighter">
			<i class="ace-icon fa fa-info-circle green"></i>
			Otros Datos
		</h3>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Nacimiento </label>

			<div class="col-sm-8">
				<input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder="Fecha Nacimiento" class="col-xs-12 calendar" title="Fecha Nacimiento" value="<?php echo $frm["FechaNacimiento"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar Nacimiento </label>

			<div class="col-sm-8">
				<input type="text" id="LugarNacimiento" name="LugarNacimiento" placeholder="LugarNacimiento" class="col-xs-12" title="Lugar Nacimiento" value="<?php echo $frm["LugarNacimiento"]; ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Expedicion Documento </label>

			<div class="col-sm-8">
				<input type="text" id="FechaExpedicionDocumento" name="FechaExpedicionDocumento" placeholder="Fecha Expedicion Documento" class="col-xs-12 calendar" title="Fecha Expedicion Documento" value="<?php echo $frm["FechaExpedicionDocumento"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estatura </label>

			<div class="col-sm-8">
				<input type="text" id="Estatura" name="Estatura" placeholder="Estatura" class="col-xs-12" title="Estatura" value="<?php echo $frm["Estatura"]; ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> GrupoSanguineo </label>

			<div class="col-sm-8">
				<input type="text" id="GrupoSanguineo" name="GrupoSanguineo" placeholder="Grupo Sanguineo" class="col-xs-12" title="Grupo Sanguineo" value="<?php echo $frm["GrupoSanguineo"]; ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

			<div class="col-sm-8">
				<?php
				if ($frm["FotoFile"]) {
				?>
					<img alt="<?php echo $frm["FotoFile"] ?>" src="<?php echo IMGINVITADO_ROOT . $frm["FotoFile"] ?>" width="100px">
					<a href="<? echo $script . ".php?action=DelImgNot&cam=FotoFile&id=" . $frm[$key] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?php
				} else {
				?>
					<input type="file" name="FotoImagen" id="FotoImagen" class="popup" title="Foto Imagen">
				<?php
				}
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">


		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>

			<div class="col-sm-8">
				<?php echo SIMHTML::formPopUp("EstadoInvitado", "Nombre", "Nombre", "IDEstadoInvitado", $frm["IDEstadoInvitado"], "[Seleccione el Estado]", "form-control mandatory", "title = \"Estado\"") ?>
			</div>
		</div>

	</div>



	<?php if ($frm["IDEstadoInvitado"] != "3")
		$oculta_razon = "style='display:none'";

	else
		$oculta_razon = "";
	?>

	<div <?php echo $oculta_razon; ?> id="divrazonbloqueo">
		<div class="form-group first ">


			<div class="col-xs-12 col-sm-6">
				<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Razón Bloqueo </label>

				<div class="col-sm-8">
					<textarea id="RazonBloqueo" name="RazonBloqueo" cols="10" rows="5" class="col-xs-12 " title="Razon Bloqueo"><?php echo $frm["RazonBloqueo"]; ?></textarea>
				</div>
			</div>

		</div>
	</div>


	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $_GET['id'] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
			<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																	else echo $frm["IDClub"];  ?>" />
			<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
				<i class="ace-icon fa fa-check bigger-110"></i>
				<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
			</button>


		</div>
	</div>

</form>