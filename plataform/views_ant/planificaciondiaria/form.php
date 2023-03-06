<?
include("cmp/footer_scripts.php");
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
								<li class="<?php if (empty($_GET[tabregistrofuncionario])) echo "active"; ?>">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'Registroporfuncionario', LANGSESSION); ?>
									</a>
								</li>
								<li class="<?php if ($_GET[tabregistrofuncionario] == "registrolote") echo "active"; ?>">
									<a data-toggle="tab" href="#registrolote">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'Registrosenlote', LANGSESSION); ?>
									</a>
								</li>
							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabregistrofuncionario])) echo "in active"; ?> ">
									<?php include("registrofuncionario.php");?>
								</div>
								<div id="registrolote" class="tab-pane fade <?php if ($_GET[tabregistrofuncionario] == "registrolote") echo "in active"; ?> ">
									<?php include("registrolote.php");?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
