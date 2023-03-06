
<?
	$id = $_GET["id"];
	$tipo = $_GET["tipo"];//si es tipo 0 se llama al contenedor en la app si es 1 se llama fuera del contenedor para imprimir
	
	if($tipo == 1){
		include( "../../procedures/general_async.php" );
	}

	$dbo =& SIMDB::get();
	
	$productos = array();
	$pagos = array();

	$sql_fac = "SELECT f.IDFacturacion, f.IDClub, f.IDSocio, f.IDResolucionFactura, CONCAT(f.Prefijo,f.Consecutivo) as NumFactura, f.SubTotal,
						f.Descuento, f.Base, f.Impuesto, f.Total, f.FechaCreacion, f.FechaVence, v.Codigo as CodigoVendedor, v.Nombre as Vendedor
					FROM Facturacion as f, VendedorFactura as v
					WHERE f.IDVendedorFactura = v.IDVendedorFactura AND IDFacturacion = ".$id;

	$qry_fac = $dbo->query($sql_fac);
	$factura = $dbo->fetchArray($qry_fac);

	$sql_info = "SELECT Nit, Nombre1, Nombre2, Direccion, Email, Telefono, Logo, IF(Regimen = 1,'Impuesto sobre las ventas - IVA ','No responsable de IVA') as Regimen, Color1, Color2, TextoFinal
					FROM InformacionFactura
					WHERE Activo = 'S' AND IDClub = ".$factura['IDClub'];

	$qry_info = $dbo->query($sql_info);
	$info = $dbo->fetchArray($qry_info);

	$sql_resolucion = "SELECT Numero, Fecha, CONCAT('Aut de ',Prefijo,'-',ValorInicial,' a ',Prefijo,'-',ValorFin) as Rango
					FROM ResolucionFactura
					WHERE IDResolucionFactura = ".$factura['IDResolucionFactura'];

	$qry_resolucion = $dbo->query($sql_resolucion);
	$resolucion = $dbo->fetchArray($qry_resolucion);

	$sql_socio = "SELECT CONCAT(Nombre,' ',Apellido) as Nombre, NumeroDocumento, CorreoElectronico, Telefono, Celular, Direccion
					FROM Socio
					WHERE IDSocio = ".$factura['IDSocio'];

	$qry_socio = $dbo->query($sql_socio);
	$socio = $dbo->fetchArray($qry_socio);

	$sql_productos = "SELECT p.Nombre as Producto, p.Descripcion, fp.Precio, fp.PrecioSinIva, fp.Cantidad, fp.SubTotal, fp.PorcentajeDescuento, 
						fp.Descuento, fp.Impuesto, fp.Base, fp.Total
					FROM FacturacionProducto as fp, ProductoFacturacion as p
					WHERE fp.IDProductoFacturacion = p.IDProductoFacturacion AND IDFacturacion = ".$factura['IDFacturacion'];

	$qry_productos = $dbo->query($sql_productos);
	while($rowP = $dbo->fetchArray($qry_productos)){
		$productos[] = $rowP;
	}

	$sql_pagos = "SELECT m.Nombre as MedioPago, fm.ValorPagado, fm.Observacion
					FROM FacturacionMediosPago as fm, MediosPago as m
					WHERE fm.IDMediosPago = m.IDMediosPago AND IDFacturacion = ".$factura['IDFacturacion'];

	$qry_pagos = $dbo->query($sql_pagos);
	while($rowM = $dbo->fetchArray($qry_pagos)){
		$pagos[] = $rowM;
	}

	$telefono = "";

	if($socio['Telefono'] != '')
		$telefono .= $socio['Telefono'];

	if($socio['Telefono'] != '' && $socio['Celular'] != '')
		$telefono .= " - ";

	if($socio['Celular'] != '');
		$telefono .= $socio['Celular'];

	//incluye los estilos para la factura
	include('estilos.php');
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="widget-box transparent" id="recent-box">
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<div id = "factura">
						<div class="headerFac clearfix">
							<div class="container">
								<figure>
									<img class="logo" src="<?= FACTURA_ROOT.$info['Logo']; ?>" alt="">
								</figure>
								<div class="company-info left">
									<h2 class="title"><?= $info['Nombre1']; ?></h2>
									<span><?= "Nit.".$info['Nit']; ?></span>
									<span class="line"></span>
									<span><?= "REGIMEN - ".strtoupper($info['Regimen']); ?></span>
									<BR>
									<span><?= "RESOLUCIÓN No. ".$resolucion['Numero']." ".$resolucion['Fecha']; ?></span>
									<BR>
									<span><?= $resolucion['Rango']; ?></span>
								</div>
								<div class="company-contact rigth">
									<? if($info['Nombre2'] != '')
										echo "<h3 class='title'>".$info['Nombre2']."</h3>" ?>

									<span><i class="material-icons">location_on</i> <?= $info['Direccion'] ?></span>
									<BR>
									<span><i class="material-icons">alternate_email</i> <?= $info['Email'] ?></span>
									<BR>
									<span><i class="material-icons">phone_in_talk</i> <?= $info['Telefono'] ?></span>

								</div>
							</div>
						</div>

						<div class="section">
							<div class="container">
								<div class = "details clearfix">
									<div class="client left">
										<p class="titleC">CLIENTE:</p>
										<p class="name">
											<?= $socio['Nombre']?><br>
											<?= $socio['NumeroDocumento']?>
										</p>
										<p>
											<?= $socio['Direccion']; ?><br>
											<?= $telefono != '' ? $telefono."<br>" : "" ?>
											<?= $socio['CorreoElectronico']; ?>
										</p>
									</div>
									<div class="data right">
										<div class="title">Factura No. <?= $factura['NumFactura']; ?> </div>
										<div class="date">Fecha de Creación: <?= $factura['FechaCreacion']; ?></div>
										<div class="date">Fecha de Vencimiento: <?= $factura['FechaVence']; ?></div>

									</div>
								</div>
							</div>
							<div class="container">
								<div class="table-wrapper">
									<table>
										<tbody class="head">
											<tr>
												<th class="no"><div>#</div></th>
												<th class="desc"><div>Producto</div></th>
												<th class="qty"><div>Cantidad</div></th>
												<th class="unit"><div>Precio</div></th>
												<th class="unit"><div>SubTotal</div></th>
												<th class="unit"><div>Descuento</div></th>
												<th class="unit"><div>Base</div></th>
												<th class="unit"><div>Impuesto</div></th>
												<th class="total"><div>Total</div></th>
											</tr>
										</tbody>
										<tbody class="body">
											<?
												$i=1;

												foreach($productos as $producto){
													$num = $i < 10 ? "0$i" : $i;
													$precio = number_format($producto['PrecioSinIva'],0,"",".");
													$subtotalProd = number_format($producto['SubTotal'],0,"",".");
													$descuentoProd = number_format($producto['Descuento'],0,"",".");
													$baseProd = number_format($producto['Base'],0,"",".");
													$impuestoProd = number_format($producto['Impuesto'],0,"",".");
													$totalProd = number_format($producto['Total'],0,"",".");

													echo "<tr>
															<td class='no'>$num</td>
															<td class='desc'>".$producto['Producto']."</td>
															<td class='qty'>".$producto['Cantidad']."</td>
															<td class='unit'>$precio</td>
															<td class='unit'>$subtotalProd</td>
															<td class='unit'>$descuentoProd</td>
															<td class='unit'>$baseProd</td>
															<td class='unit'>$impuestoProd</td>
															<td class='total'>$totalProd</td>
														</tr>";

													$i++;
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="container clearfix">
								<div class="payments left">
									<fieldset>
										<legend>PAGOS</legend>
										<table class="payments-list">
											<tbody>
												<?
													$j = 1;
													foreach($pagos as $pago){
														$nm = $j < 10 ? "0$j" : $j;
														echo "<tr>
																<td class='number'>".$nm."</td>
																<td class='method'>".$pago['MedioPago']."</td>
																<td class='payment'>".number_format($pago['ValorPagado'],0,"",".")."</td>
																<td class='note'>".$pago['Observacion']."</td>
															</tr>";

														$j++;
													}
												?>
											</tbody>
										</table>
									</fieldset>
								</div>
								<div class="totals right">
									<table class="grand-total">
										<tbody>
											<tr>
												<td class="empty"></td>
												<td class="value-name">SUBTOTAL:</td>
												<td class="value"><?= number_format($factura['SubTotal'],0,"",".");?></td>
											</tr>
											<tr>
												<td class="empty"></td>
												<td class="value-name">DESCUENTOS:</td>
												<td class="value"><?= number_format($factura['Descuento'],0,"",".");?></td>
											</tr>
											<tr>
												<td class="empty"></td>
												<td class="value-name">BASE:</td>
												<td class="value"><?= number_format($factura['Base'],0,"",".");?></td>
											</tr>
											<tr>
												<td class="empty"></td>
												<td class="value-name">IMPUESTOS:</td>
												<td class="value"><?= number_format($factura['Impuesto'],0,"",".");?></td>
											</tr>
											<tr>
												<td class="empty transparent"></td>
												<td class="grand-total value-name">TOTAL:</td>
												<td class="grand-total value"><?= number_format($factura['Total'],0,"",".");?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="container">
							<div class="company-info left no-margin">
								<span class="seller">VENDEDOR: </span>
								<span><?= strtoupper($factura['CodigoVendedor']); ?></span>
								<span class="line"></span>
								<span><?= strtoupper($factura['Vendedor']); ?></span>
							</div>
						</div>

						<div class="container">
							<div class="footerFac">
								<div class="end"><?= $info['TextoFinal']?></div>
							</div>
						</div>
						
					</div>
					<!-- onclick="printPDF()" -->
					<? if($tipo != 1){ ?>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center .no-print">
								<button class="btn btn-info btnEnviar" type="button" id="btnImprimir">
									<i class="ace-icon fa fa-print bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', 'imprimir', LANGSESSION); ?>
								</button>
							</div>
						</div>
					<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?
	include("cmp/footer_scripts.php");
?>

<script src="assets/js/jquery.print.js"></script>
<script>
	$("#btnImprimir").click(() => {		
		$("#factura").print({
        	globalStyles: true,
        	mediaPrint: false,
        	stylesheet: null,
        	noPrintSelector: ".no-print",
        	iframe: true,
        	append: null,
        	prepend: null,
        	manuallyCopyFormValues: true,
        	deferred: $.Deferred(),
        	timeout: 750,
        	title: null,
        	doctype: '<!doctype html>'
		});
		setTimeout(function(){ 
			parent.jQuery.fancybox.close();	
  		}, 2000);		
	});
</script>
