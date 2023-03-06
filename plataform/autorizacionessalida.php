<?
	include( "procedures/general.php" );
	include( "procedures/autorizacionessalida.php" );
	include("cmp/seo.php");
	
	

	$url_search = "";
	if( $_GET["action"] == "search" )
	{
		$url_search = "?oper=search_url&Accion=" . SIMNet::get("Accion");
	}//end if

?>
		


	</head>

	<body class="no-skin">
		<?
			if ($_GET["action"]!="add"):
				include( "cmp/header.php" );
			endif;	
		?>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<?
				$menu_invitados = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<?php include("cmp/breadcrumb.php"); ?>

						
					</div>

					<div class="page-content">
                    
                    <?
						SIMNotify::each();

						if( $view <> "views/".$script."/form.php" )
						{
						?>
							<div class="ace-settings-container" id="ace-settings-container">
								
								
                                <button class="btn btn-danger  fancybox" href="<?php echo $script?>.php?action=add" data-fancybox-type="iframe">
								<i class="ace-icon fa fa-file align-top bigger-125"></i>
								Nuevo <?php echo strtoupper(SIMReg::get( "title" ))?>							</button>

								
							</div>
						<?
						}//end if
						?>

						

						<div class="page-header">
							<h1>
								<?php echo strtoupper(SIMReg::get( "title" ))?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Listado de <?php echo strtoupper(SIMReg::get( "title" ))?> <?=SIMUtil::tiempo( date("Y-m-d") ) ?>
								</small>
							</h1>
						</div><!-- /.page-header -->

						
						<!-- INGRESO CON LA PISTOLA -->
						<div class="row">

							
                            <?php 	if(empty($_GET["action"])): ?>
							<div class="widget-box transparent" id="recent-box">
								<div class="widget-header">
									<h4 class="widget-title lighter smaller">
										<i class="ace-icon fa fa-users orange"></i>REGISTRAR INGRESO
									</h4>

									
								</div>
                                
								<div class="widget-body">
									<div class="widget-main padding-4">
										<div class="row">
											<div class="col-xs-12">

															
												
												<form class="form-inline formvalida" role="form" method="post" name="frmIngresoPistola" id="frmIngresoPistola" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
													

			                                        	<input id="NumeroDocumento150" type="text"  placeholder="Numero Documento" name="NumeroDocumento150" alt="150" class=" input-small txtPistola" value="" />                                                                               
			                                        	<input id="Apellido150" type="text"  placeholder="Primer Apellido" name="PrimerApellido150" alt="150" class=" input-small " value="" />                                                                               			                                        				                                        	
			                                        	<input id="Nombre150" type="text"  placeholder="Segundo Nombre" name="SegundoNombre150" alt="150" class=" input-small " value="" />                                                         
                                                        <input id="FechaNacimiento150" type="text"  placeholder="FecahNacimiento" name="FechaNacimiento150" class=" input-small " alt="150" value="" /> 
                                                        <input id="TipoSangre150" type="text"  placeholder="Tipo Sangre" name="TipoSangre150" alt="150" class=" input-small " value="" /> 
			                                        	<span class="help-inline col-xs-12 col-sm-7">
															<span class="middle" id="contentMsgP"></span>
														</span>                                                                             
													                                                                
													
				
												</form>

												<!-- PAGE CONTENT ENDS -->
											</div>
										</div>
									</div>
								</div>

								<br><br>
                                
							</div>
                            <?php endif; ?>


						</div>


						<!-- FIN INGRESO CON LA PISTOLA -->



						<div class="row">
										
								

								<?php include( $view ); ?>
                                

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->

		<?
			//include( "cmp/footer_grid.php" );
		?>

		
	</body>
</html>
