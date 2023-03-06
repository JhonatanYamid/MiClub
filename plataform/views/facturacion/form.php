<?
	include('estilos.php');
	
	$arrOpciones = $hijos;
	$hoy = date("Y-m-d");
	$tipo = 1;//club tipo sede o club sin sedes

	if($IDClub == $idPadre && !empty($arrOpciones)){
		$tipo = 2;//club tipo padre

		$sqlSede = "SELECT r.IDClub, c.Nombre
					FROM  ResolucionFactura as r, Club as c
					WHERE r.IDClub = c.IDClub AND r.Activo = 'S' AND c.IDClubPadre = $idPadre";

		$qrySede = $dbo->query($sqlSede);
	}
?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<!-- INFORMACION FACTURA -->
						<div class="widget-header widget-header-large no-margin">
							<h4 class="widget-title grey lighter">
								<i class="ace-icon fa fa-file-text green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'informaciondelafactura', LANGSESSION));?>
							</h4>
						</div>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
												while ($rSede = $dbo->fetchArray($qrySede)){   
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
												}
											?>
										</select>
									</div>
								</div>
							<? }else{ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
								</div>
							<? } ?>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'factura', LANGSESSION); ?> No. </label>
								<label class="col-sm-8 tallest" id="consecutivoTxt"></label>
								<input type="hidden" name="Prefijo" id="Prefijo" value="" />
								<input type="hidden" name="Consecutivo" id="Consecutivo" value="" />
								<input type="hidden" name="IDResolucionFactura" id="IDResolucionFactura" value="" />
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Vendedor', LANGSESSION); ?>: </label>
								<div class="col-sm-8" id="vendedores"></div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'fechadefacturacion', LANGSESSION); ?>: </label>
								<label class="col-sm-8 tallest"><?= $hoy?></label>
								<input type="hidden" name="FechaCreacion" id="FechaCreacion" value="<?= $hoy?>" />
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?= $hoy;?>" />
								</div>
							</div>
						</div>
						
						<!-- SOCIO -->
						<div class="widget-header widget-header-large no-margin">
							<h4 class="widget-title grey lighter">
								<i class="ace-icon fa fa-user green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'detalledelcliente', LANGSESSION));?>
							</h4>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'buscarcliente', LANGSESSION); ?>: </label>
								<div class="col-sm-8 limpiar">
									<input type="text" id="Buscar" name="Buscar" placeholder="<?= SIMUtil::get_traduccion('', '', 'buscarcliente', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-cliente" title="<?= SIMUtil::get_traduccion('', '', 'buscarcliente', LANGSESSION); ?>" value="" />
									<input type="hidden" name="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'selecionarcliente', LANGSESSION); ?>"  id="IDSocio" value="<?php echo $frm['IDSocio'] ?>" />
									<input type="hidden" name="Accion" id="Accion" value="<?php echo $frm['Accion'] ?>" />
								</div>
							</div>
						</div>
						<div id="divCliente" style="display:none">
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="documento"></label>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreCliente"></label>
								</div>
							</div>
							<div class="form-group first">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="direccion"></label>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="telefono"></label>
								</div>
							</div>
						</div>
						<!-- PRODUCTOS -->
						<div class="widget-header widget-header-large no-margin">
							<h4 class="widget-title grey lighter">
								<i class="ace-icon fa fa-shopping-cart green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'detalledeproductos', LANGSESSION));?>
							</h4>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12">
								<center> 
									<div id="jqGrid_containerProd">		
										<table id="productosTable"></table>
									</div>
									<div id="productospager"></div>
								</center>
							</div>
							<input type="hidden" id="productos" name="productos" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>"   value="" />
						</div>
						<!-- PAGOS -->
						<div class="widget-header widget-header-large no-margin">
							<h4 class="widget-title grey lighter">
								<i class="ace-icon fa fa-money green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'detalledepagos', LANGSESSION));?>
							</h4>
						</div>
						<div class="form-group first">
							<div class="limpiar left tablaPagos">	
								<table id="tablaPagos" class=" table table-striped table-bordered table-hover" >
									<thead id="headPagos">
										<tr>
											<th style="width:30%"><?= SIMUtil::get_traduccion('', '', 'mediosdepago', LANGSESSION);?></th>
											<th align='center' valign='middle' style="width:25%"><?= SIMUtil::get_traduccion('', '', 'valordepago', LANGSESSION);?></th>
											<th align='center' valign='middle' style="width:30%"><?= SIMUtil::get_traduccion('', '', 'observacion', LANGSESSION);?></th>
											<th align='center' valign='middle' style="width:15%"><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION);?></th>
										</tr>
										<tr>
											<td><? echo SIMHTML::formPopup('MediosPago', 'Nombre', 'IDMediosPago', 'IDMediosPago', '', SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '', "AND Activo = 'S' AND IDClub = $idPadre"); ?></td>
											<td><input type="text" id="ValorPagado" name="ValorPagado" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'valordepago', LANGSESSION); ?>" value="" /></td>
											<td><input type="text" id="Observacion" name="Observacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'observacion', LANGSESSION); ?>" value="" /></td>
											<td align='center' valign='middle'><button onclick="agregarPago()" type='button' class='button_style'><i class='ace-icon fa fa-plus blue'></i></button></td>
										</tr>
									<thead>
									<tbody id="listaPagos"></tbody>
								</table>
								<input type="hidden" id="Pagos" name="Pagos" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Pago', LANGSESSION); ?>"   value="" />
							</div>
							<div class="limpiarNum right tablaTotales">
								<table id='tablaTotales' class='table table-striped table-bordered table-hover'>
									<tr>
										<th colspan="2"><?= strtoupper(SIMUtil::get_traduccion('', '', 'totales', LANGSESSION));?></th>
									</tr>
									<tr>
										<td style="width:40%"> <?= SIMUtil::get_traduccion('', '', 'subtotal', LANGSESSION);?> </td>
										<td style="width:60%" valign='middle'><span id="SubTotalFinal"></span></td>
									</tr>
									<tr>
										<td> <?= SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION);?> </td>
										<td valign='middle'><span id="DescuentoFinal"></span></td>
									</tr>
									<tr>
										<td> <?= SIMUtil::get_traduccion('', '', 'base', LANGSESSION);?> </td>
										<td valign='middle'><span id="BaseFinal"></span></td>
									</tr>
									<tr>
										<td> <?= SIMUtil::get_traduccion('', '', 'impuesto', LANGSESSION);?> </td>
										<td valign='middle'><span id="ImpuestoFinal"></span></td>
									</tr>
									<tr>
										<td><strong> <?= strtoupper(SIMUtil::get_traduccion('', '', 'Total', LANGSESSION));?> </strong></td>
										<td valign='middle'><span id="TotalFinal"></span></td>
									</tr>
									<tr>
										<td><strong> <?= SIMUtil::get_traduccion('', '', 'pagado', LANGSESSION);?> </strong></td>
										<td valign='middle'><span id="TotalPagadoFinal"></span></td>
									</tr>
									<tr>
										<td><strong> <?= SIMUtil::get_traduccion('', '', 'pendiente', LANGSESSION);?> </strong></td>
										<td valign='middle'><span id="ValorPendiente"></span></td>
									</tr>
								</table>
							
								<input type="hidden" name="SubTotal" id="SubTotal" value=0 />
								<input type="hidden" name="Descuento" id="Descuento" value=0 />
								<input type="hidden" name="Impuesto" id="Impuesto" value=0 />
								<input type="hidden" name="Base" id="Base" value=0 />
								<input type="hidden" name="Total" id="Total" value=0 />
								<input type="hidden" name="TotalPagado" id="TotalPagado" value=0 />
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">

								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', 'facturar', LANGSESSION); ?>
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

