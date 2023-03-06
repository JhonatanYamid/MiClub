<?
	include( "procedures/general.php" );
	include( "procedures/reportesalidas.php" );
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
                    
                    <?	SIMNotify::each(); ?>

						

						<div class="page-header">
							<h1>
								<?php echo strtoupper(SIMReg::get( "title" ))?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Listado de <?php echo strtoupper(SIMReg::get( "title" ))?> <?=SIMUtil::tiempo( date("Y-m-d") ) ?>
								</small>
							</h1>
						</div><!-- /.page-header -->


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
