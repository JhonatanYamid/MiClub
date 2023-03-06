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


					<div class="col-sm-12">
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="<?php if (empty($_GET[tabsocio])) echo "active"; ?>">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'General', LANGSESSION); ?>
									</a>
								</li>



								<?php if (SIMNet::req("action") == "edit") : ?>

									<li class="<?php if ($_GET[tabsocio] == "caracteristica") echo "active"; ?>">
										<a data-toggle="tab" href="#caracteristica">
											<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'CaracteristicaProducto', LANGSESSION); ?>
										</a>
									</li>

								<?php endif; ?>

							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabsocio])) echo "in active"; ?> ">
									<?php
									if ($_GET["editarinfo"] != "n") : //condicion para cuando en porteria se ingresa a esta pantalla
										include("datos.php");
									endif;
									?>
								</div>
								<?php if (SIMNet::req("action") == "edit") : ?>

									<div id="caracteristica" class="tab-pane fade <?php if ($_GET[tabsocio] == "caracteristica") echo "in active"; ?> ">
										<?php
										include("caracteristica.php");

										?>
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