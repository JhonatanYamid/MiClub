<?php	

$IDLukerEmpleado = $_GET["id"];
$sql = "SELECT * FROM LukerBeneficiario WHERE IDLukerEmpleado=$IDLukerEmpleado";

$queryBendeficiarios = $dbo->query($sql);
$beneficiarios = $dbo->fetch($queryBendeficiarios);
$beneficiarios = isset($beneficiarios["IDLukerBeneficiario"]) ? [$beneficiarios] : $beneficiarios;


foreach($beneficiarios as $frm):
	$frm["SEXO"] = ($frm["SEXO"]=="MAS")? "Masculino" : "Femenino";

?>
<hr>
<div class="container">
	<div class="">
		
		<div class="">
			<div class="">
				<div class="">
					<div class="">
						<form class="" role="form" method="post" id="frm_beneficiario" action="" enctype="multipart/form-data">								
								<div class="row">    	                        
										<div  class="form-group col-md-4">
											<label for="form-field-1"> Primer apellido*  </label>																														
											<input type="hidden" id="EMP_CODIGO" name="EMP_CODIGO" placeholder="" class="" title="EMP_CODIGO"  value="<?php echo $frm["EMP_CODIGO"] ?>" >
											<input type="text" id="APELLIDO1" name="APELLIDO1" placeholder="" class="form-control" title="Primer apellido" maxlength="15" value="<?php echo $frm["APELLIDO1"] ?>">			
										</div>
										<div  class="form-group col-md-4">
											<label for="form-field-1"> Segundo apellido </label>																		
											<input type="text" id="APELLIDO2" name="APELLIDO2" placeholder="" class="form-control" title="Segundo apellido" maxlength="15" value="<?php echo $frm["APELLIDO2"] ?>">                                			
														
										</div>
										<div  class="form-group col-md-4">
											<label  for="form-field-1"> Nombre completo* </label>																			
											<input type="text" id="NOMBRE" name="NOMBRE" placeholder="" class="form-control" title="Nombre" maxlength="30" value="<?php echo $frm["NOMBRE"] ?>">						
										</div>																				    
								</div>
								<div class="row">
										<div  class="form-group col-md-6">
											<label for="form-field-1"> Sexo </label>
											<input type="text" id="SEXO" name="SEXO" placeholder="" class="form-control" title="SEXO" maxlength="30" value="<?php echo $frm["SEXO"] ?>">
										</div>
								</div>
								<div class="row">
										<div  class="form-group col-md-6">
											<label for="TIPO_IDENT"> Tipo de documento de identidad </label>
											<input type="text" id="TIPO_IDENT" name="TIPO_IDENT" placeholder="" class="form-control" title="Tipo de documento de identidad" maxlength="30" value="<?php echo $frm["TIPO_IDENT"] ?>">																																	
										</div>
										<div  class="form-group col-md-6">
											<label for="form-field-1"> Número de documento  </label>																			
											<input type="text" id="IDENT_NUM" name="IDENT_NUM" placeholder="" class="form-control" title="Número de documento" maxlength="15" value="<?php echo $frm["IDENT_NUM"] ?>">								
										</div>
								</div>
								<div class="row">																
										<div class="form-group col-md-4">
											<label for="UGN1_CODIGO_IDENT"> País expedición  </label>
											<input type="text" id="UGN1_CODIGO_IDENT" name="UGN1_CODIGO_IDENT" placeholder="" class="form-control" title="País expedición" maxlength="15" value="<?php echo $frm["UGN1_CODIGO_IDENT_DETALLE"] ?>">											 
										</div>									
										<div  class="form-group col-md-4">
											<label for="UGN2_CODIGO_IDENT"> Departamento expedición  </label>																														
											<input type="text" id="UGN2_CODIGO_IDENT" name="UGN2_CODIGO_IDENT" placeholder="" class="form-control" title="Departamento expedición" maxlength="15" value="<?php echo $frm["UGN2_CODIGO_IDENT_DETALLE"] ?>">
										</div>
										<div  class="form-group col-md-4">
											<label for="UGN3_CODIGO_IDENT"> Ciudad expedición </label>																														
											<input type="text" id="UGN3_CODIGO_IDENT" name="UGN3_CODIGO_IDENT" placeholder="" class="form-control" title="Ciudad expedición" maxlength="15" value="<?php echo $frm["UGN3_CODIGO_IDENT_DETALLE"] ?>">
										</div>
								</div>
								<div class="row">
									<div class="form-group col-md-6">											
											<label for="RELAC_FAM"> Parentesco  </label>																														
											<input type="text" id="RELAC_FAM" name="RELAC_FAM" placeholder="" class="form-control" title="Parentesco"  value="<?php echo $frm["RELAC_FAM_DETALLE"] ?>">						
										</div>
										<div class="form-group col-md-6">											
											<label for="FEC_NACIO"> Fecha de nacimiento  </label>																														
											<input type="text" id="FEC_NACIO" name="FEC_NACIO" placeholder="" class="form-control" title="Fecha de nacimiento"  value="<?php echo $frm["FEC_NACIO"] ?>">						
										</div>
								</div>
								<div class="row">																
										<div class="form-group col-md-4">
											<label for="UGN1_CODIGO_NACI"> País nacimiento  </label>
											<input type="text" id="UGN1_CODIGO_NACI" name="UGN1_CODIGO_NACI" placeholder="" class="form-control" title="País nacimiento"  value="<?php echo $frm["UGN1_CODIGO_NACI_DETALLE"] ?>">
										</div>									
										<div  class="form-group col-md-4">
											<label for="UGN2_CODIGO_NACI"> Departamento nacimiento  </label>
											<input type="text" id="UGN2_CODIGO_NACI" name="UGN2_CODIGO_NACI" placeholder="" class="form-control" title="Departamento nacimiento"  value="<?php echo $frm["UGN2_CODIGO_NACI_DETALLE"] ?>">											 
										</div>
										<div  class="form-group col-md-4">
											<label for="UGN3_CODIGO_NACI"> Ciudad nacimiento </label>																														
											<input type="text" id="UGN3_CODIGO_NACI" name="UGN3_CODIGO_NACI" placeholder="" class="form-control" title="Ciudad nacimiento"  value="<?php echo $frm["UGN3_CODIGO_NACI_DETALLE"] ?>">
										</div>
								</div>
								<div class="row">
										<div class="form-group col-md-4">
											<label for="BENE_SANGRE_RH"> RH* </label>
											<input type="text" id="BENE_SANGRE_RH" name="BENE_SANGRE_RH" placeholder="" class="form-control" title="RH"  value="<?php echo $frm["BENE_SANGRE_RH"] ?>">											
										</div>	
										<div class="form-group col-md-4">
											<label for="BENE_TIPO_SANGRE"> Grupo sanguíneo* </label>
											<input type="text" id="BENE_TIPO_SANGRE" name="BENE_TIPO_SANGRE" placeholder="" class="form-control" title="Grupo sanguíneo"  value="<?php echo $frm["BENE_TIPO_SANGRE"] ?>">
										</div>									
								</div>								
								<div class="row">										
										<div  class="form-group col-md-6">
											<label for="EST_CIVIL"> Estado civil* :  </label>
											<input type="text" id="EST_CIVIL" name="EST_CIVIL" placeholder="" class="form-control" title="Estado civil"  value="<?php echo $frm["EST_CIVIL_DETALLE"] ?>">
										</div>						
								</div>
								<div class="row">										
										<div  class="form-group col-md-6">
											<label for="BENEF_CAMPO_IND4"> Benificiario salud </label>																																			
											<input type="text" id="BENEF_CAMPO_IND4" name="BENEF_CAMPO_IND4" placeholder="" class="form-control" title="Benificiario salud"  value="<?php echo $frm["BENEF_CAMPO_IND4"] ?>">
										</div>											
								</div>
								<div class="row">									
										<div  class="form-group col-md-6">
											<label for="PROFESION"> Profesión </label>
											<input type="text" id="PROFESION" name="PROFESION" placeholder="" class="form-control" title="Profesión"  value="<?php echo $frm["PROFESION_DETALLE"] ?>">
										</div>										
								</div>								
								<div class="row">																	
										<div  class="form-group col-md-4">
											<label for="DIRECCION"> Dirección*  </label>																														
											<input type="text" id="DIRECCION" name="DIRECCION" placeholder="" class="form-control" title="Dirección" maxlength="250" value="<?php echo $frm["DIRECCION"] ?>">  
										</div>												
								</div>
								<div class="row">										
										<div  class="form-group col-md-4">
											<label for="TELEFONO"> Teléfono </label>																													
											<input type="text" id="TELEFONO" name="TELEFONO" placeholder="" class="form-control" title="Teléfono" maxlength="30" value="<?php echo $frm["TELEFONO"] ?>">  
										</div>										
								</div>			
								<!-- <div class="row">
										<div class="form-group col-md-6">
											<label for="TIEM_CAMPO_ALF3"> Localidad </label>
											<input type="text" id="TIEM_CAMPO_ALF3" name="TIEM_CAMPO_ALF3" placeholder="" class="form-control" title="Localidad" maxlength="30" value="<?php echo $frm["TIEM_CAMPO_ALF3"] ?>">
										</div>								
								</div>		 -->					
								<div class="row">							
										<div  class="form-group col-md-6">
											<label for="BENEF_CAMPO_IND2"> ¿Le han diagnosticado alguna enfermedad? </label>
											<input type="text" id="BENEF_CAMPO_IND2" name="BENEF_CAMPO_IND2" placeholder="" class="form-control" title="Le han diagnosticado alguna enfermedad" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_IND2"] ?>">
										</div>																											
										<div  class="form-group col-md-6">
											<label for="form-field-1"> Enfermedad diagnosticada </label>																														
											<input type="text" id="BENEF_CAMPO_ALF5" name="BENEF_CAMPO_ALF5" placeholder="" class="form-control" title="Enfermedad diagnosticada" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_ALF5"] ?>">  
										</div>												
								</div>								
								<div class="row">								
										<div  class="form-group col-md-6">
											<label for="BENEF_CAMPO_IND3"> ¿Tiene algún tipo de discapacidad? </label>
											<input type="text" id="BENEF_CAMPO_IND3" name="BENEF_CAMPO_IND3" placeholder="" class="form-control" title="Tiene algún tipo de discapacidad" maxlength="30" value="<?php echo $frm["BENEF_CAMPO_IND3"] ?>">  
										</div>
										<div  class="form-group col-md-6">
											<label for="form-field-1"> % Discapacidad </label>																														
											<input type="number" min="0" max="100" id="BENEF_CAMPO_NUM1" name="BENEF_CAMPO_NUM1" placeholder="" class="form-control" title="Discapacidad"  value="<?php echo $frm["BENEF_CAMPO_NUM1"] ?>">  
										</div>										
								</div>								
								<div class="row">								
										<div  class="form-group col-md-6">
											<label for="RESIDE_EMPLEADO"> ¿Comparte domicilio? </label>
											<input type="text" id="RESIDE_EMPLEADO" name="RESIDE_EMPLEADO" placeholder="" class="form-control" title="Comparte domicilio" maxlength="30" value="<?php echo $frm["RESIDE_EMPLEADO"] ?>">																			
										</div>									
										<div  class="form-group col-md-6">
											<label for="DEPENDIENTE"> Depende económicamente </label>																														
											<input type="text" id="DEPENDIENTE" name="DEPENDIENTE" placeholder="" class="form-control" title="Depende económicamente" maxlength="30" value="<?php echo $frm["DEPENDIENTE"] ?>">
										</div>
										<div  class="form-group col-md-6">
											<label for="BENE_ESTADO"> Activo </label>
											<input type="text" id="BENE_ESTADO" name="BENE_ESTADO" placeholder="" class="form-control" title="Activo" maxlength="30" value="<?php echo $frm["BENE_ESTADO"] ?>">											
										</div>
								</div>
								<?php if($frm[Actulizado] == 1): ?>
								<div class="row">							
									<div class="form-group col-md-6">
										<label for="ESXB_IDIOMAS"> BENEFICIARIO ACTUALIZADO </label>                                   
									</div>							
								</div>
								<?php endif; ?>
								<div class="row">
									<div class="form-group col-md-4">
										<label for=""> Motivo de inactivación: </label>
										<textarea id="Motivo" class="form-control"><?php echo $frm["MOTIVO_INACTIVACION"] ?></textarea>
									</div>

									

								</div>
								<div class="row">
											<div  class="form-group col-md-4">
												<label for=""> Adjunto: </label>
												<a href="<?php echo URLROOT. "file/luker/actualizacionempleados/beneficiarios/". $frm["Archivo"]?>" width="200">Descargar archivo</a>
											</div>
									</div>
								</div>								
						</form>						
						</div>
					</div>
				</div>
			</div><!-- /.widget-main -->
		</div><!-- /.widget-body -->
	</div><!-- /.widget-box -->
</div>
<?php endforeach;?>
<hr>
<a href="actualizacionempleados.php?action=edit&id=<?php echo $IDLukerEmpleado?>" type="button" id="" class="btn btn-secondary btn-lg btn-block">Regresar a empleado</a>
<hr>

<?
	//include( "cmp/footer_scripts.php" );
?>

<script type="text/javascript">	
	
	
</script>

