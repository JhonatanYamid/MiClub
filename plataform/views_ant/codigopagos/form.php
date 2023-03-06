<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
		</h4>		
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">						
							<div  class="form-group first ">
    	                        <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-8">
										  <!--
                                          <select name = "IDSocio" id="IDSocio" <?php if($_GET["action"]!= "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php 
										$sql_socio_club = "Select * From Socio Where IDClub = '".SIMUser::get("club")."' Order by Apellido Asc";
										$qry_socio_club = $dbo->query($sql_socio_club);
										while ($r_socio = $dbo->fetchArray($qry_socio_club)): ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if($r_socio["IDSocio"]==$frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " .$r_socio["Nombre"]); ?></option>
										    <?php
										 	endwhile;    ?>
									      </select>
                                          -->
                                          	<?php 
										$sql_socio_club = "Select * From Socio Where IDSocio = '".$frm["IDSocio"]."'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>
                                            
                                          	<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if($_GET["action"]!= "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"]) ?>" >
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
										</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo </label>
										<div class="col-sm-8">
                                        <input type="text" id="Codigo" name="Codigo" placeholder="Codigo" class="col-xs-12 mandatory" title="Codigo" value="<?php echo $frm["Codigo"];?>">
                                        <input type="button" name="GenerarCodigo" id="GenerarCodigo" value="Generar Codigo Automatico">
                                        </div>
                                        
								</div>									
							</div>	
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Desde </label>
								  <div class="col-sm-8">
								    <input type="text" id="FechaDesde" name="FechaDesde" placeholder="Fecha Desde" class="col-xs-12 calendar mandatory" title="Fecha Desde" value="<?php echo $frm["FechaDesde"] ?>" >
								  </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Hasta </label>

										<div class="col-sm-8">
										  <input type="text" id="FechaHasta" name="FechaHasta" placeholder="Fecha Hasta" class="col-xs-12 calendar mandatory" title="Fecha Hasta" value="<?php echo $frm["FechaHasta"] ?>" >
										</div>
								</div>
									
							</div>					
						<div  class="form-group first ">
                        		<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Motivo </label>
										<div class="col-sm-8">
											<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
    									</div>
								</div>	
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Disponible </label>										
                                        <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Disponible"] , 'Disponible' , "class='input mandatory'" ) ?>                                        
								</div>															
                          </div>  

						  <?php
							 if(SIMUser::get("club") == 8 || SIMUser::get("club") == 28) 
							 {
						  ?>
						  <div  class="form-group first ">
                        		<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Bono </label>
										<div class="col-sm-8">
											<input type="number" id="Valor" name="Valor" cols="10" rows="5" class="col-xs-12 mandatory" title="Valor" value="<?php echo $frm["Valor"] ?>">
    									</div>
								</div>                                														
                          </div> 
						  <?php
						  }
						  ?>
							
							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>									
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
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