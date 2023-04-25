<?php

class SIMWebServicePradera
{
    public function facturacion_potosi($datos)
    {
        $dbo = &SIMDB::get();

        if ($datos[IDSocioBeneficiario] != 0) {
            $$datos[IDSocio] = $datos[IDSocioBeneficiario];
        }

        echo $sqlSocio = "SELECT Accion,AccionPadre, NumeroDocumento, IDEstadoSocio FROM Socio WHERE IDSocio = $datos[IDSocio]";
        $qrySocio = $dbo->query($sqlSocio);
        $datos_socio = $dbo->fetchArray($qrySocio);
        echo "<br>";
        echo "<br>";

        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas";
            exit;
        }

        echo $sqlDatos = "SELECT * FROM intrfse_app WHERE IDServicio = $datos[IDServicio] AND (IDServicioTipoReserva = $datos[IDServicioTipoReserva] OR IDServicioTipoReserva = $datos[IDServicioElemento])";
        echo "<br>";
        echo "<br>";

        $qryDatos = $dbh->query($sqlDatos);
        $Datos = $qryDatos->fetch();

        $nmro_almcen = $Datos[IDAlmacen];
        $cdgo_plu = $Datos[Items];
        $precio = $Datos[Precio];

        if (!empty($nmro_almcen)) :
            echo $sqlRegistro = "SELECT MAX(nmro_rgstro) AS num FROM vntas_pos WHERE  nmro_almcen = $nmro_almcen";
            $qryRegistro = $dbh->query($sqlRegistro);
            $row = $qryRegistro->fetch();
            echo "<br>";

            $nmro_rgstro = $row[num] + 1;

            if (empty($datos_socio[AccionPadre])) {
                $datos_socio[AccionPadre] = $datos_socio[Accion];
            }

            if ($datos_socio[IDEstadoSocio] == 5) :
                $AlertaCartera = "S";
            else :
                $AlertaCartera = "N";
            endif;

            $fecha = date("Ymd", strtotime($datos[Fecha]));

            echo $sqlVentas = "INSERT INTO vntas_pos
                    (
                        nmro_almcen, nmro_rgstro,
                        fcha_rgstro, clse_scio,
                        nmro_accion, nmro_idntfccion,
                        nmbre_scio, vlor_csto,
                        vlor_iva, vlor_prpna,
                        vlor_mlta, alrta_crtra,
                        nmro_msa, nmro_evnto,
                        nmro_rsrva, nmro_caja,
                        mtvo_anlcion, nmro_msro,
                        crre_cja, clse_vnta,
                        cdgo_usrio, fcha_aprtra,
                        usrio_frma_pgo, crre_frma_pgo,
                        vlor_sldo,  fcha_vncmnto,
                        vlor_rcrgo, fcha_rcrgo,
                        cdgo_estdo, intrfse
                    )
                    VALUES
                    (
                        $nmro_almcen, $nmro_rgstro,
                        '$fecha', 1,
                        '$datos_socio[AccionPadre]', $datos_socio[NumeroDocumento],
                        '$datos[NombreSocio]', 0,
                        0, 0,
                        0, '$AlertaCartera',
                        0, 0, '$datos[IDReservaGeneral]', 1,
                        0, 1,
                        0, 1,
                        1, '$fecha $datos[Hora]',
                        0, 0,
                        0, '$fecha $datos[Hora]',
                        0, '$fecha $datos[Hora]',
                        'A', 'N'
                    )";

            $dbh->query($sqlVentas);
            echo "<br>";
            echo "<br>";

