<?php

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
								Home
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Página de Inicio
								</small>
							</h1>
						</div><!-- /.page-header -->


								<div class="alert alert-block alert-success">


                                   Bienvenido a Mi Club Web. Plataforma de administración de la App Mi Club.

                                   Ingrese a los m&oacute;dulos del menu izquierdo.
								</div>










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
