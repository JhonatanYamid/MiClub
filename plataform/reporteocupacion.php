<?
	include( "procedures/general.php" );
	include( "procedures/reporteocupacion.php" );
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
				$menu_club = " class=\"active\" ";
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




						<div class="page-header">
							<?php include("cmp/migapan.php"); ?>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


                                <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI()?>" method="get">

																<div class="col-xs-12 col-sm-12">



                                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                      <td>Fecha Inicio</td>
                                                                      <td>
                                                                      	<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="fecha inicio" value="<?php if (empty($_GET["FechaInicio"])) echo date("Y-m-d"); echo $_GET["FechaInicio"]; ?>"  >
																	 </td>
                                                                      <td>Fecha Fin</td>
                                                                      <td><input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php if (empty($_GET["FechaFin"])) echo date("Y-m-d"); echo $_GET["FechaFin"]; ?>"  ></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" align="center">

                                                                        <input type="hidden" name="action" value="search">
																		<span class="input-group-btn">

																			<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
																				<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																				Buscar
																			</button>
																		</span>

                                                                        </td>
                                                                    </tr>
                                                                </table>


																</div>

							  </form>



						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="row">


                                <div class="col-xs-12">
										<a href="procedures/excel-ocupacion.php?IDClub=<?php echo SIMUser::get("club"); ?>"><img src="assets/img/xls.gif" >Exportar</a>								
										<table id="simple-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th width="80%">Resumen Ocupacion</th>
													<th align="center">Total</th>
												</tr>
											</thead>

											<tbody>

                                             <? foreach( $array_adentro as $tipo => $total ): ?>
												<tr>
													<td width="80%">


                                                        <a class="fancybox" href="detalleocupacion.php?Tipo=<?php echo $tipo; ?>&Movimiento=Ocupacion&action=search" data-fancybox-type="iframe">
														<?php
														switch($tipo):
															case "InvitadoAcceso";
																echo "Invitado Socio";
															break;
															default:
																echo $tipo;
														endswitch;
														?>
                                                        </a>
													</td>
													<td align="center">
														<?php
														$total_adentro += (int)$total;
														echo $total; ?>
                                                     </td>
												</tr>
                                              <?php endforeach; ?>


												<tr>
	                                                <td bgcolor="#FFFFAA" style="font-weight:bold;">
														TOTAL OCUPACION
													</td>
													<td bgcolor="#FFFFAA" style="font-weight:bold;" align="center">
													<?php echo $total_adentro; ?>
                                                    </td>

											  </tr>
											</tbody>
										</table>
									</div><!-- /.span -->

									<div class="col-xs-12">
										<table id="simple-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th width="80%">Resumen Ingreso</th>
													<th width="10%">Personas</th>
													<th  width="10%">Total</th>
												</tr>
											</thead>

											<tbody>

                                             <? foreach( $array_ocupacion_entrada as $tipo => $total ): ?>
												<tr>													
													<td>
														<?php
														switch($tipo):
															case "InvitadoAcceso";
																echo "Invitado Socio";
															break;
															default:
																echo $tipo;
														endswitch;
														?>

													</td>
													<td align="center">
														<?php
															$total_ingreso_personas += (int)$array_ocupacion_entrada_personas[$tipo] ;
															echo $array_ocupacion_entrada_personas[$tipo];
														?>	
													</td>
													<td align="center">
														<?php
														$total_ingreso += (int)$total ;
														echo $total ; ?>
                                                     </td>
												</tr>
                                              <?php endforeach; ?>


												<tr>
	                                                <td bgcolor="#DAFADC" style="font-weight:bold;">
														TOTAL ENTRADAS
													</td>
													<td bgcolor="#DAFADC" style="font-weight:bold;" align="center">
														<?php echo $total_ingreso_personas; ?>
                                                    </td>
													<td bgcolor="#DAFADC" style="font-weight:bold;" align="center">
													<?php echo $total_ingreso; ?>
                                                    </td>

											  </tr>
											</tbody>
										</table>
									</div><!-- /.span -->


                                    <div class="col-xs-12">
										<table id="simple-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th width="80%" align="center">Resumen Salida</th>
													<th width="10%" >Personas</th>
													<th width="10%">Total</th>
												</tr>
											</thead>

											<tbody>

                                             <? foreach( $array_ocupacion_salida as $tipo => $total ): ?>
												<tr>
													<td>

														<?php
														switch($tipo):
															case "InvitadoAcceso";
																echo "Invitado Socio";
															break;
															default:
																echo $tipo;
														endswitch;
														?>

													</td>
													<td align="center">
														<?php
															$total_salida_personas += (int)$array_ocupacion_salida_personas[$tipo];
															echo $array_ocupacion_salida_personas[$tipo];
														?>	
													</td>
													<td align="center">
														<?php
														$total_salida += (int)$total;
														echo $total; ?>
                                                     </td>
												</tr>
                                              <?php endforeach; ?>


												<tr>
	                                                <td bgcolor="#FCD8E1" style="font-weight:bold;">
														TOTAL SALIDAS
													</td>
													<td  bgcolor="#FCD8E1" style="font-weight:bold;" align="center">
													<?php echo $total_salida_personas; ?>
                                                    </td>
													<td  bgcolor="#FCD8E1" style="font-weight:bold;" align="center">
													<?php echo $total_salida; ?>
                                                    </td>

											  </tr>
											</tbody>
										</table>
									</div><!-- /.span -->

								</div><!-- /.row -->

								<div class="hr hr-18 dotted hr-double"></div>


							</div><!-- /.col -->
						</div><!-- /.row -->







								</div><!-- PAGE CONTENT ENDS -->

							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include( "cmp/footer_grid.php" );
			?>
		</div><!-- /.main-container -->

		<?
			//include( "cmp/footer_scripts.php" );
		?>
	</body>
</html>
