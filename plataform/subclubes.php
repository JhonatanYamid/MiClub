<?
	include( "procedures/general.php" );
	include("cmp/seo.php");
?>
		<link rel="stylesheet" href="assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
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
				$menu_club = " class=\"active\" ";
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





						<div class="page-header">
							<?php include("cmp/migapan.php"); ?>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->

								<?php

								if($newmode==""): ?>
								<iv>
									<ul class="ace-thumbnails clearfix">

										<?

										if(SIMUser::get("IDPerfil")==0):

											//traer todos los clubes en el sistema
											$sql_clubes = "SELECT * FROM Club WHERE IDClubPadre='".$_GET["id"]."'";
											$qry_clubes = $dbo->query( $sql_clubes );
											while( $r_clubes = $dbo->fetchArray( $qry_clubes ) )
												$array_clubes[ $r_clubes["IDClub"] ] = $r_clubes;

											foreach( $array_clubes as $idclub => $datos_club )
											{
												//traer servicios maestros activos
												//$sql_servicios = "SELECT ServicioMaestro.Nombre FROM ServicioClub , ServicioMaestro  WHERE ServicioClub.IDClub = '" . $idclub . "' AND ServicioClub.Activo = 'S' AND ServicioClub.IDServicioMaestro = ServicioMaestro.IDServicioMaestro ";
												//$qry_servicios = $dbo->query( $sql_servicios );

												// Si el club tienen hijos cambio el link para mostrarlos
												$sql_club_hijo="SELECT IDClub FROM Club WHERE IDClubPadre = '".$idclub."' LIMIT 1";
												$r_club_hijo=$dbo->query($sql_club_hijo);
												if($dbo->rows($r_club_hijo)>0)
													$link_club="subclubes.php?id=".$idclub;
												else
													$link_club="set_club.php?id=".$idclub;

											?>

												<li>
													<a href="<?=$link_club ?>" title="administrar club" >
														<img width="150" height="150" alt="150x150" src="<?=CLUB_ROOT . $datos_club[FotoLogoApp] ?>" />
														<div class="text">
															<div class="inner"><?=$datos_club["Nombre"] ?></div>
														</div>
													</a>

													<div class="tags">



														<?
														$label = string;

														while( $r_servicios = $dbo->fetchArray( $qry_servicios ) )
														{
															$label = SIMUtil::repetition()?'label-success':'label-success'; //label-danger
														?>
															<span class="label-holder">
																<span class="label <?=$label ?>"><?=$r_servicios["Nombre"] ?></span>
															</span>
														<?
														}//end if

														?>

													</div>

													<div class="tools">


														<a href="<?=$link_club; ?>" title="administrar club" >
															<i class="ace-icon fa fa-paperclip"></i>
														</a>

														<a href="clubes.php?action=edit&id=<?php echo $datos_club["IDClub"]; ?>" title="Editar información">
															<i class="ace-icon fa fa-pencil"></i>
														</a>


													</div>


												</li>
											<?
												}//end for

											endif;
											?>





									</ul>

                                    <?php endif; ?>

                                    	<?
											include( $view );
										?>




								</div><!-- PAGE CONTENT ENDS -->

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
			include( "cmp/footer_scripts.php" );
		?>
	</body>
</html>