            echo $sqlDetalle = "INSERT INTO dtlle_vntas_pos
                    (
                        nmro_almcen,nmro_rgstro,
                        nmro_cnsctvo,cdgo_plu,
                        cntdad,vlor_untrio,
                        vlor_ttal,vlor_iva,
                        vlor_csto,aplca_prcso,
                        almcen_prcso,orden_prcso,
                        acmpmnto1,acmpmnto2,
                        acmpmnto3,nmro_psto,
                        nmro_msro,nmro_cmnda,
                        fcha_slctud,fcha_srvcio,
                        cdgo_estdo,entrda
                    )
                    VALUES
                    (
                        " . $nmro_almcen . "," . $nmro_rgstro . ",
                        1," . $cdgo_plu . ",
                        1," . $precio . ",
                        " . $precio . ",0,
                        0,'N',
                        " . $nmro_almcen . ",0,
                        0,0,
                        0,1,
                        1,0,
                        '" . $fecha . " " . $datos[Hora] . "',
                        '" . $fecha . " " . $datos[Hora] . "',
                        'S','N'
                    )";

            $dbh->query($sqlDetalle);
            echo "<br>";
            echo "<br>";


            if (($datos[MedioPago] == "CREDIBANCO V API" && $datos[CodigoRespuesta] == 2) || $datos[IDTipoPago] == 3) :

                // SI LA RESERVA ESTA PAGADA CON CREDIBANCO SE ACTUALIZA EL NUMERO DE FACUTRA Y SE INGRESA EN SPLIT_PAGOS

                echo $MaxFactura = "SELECT cnsctvo_dian as NumeroFactura FROM almcnes WHERE nmro_almcen = $nmro_almcen";
                $qryMaxFactura = $dbh->query($MaxFactura);
                $row = $qryMaxFactura->fetch();
                echo "<br>";
                echo "<br>";

                $NumeroFactura = $row[NumeroFactura] + 1;

                echo $sqlDatos = "UPDATE vntas_pos SET vlor_vnta = $datos[ValorPagado], cdgo_estdo = 'C', nmro_fctra = $NumeroFactura WHERE nmro_rsrva = $datos[IDReservaGeneral]";
                $qryDatos = $dbh->query($sqlDatos);
                echo "<br>";
                echo "<br>";

                echo $sqlDatos = "UPDATE almcnes SET cnsctvo_dian = $NumeroFactura WHERE nmro_almcen = $nmro_almcen";
                $qryDatos = $dbh->query($sqlDatos);
                echo "<br>";
                echo "<br>";

                // ACTUALIZAMOS SPLIT PAGOS

                $TipoPago = $datos[TipoMedioPago];
                if ($TipoPago == "MASTERCARD") :
                    $frma_pgo = 22;
                elseif ($TipoPago == "VISA") :
                    $frma_pgo =   23;
                elseif ($TipoPago == "AMEX") :
                    $frma_pgo =   21;
                else :
                    $frma_pgo =   24;
                endif;

                if ($datos[IDTipoPago] == 3)
                    $frma_pgo =  3;



                $vlor_pgar = $datos[ValorPagado];
                $cnsmo_ayb = $datos[ValorPagado];

                echo $MaxConsecutivo = "SELECT MAX(nmro_cnsctvo) AS Consecutivo FROM vntas_pos_split_pgos WHERE nmro_almcen = $nmro_almcen AND nmro_rgstro = $nmro_rgstro AND nmro_accion = $datos_socio[AccionPadre]";
                $qryMaxConsecutivo = $dbh->query($MaxConsecutivo);
                $row = $qryMaxConsecutivo->fetch();

                echo "<br>";
                echo "<br>";

                $nmro_cnsctvo = $row[Consecutivo] + 1;

                echo $sqlDatosSplit = "INSERT INTO vntas_pos_split_pgos 
                    (
                        nmro_almcen,nmro_rgstro,
                        nmro_cnsctvo,nmro_cntrol,
                        nmro_accion,nmbre_scio,
                        frma_pgo,vlor_pgar,
                        cnsmo_ayb,nva_frma_pgo,
                        fcha_frma_pgo,usrio_frma_pgo,
                        crre_frma_pgo,crdcntdo
                    ) 
                        VALUES 
                    (
                        $nmro_almcen,$nmro_rgstro,
                        $nmro_cnsctvo,'',
                        '$datos_socio[AccionPadre]','$datos[NombreSocio]',
                        $frma_pgo,$vlor_pgar,
                        $cnsmo_ayb,0,
                        NULL,0,
                        0,'N'
                    )";

