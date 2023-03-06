

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
													<a href="clubes.php?action=edit&id=<?=$id ?>">
														<i class="green ace-icon fa fa-home bigger-120"></i>
														Configuracion General
													</a>
												</li>

												<?php if(SIMNet::req( "action" )=="edit"): ?>
												<li>
													<?php if($_GET["vista"]=="otros") { ?>
														<a data-toggle="tab" href="#messages">
													<?php } else{	?>
															<a href="clubes.php?action=edit&vista=otros&tabclub=parametros&id=<?=$id ?>">
													<?php }?>


                        <i class="green ace-icon fa fa-check-circle bigger-120"></i>
														Otros Parametros
													</a>
												</li>

                        <li class="<?php if($_GET[tabclub]=="invitaciones") echo "active"; ?>">
													<?php if($_GET["vista"]=="otros") { ?>
														<a data-toggle="tab" href="#invitaciones">
													<?php } else{	?>
															<a href="clubes.php?action=edit&vista=otros&tabclub=invitaciones&id=<?=$id ?>">
													<?php }?>


                        <i class="green ace-icon fa fa-ticket  bigger-120"></i>
														Reglas Invitaciones
													</a>
												</li>



                        <li class="<?php if($_GET[tabclub]=="submodulos") echo "active"; ?>">
													<a href="clubes.php?action=edit&vista=submodulos&tabclub=submodulos&id=<?=$id ?>">

                            <i class="green ace-icon fa  fa-sitemap  bigger-120"></i>
														Config. Submodulos
													</a>
												</li>
												<li class="<?php if($_GET[tabclub]=="appempleados") echo "active"; ?>">
													<?php if($_GET["vista"]=="otros") { ?>
															<a data-toggle="tab" href="#appempleados">
													<?php } else{	?>
															<a href="clubes.php?action=edit&vista=otros&tabclub=appempleados&id=<?=$id ?>">
													<?php }?>

                          <i class="green ace-icon fa fa-mobile  bigger-120"></i>
														App Empleados
													</a>
												</li>
												<li class="<?php if($_GET[tabclub]=="tiposociomodulo") echo "active"; ?>">
													<?php if($_GET["vista"]=="otros") { ?>
															<a data-toggle="tab" href="#tiposociomodulo">
													<?php } else{	?>
															<a href="clubes.php?action=edit&vista=otros&tabclub=tiposociomodulo&id=<?=$id ?>">
													<?php }?>

                        	<i class="green ace-icon fa fa-eye  bigger-120"></i>
														Modulos por tipo socio
													</a>
												</li>

												<li class="<?php if($_GET[tabclub]=="permisosociomodulo") echo "active"; ?>">
													<?php if($_GET["vista"]=="otros") { ?>
															<a data-toggle="tab" href="#permisosociomodulo">
													<?php } else{	?>
															<a href="clubes.php?action=edit&vista=otros&tabclub=permisosociomodulo&id=<?=$id ?>">
													<?php }?>

                        	<i class="green ace-icon fa fa-cogs  bigger-120"></i>
														Permisos Modulos y Reservas
													</a>
												</li>


                                                <?php endif; ?>

											</ul>

											<div class="tab-content">
												<div id="home" class="tab-pane fade <?php if(empty($_GET[tabclub])) echo "in active"; ?> ">
													<?php
													if(empty($_GET["vista"])){
														include ("club.php");
													}

													?>
												</div>

                        <?php if(SIMNet::req( "action" )=="edit"): ?>
												<div id="messages" class="tab-pane fade <?php if($_GET[tabclub]=="parametros") echo "in active"; ?>">
													Parametros
													<?php

														if(!empty($_GET["vista"]) || !empty($_GET["tabclub"]) )
															include ("parametroclub.php");
														?>
												</div>

                        <div id="invitaciones" class="tab-pane fade <?php if($_GET[tabclub]=="invitaciones") echo "in active"; ?>">
													Invitaciones
													<?php
													if(!empty($_GET["vista"]) || !empty($_GET["tabclub"]))
														include ("invitaciones.php");
													?>
												</div>

                        <div id="submodulos" class="tab-pane fade <?php if($_GET[tabclub]=="submodulos") echo "in active"; ?>">
													Submodulos
													<?php
													if($_GET["vista"]=="submodulos")
														include ("submodulos.php");
													?>
												</div>

												

                        <div id="appempleados" class="tab-pane fade <?php if($_GET[tabclub]=="appempleados") echo "in active"; ?>">
													App Empeados
													<?php
													if(!empty($_GET["vista"]) || !empty($_GET["tabclub"]))
														include ("appempleados.php");
														?>
												</div>

												<div id="tiposociomodulo" class="tab-pane fade <?php if($_GET[tabclub]=="tiposociomodulo") echo "in active"; ?>">
													Tipo
													<?php
													if(!empty($_GET["vista"]) || !empty($_GET["tabclub"]))
														include ("tiposociomodulos.php");
													?>
												</div>

												<div id="permisosociomodulo" class="tab-pane fade <?php if($_GET[tabclub]=="permisosociomodulo") echo "in active"; ?>">
													Los modulos o reservas configurados aca solo podrán ser vistos por las personas que se agreguen en esta pantalla
													<?php
													if(!empty($_GET["vista"]) || !empty($_GET["tabclub"]))
														include ("permisosociomodulos.php");
													?>
												</div>



											<?php endif; ?>

											</div>
										</div>



				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
