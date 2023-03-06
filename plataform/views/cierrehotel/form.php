<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR  NUEVA <?php echo strtoupper(SIMReg::get( "title" ))?>
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">


						<div  class="form-group first ">

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio </label>

									<div class="col-sm-8">
										<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php if(empty($frm["FechaInicio"]))  echo ""; else echo $frm["FechaInicio"]; ?>" autocomplete="off" required >
									</div>
							</div>

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

									<div class="col-sm-8">
										<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php if(empty($frm["FechaFin"]))  echo ""; else echo $frm["FechaFin"]; ?>" autocomplete="off" required>
									</div>
							</div>

						</div>

						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Habitacion </label>

									<div class="col-sm-8">

										<?php
										$r_valor_tabla_tipohab =& $dbo->all( "TipoHabitacion" , " IDClub = '".SIMUser::get("club")."' ");
										while( $r_valor = $dbo->object( $r_valor_tabla_tipohab ) ):
											$array_tipohab[]= $r_valor;
										endwhile;

										$array_datos=explode("|",$frm["IDTipoHabitacion"]);
										if(count($array_datos)>0){
											foreach ($array_datos as $key => $value) {
												if(!empty($value))
													$array_datos_guardados[]= $value;
											}
										}
										?>

										<select style="width:100%" multiple class=" chosen-select form-control" name="TipoHabitacion[]" id="TipoHabitacion" data-placeholder="Seleccione...">
												<?php
													foreach( $array_tipohab as $id => $r_valor){
															if(count($array_datos_guardados)<=0):
																		$seleccionar = "";
															elseif(in_array($r_valor->IDTipoHabitacion,$array_datos_guardados)):
																		$seleccionar = "selected";
															else:
																		$seleccionar = "";
															endif;
															?>
															<option value="<?php echo $r_valor->IDTipoHabitacion ?>" <?php echo $seleccionar; ?>>
																<?php  echo $r_valor->Nombre;  ?>
															</option>
													<?php } ?>
										</select>

									</div>
							</div>
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Torre </label>

									<div class="col-sm-8">
										<?php
										$r_valor_tabla_torre =& $dbo->all( "Torre" , " IDClub = '".SIMUser::get("club")."' ");
										while( $r_valor = $dbo->object( $r_valor_tabla_torre ) ):
										$array_torre[]= $r_valor;
										endwhile;

										$array_datos_guardados=array();
										$array_datos=explode("|",$frm["IDTorre"]);
										if(count($array_datos)>0){
											foreach ($array_datos as $key => $value) {
												if(!empty($value))
													$array_datos_guardados[]= $value;
											}
										}


										?>

										<select style="width:100%" multiple class=" chosen-select form-control" name="Torre[]" id="Torre" data-placeholder="Seleccione...">
												<?php
													foreach( $array_torre as $id => $r_valor){
															if(count($array_datos_guardados)<=0):
																		$seleccionar = "";
															elseif(in_array($r_valor->IDTorre,$array_datos_guardados)):
																		$seleccionar = "selected";
															else:
																		$seleccionar = "";
															endif;
															?>
															<option value="<?php echo $r_valor->IDTorre ?>" <?php echo $seleccionar; ?>>
																<?php  echo $r_valor->Nombre;  ?>
															</option>
													<?php } ?>
										</select>
										</div>
							</div>
						</div>

						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habitacion </label>

									<div class="col-sm-8">
										<?php
										$r_valor_tabla_hab =& $dbo->all( "Habitacion" , " IDClub = '".SIMUser::get("club")."' ");
										while( $r_valor = $dbo->object( $r_valor_tabla_hab ) ):
											$array_hab[]= $r_valor;
										endwhile;

										$array_datos_guardados=array();
										$array_datos=explode("|",$frm["IDHabitacion"]);
										if(count($array_datos)>0){
											foreach ($array_datos as $key => $value) {
												if(!empty($value))
													$array_datos_guardados[]= $value;
											}
										}
										?>

										<select style="width:100%" multiple class=" chosen-select form-control" name="Habitacion[]" id="Habitacion" data-placeholder="Seleccione...">
												<?php
													foreach( $array_hab as $id => $r_valor){
															if(count($array_datos_guardados)<=0):
																		$seleccionar = "";
															elseif(in_array($r_valor->IDHabitacion,$array_datos_guardados)):
																		$seleccionar = "selected";
															else:
																		$seleccionar = "";
															endif;
															?>
															<option value="<?php echo $r_valor->IDHabitacion ?>" <?php echo $seleccionar; ?>>
																<?php  echo utf8_encode($dbo->getFields("TipoHabitacion","Nombre","IDTipoHabitacion =".$r_valor->IDTipoHabitacion)) . " " . $r_valor->NumeroHabitacion . " - ";  ?>
															</option>
													<?php } ?>
										</select>
									</div>
							</div>

						</div>



              <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Motivo Cierre </label>

										<div class="col-sm-8">
											<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
    									</div>
								</div>

							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>


								</div>
							</div>

					</form>
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>
