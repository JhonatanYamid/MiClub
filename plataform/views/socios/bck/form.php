
<?
include("cmp/footer_scripts.php");
?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO
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
										Datos Personales
									</a>
								</li>
								<li class="<?php if ($_GET[tabsocio] == "cargaplanosocio") echo "active"; ?>">
									<a data-toggle="tab" href="#cargaplanosocio">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										Actualizar <?php echo $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];	?> Lote
									</a>
								</li>
								<li class="<?php if ($_GET[tabsocio] == "cargamovimiento") echo "active"; ?>">
									<a data-toggle="tab" href="#cargamovimiento">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										Subir Movimientos
									</a>
								</li>
								<?php if (SIMNet::req("action") == "edit") : ?>
									<li class="<?php if ($_GET[tabsocio] == "vehiculos") echo "active"; ?>">
										<a data-toggle="tab" href="#vehiculos">
											<i class="green ace-icon fa fa-car  bigger-120"></i>
											Vehiculos
										</a>
									</li>

									<li class="<?php if ($_GET[tabsocio] == "licencias") echo "active"; ?>">
										<a data-toggle="tab" href="#licencias">
											<i class="green ace-icon fa fa-ticket  bigger-120"></i>
											Licencias de Conduccion
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "predios") echo "active"; ?>">
										<a data-toggle="tab" href="#predios">
											<i class="green ace-icon fa fa-home  bigger-120"></i>
											Predios
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "mascotas") echo "active"; ?>">
										<a data-toggle="tab" href="#mascotas">
											<i class="green ace-icon fa fa-linux  bigger-120"></i>
											Mascotas
										</a>
									</li>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "vacuna") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											Vacunación
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "vacuna2") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna2">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											Vacunación 2
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "ausente") echo "active"; ?>">
										<a data-toggle="tab" href="#ausente">
											<i class="green ace-icon fa fa-pause  bigger-120"></i>
											Ausencias
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "permisosclub") echo "active"; ?>">
										<a data-toggle="tab" href="#permisosclub">
											<i class="green ace-icon fa fa-times  bigger-120"></i>
											Permiso Clubes
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
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div>
