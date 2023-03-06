<?
	include( "procedures/general.php" );
	include( "procedures/reportevacunacion.php" );
	include( "cmp/seo.php" );
?>
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


						<?php include("cmp/breadcrumb.php"); ?>




					</div>

					<div class="page-content">



						<?
						SIMNotify::each();

						?>


						<div class="page-header">
                        	<?php include("cmp/migapan.php"); ?>

						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
										<?
											include( $view );
										?>
							</div><!-- /.main-content -->

			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->


	</body>
</html>
