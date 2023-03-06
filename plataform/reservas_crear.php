<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	//include( "cmp/seo.php" );
?>
	</head>

	<body class="no-skin">
		


		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			
			<div class="main-content">
				<div class="main-content-inner">
					

					<div class="page-content">


					
						<?
						SIMNotify::each();

						
						?>


						<div class="page-header">
							<h1>
								Home
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Nueva Reserva <?=$datos_servicio[$ids]["Nombre"] ?>
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								

								<div class="row">
									<div class="col-sm-12">
										



										<?
											include( $view );
										?>



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
		</div><!-- /.main-container -->

		
	</body>
</html>
