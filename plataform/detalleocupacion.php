<?
	include( "procedures/general.php" );
	include( "procedures/detalleocupacion.php" );
	include( "cmp/seo.php" );
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
