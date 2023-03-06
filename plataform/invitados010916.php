<?
	include( "procedures/general.php" );
	include( "procedures/invitados.php" );
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
								Nuevo Invitado							</button>

								
							</div>
						<?
						}//end if
						?>

						

						<div class="page-header">
							<h1>
								Invitados
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Listado de Invitados <?=SIMUtil::tiempo( date("Y-m-d") ) ?>
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
