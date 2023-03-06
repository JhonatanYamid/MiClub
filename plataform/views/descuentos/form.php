<? 
	$txtValues = "";
	if($IDClub == $idPadre){
		$arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
		$arrValue = array();

		if($action == 'edit'){
			$sqlValue = "SELECT IDClub FROM DescuentosClub WHERE IDDescuentos = $id";
			$resValue = $dbo->query($sqlValue);

			while($rowValue = $dbo->fetchArray($resValue)) {
				$arrValue[] = $rowValue['IDClub'];
			}
		}
	}

	$frm['Editar'] = $frm['Editar'] == '' ? 'S' : $frm['Editar'];
	$frm['Eliminar'] = $frm['Eliminar'] == '' ? 'S' : $frm['Eliminar'];
	$frm['Activo'] = $frm['Activo'] == '' ? 'S' : $frm['Activo'];
	
?>
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
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'descripcion', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="3" class="col-xs-12"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>
						</div>						
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'tipodecalculo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<select name = "TipoCalculo" id="TipoCalculo" class="mandatory" onchange = "labelsChange()">
                                        <?
                                            $options = array(
                                                SIMUtil::get_traduccion('', '', 'porporcentaje', LANGSESSION) => "1",
                                                SIMUtil::get_traduccion('', '', 'convalorfijo', LANGSESSION) => "2"
                                            );
                                            foreach($options as $key => $val){              
                                                if($frm["TipoCalculo"] == $val){
                                                    echo '<option value="' .$val. '" selected="selected">' .$key. '</option>';
                                                }else{
                                                    echo '<option value="' .$val. '">' .$key. '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1" id='lblCalculo'> <?= SIMUtil::get_traduccion('', '', 'ingresarporcentajeenlafactura', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["EnFactura"], 'EnFactura', "class='input'") ?>
								</div>
							</div>
						</div>						
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6 valor" style="display:none">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1" id='lblValor'> <?= SIMUtil::get_traduccion('', '', 'porcentajededescuento', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="number" id="ValorDescuento" name="ValorDescuento" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'porcentajededescuento', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'porcentajededescuento', LANGSESSION); ?>" value="<?php echo $frm["ValorDescuento"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'requiereadministrador', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermisoAdmin"], 'PermisoAdmin', "class='input'") ?>
								</div>
							</div>
						</div>					
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="CuentaContable" name="CuentaContable" placeholder="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" value="<?php echo $frm["CuentaContable"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>
						</div>	
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type='text' id='DisponibleDesde' name='DisponibleDesde' value="<?php echo $frm["DisponibleDesde"]; ?>" class='calendar' placeholder='<?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION);?>'   $title="<?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION);?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type='text' id='DisponibleHasta' name='DisponibleHasta' value="<?php echo $frm["DisponibleHasta"]; ?>" class='calendar' placeholder='<?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION);?>'   $title="<?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION);?>">
								</div>
							</div>
						</div>
						<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeditar', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Editar"], 'Editar', "class='input'"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeliminar', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Eliminar"], 'Eliminar', "class='input'"); ?>
									</div>
								</div>
							</div>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'habilitaren', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<div class="form-actions no-margin" style="padding:10px">
											<label class="checkgroup"><input type="checkbox" name="selAll" id="selAll" value=true > <?= SIMUtil::get_traduccion('', '', 'seleccionartodo', LANGSESSION); ?></label>
										</div>
										<? echo SIMHTML::formCheckGroup2($arrOpciones, $arrValue, "clubes[]" ,"onclick = 'chekedAll()'" ,"selOne");?>
									</div>
								</div>
							</div>
						<? } ?>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $idPadre?>" />
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

	chekedAll();
	labelsChange();	
	
	if($("input[name='EnFactura']:checked").val() == 'N'){
		$(".valor").show("slow");
	}

	$("#selAll").change(function() {

		if (this.checked) {
            $(".selOne").each(function() {
                this.checked=true;
            });

        } else {
            $(".selOne").each(function() {
                this.checked=false;
            });
        }
    });

	function chekedAll(){
		var check = 1;

		$(".selOne").each( function () {
			if(!($(this).is(':checked'))){
				check = 0;
			}
		});

		if (check == 1) {
			$("#selAll").prop("checked", true);
		}else{
			$("#selAll").prop("checked", false);
		}
	}
	
	$("input[name='EnFactura']").change(function (){
      
	    if($(this).val() == 'S'){
            $(".valor").hide("slow");
        }else if($(this).val() == 'N'){
            $(".valor").show("slow");
        }
    });

	function labelsChange(){
		
		var txtValorFac = '<?= SIMUtil::get_traduccion('', '', 'ingresarvalorenlafactura', LANGSESSION); ?>';
		var txtValor = '<?= SIMUtil::get_traduccion('', '', 'valordedescuento', LANGSESSION); ?>';
		
		var txtPorcentajeFac = '<?= SIMUtil::get_traduccion('', '', 'ingresarporcentajeenlafactura', LANGSESSION); ?>';
		var txtPorcentaje = '<?= SIMUtil::get_traduccion('', '', 'porcentajededescuento', LANGSESSION); ?>';

		var TipoCalculo = $("#TipoCalculo").val();

		var tipo = txtPorcentaje;
		var tipoFac = txtPorcentajeFac;

		if(TipoCalculo == 2){
			tipo = txtValor;
			tipoFac = txtValorFac;
		}

		$("#lblValor").text(tipo+":");
		$("#lblCalculo").text(tipoFac+":");
		$("#ValorDescuento").attr( "title", tipo);
		$("#ValorDescuento").attr( "placeholder", tipo);
	}
	
</script>