<!-- MODAL DE SELECCION DE PRODUCTOS -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="LabelProductos" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-lg">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelProductos"><i class="ace-icon fa fa-shopping-cart fa-lg green"></i>  <?= ucwords(SIMUtil::get_traduccion('', '', 'agregarproducto', LANGSESSION));?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<body data-spy="scroll" data-target="#navbar-productos">
					<div id="navbar-productos">
						<div class="row">
							<div class="col-sm-12 form-actions text-center no-margin" style="padding:5px;" id="divBuscarP">
								<label class="col-sm-2 tallest control-label no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'producto', LANGSESSION); ?>: </label>
								<div class="col-sm-9 text-left"><input type="text" id="BuscarProducto" name="BuscarProducto" placeholder="<?= SIMUtil::get_traduccion('', '', 'buscarproducto', LANGSESSION); ?>" class="col-sm-12 autocomplete-ajax-producto" value="" /></div>
								<div class="col-sm-1"></div>
							</div>

							<div id="divContentP" style="display:none">
								<div class="col-sm-12 no-padding no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right blue"><h5 class="txtCont" id="codProducto"></h5></label>
									<label class="col-sm-9 tallest blue"><h5 class="txtCont" id="nmProducto"></h5></label>
									<input type="hidden" name="IDProductoFacturacion" id="IDProductoFacturacion" value="" />
								</div>
								<div class="form-group first col-sm-12 no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'precio', LANGSESSION); ?>:</label>
									<label class="col-sm-4 tallest txtCont precio"></label>
									<input type="hidden" name="Precio" id="Precio" value=0 />
									
									<label class="col-sm-3 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'preciosiniva', LANGSESSION); ?>:</label>
									<label class="col-sm-3 tallest txtCont precioSinIva"></label>
									<input type="hidden" name="PrecioSinIva" id="PrecioSinIva" value=0 />
								</div>
								<div class="form-group first col-sm-12 no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?>:</label>
									<div class="col-sm-4 text-left">
										<input type="text" id="Cantidad" name="Cantidad" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?>" value=1 />
									</div>

									<label class="col-sm-3 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'subtotal', LANGSESSION); ?>:</label>
									<label class="col-sm-3 tallest txtCont subtotal"></label>
									<input type="hidden" name="SubTotalProd" id="SubTotalProd" value=0 />
								</div>
								<div class="form-group first col-sm-12 no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION); ?>:</label>
									<div class="col-sm-4 tallest" id="selDescuentos"></div>

									<label class="col-sm-3 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'valordedescuento', LANGSESSION); ?>:</label>
									<div class="col-sm-3 tallest txtCont descuento"></div>
									<input type="hidden" name="DescuentoProd" id="DescuentoProd" value=0 />
								</div>
								<div class="form-group first col-sm-12 no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'base', LANGSESSION); ?>:</label>
									<label class="col-sm-4 tallest txtCont base"></label>
									<input type="hidden" name="BaseProd" id="BaseProd" value=0 />

									<label class="col-sm-3 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'impuesto', LANGSESSION); ?>:</label>
									<label class="col-sm-3 tallest"><span class ="txtCont" id="impuestotxt"></span> (<span class ="txtCont" id="nmImpuesto"></span>)</label>
									<input type="hidden" name="valImpuesto" id="valImpuesto" value=0 />
									<input type="hidden" name="ImpuestoProd" id="ImpuestoProd" value=0 />
								</div>
								<div class="form-group first col-sm-12 no-margin">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"><strong><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?>:</strong></label>
									<label class="col-sm-4 tallest"><strong class="total txtCont"></strong></label>
									<input type="hidden" name="TotalProd" id="TotalProd" value=0 />
								</div>
								<div class="form-group first col-sm-12 no-margin fechas" style="display:none">
									<label class="col-sm-2 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>:</label>
									<div class="col-sm-4 text-left">
										<input type="text" id="fechaInicio" name="fechaInicio" class="calendar" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaIncio', LANGSESSION); ?>" value="" />
									</div>

									<label class="col-sm-3 tallest control-label text-right no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>:</label>
									<label class="col-sm-3 tallest txtCont fechafin"></label>
									<input type="hidden" name="fechaFin" id="fechaFin" value=0 />
								</div>
							</div>
							<!-- SECCION BENEFICIARIOS -->
							<div class="col-sm-12" style="display:none" id="beneficiarios" >
								<div class="widget-header col-sm-11">
									<h4 class="widget-title lighter smaller">
										<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'beneficiarios', LANGSESSION); ?>
									</h4>
								</div>
								<div class="tallest2 col-sm-12">
									<label class="col-sm-2 tallest control-label no-padding-right text-right"> <?= SIMUtil::get_traduccion('', '', 'Beneficiario', LANGSESSION); ?>: </label>
									<div class="col-sm-9 text-left">
										<input type="text" id="BuscarBeneficiario" name="BuscarBeneficiario" placeholder="<?= SIMUtil::get_traduccion('', '', 'buscarbeneficiario', LANGSESSION); ?>" class="col-sm-12 autocomplete-ajax-beneficiarios" value="" />
									</div>
								</div>
								<div class="p first col-sm-12">
									<table id="simple-table" class="table table-striped table-bordered table-hover">
										<thead id="headBenef"><thead>
										<tbody id="listaBenef"></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</body>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
				<button type="button" class="btn btn-primary" id="agregarProducto"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION);?></button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DE DESCUENTO -->
