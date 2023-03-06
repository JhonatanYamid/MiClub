<?php
	include( "procedures/general.php" );
	include( "procedures/cursoinscripcion.php" );
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

						<?
						SIMNotify::each();
						?>


						<div class="page-header">
							<h1>
								Home
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?>
									<i class="ace-icon fa fa-angle-double-right"></i>
									CURSO INSCRIPCION
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


								<div class="row">
									<div class="col-sm-12">


											<?php
												$datosdecode = base64_decode($_GET["datosseleccion"]);
												$datos=json_decode($datosdecode);
											?>


																<form id="frmCursoIncripcion" name="frmCursoIncripcion" action="" method="post" enctype="multipart/form-data">
																	  <table id="simple-table" class="table table-striped table-bordered table-hover">
																			<tr>
                                        <td>Club</td>
                                        <td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_GET["IDClub"]."'" ); ?></td>
                                      </tr>
                                     <tr>
                                       <td>Curso</td>
                                       <td><?php echo $datos->Nombre . " " . $datos->Nivel; ?></td>
                                     </tr>
                                     <tr>
                                     	<td>Sede</td>
                                        <td><?php echo $datos->Sede; ?></td>
                                     </tr>
																		 <tr>
                                       <td>Dia</td>
                                       <td><?php echo $datos->Dia; ?>
																		 </td>
                                     </tr>
																		 <tr>
                                       <td>Fecha  Inicio</td>
                                       <td><?php echo $datos->FechaInicio . " al " .$datos->FechaFin ?> <b>Hora:</b> <?php echo $datos->HoraDesde; ?> </td>
                                     </tr>
																		 <tr>
                                       <td>Entrenador</td>
                                       <td><?php echo $datos->Entrenador; ?></td>
                                     </tr>
																		 <tr>
																			 <td>TIPO DE INSCRIPCION</td>
																			 <td>
																				 <table width="100%">
																					 <tr>
																						 <td valign="top">
																							 <input type="radio" name="TipoInscripcion" class="form-control" value="Mes" checked="checked" >
																							 1 MES WEB (<?php echo "$".number_format($datos->ValorMes,0,'','.');  ?>)
																							 <br>1 MES PRESENCIAL (<?php echo "$".number_format($datos->ValorMesPresencial,0,'','.');  ?>)
																						 </td>

																						 <td valign="top">
																							 <input type="radio" name="TipoInscripcion" class="form-control" value="Mes">
																							 1 MES Convenio ($0)
																						 </td>
																						 <td valign="top">

																							 <!--TEMPORALMENTE
																							 <input type="radio" name="TipoInscripcion" class="form-control" value="Trimestre">TRIMESTRE (<?php echo "$".number_format($datos->ValorTrimestre,0,'','.');  ?>)
																							 <br><strong>Ser&aacute; inscrito en las siguientes fechas:</strong>
																							 <?php
																							 	$sql_calendario="SELECT * FROM CursoCalendario WHERE IDCursoTipo = '".$datos->IDCursoTipo."' and FechaInicio > '".$datos->FechaInicio."' And IDClub =  '".$_GET["IDClub"]."' ORDER BY FechaInicio ASC LIMIT 2 " ;
																								$r_calendario=$dbo->query($sql_calendario);
																								if($dbo->rows($r_calendario)<2){
																									echo "<strong>No es posible registrar el trimestre ya que no no hay creados mas cursos despues de esta fecha</strong>";
																								}
																								else{
																									while($row_calendario = $dbo->fetchArray($r_calendario)){
																										$sql_siguientes = "SELECT IDCursoHorario
																																						FROM CursoHorario
																																						WHERE IDClub = '".$_GET["IDClub"]."' and IDCursoEntrenador = '".$datos->IDCursoEntrenador."'
																																						and IDCursoNivel = '".$datos->IDCursoNivel."' and IDCursoSede = '".$datos->IDCursoSede."'
																																						and IDCursoTipo = '".$datos->IDCursoTipo."' and HoraDesde = '".$datos->HoraDesde."'";
																										$r_siguientes = $dbo->query($sql_siguientes);
																										while($row_siguiente = $dbo->fetchArray($r_siguientes)){
																											//Verifico si quedan cupos
																											$inscritos = SIMWebServiceApp::get_curso_inscritos($_GET["IDClub"],$row_siguiente["IDCursoHorario"],$row_calendario["IDCursoCalendario"],$datos->HoraDesde);
																											if($inscritos<=$row_siguiente["Cupo"]){
																												echo "<strong>No es posible registrar el trimestre en la fecha ".$row_calendario["FechaInicio"]."  ya tiene el cupo completo.</strong>";
																											}
																											else{
																												$array_id_calendario[]=$row_calendario["IDCursoCalendario"];
																												echo "<br>" . $row_calendario["FechaInicio"] . " al " . $row_calendario["FechaFin"];
																												$array_id_trimestre[]=$row_siguiente["IDCursoHorario"];
																											}

																										}
																										if(count($array_id_calendario)>0){
																											$id_calendario=implode(",",$array_id_calendario);
																										}
																									}
																								}
																							  ?>
																							-->
																						 </td>
																					 </tr>
																				 </table>


																			 </td>
																		 </tr>
																		 <tr>
																			 <td colspan="2" align="center">
																				 <button class="btn btn-info" type="sumbit" >
											 										<i class="ace-icon fa fa-check bigger-110"></i>
											 										<?php echo $titulo_accion; ?> Inscribir
											 									</button>
																			 </td>
																		 </tr>


										<tr>
											<td align="center" colspan="2">
												<input type="hidden" name="IDCursoHorario"  id="IDCursoHorario" value="<?php echo $_GET["IDCursoHorario"];  ?>" />
												<input type="hidden" name="IDCursoCalendario"  id="IDCursoCalendario" value="<?php echo $_GET["calendario"];  ?>" />
												<input type="hidden" name="Cupos"  id="Cupos" value="<?php echo $_GET["cupos"];  ?>" />
												<input type="hidden" name="Valor"  id="Valor" value="<?php echo $_GET["valor"];  ?>" />
												<input type="hidden" name="HoraDesde"  id="HoraDesde" value="<?php echo $_GET["horadesde"];  ?>" />
												<input type="hidden" name="IDSocio"  id="IDSocio" value="<?php echo $_GET["IDSocio"];  ?>" />
												<input type="hidden" name="action" id="action" value="insert" />
												<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET["IDClub"];  ?>" />
												<input type="hidden" name="IDCursoCalendarioTrimestre" id="IDCursoCalendarioTrimestre" value="<?php echo $id_calendario;  ?>" />
										 </tr>
									</table>
								</form>



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

			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->


	</body>
</html>
