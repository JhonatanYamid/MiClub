<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
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
												<li class="<?php if(empty($_GET[tabclub])) echo "active"; ?>">
													<a data-toggle="tab" href="#home">
														<i class="green ace-icon fa fa-home bigger-120"></i>
														Evento
													</a>
												</li>


                    	<?php if(SIMNet::req( "action" )=="edit"): ?>

												<li class="<?php if($_GET[tabclub]=="votantes") echo "active"; ?>">
													<a data-toggle="tab" href="#messages">
															<i class="green ace-icon fa fa-users bigger-120"></i>
														Lista Votantes
														<span class="badge badge-danger"></span>
													</a>
												</li>

												<li class="<?php if($_GET[tabclub]=="registrovotantes") echo "active"; ?>">
													<a data-toggle="tab" href="#registrovotantes">
															<i class="green ace-icon fa fa-check-square-o bigger-120"></i>
														Registro de Votantes
														<span class="badge badge-danger"></span>
													</a>
												</li>

												<li class="<?php if($_GET[tabclub]=="asociarvotacion") echo "active"; ?>">
													<a data-toggle="tab" href="#asociarvotacion">
															<i class="green fa fa-filter  glyphicon-file bigger-120"></i>
															Asociar Votaciones
														<span class="badge badge-danger"></span>
													</a>
												</li>

												<li class="<?php if($_GET[tabclub]=="exportaingreso") echo "active"; ?>">
													<a data-toggle="tab" href="#exportaingreso">
															<i class="green ace-icon fa fa-exchange bigger-120"></i>
															Exportar Registro Salida Evento
														<span class="badge badge-danger"></span>
													</a>
												</li>

												<li>
													<a  href="screen/pantallavotacion.php?IDVotacionEvento=<?php echo $_GET["id"] ?>&IDClub=<?php echo SIMUser::get("club"); ?>" target="_blank">
															<i class="green ace-icon fa fa-desktop bigger-120"></i>
															Ver Pantalla tv
														<span class="badge badge-danger"></span>
													</a>
												</li>
                      <?php endif; ?>

											</ul>

											<div class="tab-content">
												<div id="home" class="tab-pane fade <?php if(empty($_GET[tabclub])) echo "in active"; ?> ">
													<?php include ("evento.php"); ?>
												</div>


                      <?php if(SIMNet::req( "action" )=="edit"): ?>
												<div id="messages" class="tab-pane <?php if($_GET[tabclub]=="votantes") echo "in active"; ?>">
													<?php include ("listavotante.php"); ?>
												</div>

												<div id="registrovotantes" class="tab-pane <?php if($_GET[tabclub]=="registrovotantes") echo "in active"; ?>">
													<?php include ("registrovotante.php"); ?>
												</div>

												<div id="asociarvotacion" class="tab-pane <?php if($_GET[tabclub]=="asociarvotacion") echo "in active"; ?>">
													<?php include ("asociarvotacion.php"); ?>
												</div>

												<div id="exportaingreso" class="tab-pane <?php if($_GET[tabclub]=="exportaingreso") echo "in active"; ?>">
													<?php include ("exportaingreso.php"); ?>
												</div>

                      <?php endif; ?>

											</div>
										</div>


				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
