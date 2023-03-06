<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

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
								<li class="active">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-home bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'Galeria', LANGSESSION); ?>
									</a>
								</li>


								<?php if (SIMNet::req("action") == "edit") : ?>

									<li>
										<a data-toggle="tab" href="#messages">
											<?= SIMUtil::get_traduccion('', '', 'GaleriaFotos', LANGSESSION); ?>
											<span class="badge badge-danger"><?php echo count($array_fotos); ?></span>
										</a>
									</li>

								<?php endif; ?>

							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade in active">
									<?php include("galeria.php"); ?>
								</div>

								<?php if (SIMNet::req("action") == "edit") : ?>

									<div id="messages" class="tab-pane fade">
										<?php include("fotos.php"); ?>
									</div>


								<?php endif; ?>

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