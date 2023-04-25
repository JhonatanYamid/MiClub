<?php

class SIMWebServiceDomiciliosBTC
{
    public function enviar_domicilio($IDDomicilio, $Version)
    {
        $dbo = SIMDB::get();
        $ENCFechaTrx = date("Y/m/d");
        $ENCHoraTrx = date("H:i:s");

        $DatosDomicilio = $dbo->fetchAll("Domicilio$Version", "IDDomicilio = $IDDomicilio");
        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $DatosDomicilio[IDSocio]");
        $Cedula = $DatosSocio[NumeroDocumento];
        $Nombre = $DatosSocio[Nombre];
        $Apellido = $DatosSocio[Apellido];
        $Celular = $DatosSocio[Celular];

        if (empty($Celular)) :
            $Celular = $DatosDomicilio[Celular];
        endif;

        $Direccion = $DatosSocio[Direccion];
        $CorreoElectronico = $DatosSocio[CorreoElectronico];

        $ENCConsTrx = $DatosDomicilio[Numero];
        $Comentarios = $DatosDomicilio[ComentariosSocio];

        $datos_restaurante = $dbo->fetchAll("RestauranteDomicilio$Version", "IDRestauranteDomicilio = $DatosDomicilio[IDRestauranteDomicilio]");
        $ALMCodigo = $datos_restaurante['IDRestauranteDomicilioExterno'];
        $ALMNombre = $datos_restaurante['Nombre'];

        if ($ALMCodigo == '' || empty($ALMCodigo)) :
            $ALMCodigo = 'A03';
        endif;

        if (empty($ALMNombre)) :
            $ALMNombre = 'ADMINISTRACION';
        endif;

        // ARMAMOS LOS PRODUCTOS
        $SQLDetalleDomicilio = "SELECT * FROM DomicilioDetalle$Version WHERE IDDomicilio = $IDDomicilio";
        $QRYDetalleDomicilio = $dbo->query($SQLDetalleDomicilio);

        $nitem = 12;
        while ($Detalle = $dbo->fetchArray($QRYDetalleDomicilio)) :

            $datos_producto = $dbo->fetchAll("Producto$Version", "IDProducto = $Detalle[IDProducto]");
            $REFCodigo1 = $datos_producto[IDProductoExterno];
            $REFNombreLargo = $datos_producto[Nombre];
            $REFPrecioLista = $Detalle[ValorUnitario];
            $IRFBruto = $Detalle[Total];
            $IRFPago = $IRFBruto;
            $IRFCantidad = $Detalle[Cantidad];

            if ($REFCodigo1 == 0) :
                $REFCodigo1 = 'ARC010';
            endif;

            $SQLInsumos = "SELECT * FROM  DomicilioCaracteristica WHERE IDDomicilio = $IDDomicilio AND IDProducto = $Detalle[IDProducto]";
            $QRYInsumos = $dbo->query($SQLInsumos);

            $Insumos = "";

            $nitemInsumo = 1;
            while ($Insumo = $dbo->fetchArray($QRYInsumos)) :

                $datos_caracteristica = $dbo->fetchAll("CaracteristicaProducto", "IDCaracteristicaProducto = $Insumo[IDCaracteristicaProducto]");
                $REFCodigo1Insumo = $datos_caracteristica[CodigoCaracteristicaExt];
                $REFNombreLargoInsumo = $datos_caracteristica[Nombre];
                $Insumos .= "
                        <INSUMOS>
                            <item nitem=\"" . $nitemInsumo . "\" Tipo=\"Referencia\" Tiporef=\"insumo\" Visible=\"True\" EsVariable=\"True\">

                                <REFCodClasificacion>00001000060000100250</REFCodClasificacion>

                                <REFCodigo1>" . $REFCodigo1Insumo . "</REFCodigo1>

                                <REFNombreLargo>" . $REFNombreLargoInsumo . "</REFNombreLargo>

                                <CARCodigo1>352</CARCodigo1>
                                <IRFCantidad>1</IRFCantidad>
                                <Estado>ACTIVO</Estado>
                                <PRVCodigo/>
                                <REFInventario>true</REFInventario>
                                <REFEsParaVenta>false</REFEsParaVenta>
                                <REFFactorConversion>1</REFFactorConversion>
                                <IRFVenta>0</IRFVenta>
                                <IRFBruto>0</IRFBruto>
                                <IRFValorImpuesto>0</IRFValorImpuesto>
                                <IRFNeto>0</IRFNeto>
                            </item>
                        </INSUMOS>";

                $nitemInsumo++;
            endwhile;


            $XML .= "
                        <item Visible=\"True\" Tipo=\"Referencia\" nitem=\"" . $nitem . "\" Tiporef=\"normal\">
                            <REFCodClasificacion>00001000060000100250</REFCodClasificacion>
                            <REFCodigo1>" . $REFCodigo1 . "</REFCodigo1><!--Codigo 1 de referencia-->
                            <REFNombreLargo>" . $REFNombreLargo . "</REFNombreLargo><!--Dato fijo-->                           

                            <REFPrecioLista>" . $REFPrecioLista . "</REFPrecioLista><!--Precio unitario de referencia-->
                            <IRFBruto>" . $IRFBruto . "</IRFBruto><!--Precio unitario * cantidad-->
                            <IRFDescuento>0</IRFDescuento><!--Dato fijo-->
                            <IRFPago>" . $IRFPago . "</IRFPago><!--Precio unitario * cantidad-->
                            <IRFCantidad>" . $IRFCantidad . "</IRFCantidad><!--Cantidad de referencia-->

                            <IRFValorImpuesto>926</IRFValorImpuesto><!--Valor de impuesto de la referncia-->                            
                            <IRFImpuesto>8</IRFImpuesto><!--Porcentaje de impuesto de la referencia enviada--> 

                            <REFEsCombo>0</REFEsCombo><!--Dato fijo-->
                            <REFUltimoCosto>0</REFUltimoCosto><!--Dato fijo-->
                            <PRVCodigo/><!--Dato fijo-->
                            <REFCapturaSerial>false</REFCapturaSerial><!--Dato fijo-->
                            <REFManejaLotes>false</REFManejaLotes><!--Dato fijo-->
                            <REFFactorConversion>1</REFFactorConversion><!--Dato fijo-->
                            <REFInventario>true</REFInventario><!--Dato fijo-->
                            <REFEsParaVenta>true</REFEsParaVenta><!--Dato fijo-->
                            <estado>ACTIVO</estado><!--Dato fijo-->
                            <IRFPagNoVenta>0</IRFPagNoVenta><!--Dato fijo-->
                            <IRFVenta>0</IRFVenta><!--Precio unitario * cantidad-->
                            <IRFValorImpuestoNeto>0</IRFValorImpuestoNeto><!--Dato fijo-->
                            <IRFComision>0</IRFComision><!--Dato fijo-->
                            <IRFImpuestoAsumido>0</IRFImpuestoAsumido><!--Valor impuesto referencia-->
                            <IRFNeto>0</IRFNeto><!--Precio unitario * cantidad-->
                            <REFPuntos>0</REFPuntos><!--Dato fijo-->

                            <CAPTURAS>
                                <item Tipo=\"LetraDetalle\" Imprime=\"false\">
                                    <ICPPresentacion>UNIDAD - UNIDAD</ICPPresentacion>
                                    <ICPDescripcion>UNIDAD - UNIDAD</ICPDescripcion>
                                    <ICPCadena>UND</ICPCadena>
                                    <ICPLetra>PRES</ICPLetra>
                                    <ICPValorNumerico>1</ICPValorNumerico>
                                </item>
                            </CAPTURAS>   
                            
                            " . $Insumos . "

                        </item>";

            $nitem++;
        endwhile;

        echo $POST = "   
            <transaccion MovCerrado=\"true\" Recalcular=\"true\">   <!--Dato fijo-->
                <USUARIO>624154454F2912704B06435E06425001703E0BE3AFBC</USUARIO> <!--Dato fijo-->
                <CLAVE>624154454F2912704B06435E06425001706657A2F2</CLAVE>   <!--Dato fijo-->
                <encabezado>
                    <ENCDescripcion>Pedido Web</ENCDescripcion> <!--Dato fijo-->
                    <GMVCodigo>ADM</GMVCodigo><!--Dato fijo-->
                    <MOVCodigo>PEDWEB</MOVCodigo><!--Dato fijo-->
                    <movimiento>Standar</movimiento><!--Dato fijo-->
                    <USUCodigo>Admin</USUCodigo><!--Usuario Fijo definido por BTCC-->
                    <CAJCodigo>A0401</CAJCodigo><!--codigo de caja indicado por BTCC-->
                    <IDEMP>BTCC</IDEMP> <!--Dato fijo-->

                    <ALMCodigo>" . $ALMCodigo . "</ALMCodigo><!--Codigo de almacen segun codigo de caja ingresada en la captura CAJCodigo-->
                    <ALMNombre>" . $ALMNombre . "</ALMNombre><!--Nombre de almacen segun codigo de almacen ingresada en la captura ALMCcodigo-->
                    
                    <MONCodigo>1</MONCodigo>    <!--Dato fijo-->
                    <ENCFechaTrx>" . $ENCFechaTrx . "</ENCFechaTrx> <!--Fecha de envio de la transaccion con formato AAAA/MM/DD-->
                    <ENCHoraTrx>" . $ENCHoraTrx . "</ENCHoraTrx>    <!--Hora de envio de transaccion-->
                    <ENCModo>L-C</ENCModo>  <!--Dato fijo-->
                    <ENCTipoProc>Standar</ENCTipoProc>  <!--Dato fijo-->
                    <ENCConsTrx>" . $ENCConsTrx . "</ENCConsTrx>    <!--Consecutivo que va a quedar identificando la transaccion-->
                    <ENCTasaConversion>1</ENCTasaConversion>    <!--Dato fijo-->        

                    <ENCTotalReferencias>1</ENCTotalReferencias><!--Suma del total de unidades enviadas en la transaccion (Suma IRFCantidad de cada una de las referencias ingresadas)-->
                    
                    <ENCBruto>0</ENCBruto>  <!--Dato fijo-->
                    <ENCDescuento>0</ENCDescuento>  <!--Dato fijo-->
                    <ENCPagNoVenta>0</ENCPagNoVenta>    <!--Dato fijo-->
                    <ENCVenta>0</ENCVenta>  <!--Dato fijo-->
                    <ENCImpuestos>0</ENCImpuestos>  <!--Dato fijo-->
                    <ENCComision>0</ENCComision>    <!--Dato fijo-->
                    <ENCNeto>0</ENCNeto>    <!--Dato fijo-->
                    <ENCRecaudo>0</ENCRecaudo>  <!--Dato fijo-->
                    <ENCImpuestoAsumido>0</ENCImpuestoAsumido>  <!--Dato fijo-->
                    <ENCPuntos>0</ENCPuntos>    <!--Dato fijo-->
                    <ENCEstadoLinea>L</ENCEstadoLinea>  <!--Dato fijo-->
                    <ENCRespuesta>OK</ENCRespuesta> <!--Dato fijo-->
                    <ENCDescRespuesta>NO APLICA</ENCDescRespuesta>  <!--Dato fijo-->
                </encabezado>                
                
                <detalle>
                    <items>
                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"1\">
                            <ICPPresentacion>" . $ENCConsTrx . "</ICPPresentacion><!--Numero de Pedido web-->
                            <ICPDescripcion>Numero de Pedido Web</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $ENCConsTrx . "</ICPCadena><!--Numero de Pedido web-->
                            <ICPLetra>NUMPEDW</ICPLetra><!--Dato fijo-->                                
                        </item>                        

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"2\">
                            <ICPPresentacion>" . $Cedula . "</ICPPresentacion><!--Numero de Cedula Cliente-->
                            <ICPDescripcion>Identificación</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $Cedula . "</ICPCadena><!--Numero de Cedula Cliente-->
                            <ICPLetra>CLI</ICPLetra><!--Dato fijo-->
                        </item>                        

                        <item Tipo=\"Letra\" Visible=\"True\" Imprime=\"True\" nitem=\"3\">
                            <ICPPresentacion>CÉDULA DE CIUDADANÍA</ICPPresentacion>
                            <ICPDescripcion>Tipo Documento FE</ICPDescripcion>
                            <ICPCadena>CC</ICPCadena>
                            <ICPLetra>TDPJFE</ICPLetra>
                        </item>
                        
                        <item Tipo=\"Letra\" Visible=\"True\" Imprime=\"True\" nitem=\"4\">
                            <ICPPresentacion>" . $Nombre . "</ICPPresentacion><!--Nombre y apellido del cliente-->
                            <ICPDescripcion>Nombres</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $Nombre . "</ICPCadena><!--Nombre y apellido del cliente-->
                            <ICPLetra>NOM</ICPLetra><!--Dato fijo-->
                        </item>

                        <item Tipo=\"Letra\" Visible=\"True\" Imprime=\"True\" nitem=\"5\">
                            <ICPPresentacion>" . $Apellido . "</ICPPresentacion>
                            <ICPDescripcion>Apellidos</ICPDescripcion>
                            <ICPCadena>" . $Apellido . "</ICPCadena>
                            <ICPLetra>APE</ICPLetra>
                        </item>

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"6\">
                            <ICPPresentacion>" . $Direccion . "</ICPPresentacion><!--Direccion del cliente-->
                            <ICPDescripcion>Dirección</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $Direccion . "</ICPCadena><!--Direccion del cliente-->
                            <ICPLetra>DIR</ICPLetra><!--Dato fijo-->
                        </item> 

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"7\">
                            <ICPPresentacion>" . $Celular . "</ICPPresentacion><!--Numero de Celular del cliente-->
                            <ICPDescripcion>Telefono</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $Celular . "</ICPCadena><!--Numero de celular del cliente-->
                            <ICPLetra>TEL</ICPLetra><!--Dato fijo-->
                        </item>

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"8\">
                            <ICPPresentacion>" . $CorreoElectronico . "</ICPPresentacion><!--Correo electronico de cliente-->
                            <ICPDescripcion>Correo Electronico</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $CorreoElectronico . "</ICPCadena><!--Correo electronico del cliente-->
                            <ICPLetra>MAIL</ICPLetra><!--Dato fijo-->
                        </item>

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"9\">
                            <ICPPresentacion>" . $Comentarios . "</ICPPresentacion><!--Observacion ingresada por el cliente-->
                            <ICPDescripcion>Observacion</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . $Comentarios . "</ICPCadena><!--Observacion ingresada por el cliente-->
                            <ICPLetra>OBS</ICPLetra><!--Dato fijo-->
                        </item>

                        <item Imprime=\"True\" Visible=\"True\" Tipo=\"Letra\" nitem=\"10\">
                            <ICPPresentacion>109 Apps</ICPPresentacion><!--Nombre del Vendedor definido por BTCC-->
                            <ICPDescripcion>Vendedor</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>pdprgalvis</ICPCadena><!--Codigo del Vendedor definido por BTCC (Debe existir el usuario en COMERSSIA)-->
                            <ICPLetra>VEN</ICPLetra><!--Dato fijo-->
                        </item>

                        <item Tipo=\"Letra\" Visible=\"True\" Imprime=\"False\" nitem=\"11\">
                            <ICPPresentacion>" . $ALMNombre . "</ICPPresentacion>
                            <ICPDescripcion>Almacen</ICPDescripcion>
                            <ICPCadena>" . $ALMCodigo . "</ICPCadena>
                            <ICPLetra>ALMD</ICPLetra>
                        </item>

                        " . $XML . "

                        <item Imprime=\"false\" Tipo=\"Letra\" nitem=\"" . $nitem . "\">
                            <ICPPresentacion>" . date('Y-m-d H:i:s') . "</ICPPresentacion><!--Fecha y hora de transaccion-->
                            <ICPDescripcion>Fecha Trabajo</ICPDescripcion><!--Dato fijo-->
                            <ICPCadena>" . date('Y-m-d H:i:s') . "</ICPCadena><!--Fecha y hora de transaccion-->
                            <ICPLetra>PDPFA</ICPLetra><!--Dato fijo-->
                        </item>

                    </items>
                </detalle>               

            </transaccion>";

        // echo $POST;
        // exit;


        $url = 'HTTP://AUDITORIA.COMERSSIA.COM/PDPINTEGRACION/WSINTEGRACION.ASMX?WSDL';
        $options = array(
            "soap_version" => SOAP_1_2,
            "cache_wsdl" => WSDL_CACHE_NONE,
            "exceptions" => false
        );

        $client = new SoapClient($url, $options);

        $xmlr = new SimpleXMLElement($POST);

        $xml = $xmlr->asXML();

        $file = fopen("archivo.xml", "w");
        fwrite($file, $xml . PHP_EOL);
        fclose($file);

        $zip = new ZipArchive();
        $filename = "./archivo.zip";

        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }

        $zip->addFile("archivo.xml");
        $zip->close();

        $byte_array = file_get_contents($filename);

        $ap_param['pi_sIdemp'] = 'BTCC';
        $ap_param['pi_sEnvio'] = $byte_array;

        $result = $client->wm_EnvioTransacciones($ap_param);

        $archivo = (array) $result;
        $resp = $archivo['wm_EnvioTransaccionesResult'];
        $resp2 = (array) $resp;
        $strxml = $resp2['any'];
        $xmlres = new SimpleXMLElement($strxml);

        var_dump($xmlres);
        die();
    }
}
