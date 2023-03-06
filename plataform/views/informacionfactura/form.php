<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<div class="form-group first ">
							<? $arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
								if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
									<div class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?> </label>
										<div class="col-sm-8">
											<select name = "IDClub" id="IDClub">
												<?
													$arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
													foreach($arrOpciones as $key => $val){              
														if($frm["IDClub"] == $val){
															echo '<option value="' .$val. '" selected="selected">' .$key. '</option>';
														}else{
															echo '<option value="' .$val. '">' .$key. '</option>';
														}
													}
												?>
											</select>
										</div>
									</div>
							<? }else{ ?>
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
							<? } ?>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Razonsocial', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Nombre1" name="Nombre1" placeholder="<?= SIMUtil::get_traduccion('', '', 'Razonsocial', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Razonsocial', LANGSESSION); ?>" value="<?php echo $frm["Nombre1"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombrecomercial', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Nombre2" name="Nombre2" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombrecomercial', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Nombrecomercial', LANGSESSION); ?>" value="<?php echo $frm["Nombre2"]; ?>"></div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nit: </label>
								<div class="col-sm-8"><input type="text" id="Nit" name="Nit" placeholder="Nit" class="col-xs-12 mandatory" title="Nit" value="<?php echo $frm["Nit"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'matriculamercantil', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="MatriculaMercantil" name="MatriculaMercantil" placeholder="<?= SIMUtil::get_traduccion('', '', 'matriculamercantil', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'matriculamercantil', LANGSESSION); ?>" value="<?php echo $frm["MatriculaMercantil"]; ?>"></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Regimen', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<?	
										$arrRegimen = array(
											1 => SIMUtil::get_traduccion('', '', 'Impuestosobrelasventas', LANGSESSION),
											2 => SIMUtil::get_traduccion('', '', 'NoresponsabledeIVA', LANGSESSION),
										);
										echo SIMHTML::formPopupArray($arrRegimen,$frm["Regimen"],'Regimen',SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"","")
									?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoOrganizacion', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<?	
										$arrTipoOrganizacion = array(
											1 => SIMUtil::get_traduccion('', '', 'Personajuridica', LANGSESSION),
											2 => SIMUtil::get_traduccion('', '', 'Personanatural', LANGSESSION),
										);
										echo SIMHTML::formPopupArray($arrTipoOrganizacion,$frm["TipoOrganizacion"],'TipoOrganizacion',SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"","")
									?>
								</div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'pais', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formPopup('PaisDian', 'Nombre', 'Nombre', 'IDPaisDian', $frm["IDPaisDian"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', 'onchange="selDepartamentos()"', ''); ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Departamento', LANGSESSION); ?>: </label>
								<div class="col-sm-8" id="departamentos"></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Ciudad', LANGSESSION); ?>: </label>
								<div class="col-sm-8" id="ciudades"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'codigopostal', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="CodigoPostal" name="CodigoPostal" placeholder="<?= SIMUtil::get_traduccion('', '', 'codigopostal', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'codigopostal', LANGSESSION); ?>" value="<?php echo $frm["CodigoPostal"]; ?>"></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Direccion" name="Direccion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>" value="<?php echo $frm["Direccion"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Telefono" name="Telefono" placeholder="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>" value="<?php echo $frm["Telefono"]; ?>"></div>
							</div>
						</div>		
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Email" name="Email" placeholder="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>" value="<?php echo $frm["Email"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'textofinal', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<textarea id="TextoFinal" name="TextoFinal" cols="10" rows="3" class="col-xs-12"><?php echo $frm["TextoFinal"]; ?></textarea>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<? 
								$color1 = empty($frm["Color1"]) ? "#FFFFFF" : $frm["Color1"];
								$color2 = empty($frm["Color2"]) ? "#FFFFFF" : $frm["Color2"];
							?>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>1: </label>
								<div class="col-sm-8"><input type="color" id="Color1" name="Color1" placeholder="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>1" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>1" value="<?= $color1; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>2: </label>
								<div class="col-sm-8"><input type="color" id="Color2" name="Color2" placeholder="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>2" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>2" value="<?= $color2; ?>"></div>
							</div>
						</div>	
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Logo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input name="Logo" id="Logo" class="" title="<?= SIMUtil::get_traduccion('', '', 'Logo', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
									<?  if (!empty($frm['Logo'])) {
										echo "<img src='" . FACTURA_ROOT . $frm["Logo"] . "' width=100 height=100>";
										echo "<a href='informacionFactura.php?action=delfoto&logo=".$frm['Logo']."&campo=Logo&id=". $frm[$key]."' class='ace-icon glyphicon glyphicon-trash'>&nbsp;</a>";
									}?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>
						</div>	
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>
<script>
	selDepartamentos();

	function selDepartamentos(){

		let idPais = $('#IDPaisDian').val();
		let idDepartamento = '<?= $frm['IDDepartamentoDian'];?>';

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "departamentos",
				idPais: idPais,
				idDepartamento: idDepartamento
			},
			dataType: "html",
			url: "includes/async/informacionfactura.async.php",
			success: function (data) {
				$("#departamentos").html(data);
				selCiudades();
			}
		}); 
	}

	function selCiudades(){

		let idDepartamento = $('#IDDepartamentoDian').val();
		let idCiudadDian = '<?= $frm['IDCiudadDian'];?>';

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "form",
				proceso: "ciudades",
				idDepartamento: idDepartamento,
				idCiudad: idCiudadDian
			},
			dataType: "html",
			url: "includes/async/informacionfactura.async.php",
			success: function (data) {
				$("#ciudades").html(data);
			}
		}); 
	}

</script>