<div class="modal fade" id="modalDescuento" tabindex="-1" role="dialog" aria-labelledby="LabelDescuento" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-sm">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelDescuento"> <?= ucwords(SIMUtil::get_traduccion('', '', 'descuento', LANGSESSION));?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<div class="container-fluid">
      				<div class="row">
						<div class="col-sm-12 text-center">
							<input type="text" id="valDescuento" name="valDescuento" onkeypress='return (event.charCode >= 48 && event.charCode <= 57)' placeholder="<?= SIMUtil::get_traduccion('', '', 'valordedescuento', LANGSESSION); ?>" value="" />
							<input type="hidden" id="nomDescuento" name="nomDescuento" value="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
				<button type="button" class="btn btn-primary" id="agregarDescuento"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION);?></button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DE PERMISO DE ADMINISTRADOR -->
<div class="modal fade" id="modalPermiso" tabindex="-1" role="dialog" aria-labelledby="LabelPermiso" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-sm">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelPermiso"> <?= ucwords(SIMUtil::get_traduccion('', '', 'autorizaciondedescuento', LANGSESSION));?></h5>
				<button type="button" class="close cerrar" style="line-height:0px !important;" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="text" id="usuario" name="usuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" value="" />
						</div>
						<div class="col-sm-12 text-center">
							<input type="password" id="clave" name="clave" placeholder="Password" value="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary cerrar"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
				<button type="button" class="btn btn-primary" id="autorizar"><?= SIMUtil::get_traduccion('', '', 'autorizar', LANGSESSION);?></button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DE FECHA -->
<div class="modal fade" id="modalFecha" tabindex="-1" role="dialog" aria-labelledby="LabelFecha" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-sm">
			<div class="modal-header">               
				<h5 class="modal-title" id="LabelFecha"> <?= ucwords(SIMUtil::get_traduccion('', '', 'FechaIncio', LANGSESSION));?></h5>
				<button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow:visible;">
				<div class="container-fluid">
      				<div class="row">
						<div class="col-sm-12 text-center">
							<input type="text" id="fechaActiva" name="fechaActiva" class="calendar" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaIncio', LANGSESSION); ?>" value="<?= $hoy;?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= SIMUtil::get_traduccion('', '', 'cerrar', LANGSESSION);?></button>
				<button type="button" class="btn btn-primary" id="editarFecha"><?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION);?></button>
			</div>
		</div>
	</div>
</div>
<?
	include("cmp/footer_scripts.php");
	include('js/general.php');
	include('js/producto.php');
	include('js/beneficiarios.php');
	include('js/descuentos.php');
	include('js/pagos.php');
?>