                $qryDatosSplit = $dbh->query($sqlDatosSplit);
                echo "<br>";
                echo "<br>";

            endif;

            $insertsql = json_encode($sqlVentas);
            $insertsql2 = json_encode($sqlDetalle);

            $insert = "INSERT INTO LogInsertFacuracionPradera (IDReservaGeneral, SQLventas, SQLdetalle) VALUES ('$datos[IDReservaGeneral]', $insertsql, $insertsql2)";
            $insertLog = $dbo->query($insert);
        endif;
    }

    public function cancelar_reserva_facturacion_potosi($IDReserva)
    {
        $dbo = &SIMDB::get();

        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas";
            exit;
        }

        $sqlDatos = "UPDATE vntas_pos SET mtvo_anlcion = 67, cdgo_estdo = 'E' WHERE nmro_rsrva = $IDReserva";

        $qryDatos = $dbh->query($sqlDatos);
    }

    public function facturacion_domicilio($IDDomicilio, $Version)
    {
        $dbo = SIMDB::get();

        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas";
            exit;
        }

        $datos_domicilio = $dbo->fetchAll("Domicilio$Version", "IDDomicilio = $IDDomicilio");
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $datos_domicilio[IDSocio]");

        if (empty($datos_socio[AccionPadre])) {
            $datos_socio[AccionPadre] = $datos_socio[Accion];
        }

        if ($datos_socio[IDEstadoSocio] == 5) :
            $AlertaCartera = "S";
        else :
            $AlertaCartera = "N";
        endif;

        $nmro_almcen = 21;
        $NombreSocio = $datos_socio[Nombre] . " " . $datos_socio[Apellido];

        $sqlRegistro = "SELECT MAX(nmro_rgstro) AS num FROM vntas_pos WHERE  nmro_almcen = $nmro_almcen";
        $qryRegistro = $dbh->query($sqlRegistro);
        $row = $qryRegistro->fetch();

        $nmro_rgstro = $row[num] + 1;

        $fecha = date("Ymd", strtotime(substr($datos_domicilio[HoraEntrega], 0, 10)));
        $Hora  = substr($datos_domicilio[HoraEntrega], 11, 19);

        $sqlVentas = "INSERT INTO vntas_pos
                    (
                        nmro_almcen, nmro_rgstro, fcha_rgstro, clse_scio, nmro_accion, nmro_idntfccion,
                        nmbre_scio, vlor_csto, vlor_iva, vlor_prpna, vlor_mlta, alrta_crtra, nmro_msa, nmro_evnto, nmro_rsrva, nmro_caja,
                        mtvo_anlcion, nmro_msro, crre_cja, clse_vnta, cdgo_usrio, fcha_aprtra, usrio_frma_pgo, crre_frma_pgo,
                        vlor_sldo,  fcha_vncmnto,  vlor_rcrgo, fcha_rcrgo, cdgo_estdo, intrfse
                    )
                    VALUES
                    ($nmro_almcen,$nmro_rgstro,'$fecha', 1,'$datos_socio[AccionPadre]',$datos_socio[NumeroDocumento],
                    '$NombreSocio', 0, 0, 0, 0, '$AlertaCartera', 0, 0, 0, 1, 0, 1, 0, 1, 1, '$fecha $Hora',
                        0, 0, 0, '$fecha $Hora', 0, '$fecha $Hora', 'A', 'N')";

        $dbh->query($sqlVentas);

        $sqlDetalle = "INSERT INTO dtlle_vntas_pos
                    (
                        nmro_almcen,nmro_rgstro, nmro_cnsctvo,cdgo_plu,
                        cntdad,vlor_untrio, vlor_ttal,vlor_iva,
                        vlor_csto,aplca_prcso,
                        almcen_prcso,orden_prcso,
                        acmpmnto1,acmpmnto2,
                        acmpmnto3,nmro_psto,
                        nmro_msro,nmro_cmnda,
                        fcha_slctud,fcha_srvcio,
                        cdgo_estdo,entrda
                    )
                    VALUES
                    ($nmro_almcen, $nmro_rgstro, 1,$cdgo_plu,
                        1,$precio,$precio,0, 0,'N',
                        $nmro_almcen,0,0,0,
                        0,1,1,0,
                        '$fecha $Hora', '$fecha $Hora',
                        'S','N'
                    )";

        $dbh->query($sqlDetalle);
    }

    public function actuliza_vntas_pos_split_pgos($NumeroFactura, $IDFactura, $TipoPago,  $PagoTotal, $DATOS = "")
    {


        $dbo = &SIMDB::get();

        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas";
            exit;
        }

        $nmro_almcen = $DATOS[Almacen];
        $vlor_vnta = $DATOS[valor];
        $nmro_rgstro = $IDFactura;

        $sql_facturas = "SELECT TOP 1 * FROM v_ventas WHERE nmro_almcen = '$nmro_almcen' AND nmro_rgstro = '$nmro_rgstro' Order by fcha_rgstro desc";
        $qry_factura = $dbh->query($sql_facturas);
        $r = $qry_factura->fetch();

        $nmro_accion = $r[nmro_accion];
        $nmbre_scio = $r[nmbre_scio];


        if ($TipoPago == "CargoSocio") :
            $frma_pgo = 3;
        elseif ($TipoPago == "MASTERCARD") :
            $frma_pgo = 22;
        elseif ($TipoPago == "VISA") :
            $frma_pgo =   23;
        elseif ($TipoPago == "AMEX") :
            $frma_pgo =   21;
        else :
            $frma_pgo =   24;
        endif;


        $vlor_pgar = $PagoTotal;
        $cnsmo_ayb = $PagoTotal;

        // BUSCAMOS EL CONSECUTIVO

        $MaxConsecutivo = "SELECT MAX(nmro_cnsctvo) AS Consecutivo FROM vntas_pos_split_pgos WHERE nmro_almcen = $nmro_almcen AND nmro_rgstro = $nmro_rgstro AND nmro_accion = '" . $nmro_accion . "'";
        $qryMaxConsecutivo = $dbh->query($MaxConsecutivo);
        $row = $qryMaxConsecutivo->fetch();


        $nmro_cnsctvo = $row[Consecutivo] + 1;

        $sqlDatos = "INSERT INTO vntas_pos_split_pgos 
                            (
                                nmro_almcen,nmro_rgstro,
                                nmro_cnsctvo,nmro_cntrol,
                                nmro_accion,nmbre_scio,
                                frma_pgo,vlor_pgar,
                                cnsmo_ayb,nva_frma_pgo,
                                fcha_frma_pgo,usrio_frma_pgo,
                                crre_frma_pgo,crdcntdo
                            ) 
                                VALUES 
                            (
                                $nmro_almcen,$nmro_rgstro,
                                $nmro_cnsctvo,'',
                                '$nmro_accion','$nmbre_scio',
                                $frma_pgo,$vlor_pgar,
                                $cnsmo_ayb,0,
                                NULL,0,
                                0,'N'
                            )";

        $qryDatos = $dbh->query($sqlDatos);

        $Propina = $DATOS[Propina];
        $Almacen = $nmro_almcen;

        if (empty($Propina))
            $Propina = 0;

        $sqlDatos = "UPDATE vntas_pos SET vlor_prpna = '$Propina', nmro_fctra = '$NumeroFactura',vlor_vnta='$vlor_pgar', cdgo_estdo = 'C' WHERE nmro_rgstro = '$IDFactura' AND nmro_almcen = $Almacen";
        $qryDatos = $dbh->query($sqlDatos);

        $sqlDatosDian = "UPDATE almcnes SET cnsctvo_dian = '$NumeroFactura' WHERE nmro_almcen = $Almacen";
        $qryDatosDian = $dbh->query($sqlDatosDian);
    }
}
