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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm" name="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">




						<?php
						//Campos carrera 
						$sql_campos_registro = "Select Campo.*,CF.Valores From CarreraFormulario CF, CampoFormulario Campo, SegmentoFormulario SF Where CF.IDCampoFormulario=Campo.IDCampoFormulario and SF.IDSegmentoFormulario=Campo.IDSegmentoFormulario and CF.IDCarrera = '" . $frm[IDCarrera] . "' and CF.Activo='S' and CF.IDTipoCarrera = '" . $frm[IDTipoCarrera] . "' and  Campo.IDSegmentoFormulario <> 4 Order By SF.Orden, Campo.IDSegmentoFormulario, CF.Orden ASC";
						$qry_campos_registro = $dbo->query($sql_campos_registro);
						while ($r_campos_registro = $dbo->fetchArray($qry_campos_registro)) {
							$array_campos_registro[] = $r_campos_registro;
						}

						$contador_campo = 2;
						foreach ($array_campos_registro as $key_campo => $value_campo) :
							if ($segmento_anterior != $value_campo["IDSegmentoFormulario"]) :
								if ($contador_campo % 2 == 1) :
									echo '<div  class="form-group first ">';
								endif;
						?>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $value_campo["EtiquetaCampo"]; ?> </label>

									<div class="col-sm-8">
										<?php mostrar_campo($value_campo, $frm["IDParticipante"]); ?>
									</div>
								</div>

								<?php if ($contador_campo % 2 == 1) :
									echo '</div>';
								endif;
								?>
						<?php
							endif;
							$contador_campo++;
						endforeach;
						?>






						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Carrera </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Carrera", "Nombre", "Nombre", "IDCarrera", $frm["IDCarrera"], "[Seleccione]", "form-control mandatory", "title = \"Carrera\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Carrera </label>
								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("TipoCarrera", "Nombre", "Nombre", "IDTipoCarrera", $frm["IDTipoCarrera"], "[Seleccione]", "form-control mandatory", "title = \"Tipo Carrera\"") ?>

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Factura </label>

								<div class="col-sm-8">
									<input type="text" id="IDFactura" name="IDFactura" placeholder="Factura" class="col-xs-12 mandatory" title="Factura" value="<?php echo $frm["IDFactura"]; ?>" readonly>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vendedor </label>
								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Vendedor", "Nombre", "Nombre", "IDVendedor", $frm["IDVendedor"], "[Seleccione]", "form-control", "title = \"Vendedor\"") ?>

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Departamento </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Departamento", "Nombre", "Nombre", "IDDepartamento", $frm["IDDepartamento"], "[Seleccione]", "form-control mandatory", "title = \"Departamento\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciudad </label>
								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Ciudad", "Nombre", "Nombre", "IDCiudad", $frm["IDCiudad"], "[Seleccione]", "form-control mandatory", "title = \"Ciudad\"") ?>

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>

								<div class="col-sm-8">
									<input type="text" id="Estado" name="Estado" placeholder="Estado" class="col-xs-12 mandatory" title="Estado" value="<?php echo $frm["Estado"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Inscripcion </label>
								<div class="col-sm-8">
									<input type="text" id="ValorInscripcion" name="ValorInscripcion" placeholder="Valor Inscripcion" class="col-xs-12 mandatory" title="Valor Inscripcion" value="<?php echo $frm["ValorInscripcion"]; ?>">

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cedula Recibe Camiseta </label>

								<div class="col-sm-8">
									<input type="text" id="CedulaRecibeCamiseta" name="CedulaRecibeCamiseta" placeholder="Cedula Recibe Camiseta" class="col-xs-12" title="Cedula Recibe Camiseta" value="<?php echo $frm["CedulaRecibeCamiseta"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Recibe Camiseta </label>
								<div class="col-sm-8">
									<input type="text" id="NombreRecibeCamiseta" name="NombreRecibeCamiseta" placeholder="Nombre Recibe Camiseta" class="col-xs-12" title="Nombre Recibe Camiseta" value="<?php echo $frm["NombreRecibeCamiseta"]; ?>">

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Camiseta </label>

								<div class="col-sm-8">
									<input type="text" id="NumeroCamiseta" name="NumeroCamiseta" placeholder="Numero Camiseta" class="col-xs-12" title="Numero Camiseta" value="<?php echo $frm["NumeroCamiseta"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Talla Entregada </label>
								<div class="col-sm-8">
									<input type="text" id="TallaEntregada" name="TallaEntregada" placeholder="Talla Entregada" class="col-xs-12" title="Talla Entregada" value="<?php echo $frm["TallaEntregada"]; ?>">

								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Entrega Camiseta </label>

								<div class="col-sm-8">
									<input type="text" id="FechaEntrega" name="FechaEntrega" placeholder="Fecha Entrega" class="col-xs-12" title="Fecha Entrega" value="<?php echo $frm["FechaEntrega"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vendedor Entrega </label>
								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("Vendedor", "Nombre", "Nombre", "IDVendedor", $frm["IDVendedorEntrega"], "[Seleccione]", "form-control", "title = \"Vendedor\"") ?>

								</div>
							</div>

						</div>



						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm">
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

<?
include("cmp/footer_scripts.php");
?>