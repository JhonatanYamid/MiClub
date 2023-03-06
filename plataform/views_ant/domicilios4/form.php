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
								<?php if (SIMNet::req("action") == "edit") : ?>
									<li class="<?php if (empty($_GET[tabdomicilios])) echo "active"; ?>">
										<a data-toggle="tab" href="#home">
											<i class="green ace-icon fa fa-coffee bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'HacerPedido', LANGSESSION); ?>
										</a>
									</li>
								<?php endif; ?>

								<?php if (SIMNet::req("action") == "add") : ?>
									<li class="<?php if (empty($_GET[tabdomicilios])) echo "active"; ?>">
										<a data-toggle="tab" href="#messages">
											<i class="green ace-icon fa fa-cutlery bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Crearordencompleta', LANGSESSION); ?>
										</a>
									</li>

								<?php endif; ?>
							</ul>

							<div class="tab-content">
								<?php if (SIMNet::req("action") == "edit") : ?>
									<div id="home" class="tab-pane fade <?php if (empty($_GET[tabdomicilios])) echo "in active"; ?> ">
										<?php include("datos.php"); ?>
									</div>
								<?php endif; ?>

								<?php if (SIMNet::req("action") == "add") : ?>
									<div id="messages" class="tab-pane fade <?php if (empty($_GET[tabdomicilios])) echo "in active"; ?>">
										<?php include("orden.php"); ?>
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