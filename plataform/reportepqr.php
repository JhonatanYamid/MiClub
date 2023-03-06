<?
	include( "procedures/general.php" );
	include( "procedures/reportepqr.php" );
	include( "cmp/seo.php" );
?>
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


								<div class="row">
									<div class="col-sm-12">
										<div class="widget-box transparent" id="recent-box">
											<div class="widget-header">
												<h4 class="widget-title lighter smaller">
													<i class="ace-icon fa fa-users orange"></i>Filtrar
												</h4>


											</div>

											<div class="widget-body">
												<div class="widget-main padding-4">
													<div class="row">
														<div class="col-xs-12">
															<!-- PAGE CONTENT BEGINS -->


															<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI()?>" method="get">

																<div class="col-xs-12 col-sm-12">



                                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                      <td>Estado</td>
                                                                      <td><?php echo SIMHTML::formPopUp( "PqrEstado" , "Nombre" , "Nombre" , "IDPqrEstado" , "" , "[Seleccione]" , "form-control" , "title = \"Estado\"" )?></td>
                                                                       <td>Desde</td>
                                                                      <td>
                                                                      <input type="text" id="FechaDesde" name="FechaDesde" placeholder="Fecha Desde" class="col-xs-12 calendar" title="Fecha Desde" value="<?php echo $frm["FechaDesde"] ?>" >
                                                                      </td>

                                                                      <td>Hasta</td>
                                                                      <td>
                                                                      <input type="text" id="FechaHasta" name="FechaHasta" placeholder="Fecha Hasta" class="col-xs-12 calendar" title="Fecha Hasta" value="<?php echo $frm["FechaHasta"] ?>" >
                                                                      </td>
                                                                      </tr>
                                                                      <tr>
                                                                      <td>Area</td>
                                                                      <td>
																	  <select name="IDArea" id="IDArea" class="form-control">
                                                                      	<option value=""></option>
                                                                        <?php


																																				if(SIMUser::get("IDPerfil") != 0 ):
																																					//Consulto las areas
																																					$sql_area_usuario="Select * From UsuarioArea Where IDUsuario = '".SIMUser::get("IDUsuario")."'";
																																					$result_area_usuario=$dbo->query($sql_area_usuario);
																																					while($row_area=$dbo->fetchArray($result_area_usuario)):
																																						$array_areas_consulta [] = $row_area["IDArea"];
																																					endwhile;
																																					if(count($array_areas_consulta)>0):
																																						$id_areas = implode(",",$array_areas_consulta);
																																					endif;
																																					$condicion_area = " and IDArea in (".$id_areas.")";
																																				endif;

																			$sql_area= "Select * From Area Where IDClub = '".SIMUser::get( "club" )."' " .  $condicion_area;
																			$result_area = $dbo->query($sql_area);
																			while ($row_area = $dbo->fetchArray($result_area)): ?>
																				<option value="<?php echo $row_area["IDArea"]; ?>"><?php echo $row_area["Nombre"]; ?></option>
																			<?php endwhile;	?>
                                                                      </select>
	                                                                   <td></td>
                                                                      <td>

                                                                      </td>

                                                                      <td></td>
                                                                      <td>

                                                                      </td>


                                                                      </tr>
                                                                    <tr>
                                                                        <td colspan="6" align="center">

                                                                        <input type="hidden" name="action" value="search">
																		<span class="input-group-btn">

																			<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																				<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																				Filtrar
																			</button>
																		</span>


                                                                        </td>
                                                                    </tr>
                                                                </table>


																</div>




															</form>
														</div>
													</div>




												</div><!-- /.widget-main -->
											</div><!-- /.widget-body -->
										</div><!-- /.widget-box -->



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
	include( "cmp/footer_scripts.php" );
?>

	       <script>
		   	$('#gritter-center').on(ace.click_event, function(){
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
