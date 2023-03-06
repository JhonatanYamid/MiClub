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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Habitacion </label>

										<div class="col-sm-8">
											<select name = "IDTipoHabitacion" id="IDTipoHabitacion" class="form-control">
                                        		<option value=""></option>
                                             	<?php
												$sql_tipohab_club = "Select * From TipoHabitacion  Where  IDClub = '".SIMUser::get("club")."'";
												$qry_tipohab_club = $dbo->query($sql_tipohab_club);
												while ($r_tipohab = $dbo->fetchArray($qry_tipohab_club)): ?>
													<option value="<?php echo $r_tipohab["IDTipoHabitacion"]; ?>" <?php if($r_tipohab["IDTipoHabitacion"]==$frm["IDTipoHabitacion"]) echo "selected";  ?>><?php echo $r_tipohab["Nombre"]; ?></option>
												<?php endwhile;  ?>
                                        	</select>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Tarifa </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( array("Socio"=>"Socio","Invitado"=>"Invitado") ) , $frm["TipoTarifa"] , 'TipoTarifa' , "class='input' form-control" ) ?>
    									</div>
								</div>

							</div>

							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Socio </label>
									<div class="col-sm-8"> 
										<?php
										$sql_tipo_socio = "SELECT TS.IDTipoSocio,Nombre FROM TipoSocio TS, ClubTipoSocio CTS WHERE TS.IDTipoSocio=CTS.IDTipoSocio AND IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
										$result_tipo_socio = $dbo->query($sql_tipo_socio); ?>
											
										<select name="TipoSocio" id="TipoSocio" class="form-control">
											<option value="">[Seleccione Tipo Socio]</option> 
											<? while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) { ?> 
												<option value="<? echo $row_tipo_soc["Nombre"];  ?>" <? if ($frm["TipoSocio"] == $row_tipo_soc["Nombre"]) echo "selected"; ?>><? echo $row_tipo_soc["Nombre"];  ?></option> 
											<? } ?>
										</select> 
										
									</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tarifa para pasadia </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["TarifaParaPasadia"] , 'TarifaParaPasadia' , "class='input' form-control" ) ?>
    									</div>
								</div>

							</div>


                           <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Temporada </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( array("Alta"=>"Alta","Baja"=>"Baja") ) , $frm["Temporada"] , 'Temporada' , "class='input' form-control" ) ?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Socio adicional </label>

										<div class="col-sm-8">
											<input type="text" id="ValorSocioAdicional" name="ValorSocioAdicional" placeholder="Valor Socio Adicional" class="col-xs-12 " title="Valor Socio Adicional" value="<?php echo $frm["ValorSocioAdicional"]; ?>" >
    									</div>
								</div>

							</div>

              				<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Invitado Externo adicional </label>

									<div class="col-sm-8">
										<input type="text" id="ValorExternoAdicional" name="ValorExternoAdicional" placeholder="Valor Externo Adicional" class="col-xs-12 " title="Valor Externo Adicional" value="<?php echo $frm["ValorExternoAdicional"]; ?>" >
									</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor </label>

									<div class="col-sm-8">
										<input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $frm["Valor"]; ?>" >
									</div>
								</div>

							</div>
							<?php if(SIMUser::get("club") == 70):?>

              				<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Invitado Externo adicional con sofacama </label>

									<div class="col-sm-8">
										<input type="text" id="ValorExternoAdicionalSofacama" name="ValorExternoAdicionalSofacama" placeholder="Valor Externo Adicional Sofacama" class="col-xs-12 " title="Valor Externo Adicional" value="<?php echo $frm["ValorExternoAdicionalSofacama"]; ?>" >
									</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Socio adicional con sofacama </label>

									<div class="col-sm-8">
										<input type="text" id="ValorSocioAdicionalSofacama" name="ValorSocioAdicionalSofacama" placeholder="Valor Socio Adicional Sofacama" class="col-xs-12 mandatory" title="ValorSocioAdicionalSofacama" value="<?php echo $frm["ValorSocioAdicionalSofacama"]; ?>" >
									</div>
								</div>

							</div>
							<?php endif; ?>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Aplica para el año </label>


										<div class="col-sm-8">
											<select name="Year" id="Year" class="form-control">
													<option>[Seleccione]</option>
													<?php
													$proximo_year=date("Y")+1;
													$year_hasta=date("Y")-5;
													for($year=$proximo_year;$year>=$year_hasta;$year--){ ?>
															<option value="<?php echo $year; ?>" <?php if($frm["Year"]==$year) echo "selected";  ?>><?php echo $year; ?></option>
													<?php } ?>
											</select>
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
