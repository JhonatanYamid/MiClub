<?
	$whCat = "";
	$txtValues = "";
		
	if($IDClub == $idPadre){
		$arrOpciones = SIMUtil::ObtenerHijosClubPadre($IDClub);
		$arrValue = array();
		
		if($action == 'edit'){
			$sqlValue = "SELECT IDClub FROM TipoFacturacionClub WHERE IDTipoFacturacion = $id";
			$resValue = $dbo->query($sqlValue);

			while($rowValue = $dbo->fetchArray($resValue)) {
				$arrValue[] = $rowValue['IDClub'];
			}
		}
	}else{
		$whCat = "AND IDCategoriaFacturacion IN (SELECT IDCategoriaFacturacion FROM CategoriaFacturacionClub WHERE IDClub = $IDClub)";
	}
	
	$frm['Editar'] = $frm['Editar'] == '' ? 'S' : $frm['Editar'];
	$frm['Eliminar'] = $frm['Eliminar'] == '' ? 'S' : $frm['Eliminar'];
	$frm['Activo'] = $frm['Activo'] == '' ? 'S' : $frm['Activo'];
	
	$frm['Precio'] = $frm['Precio'] == '' ? 'N' : $frm['Precio'];
	$frm['FechaActivacion'] = $frm['FechaActivacion'] == '' ? 'N' : $frm['FechaActivacion'];
	$frm['PermitirReservar'] = $frm['PermitirReservar'] == '' ? 'N' : $frm['PermitirReservar'];
	$frm['NumSesiones'] = $frm['NumSesiones'] == '' ? 'N' : $frm['NumSesiones'];
	$frm['Beneficiarios'] = $frm['Beneficiarios'] == '' ? 'N' : $frm['Beneficiarios'];
	$frm['ControlAcceso'] = $frm['ControlAcceso'] == '' ? 'N' : $frm['ControlAcceso'];
	$frm['Congelaciones'] = $frm['Congelaciones'] == '' ? '3' : $frm['Congelaciones'];

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
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'categoria', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formPopup('CategoriaFacturacion', 'Nombre', 'IDCategoriaFacturacion', 'IDCategoriaFacturacion', $frm["IDCategoriaFacturacion"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', 'onchange = "changeCategoria()"', "AND Activo = 'S' AND IDClub = $idPadre $whCat"); ?>
								</div>
							</div>
						</div>
						<div id="secc1" style="display: none;">
							<div class="form-group first">							
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permitirreservar', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermitirReservar"], 'PermitirReservar', "class='input'") ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 divReserva" style="display: none; margin-bottom: 10px;">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ingresarnumerodesesiones', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NumSesiones"], 'NumSesiones', "class='input'") ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 divReserva" style="display: none;">
									<? $arrOpPer = array(SIMUtil::get_traduccion('', '', 'ilimitadas', LANGSESSION)=>1,SIMUtil::get_traduccion('', '', 'ingresarcantidad', LANGSESSION)=>2,SIMUtil::get_traduccion('', '', 'nopermitidas', LANGSESSION)=>3);?>
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'congelaciones', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formRadioGroup($arrOpPer, $frm["Congelaciones"], 'Congelaciones', "", ""); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'elegirfechadeactivacion', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["FechaActivacion"], 'FechaActivacion', "class='input'") ?>
									</div>
								</div>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permitirbeneficiarios', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Beneficiarios"], 'Beneficiarios', "class='input'") ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permitircontroldeacceso', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ControlAcceso"], 'ControlAcceso', "class='input'") ?>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'agregarprecio', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Precio"], 'Precio', "class='input'") ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input'") ?>
								</div>
							</div>
						</div>
						<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeditar', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Editar"], 'Editar', "class='input'"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeliminar', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Eliminar"], 'Eliminar', "class='input'"); ?>
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
										<? echo SIMHTML::formCheckGroup2($arrOpciones, $arrValue, "clubes[]" ,"" ,"selOne");?>
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
	changeCategoria();
	chekedAll();
	
	var permitirReserva = $("input[name='PermitirReservar']:checked").val();

	if(permitirReserva == 'S'){
		$(".divReserva").show("slow");
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

	function changeCategoria(){
		
		var val = $('#IDCategoriaFacturacion').val();

		if(val == '1'){
			$("#secc1").show("slow");
		}else{
			$("#secc1").hide("slow");
		}
	}

	$("input[name='PermitirReservar']").change(function (){
      
		if($(this).val() == 'N'){
			$(".divReserva").hide("slow");
		}else if($(this).val() == 'S'){
			$(".divReserva").show("slow");
		}
	});
	
</script>