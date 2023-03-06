<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

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
					<div class="col-sm-12">
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="<?php if (empty($_GET[tabnoticias])) echo "active"; ?>">
									<a data-toggle="tab" href="#home">
										<i class="green ace-icon fa fa-gavel bigger-120"></i>
										<?= SIMUtil::get_traduccion('', '', 'Noticia', LANGSESSION); ?>
									</a>
								</li>

								<?php if (SIMNet::req("action") == "edit") : ?>
									<li class="<?php if ($_GET[tabnoticias] == "comentarios") echo "active"; ?>">
										<a data-toggle="tab" href="#messages">
											<i class="green ace-icon fa fa-comments-o bigger-120"></i>
											<?= SIMUtil::get_traduccion('', '', 'Comentarios', LANGSESSION); ?>
										</a>
									</li>

								<?php endif; ?>
							</ul>

							<div class="tab-content">
								<div id="home" class="tab-pane fade <?php if (empty($_GET[tabnoticias])) echo "in active"; ?> ">
									<?php include("datos.php"); ?>
								</div>

								<?php if (SIMNet::req("action") == "edit") : ?>
									<div id="messages" class="tab-pane fade <?php if ($_GET[tabnoticias] == "comentarios") echo "in active"; ?>">
										<?php include("comentarios.php"); ?>
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
	//include( "cmp/footer_scripts.php" );
	?>