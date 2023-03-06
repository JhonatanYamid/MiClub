<?
include("procedures/general.php");
include("procedures/configuracionvacunacion2.php");
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
					if ($view <> "views/" . $script . "/form.php") {
					?>
						<div class="ace-settings-container" id="ace-settings-container">
							<button class="btn btn-danger btnRedirect" rel="<?php echo $script ?>.php?action=add">
								<i class="ace-icon fa fa-file align-top bigger-125"></i>
								Nuevo <?php echo SIMReg::get("title") ?>
							</button>
						</div>
					<?
					} //end if
					?>

					<div class="page-header">
						<?php include("cmp/migapan.php"); ?>
					</div><!-- /.page-header -->
					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->
							<div class="row">
								<div class="col-sm-12">
									<div class="widget-box transparent" id="recent-box">
										<div class="widget-body">
											<div class="widget-main padding-4">
												<div class="row">
													<div class="col-xs-12">

													</div>
												</div>
											</div><!-- /.widget-main -->
										</div><!-- /.widget-body -->
									</div><!-- /.widget-box -->
									<?
									include($view);
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
		<script>
			$('#gritter-center').on(ace.click_event, function() {
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