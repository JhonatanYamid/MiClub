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


								<?php if (SIMNet::req("action") == "edit") : ?>


									<li class="<?php if ($_GET[tabsocio] == "vehiculos") echo "active"; ?>">
										<a data-toggle="tab" href="#vehiculos">
											<i class="green ace-icon fa fa-car  bigger-120"></i>
											Vehiculos
										</a>
									</li>

									<li class="<?php if ($_GET[tabsocio] == "vacuna") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											Vacunación
										</a>
									</li>
									<li class="<?php if ($_GET[tabsocio] == "vacuna") echo "active"; ?>">
										<a data-toggle="tab" href="#vacuna2">
											<i class="green ace-icon fa fa-medkit  bigger-120"></i>
											Vacunación 2
										</a>
									</li>

									<?php if ($frm["TipoUsuario"] == "admin" && $frm["IDPerfil"] == "0") { ?>
										<li class="<?php if ($_GET[tabsocio] == "listaclub") echo "active"; ?>">
											<a data-toggle="tab" href="#listaclub">
												<i class="green ace-icon fa fa-medkit  bigger-120"></i>
												Asociar clubes
											</a>
										</li>
									<?php } ?>


								<?php endif; ?>

							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabsocio])) echo "in active"; ?> ">
									<?php
									if ($_GET["editarinfo"] != "n") : //condicion para cuando en porteria se ingresa a esta pantalla
										include("datospersonales.php");
									endif;
									?>
								</div>





								<?php if (SIMNet::req("action") == "edit") : ?>

									<div id="vehiculos" class="tab-pane fade <?php if ($_GET[tabsocio] == "VehiculoUsuarios") echo "in active"; ?>">
										<?php include("vehiculos.php"); ?>
									</div>
									<div id="vacuna" class="tab-pane fade <?php if ($_GET[tabsocio] == "vacuna") echo "in active"; ?>">
										<?php include("vacuna.php"); ?>
									</div>
									<div id="vacuna2" class="tab-pane fade <?php if ($_GET[tabsocio] == "vacuna2") echo "in active"; ?>">
										<?php include("vacuna2.php"); ?>
									</div>
									<div id="listaclub" class="tab-pane fade <?php if ($_GET[tabsocio] == "listaclub") echo "in active"; ?>">
										<?php include("listaclub.php"); ?>
									</div>
								<?php endif; ?>

							</div>
						</div>


					</div>

				</div>
			</div>


		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>