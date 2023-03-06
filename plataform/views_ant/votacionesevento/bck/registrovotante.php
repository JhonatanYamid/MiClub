
<div class="widget-box transparent" id="recent-box">

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frmRegistroV" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-8 control-label no-padding-right" for="form-field-1"> Numero de documento o accion </label>

										<div class="col-sm-4">
											<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Numero Documento" class="col-xs-12 mandatory" title="NumeroDocumento" value="" >
											<button class="btn btn-info btnEnviar" type="submit" rel="frm<?php echo $script; ?>" >
												<i class="ace-icon fa fa-check bigger-110"></i>
												Buscar
											</button>
											<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
											<input type="hidden" name="action" id="action" value="BuscarVotante" />
											<input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
										</div>
								</div>

							</div>

					</form>

					<?php
					//echo " Cedula = '".base64_decode($_GET["Parametro"])."' and IDVotacionEvento = '".$_GET["id"]."' ";
					$datos_votante =$dbo->fetchAll("VotacionVotante"," Cedula = '".base64_decode($_GET["Parametro"])."' and IDVotacionEvento = '".$_GET["id"]."' ","array");
					if((int)$datos_votante["IDVotacionVotante"]>0){
						if($datos_votante["Presente"]=="S"){
							$checksi="checked";
							$checkno="";
							$label_estado="REGISTRADO";
							$class_estado="green";
						}
						else{
							$checkno="checked";
							$checksi="";
							$label_estado="NO Registrado";
							$class_estado="red";
					}
						?>
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
									<td><h3>Estado</h3></td>
									<td colspan="3" class="<?php echo $class_estado; ?>"><h3><div id="msgestadoregistro"><?php echo $label_estado;  ?></div></h3></td>
									<td><h3>Total coeficiente representado</h3></td>
									<td colspan="3" class="green"><h3>
										<?php
										$coeficiente=SIMUtil::verifica_coeficiente($datos_votante["IDSocio"]);
										/*
										$sql_sumapoderes="SELECT Coeficiente FROM VotacionVotante WHERE IDVotacionVotante in (SELECT IDVotacionVotanteDelegaPoder FROM VotacionPoder WHERE IDVotacionVotante = '".$datos_votante["IDVotacionVotante"]."') ";
										$r_sumapoderes=$dbo->query($sql_sumapoderes);
										while($row_poderes=$dbo->fetchArray($r_sumapoderes)){
											$suma_otorgados+=$row_poderes["Coeficiente"];
										}
										$coeficiente_total=$suma_otorgados+$datos_votante["Coeficiente"];
										*/
										echo $coeficiente;
										 ?>
										</h3></td>
							</tr>
								<tr>
										<td><h3>ID</h3></td>
										<td class="green"><h3><?php echo $datos_votante["Cedula"];  ?></h3></td>
										<td><h3>Nombre</h3></td>
										<td class="green"><h3><?php echo $datos_votante["Nombre"];  ?></h3></td>
										<td><h3>Coeficiente</h3></td>
										<td class="green"><h3><?php echo $datos_votante["Coeficiente"];  ?></h3></td>
										<td><h3>Lote</h3></td>
										<td class="green"><h3><?php echo $datos_votante["NumeroCasa"];  ?></h3></td>
								</tr>
						</table>

						<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
										<i class="ace-icon fa fa-download green"></i>
									VOTACION
								</h3>
						</div>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
								<tbody>
								<tr>
										<td style="width:250px !important;"><b>Presente</b></td>
										<td>
											<input type="radio" value="S" class="btnvotantepresente" name="VotantePresente<?php echo $datos_votante["IDVotacionVotante"]; ?>" idvotante="<?php echo $datos_votante["IDVotacionVotante"]; ?>" usuarioregistra="<?php echo SIMUser::get("IDUsuario"); ?>" <?php echo $checksi ?>>Si
											<input type="radio" value="N" class="btnvotantepresente" name="VotantePresente<?php echo $datos_votante["IDVotacionVotante"]; ?>" idvotante="<?php echo $datos_votante["IDVotacionVotante"]; ?>" usuarioregistra="<?php echo SIMUser::get("IDUsuario"); ?>" <?php echo $checkno ?> >No
											<div name='msgupdate<?php echo $datos_votante["IDVotacionVotante"]; ?>' id='msgupdate<?php echo $datos_votante["IDVotacionVotante"]; ?>'></div>
										</td>
								</tr>

								<tr>
										<td><b>Otorgar Poder a: </b></td>
										<td>
											<button class="btn btn-info fancyboxpoder" href="registrapoder.php?Tipo=Propietario&IDVotantePadre=<?php echo $datos_votante["IDVotacionVotante"]; ?>&IDClub=<?php echo SIMUser::get("club"); ?>&IDVotacionEvento=<?php echo $_GET["id"]; ?>&IDUsuarioRegistra=<?php echo SIMUser::get("IDUsuario"); ?>" data-fancybox-type="iframe">
											<i class="ace-icon fa  fa-exchange align-top bigger-125"></i>
											Propietario
											</button>

											<button class="btn btn-info fancyboxpoder" href="registrapoder.php?Tipo=Externo&IDVotantePadre=<?php echo $datos_votante["IDVotacionVotante"]; ?>&IDClub=<?php echo SIMUser::get("club"); ?>&IDVotacionEvento=<?php echo $_GET["id"]; ?>&IDUsuarioRegistra=<?php echo SIMUser::get("IDUsuario"); ?>" data-fancybox-type="iframe">
											<i class="ace-icon fa  fa-exchange align-top bigger-125"></i>
											Externo
											</button>

										</td>
								</tr>
								<tbody>
						</table>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
											<th colspan="6">Poder Otorgado a:</th>

							</tr>
										<tr>
														<th>Cedula</th>
														<th>Nombre</th>
														<th>Predio</th>
														<th>Coeficiente</th>
														<th>Moroso ?</th>
														<th>Eliminar Poder</th>
										</tr>
										<tbody id="listacontactosanunciante">
										<?php
										$sql_poder="SELECT IDVotacionVotanteDelegaPoder,IDVotacionPoder,IDVotacionVotante FROM VotacionPoder WHERE IDVotacionVotanteDelegaPoder = '".$datos_votante["IDVotacionVotante"]."'";
										$r_poder=$dbo->query($sql_poder);
										while($row_poder=$dbo->fetchArray($r_poder)){
											$datos_otorga =$dbo->fetchAll("VotacionVotante"," IDVotacionVotante = '".$row_poder["IDVotacionVotante"]."' ","array");
										?>
											<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
															<td><?php echo $datos_otorga["Cedula"]; ?></td>
															<td><?php echo $datos_otorga["Nombre"]; ?></td>
															<td><?php echo $datos_otorga["NumeroCasa"]; ?></td>
															<td><?php echo $datos_otorga["Coeficiente"]; ?></td>
															<td><?php echo $datos_otorga["Moroso"]; ?></td>
															<td>
																<?php echo '<a class="green" href="votacionesevento.php?action=EliminaPoder&IDVotacionPoder='.$row_poder["IDVotacionPoder"].'&tabclub=votantes&IDVotacionEvento='.$datos_otorga["IDVotacionEvento"].'">Eliminar</a>'; ?>
															</td>
											</tr>
									<?php } ?>
										</tbody>

						</table>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
											<th colspan="6">PODERES:</th>

							</tr>
										<tr>
														<th>Cedula</th>
														<th>Nombre</th>
														<th>Predio</th>
														<th>Coeficiente</th>
														<th>Moroso ?</th>
														<th>Eliminar Poder</th>
										</tr>
										<tbody id="listacontactosanunciante">
										<?php
										$sql_poder="SELECT IDVotacionVotanteDelegaPoder,IDVotacionPoder,IDVotacionVotante FROM VotacionPoder WHERE IDVotacionVotante = '".$datos_votante["IDVotacionVotante"]."'";
										$r_poder=$dbo->query($sql_poder);
										while($row_poder=$dbo->fetchArray($r_poder)){
											$datos_otorga =$dbo->fetchAll("VotacionVotante"," IDVotacionVotante = '".$row_poder["IDVotacionVotanteDelegaPoder"]."' ","array");
										?>
											<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
															<td><?php echo $datos_otorga["Cedula"]; ?></td>
															<td><?php echo $datos_otorga["Nombre"]; ?></td>
															<td><?php echo $datos_otorga["NumeroCasa"]; ?></td>
															<td><?php echo $datos_otorga["Coeficiente"]; ?></td>
															<td><?php echo $datos_otorga["Moroso"]; ?></td>
															<td>
																<?php echo '<a class="green" href="votacionesevento.php?action=EliminaPoder&IDVotacionPoder='.$row_poder["IDVotacionPoder"].'&tabclub=votantes&IDVotacionEvento='.$datos_otorga["IDVotacionEvento"].'">Eliminar</a>'; ?>
															</td>
											</tr>
									<?php } ?>
										</tbody>

						</table>




	        <?php }
	        elseif(!empty($_GET["Parametro"])){
	          echo "<h2>No encontrado por favor verifique</h2>";
	        }
					?>


				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->
