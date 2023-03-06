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
												<li class="<?php if(empty($_GET[tabinvitado])) echo "active"; ?>">
													<a data-toggle="tab" href="#home">
														<i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
														Datos Personales
													</a>
												</li>

												<?php if(SIMNet::req( "action" )=="edit"): ?>
												<li class="<?php if($_GET[tabinvitado]=="observaciones") echo "active"; ?>">
													<a data-toggle="tab" href="#observaciones">
                                                    	<i class="green ace-icon fa fa-book bigger-120"></i>
														Bitacora Observaciones / Bloqueos
													</a>
												</li>


											<li class="<?php if($_GET[tabinvitado]=="invitaciones") echo "active"; ?>">
													<a href="https://www.miclubapp.com/plataform/views/invitadosgeneral/invitacionespage.php?IDInvitado=<?php echo $frm[$key]; ?>" class="fancybox" data-fancybox-type="iframe" >
                                                    	<i class="green ace-icon fa fa-book bigger-120"></i>
														Bitacora Invitaciones
													</a>
												</li>

                      <li class="<?php if($_GET[tabinvitado]=="autorizaciones") echo "active"; ?>">
													<a href="https://www.miclubapp.com/plataform/views/invitadosgeneral/autorizacionespage.php?IDInvitado=<?php echo $frm[$key]; ?>" class="fancybox" data-fancybox-type="iframe" >
                                                    	<i class="green ace-icon fa fa-briefcase  bigger-120"></i>
														Bitacora Autorizaciones
													</a>
												</li>

                                                <li class="<?php if($_GET[tabinvitado]=="vehiculos") echo "active"; ?>">
													<a data-toggle="tab" href="#vehiculos">
                                                    	<i class="green ace-icon fa fa-car  bigger-120"></i>
														Vehiculos
													</a>
												</li>

                                               <li class="<?php if($_GET[tabinvitado]=="licencias") echo "active"; ?>">
													<a data-toggle="tab" href="#licencias">
                                                    	<i class="green ace-icon fa fa-ticket  bigger-120"></i>
														Licencias de Conduccion
													</a>
												</li>

                                               <li class="<?php if($_GET[tabinvitado]=="equipos") echo "active"; ?>">
													<a data-toggle="tab" href="#equipos">
                                                    	<i class="green ace-icon fa fa-laptop  bigger-120"></i>
															Equipos
													</a>
												</li>

                                                <?php endif; ?>

											</ul>

											<div class="tab-content">
												<div id="home" class="tab-pane fade <?php if(empty($_GET[tabinvitado])) echo "in active"; ?> ">
													<?php include ("datospersonales.php"); ?>
												</div>

                                                <?php if(SIMNet::req( "action" )=="edit"): ?>

                                                 <div id="observaciones" class="tab-pane fade <?php if($_GET[tabinvitado]=="observaciones") echo "in active"; ?>">
													 <?php
													 //Se solicta desactiovar
													 include ("observaciones.php"); ?>
												</div>

												<div id="invitaciones" class="tab-pane fade <?php if($_GET[tabinvitado]=="invitaciones") echo "in active"; ?>">
													<?php
													if($_GET["refiere"]!="porteria"){
														//echo ' <iframe width="100%" height="600" src="https://www.miclubapp.com/plataform/views/invitadosgeneral/invitacionespage.php?IDInvitado='.$frm[$key].'" frameborder="0" allowfullscreen></iframe>';
													//include ("invitaciones.php");
													}
													?>
												</div>

                                                <div id="autorizaciones" class="tab-pane fade <?php if($_GET[tabinvitado]=="autorizaciones") echo "in active"; ?>">
													<?php
													if($_GET["refiere"]!="porteria"){
														//echo ' <iframe width="100%" height="600" src="https://www.miclubapp.com/plataform/views/invitadosgeneral/autorizacionespage.php?IDInvitado='.$frm[$key].'" frameborder="0" allowfullscreen></iframe>';
														//include ("autorizacion.php");
													}
													?>
												</div>

                                                <div id="vehiculos" class="tab-pane fade <?php if($_GET[tabinvitado]=="vehiculos") echo "in active"; ?>">
													<?php include ("vehiculos.php"); ?>
												</div>

                                               <div id="licencias" class="tab-pane fade <?php if($_GET[tabinvitado]=="licencias") echo "in active"; ?>">
													<?php include ("licencias.php"); ?>
												</div>

                                               <div id="equipos" class="tab-pane fade <?php if($_GET[tabinvitado]=="equipos") echo "in active"; ?>">
													<?php include ("equipos.php"); ?>
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
	include( "cmp/footer_scripts.php" );
?>
