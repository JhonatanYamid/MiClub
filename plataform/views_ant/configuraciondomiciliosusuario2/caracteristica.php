<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>

<div class="widget-box transparent" id="recent-box">

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->



                    <div class="row">
									<div class="col-sm-12">

										<div id="accordion" class="accordion-style1 panel-group">

											<?php
											//para los corrdinadores que solo puedan ver fechas de cierre
											if(SIMUser::get("IDPerfil")!=31){	?>
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed " data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
															<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;<?= SIMUtil::get_traduccion('', '', 'Categorias(ejemploTermino,Acompañamientos,Temperatura)', LANGSESSION); ?>
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse <?php if($_GET["tab"]=="categoriap" ) echo "in"; ?>" id="collapseOne">
													<div class="panel-body">
													<?php include("categorias.php"); ?>
													</div>
												</div>
											</div>

											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
															<i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;<?= SIMUtil::get_traduccion('', '', 'Caracteristicas', LANGSESSION); ?>
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse <?php if($_GET["tab"]=="caracteristicap") echo "in"; ?>" id="collapseTwo">
													<div class="panel-body">
                            <?php include("caracteristicaproducto.php"); ?>
													</div>
												</div>

											</div>

											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTree">
															<i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;<?= SIMUtil::get_traduccion('', '', 'PreguntasDomicilio', LANGSESSION); ?>
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse <?php if($_GET["tab"]=="domiciliopregunta") echo "in"; ?>" id="collapseTree">
													<div class="panel-body">
                            <?php include("preguntadomicilio.php"); ?>
													</div>
												</div>

											</div>


												<?php } ?>





										</div>
									</div><!-- /.col -->

			</div>


		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
