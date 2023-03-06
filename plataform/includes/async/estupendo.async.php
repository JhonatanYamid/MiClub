<? 
    include( "../../procedures/general_async.php" );

    SIMUtil::cache( "text/json" );
    $dbo =& SIMDB::get();

    $oper = SIMNet::req("oper");

    switch($oper){

        case "crearArchivo":

            $id = SIMNet::req("idFactura");

            //prefijo SETT para pruebas
            $sql_fac = "SELECT f.IDFacturacion, f.IDClub, f.IDSocio, f.IDResolucionFactura, CONCAT('SETT',f.Consecutivo) as Secuencial, f.SubTotal,
                                f.Descuento, f.Base, f.Impuesto,f.Total, f.FechaCreacion, f.HoraCreacion, f.FechaVence, v.Codigo, v.Nombre as Vendedor
                            FROM Facturacion as f, VendedorFactura as v
                            WHERE f.IDVendedorFactura = v.IDVendedorFactura AND IDFacturacion = ".$id;

            $qry_fac = $dbo->query($sql_fac);
            $factura = $dbo->fetchArray($qry_fac);

            $sql_info = "SELECT Nit, MatriculaMercantil, Nombre1, Nombre2, i.IDPaisDian, p.Nombre as Pais, p.Codigo as CodPais,  IF(i.IDDepartamentoDian < 10, CONCAT('0',i.IDDepartamentoDian), i.IDDepartamentoDian) as IDDepartamento, 
                            d.Nombre as Departamento, IF(i.IDDepartamentoDian < 10,CONCAT('0',i.IDCiudadDian),i.IDCiudadDian) as IDCiudad,c.Nombre as Ciudad, CodigoPostal, Direccion, Regimen, TipoOrganizacion
                        FROM InformacionFactura as i, PaisDian as p, DepartamentoDian as d, CiudadDian as c 
                        WHERE
                            i.IDPaisDian = p.IDPaisDian AND i.IDDepartamentoDian = d.IDDepartamentoDian AND i.IDCiudadDian = c.IDCiudadDian AND Activo = 'S' AND IDClub = ".$factura['IDClub'];

            $qry_info = $dbo->query($sql_info);
            $info = $dbo->fetchArray($qry_info);

            $sql_socio = "SELECT CONCAT(s.Nombre,' ',s.Apellido) as Nombre, s.NumeroDocumento, s.TipoDocumento, s.CorreoElectronico, s.Celular, s.Direccion, s.IDPaisDian, p.Nombre as Pais,
                            p.Codigo as CodPais, IF(s.IDDepartamentoDian < 10, CONCAT('0',s.IDDepartamentoDian), s.IDDepartamentoDian) as IDDepartamentoDian, d.Nombre as Departamento, 
                            IF(s.IDDepartamentoDian < 10,CONCAT('0',s.IDCiudadDian),s.IDCiudadDian) as IDCiudad, c.Nombre as Ciudad, s.CodigoPostal
                            FROM Socio as s, PaisDian as p, DepartamentoDian as d, CiudadDian as c
                            WHERE 
                                s.IDPaisDian = p.IDPaisDian AND s.IDDepartamentoDian = d.IDDepartamentoDian AND s.IDCiudadDian = c.IDCiudadDian AND IDSocio = ".$factura['IDSocio'];

            $qry_socio = $dbo->query($sql_socio);
            $socio = $dbo->fetchArray($qry_socio);

            $sql_productos = "SELECT p.Nombre as Producto, p.Descripcion, fp.Precio, fp.PrecioSinIva, fp.Cantidad, fp.SubTotal, fp.PorcentajeDescuento, 
                                fp.Descuento, fp.NombreDescuento, fp.PorcentajeImpuesto, fp.Impuesto, fp.Base, fp.Total
                            FROM FacturacionProducto as fp, ProductoFacturacion as p
                            WHERE fp.IDProductoFacturacion = p.IDProductoFacturacion AND IDFacturacion = ".$factura['IDFacturacion'];

            $qry_productos = $dbo->query($sql_productos);

            while($rowP = $dbo->fetchArray($qry_productos)){
                $productos[] = $rowP;

                $prcIm = $rowP['PorcentajeImpuesto'];
                if (array_key_exists($prcIm, $impuestos)) {
                    $impuestos[$prcIm]['BaseTot'] += $rowP['Base'];
                    $impuestos[$prcIm]['ImpuestoTot'] += $rowP['Impuesto'];
                }else{
                    $impuestos[$prcIm]['BaseTot'] = $rowP['Base'];
                    $impuestos[$prcIm]['ImpuestoTot'] = $rowP['Impuesto'];
                }
            }
            
            $sql_pagos = "SELECT m.Nombre as MedioPago, md.Codigo, fm.ValorPagado, fm.Observacion
                            FROM FacturacionMediosPago as fm, MediosPago as m, MediosPagoDian as md
                            WHERE fm.IDMediosPago = m.IDMediosPago AND m.IDMediosPagoDian = md.IDMediosPagoDian AND IDFacturacion = ".$factura['IDFacturacion'];

            $qry_pagos = $dbo->query($sql_pagos);
            $numPago = 1;
            while($rowM = $dbo->fetchArray($qry_pagos)){
                $rowM['numPago'] = $numPago;
                $pagos[] = $rowM;

                $numPago++;
            }
            
            //INICIA EL ARCHIVO
            $fileName = ESTUPENDO_DIR.$factura['Secuencial'].".txt";
            $secciones = "";
                
            if (file_exists($fileName))
                unlink($fileName);
            
            $file = fopen($fileName, "w");
            //$lineaUtf8 = iconv('UTF-8', 'Windows-1252', $miCadena); // Codificación inicial, Codificación final, Cadena a convertir.
            
            //SECCION 01||Campos que identifican a la factura
            $codigoTipoDocumento = '01';//Factura de venta nacional
            $motivoFactura = '01';//Factura de venta nacional
            $secuencial = trim($factura['Secuencial']);//Prefijo + Consecutivo(9 caracteres)
            $fechaEmision = trim($factura['FechaCreacion']);//Fecha de emision de la factura
            $horaEmision = trim($factura['HoraCreacion']);//Hora de creacion de la factura
            $tipoOperacion = '10';//09-Servicios AIU, 10-Estandar, 11-Mandatos
            $IDReferencia = '';//Prefijo+Consecutivo referenciado(opcional)
            $fechaEmisionOrden = '';//Fecha de emision de la orden(opcional)
            $fechaVencimiento = trim($factura['FechaVence']);//Fecha de vencimiento de la factura(opcional)
            
            $secciones .= "01|".
                    $codigoTipoDocumento."|".
                    $motivoFactura."|".
                    $secuencial."|".
                    $fechaEmision."|".
                    $horaEmision."|".
                    $tipoOperacion."|".
                    $IDReferencia."|".
                    $fechaEmisionOrden."|".
                    $fechaVencimiento."\r\n";

            //SECCION 2||Información del emisor
              
            $nit = explode("-", $info['Nit']);

            $tipoIdEmisor = '31';//Tipo de documento del emisor op 31 = Nit(ver opciones en config.inc.php-$tipoDocEstupendo)
            $idEmisor = trim($nit[0]);//Identificacion del emisor
            $razonSocial = trim($info['Nombre1']);//Razon social del emisor
            $nombreComercial = trim($info['Nombre2']);//Nombre comercial o nombres(separa por;)actualmente solo se coloca 1
            $tipoOrganizacion = trim($info['TipoOrganizacion']);//Tipo de organizacion juridica(personas) 1-Persona Juridica, 2-Persona Natural 
            $codigoDepartamento = "11"; //trim($info['IDDepartamento']);//Codigo del departamento segun tabla de la Dian 
            $nombreDepartamento = "Bogota, D.C.";//trim($info['Departamento']);//Nombre del departamento en donde se encuentra el emisor
            $codigoMunicipio = "11001";//trim($info['IDCiudad']);//Codigo del municipio segun tabla de la Dian 
            $nombreCiudad = "Bogota, D.C.";//trim($info['Ciudad']);//Nombre del ciudad en donde se encuentra el emisor
            $direccion = trim($info['Direccion']);//Direccion del emisor
            $codigoPais = trim($info['CodPais']);//Codigo del Pais usando estandar ISO 3166-1
            $nombrePais = trim($info['Pais']);//Nombre del Pais en donde se encuentra el emisor
            $regimen = $info['Regimen'] == 1 ? '48' : '49';// Tipo de regimen correspondiente al emisor 48(IVA)-49(No Responsable)
            $codigopostal = $info['CodigoPostal'] != '' ? trim($info['CodigoPostal']) : '000000';//Codigo postal del emisor
            $actividadEconomica = '9312';//Codigo de la actividad economica segun lista CIIU(8552-Actividades de clubes deportivos) para ACTIVE BODYTECH
            $obligaciones = 'R-99-PN';//Obligaciones del contribuyente R-99-PN = no aplica
            $detallesTributarios = '01=IVA';//(VERIFICAR)Detalles tributarios del emisor 
            $numeroMatricula = trim($info['MatriculaMercantil']);//Numero de la matricula mercantil(id sucursal/pnto de facturacion)

            $secciones .= "02|".
                $tipoIdEmisor."|".
                $idEmisor."|".
                $razonSocial."|".
                $nombreComercial."|".
                $tipoOrganizacion."|".
                $codigoDepartamento."|".
                $nombreDepartamento."|".
                $codigoMunicipio."|".
                $nombreCiudad."|".
                $direccion."|".
                $codigoPais."|".
                $nombrePais."|".
                $regimen."|".
                $codigopostal."|".
                $actividadEconomica."|".
                $obligaciones."|".
                $detallesTributarios."|".
                $numeroMatricula."\r\n";

            //SECCION 3||Información del receptor(cliente)
            $tipoIdentificacion = trim($tipoDocEstupendo[$socio['TipoDocumento']]);//Tipo de documento del receptor(opciones en config.inc.php)
            $identificacion = trim($socio['NumeroDocumento']);//identificacion del receptor
            $nombreReceptor = trim($socio['Nombre']);//Este campo aplica para razon social y nombre comercial del receptor
            $tipoOrganizaRecept = '2';//Tipo de organizacion juridica 2 = Persona Natural //VERIFICAR
            $codigoDpto = trim($socio['IDDepartamentoDian']);//Codigo de departamento del receptor
            $nmDepto = trim($socio['Departamento']);//departamento del receptor
            $codMncpio = trim($socio['IDCiudad']);//Codigo de la Ciudad del receptor
            $nmMncpio = trim($socio['Ciudad']);//Nombre de la Ciudad del receptor
            $dirReceptor = trim($socio['Direccion']);//Direccion del receptor
            $codPais = trim($socio['CodPais']);//Codigo del pais del receptor
            $nmPais = trim(strtoupper($socio['Pais']));//Nombre del pais del receptor
            $regimenReceptor = '49';//Tipo de regimen del receptor, 49 = no responsable de Iva 
            $emailReceptor = trim($socio['CorreoElectronico']);//Email del receptor
            $telefono = trim($socio['Celular']);//Telefono celular del receptor
            $codigoPostal = $socio['CodigoPostal'] != '' ? trim($socio['CodigoPostal']) : '000000';//Codigo Postal del receptor
            $obligaReceptor = "R-99-PN";//Obligaciones del contribuyente, R-99-PN = No aplica
            $detReceptor = "ZZ=No aplica";//Detalles tributarios del receptor 
            $idAutorizado = "";//ID de la persona autorizada para descargar documentos(opcional)
            $tipoIdAutorizado = "";//Tipo de identificacion del autorizado para descargar(opcional)

            $secciones .= "03|".
                $tipoIdentificacion."|".
                $identificacion."|".
                $nombreReceptor."|".
                $nombreReceptor."|".
                $tipoOrganizaRecept."|".
                $codigoDpto."|".
                $nmDepto."|".
                $codMncpio."|".
                $nmMncpio."|".
                $dirReceptor."|".
                $codPais."|".
                $nmPais."|".
                $regimenReceptor."|".
                $emailReceptor."|".
                $telefono."|".
                $codigoPostal."|".
                $obligaReceptor."|".
                $detReceptor."|".
                $idAutorizado."|".
                $tipoIdAutorizado."\r\n";

            //SECCION 4||Cargos o Descuentos Totales(Opcional)
            //SECCION 5||Para Valores de Retención Totales(Opcional)
            ksort($impuestos);
            foreach($impuestos as $key => $values){
                
                //SECCION 10||Impuestos totales(se agrupan por porcentaje de impuesto y se suman los totales por cada uno)
                $codigoImpuesto = '01';//Codigo del impuesto aplicado a la factura 01=IVA
                $valorSinImpuesto = trim($values['BaseTot']);//Valor del subtotal sin impuesto.
                $porcentajeImpuesto = trim($key);//Porcentaje del impuesto aplicado
                $totalImpuesto = trim($values['ImpuestoTot']);//Total del impuesto aplicado
                $unidadMedida = '';//Unidad de medida base para el tributo
                $codigoUnidad = '';//Identificacion de la unidad de medida
                $valorUnitario = '0.00';//El valor nominal del tributo por unidad

                $secciones .= "10|".
                    $codigoImpuesto."|".
                    $valorSinImpuesto."|".
                    $porcentajeImpuesto."|".
                    $totalImpuesto."|".
                    $unidadMedida."|".
                    $codigoUnidad."|".
                    $valorUnitario."\r\n";
            }
            
            //SECCION 11||TOTALES
            $subtotalFactura = trim($factura['Base']);//Valor total de la factura sin impuestos
            $totalBaseImponible = trim($factura['Base']);//Total valor base imponible: base imponible para el calculo de los tributos
            $totalMasImpuestos = trim($factura['Total']);//Total del valor bruto mas tributos
            $totalFactura = trim($factura['Total']);//Total de la factura
            $descuento = "0";//Valor total del descuento aplicado a la factura
            $cargos = '0.00';//Suma de todos los cargos aplicados a nivel de la factura
            $anticipos = '0.00';//Suma de todos los pagos anticipados
            $moneda = 'COP';//Codigo de la moneda usado en el estandar ISO 4217

            $secciones .= "11|".
                $subtotalFactura."|".
                $totalBaseImponible."|".
                $totalMasImpuestos."|".
                $totalFactura."|".
                $descuento."|".
                $cargos."|".
                $anticipos."|".
                $moneda."\r\n";

            $i = 1;
            foreach($productos as $producto){

                $numItem = $i;//Secuencial numerico del item 
                
                //SECCION 12||DETALLE DE ITEMS
                $codigoPrincipal = trim($factura['Codigo']);//Codigo del vendedor correspondiente al artículo
                $codigoAuxiliar = "";//Codigo auxiliar del vendedor correspondiente al articulo(Opcional)
                $descripcion = trim($producto['Producto']);//Descripcion del producto
                $cantidad = trim($producto['Cantidad']);//Cantidad de producto
                $cantidadUnidad = "1";//Cantidad de unidad del articulo por empaque
                $codigoUnidadMedida = "EA";//Identificacion de la unidad de medida NIU-número de unidades internacionales
                $precioUnitario = trim($producto['PrecioSinIva']);//Precio unitario del producto
                $tipoPrecio = "01";//Codigo del tipo de precio informado 01-Valor Comercial
                $valorTotalItem = trim($producto['Base']);//Cantidad de producto por el precio unitario 
                $descripcionAdicional = trim($producto['Descripcion']);//Descripcion adicional del producto(Opcional)
                $descuentoItem = trim($producto['Descuento']);//Descuento aplicado al item(Opcional)
                $valorNoComercial = '0.00';//Valor del precio referencia del item que se da como muestra o regalo sin valor comercial(Obligatorio si se trata de muestras comerciales)
                $marca = "";//Marca del articulo (Obligatorio si la factura es internacional)
                $modelo = "";//Modelo del producto (Obligatorio si la factura es internacional)

                $secciones .= "12|".
                    $numItem."|".
                    $codigoPrincipal."|".
                    $codigoAuxiliar."|".
                    $descripcion."|".
                    $cantidad."|".
                    $cantidadUnidad."|".
                    $codigoUnidadMedida."|".
                    $precioUnitario."|".
                    $tipoPrecio."|".
                    $valorTotalItem."|".
                    $descripcionAdicional."|".
                    $descuentoItem."|".
                    $valorNoComercial."|".
                    $marca."|".
                    $modelo."\r\n";

                //SECCION 13||Impuestos de los detalles
                $idTributo = "01";//Identificador del tributo 01-IVA
                $valorImpuesto = trim($producto['Impuesto']);//Valor del impuesto
                $prcntjImpuesto = trim($producto['PorcentajeImpuesto']);//Porcentaje del IVA aplicado al item
                $valorBase = trim($producto['Base']);//Base Imponible sobre la que se calcula el valor del tributo
                $unidadMedidaBase = "";//Unidad de medida base para el tributo
                $idMedida = "";//Identificacion de la unidad de medida
                $valorCantidadxUnidad = "";//Valor del tributo por unidad

                $secciones .= "13|".
                    $numItem."|".
                    $idTributo."|".
                    $valorImpuesto."|".
                    $prcntjImpuesto."|".
                    $valorBase."|".
                    $unidadMedidaBase."|".
                    $idMedida."|".
                    $valorCantidadxUnidad."\r\n";

                    if($producto['Descuento'] > 0){
                        //SECCION 14||Cargos o Descuentos de los detalles
                        $tipoElemento = "False";//Indica que el elemento es un cargo(true) o descuento(false)
                        $razonCargo = trim($producto['NombreDescuento']);//Descripcion del descuento
                        $porcentajeDescuento = trim($producto['PorcentajeDescuento']);//Porcentaje aplicado en decimales
                        $ValorTotal = trim($producto['Descuento']);//Valor total del descuento
                        $ValorBase = trim($producto['SubTotal']);//Valor base para calcular el descuento

                        $secciones .= "14|".
                            $numItem."|".
                            $numItem."|".
                            $tipoElemento."|".
                            $razonCargo."|".
                            $porcentajeDescuento."|".
                            $ValorTotal."|".
                            $ValorBase."\r\n";
                    }

                $i++;
            }

            //SECCION 15||Valores de retencion por detalles(Opcional)
            //SECCION 16||Grupo de campos para informaciones relacionadas con la tasa de cambio de moneda extranjera a peso colombiano (COP)(Opcional)
            //SECCION 17||Grupo de información exclusivo para referenciar la NC o ND que dio origen a la presente Factura Electrónica(Opcional)
            //SECCION 18||Información para entrega de bienes(Opcional)
            $p = 1;
            foreach($pagos as $pago){
                //SECCION 19||Pagos de la factura
                $metodoPago = "1";//Metodos de pago 1-Contado, 2-Credito
                $medioPago = trim($pago['Codigo']);//Codigo correspondiente al medio de pago
                $idPago = $p;//Identificador del pago(opcional)

                $secciones .= "19|".
                    $metodoPago."|".
                    $medioPago."|".
                    $fechaVencimiento."|".
                    $idPago."\r\n";

                $p++;
            }

            //SECCION 40||Campos Adicionales(opcional).Estos valores no aparecen en la factura normalmente
            foreach($pagos as $pago){

                $nmMedioPago = $pago['MedioPago'];//Nombre del medio de pago
                $valPago = $pago['ValorPagado'];//Valor pagado
                                
                $secciones .= "40|".
                    $nmMedioPago."|".
                    $valPago."\r\n";
            }
            $nmEtiqueta = "Vendedor";//Etiqueta adicional
            $nmVendedor = $factura['Vendedor'];//Nombre del vendedor
            
            $secciones .= "40|".
                $nmEtiqueta."|".
                $nmVendedor."\r\n";

            //SECCION 20||Anticipos de la factura(opcional)
            //SECCION 21||Información que describen uno o más documentos de despacho para este documento(opcional)
            //SECCION 22||Información que describen uno o más documentos recibidos para este documento.(opcional)
            //SECCION 23||Documentos Adicionales(opcional)
            

            // fwrite($file, utf8_encode($secciones));
            fwrite($file,$secciones);
            fclose($file);

            $dbo->update(array("TxtEstupendo" => $factura['Secuencial'].".txt"), 'Facturacion', 'IDFacturacion', $id);

            $archivo = base64_encode($secciones);

            $responce['archivo'] = $archivo;
            $responce['nombre'] = $factura['Secuencial'];

            echo json_encode($responce); 
        break;
    }
?>
