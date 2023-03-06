<? 
	$whCat = "";
	$valPrecio = 0;
	$txtValues = "";
	$habilitarHtml = "";
	$arrOpciones = $arrHijos;

	$sqlValue = "SELECT IDClub, Precio FROM ProductoPrecio WHERE IDProductoFacturacion = $id";
		
	if($IDClub == $idPadre && !(empty($arrOpciones))){
		
		$arrValue = array();
		$arrValueTxt = array();

		if($action == 'edit'){
			
			$resValue = $dbo->query($sqlValue);

			while($rowValue = $dbo->fetchArray($resValue)) {
				$clubPr = $rowValue['IDClub'];
				$arrValue[$clubPr] = $rowValue['Precio'];

				$txtPrecio = $clubPr."|".$rowValue['Precio'];
				array_push($arrValueTxt, $txtPrecio);
			}
			$txtValues = implode(",", $arrValueTxt);
		}

		$sqlServicio = "SELECT sc.IDServicioMaestro, GROUP_CONCAT(DISTINCT (IF(sc.TituloServicio = '',sm.Nombre,sc.TituloServicio))) as Nombre
						FROM ServicioClub as sc
							LEFT JOIN ServicioMaestro as sm  ON sc.IDServicioMaestro = sm.IDServicioMaestro
						WHERE 
							sc.IDClub in (SELECT IDClub FROM Club WHERE IDClubPadre = $IDClub OR IDClub = $IDClub)
							AND activo = 'S'
						GROUP BY sc.IDServicioMaestro";

	}else{
		
		$whCat = "AND IDCategoriaFacturacion IN (SELECT IDCategoriaFacturacion FROM CategoriaFacturacionClub WHERE IDClub = $IDClub)";

		$sqlServicio = "SELECT sc.IDServicioMaestro, IF(sc.TituloServicio = '',sm.Nombre,sc.TituloServicio) as Nombre
						FROM ServicioClub as sc
							LEFT JOIN ServicioMaestro as sm  ON sc.IDServicioMaestro = sm.IDServicioMaestro
						WHERE sc.IDClub = $IDClub AND activo = 'S'";


		if($action == 'edit'){
			$sqlValue .= " AND IDClub = $IDClub";

			$resValue = $dbo->query($sqlValue);
			$rowValue = $dbo->fetchArray($resValue);

			$valPrecio = $rowValue['Precio'];
		}
	}

	$rstaServicio = $dbo->query($sqlServicio);

	while ($rowServicio = $dbo->fetchArray($rstaServicio)){
		$nom = $rowServicio['Nombre'];
		$val = $rowServicio['IDServicioMaestro'];

		$arrOpServicio[$val] = $nom;
	}

	if($IDClub == $idPadre && !empty($arrOpciones)){
		$precioHtml = '<div class="col-xs-12 col-sm-1"></div>'
					.'<div class="col-xs-12 col-sm-10">'
						.'<div class="col-sm-12">'
							.'<div class="form-actions no-margin" style="padding:10px">'
								.'<center> <h4 class="blue "> '. ucwords(SIMUtil::get_traduccion("", "", "listadeprecios", LANGSESSION)) .' </h4></center>'
								.'<label class="checkgroup"><input type="checkbox" name="selAll" id="selAll" value=true > '. SIMUtil::get_traduccion("", "", "habilitartodoslosproductos", LANGSESSION).' </label>'
							.'</div>'
							. SIMHTML::formCheckInput($arrOpciones, $arrValue, "precio" ,"" ,"selOne","selInpt") .''
							.'<input type="hidden" id="precios" name="precios" value ="'.$txtValues.'">'
						.'</div>'
					.'</div>'
					.'<div class="col-xs-12 col-sm-1"></div>';

		
		$habilitarHtml = '<div class="col-xs-12 col-sm-6">'
							.'<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> '. SIMUtil::get_traduccion('', '', 'habilitaren', LANGSESSION).': </label>'
							.'<div class="col-sm-8">'
								.'<div class="form-actions no-margin" style="padding:10px">'
									.'<label class="checkgroup"><input type="checkbox" name="selAll" id="selAll" value=true > '. SIMUtil::get_traduccion('', '', 'seleccionartodo', LANGSESSION).' </label>'
								.'</div>'
								. SIMHTML::formCheckGroup2($arrOpciones, $arrValue, "precio" ,"","selOne").''
								.'<input type="hidden" id="precios" name="precios" value ="'.$txtValues.'">'
							.'</div>'
						.'</div>';
	}
	else{
		$precioHtml = '<div class="col-xs-12 col-sm-6">'
						.'<label class="col-sm-4 control-label no-padding-right" for="form-field-1">'. SIMUtil::get_traduccion('', '', 'precio', LANGSESSION) .'</label>'
						.'<div class="col-sm-8"><input type="text" id="precio" name="precio" placeholder="'. SIMUtil::get_traduccion('', '', 'precio', LANGSESSION) .'" class="col-xs-12" title="'. SIMUtil::get_traduccion('', '', 'precio', LANGSESSION) .'" value="'.$valPrecio.'"></div>'
					.'</div>';
	}

	$precioHtml = preg_replace('/\s\s+/'," ",$precioHtml); 
	$habilitarHtml = preg_replace('/\s\s+/'," ",$habilitarHtml); 

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
						<!-- DATOS BASICOS -->
						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-file-text-o green"></i> <? echo ucwords(SIMUtil::get_traduccion('', '', 'datosbasicos', LANGSESSION));?>
							</h3>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'categoria', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formPopup('CategoriaFacturacion', 'Nombre', 'IDCategoriaFacturacion', 'IDCategoriaFacturacion', $frm["IDCategoriaFacturacion"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), 'mandatory', 'onchange="selTipoFacturacion()" title="'.SIMUtil::get_traduccion('', '', 'categoria', LANGSESSION).'"', "AND Activo = 'S' AND IDClub = $idPadre $whCat"); ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'tipo', LANGSESSION); ?> </label>
								<div class="col-sm-8" id="divTipoFacturacion"></div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'descripcion', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="3" class="col-xs-12"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>/PLU </label>
								<div class="col-sm-8"><input type="text" id="Codigo" name="Codigo" placeholder="<?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>" value="<?php echo $frm["Codigo"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="CuentaContable" name="CuentaContable" placeholder="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'cuentacontable', LANGSESSION); ?>" value="<?php echo $frm["CuentaContable"]; ?>"></div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'vigenciaproducto', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="number" id="Vigencia" name="Vigencia" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'vigenciaproducto', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'vigenciaproducto', LANGSESSION); ?>" value="<?php echo $frm["Vigencia"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'unidaddetiempo', LANGSESSION); ?> </label>
								<div class="col-sm-8"><? echo SIMHTML::formPopupArray(SIMResources::$unidadTiempo,$frm["TipoVigencia"],'TipoVigencia',SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"","")?></div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input'") ?>
								</div>
							</div>
						</div>
						
						<!-- DETALLES RESERVA -->
						<div id="divReserva" style="display: none;">
							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-calendar green"></i> <? echo ucwords(SIMUtil::get_traduccion('', '', 'configuraciondereservas', LANGSESSION));?>
								</h3>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?> </label>
									<div class="col-sm-8"><? echo SIMHTML::formPopupArray($arrOpServicio,$frm["IDServicioMaestro"],'IDServicioMaestro',SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"","title='".SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION)."'"); ?></div>
								</div>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6 divSesion" style="display: none;">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'numerodesesiones', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<input type="number" id="NumSesiones" name="NumSesiones" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'numerodesesiones', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'numerodesesiones', LANGSESSION); ?>" value="<?php echo $frm["NumSesiones"]; ?>">
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 divCong" style="display: none;">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'cantidaddecongelaciones', LANGSESSION); ?> </label>
									<div class="col-sm-8"><input type="number" id="NumCongelacion" name="NumCongelacion" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'cantidaddecongelaciones', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'cantidaddecongelaciones', LANGSESSION); ?>" value="<?php echo $frm["NumCongelacion"]; ?>"></div>
								</div>
							</div>
							<div class="form-group first divTimeCong" style="display: none;">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'tiempodecongelaciones', LANGSESSION); ?> </label>
									<div class="col-sm-8"><input type="number" id="TimeCongelacion" name="TimeCongelacion" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'tiempodecongelaciones', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'tiempodecongelaciones', LANGSESSION); ?>" value="<?php echo $frm["TimeCongelacion"]; ?>"></div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'unidaddetiempo', LANGSESSION); ?> </label>
									<div class="col-sm-8"><? echo SIMHTML::formPopupArray(SIMResources::$unidadTiempo,$frm["TipoCongelacion"],'TipoCongelacion',SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),"","")?></div>
								</div>
							</div>
						</div>

						<!-- CONTROL DE ACCESO -->
						<div id="divAcceso">
							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-key green"></i> <? echo ucwords(SIMUtil::get_traduccion('', '', 'controldeacceso', LANGSESSION));?>
								</h3>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteaccesoaotrassedes', LANGSESSION); ?> </label>
									<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AccesoSedes"], 'AccesoSedes', "class='input'"); ?></div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'jornadasenlasquepuedeacceder', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<? 
											$arrOpJor = array("Normal"=>"Normal", "Piscina Tarde"=>"Piscina Tarde");
											$arrValueJor = array();
											if($frm["JornadasAcceso"] != ''){
												$arrValueJor = explode(",", $frm["JornadasAcceso"]);
											}
											
											echo SIMHTML::formCheckGroup2($arrOpJor, $arrValueJor, "JornadasAccesoCh" ,"onclick = funcionCheck('JornadasAcceso')" ,"");
										?>
										<input type='hidden' id='JornadasAcceso' name='JornadasAcceso' value="<?php echo $frm["JornadasAcceso"]; ?>">
									</div>
								</div>
							</div>
						</div>

						<!-- PRECIO DE VENTA -->
						<div id='divPrecio' style="display: none;">
							<div class="widget-header widget-header-large ">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-calendar green"></i> <? echo ucwords(SIMUtil::get_traduccion('', '', 'preciosdeventa', LANGSESSION));?>
								</h3>
							</div>

							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<input type='text' id='FacturacionInicio' name='FacturacionInicio' value="<?php echo $frm["FacturacionInicio"]; ?>" class='calendar' placeholder='<?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION);?>'   $title="<?= SIMUtil::get_traduccion('', '', 'disponibledesde', LANGSESSION);?>">
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<input type='text' id='FacturacionFin' name='FacturacionFin' value="<?php echo $frm["FacturacionFin"]; ?>" class='calendar' placeholder='<?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION);?>'   $title="<?= SIMUtil::get_traduccion('', '', 'disponiblehasta', LANGSESSION);?>">
									</div>
								</div>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'impuesto', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formPopup('Impuestos', 'Nombre', 'Nombre', 'IDImpuestos', $frm["IDImpuestos"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '', "AND Activo = 'S' AND IDClub = $idPadre"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'esuningresodeterceros', LANGSESSION); ?> </label>
									<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["IngresoTerceros"], 'IngresoTerceros', "class='input'"); ?></div>
								</div>
							</div>
							<div class="form-group first" style="display: none;" id="divPrecioCh"></div>
						</div>

						<!-- INFORMACION ADICIONAL -->
						<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-cog green"></i> <? echo ucwords(SIMUtil::get_traduccion('', '', 'configuracionadicional', LANGSESSION));?>
								</h3>
							</div>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeditar', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Editar"], 'Editar', "class='input'"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'permiteeliminar', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Eliminar"], 'Eliminar', "class='input'"); ?>
									</div>
								</div>
							</div>
							<div class="form-group first" style="display: none;" id="divHabilitar"></div>
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
	include("jsProducto.php");
?>
