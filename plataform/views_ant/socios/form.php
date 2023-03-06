<?
include("cmp/footer_scripts.php");
?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?>
			<?php echo $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];	?>
		</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->
					<div class="col-sm-12">
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="<?php if (empty($_GET[tabsocio])) echo "active"; ?>">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'DatosPersonales', LANGSESSION); ?>
									</a>
								</li>
								<li class="<?php if ($_GET[tabsocio] == "cargaplanosocio") echo "active"; ?>">
									<a data-toggle="tab" href="#cargaplanosocio">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?> <?php echo $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];	?> <?= SIMUtil::get_traduccion('', '', 'Lote', LANGSESSION); ?>
									</a>
								</li>
								<li class="<?php if ($_GET[tabsocio] == "cargamovimiento") echo "active"; ?>">
									<a data-toggle="tab" href="#cargamovimiento">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'SubirMovimientos', LANGSESSION); ?>
									</a>
								</li>
								<?php if (SIMNet::req("action") == "edit") : ?>
									<li class="<?php if ($_GET[tabsocio] == "vehiculos") echo "active"; ?>">
										<a data-toggle="tab" href="#vehiculos">
											<i class="green ace-icon fa fa-car  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Vehiculos', LANGSESSION); ?>
										</a>
									</li>

									<li class="<?php if ($_GET[tabsocio] == "licencias") echo "active"; ?>">
										<a data-toggle="tab" href="#licencias">
											<i class="green ace-icon fa fa-ticket  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'LicenciasdeConduccion', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "predios") echo "active"; ?>">
										<a data-toggle="tab" href="#predios">
											<i class="green ace-icon fa fa-home  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Predios', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "mascotas") echo "active"; ?>">
										<a data-toggle="tab" href="#mascotas">
											<i class="green ace-icon fa fa-linux  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Mascotas', LANGSESSION); ?>
										</a>
									</li>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "vacuna") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "vacuna2") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna2">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Vacunación2', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "ausente") echo "active"; ?>">
										<a data-toggle="tab" href="#ausente">
											<i class="green ace-icon fa fa-pause  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Ausencias', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "permisosclub") echo "active"; ?>">
										<a data-toggle="tab" href="#permisosclub">
											<i class="green ace-icon fa fa-times  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'PermisoClubes', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "historicopagos") echo "active"; ?>">
										<a data-toggle="tab" href="#historicopagos">
											<i class="green ace-icon fa fa-money  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Historialdecompras', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "historicoservicios") echo "active"; ?>">
										<a data-toggle="tab" href="#historicoservicios">
											<i class="green ace-icon fa fa-child  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Historicoservicios', LANGSESSION); ?>
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "habitaciones") echo "active"; ?>">
										<a data-toggle="tab" href="#habitaciones">
											<i class="green ace-icon fa fa-child  bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'FraccionesdeHabitaciones', LANGSESSION); ?>
										</a>
									</li>

								<?php endif; ?>

							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabsocio])) echo "in active"; ?> ">
									<?php
									if ($_GET["editarinfo"] != "n") : //condicion para cuando en porteria se ingresa a esta pantalla
										//include("datospersonales.php");
										include("datospersonales.php");
									endif;
									?>
								</div>
								<div id="cargaplanosocio" class="tab-pane fade <?php if ($_GET[tabsocio] == "cargaplanosocio") echo "in active"; ?> ">
									<?php
									if ($_GET["editarinfo"] != "n") : //condicion para cuando en porteria se ingresa a esta pantalla
										include("cargaplanosocio.php");
									endif;
									?>
								</div>

								<div id="cargamovimiento" class="tab-pane fade <?php if ($_GET[tabsocio] == "cargamovimientocargamovimiento") echo "in active"; ?> ">
									<?php
									if ($_GET["editarinfo"] != "n") : //condicion para cuando en porteria se ingresa a esta pantalla
										include("cargamovimiento.php");
									endif;
									?>
								</div>

								<?php if (SIMNet::req("action") == "edit") : ?>

									<div id="habitaciones" class="tab-pane fade <?php if ($_GET[tabsocio] == "habitaciones") echo "in active"; ?>">
										<?php include("habitacionessocio.php"); ?>
									</div>

									<div id="vehiculos" class="tab-pane fade <?php if ($_GET[tabsocio] == "vehiculos") echo "in active"; ?>">
										<?php include("vehiculos.php"); ?>
									</div>

									<div id="licencias" class="tab-pane fade <?php if ($_GET[tabsocio] == "licencias") echo "in active"; ?>">
										<?php include("licencias.php"); ?>
									</div>

									<div id="predios" class="tab-pane fade <?php if ($_GET[tabsocio] == "predios") echo "in active"; ?>">
										<?php include("predios.php"); ?>
									</div>
									<div id="mascotas" class="tab-pane fade <?php if ($_GET[tabsocio] == "mascotas") echo "in active"; ?>">
										<?php include("mascotas.php"); ?>
									</div>
									<div id="vacuna" class="tab-pane fade <?php if ($_GET[tabsocio] == "vacuna") echo "in active"; ?>">
										<?php include("vacuna.php"); ?>
									</div>
									<div id="vacuna2" class="tab-pane fade <?php if ($_GET[tabsocio] == "vacuna2") echo "in active"; ?>">
										<?php include("vacuna2.php"); ?>
									</div>
									<div id="ausente" class="tab-pane fade <?php if ($_GET[tabsocio] == "ausente") echo "in active"; ?>">
										<?php include("ausente.php"); ?>
									</div>
									<div id="permisosclub" class="tab-pane fade <?php if ($_GET[tabsocio] == "permisosclub") echo "in active"; ?>">
										<?php include("permisosclub.php"); ?>
									</div>
									<div id="historicopagos" class="tab-pane fade <?php if ($_GET[tabsocio] == "ausente") echo "in active"; ?>">
										<?php include("historicopagos.php"); ?>
									</div>
									<div id="historicoservicios" class="tab-pane fade <?php if ($_GET[tabsocio] == "historicoservicios") echo "in active"; ?>">
										<?php include("historicoservicios.php"); ?>
									</div>



								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div>