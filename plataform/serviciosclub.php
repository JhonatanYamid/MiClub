<?
	include( "procedures/general.php" );
	include( "procedures/serviciosclub.php" );
	include( "cmp/seo.php" );

	$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '".$_GET["ids"]."'");
	//$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro  . "'");

	$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get( "club" )."' and IDServicioMaestro = '" . $id_servicio_maestro . "'" );
	if(empty($nombre_servicio_personalizado))
		$nombre_servicio_personalizado = $nombre_servicio_personalizado;




?>
	</head>

	<body class="no-skin">

		<?
			include( "cmp/header.php" );
		?>


		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<?
				$menu_home = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="index.php">Home</a>
							</li>
							<li ><?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?></li>
							<li class="active" ><?php echo SIMReg::get( "title" )?></li>
                            <li class="active" >
                              <? echo $nombre_servicio_personalizado; ?>
                            </li>

						</ul><!-- /.breadcrumb -->


					</div>

					<div class="page-content">



						<?
						SIMNotify::each();

						if( $view <> "views/".$script."/form.php" )
						{
						?>
							<div class="ace-settings-container" id="ace-settings-container">

								<button class="btn btn-danger btnRedirect" rel="<?php echo $script?>.php?action=add">
									<i class="ace-icon fa fa-file align-top bigger-125"></i>
									Nuevo <?php echo SIMReg::get( "title" )?>
								</button>


							</div>
						<?
						}//end if
						?>


						<div class="page-header">
							<h1>
							<i class="ace-icon fa fa-angle-double-right"></i>
								Configuracion <?                
								echo $nombre_servicio_personalizado;

								 ?>

							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->

								<ul class="nav nav-tabs" id="myTab">

									<?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 1):?>										
									<li >
										<a data-toggle="tab" class="noTabLink" href="reservassorteo.php?action=edit&ids=<?= $_GET["ids"] ?>">
										<i class="green ace-icon fa fa-trophy bigger-120"></i> Inscritos Sorteo </a>
									</li>
									<?php else: ?> 
									<li >
										<a class="noTabLink" href="reservas.php?ids=<?php echo $_GET["ids"]; ?>">
											<i class="green ace-icon fa fa-calendar bigger-120"></i>
											Reservas
										</a>
									</li>
									<?php endif; ?>								


									<?php								
									$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoConfiguracion");
									if (
										SIMUser::get("IDPerfil") <= 2 || SIMUser::get("IDPerfil") == 21 || SIMUser::get("IDPerfil") == 22 || SIMUser::get("IDPerfil") == 23 || SIMUser::get("IDPerfil") == 27 || SIMUser::get("IDPerfil") == 31
										|| SIMUser::get("IDPerfil") == 32 || SIMUser::get("IDPerfil") == 30 || SIMUser::get("IDPerfil") == 10 || SIMUser::get("IDPerfil") == 7 || $Permiso == 1
									) : ?>
										<li class="active">
											<a data-toggle="tab" class="noTabLink" href="serviciosclub.php?action=edit&ids=<?= $_GET["ids"] ?>">
												<i class="green ace-icon fa fa-gear bigger-120"></i>
												Configuración
											</a>
										</li>

										<?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 1):?>
										<li >
											<a data-toggle="tab" class="noTabLink" href="sorteo.php?action=edit&ids=<?= $_GET["ids"] ?>">
											<i class="green ace-icon fa fa-trophy bigger-120"></i> Sorteo </a>
										</li>										
										<?php endif; ?> 
										
									<?php endif;
									$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoExportar");
									if ($Permiso == 1 && $datos_servicio[$_GET["ids"]][TipoSorteo] == 0) :
									?>
										<li>
											<a data-toggle="tab" class="noTabLink" href="exportareserva.php?action=edit&ids=<?= $_GET["ids"] ?>">
												<i class="green ace-icon fa fa-download bigger-120"></i>
												Exportar Reservas
											</a>
										</li>
										<li>
											<a data-toggle="tab" class="noTabLink" href="exportarsanciones.php?action=edit&ids=<?= $_GET["ids"] ?>">
												<i class="green ace-icon fa fa-download bigger-120"></i>
												Exportar Sanciones
											</a>
										</li>

										<li>
											<a data-toggle="tab" class="noTabLink" href="exportareservaeliminada.php?action=edit&ids=<?= $_GET["ids"] ?>">
												<i class="green ace-icon fa fa-download bigger-120"></i>
												Exportar Reservas Eliminadas
											</a>
										</li>										

									<?php endif; ?>
									<?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 0):?>
									<li>
										<a data-toggle="tab" class="noTabLink" href="listaespera.php?action=edit&ids=<?= $_GET["ids"] ?>">
											<i class="green ace-icon fa fa-bell-o bigger-120"></i>
											Inscritos Lista de espera
										</a>
									</li>
									<?php endif; ?>


									<?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 112) { ?>
										<li>
											<a data-toggle="tab" class="noTabLink" href="cargamasivareservas.php?action=edit&ids=<?= $_GET["ids"] ?>">
												<i class="green ace-icon fa fa-bell-o bigger-120"></i>
												Cargar reservas
											</a>
										</li>
									<?php } ?>

								</ul>



								<div class="row">
									<div class="col-sm-12">
										<?	include( $view ); ?>
									</div><!-- /.col -->


								</div><!-- /.row -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include("cmp/footer.php");
			?>


            <?php include( "cmp/footer.php" ); ?>
           <link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
           <script>
		   	$('#gritter-center').on(ace.click_event, function(){
					$.gritter.add({
						title: 'This is a centered notification',
						text: 'Just add a "gritter-center" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
						class_name: 'gritter-info gritter-center' + (!$('#gritter-light').get(0).checked ? ' gritter-light' : '')
					});

					return false;
				});
		   </script>



		</div><!-- /.main-container -->


	</body>
</html>
