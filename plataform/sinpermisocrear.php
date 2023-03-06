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
							<li class="active">Página principal</li>
						</ul><!-- /.breadcrumb -->						
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								ATENCION!<small></small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->								

								<div class="row">
									<div class="col-sm-6">
										<div class="widget-box transparent" id="recent-box">
											<div class="widget-header">
												<h4 class="widget-title lighter smaller">
													<i class="ace-icon fa fa-users orange"></i
													>No tiene los permisos suficientes para crear registros!
												</h4>												
											</div>
											<div class="widget-body">
												<div class="widget-main padding-4">
													<div class="row">
														<div class="col-xs-12">
															<!-- PAGE CONTENT BEGINS -->															
														</div>
													</div>
												</div><!-- /.widget-main -->
											</div><!-- /.widget-body -->
										</div><!-- /.widget-box -->
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
		<?
			include( "cmp/footer_scripts.php" );
		?>
	</body>
</html>
