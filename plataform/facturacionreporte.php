<?
include("procedures/general.php");
include("procedures/facturacionreporte.php");
include("cmp/seo.php");
?>
</head>

<body class="no-skin">

	<?
	include("cmp/header.php");
	?>

	<div class="main-container" id="main-container">
		<script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>

		<?
			$menu_home = " class=\"active\" ";
			include("cmp/menu.php");
		?>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs" id="breadcrumbs">
					<script type="text/javascript">
						try {
							ace.settings.check('breadcrumbs', 'fixed')
						} catch (e) {}
					</script>

					<?php include("cmp/breadcrumb.php"); ?>
				</div>

				<div class="page-content">
					<?
					
					SIMNotify::each();
					if ($view == "views/" . $script . "/form.php") {
                                                          include($view);
					}else{  
						   include($view);
					  } ?>

					 
					  

							<!-- PAGE CONTENT ENDS -->
						 
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

		<?
		include("cmp/footer.php");
		?>
	</div><!-- /.main-container -->

</body>

</html>
