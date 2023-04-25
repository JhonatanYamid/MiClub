<?php
class SIMWebServiceFacturas
{


    //CONFIGURACION FACTURACION
    public function Configuracion_facturacion($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $response = array();
        $datos_config_facturacion = $dbo->fetchAll("ConfiguracionFacturacion", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        $configuracion["IDClub"] = $IDClub;

        $codigo = $datos_socio["Accion"];
        $titular = substr("$codigo", -2);
        if ($titular == "01") {
            $configuracion["MostrarFiltroFamiliares"] = "N";
        } else {
            $configuracion["MostrarFiltroFamiliares"] = $datos_config_facturacion["MostrarFiltroFamiliares"];
        }

        $configuracion["FiltroFamiliaresLabel"] = $datos_config_facturacion["FiltroFamiliaresLabel"];
        $configuracion["MostrarSeccionesHistorial"] = $datos_config_facturacion["MostrarSeccionesHistorial"];
        $configuracion["SeccionesHistorialLabel"] = $datos_config_facturacion["SeccionesHistorialLabel"];
        $configuracion["SeccionesPendientesPagoLabel"] = $datos_config_facturacion["SeccionesPendientesPagoLabel"];
        $configuracion["PermitePaginar"] = $datos_config_facturacion["PermitePaginar"];

        $configuracion["BuscadorFechas"] = $datos_config_facturacion["BuscadorFechas"];
        $configuracion["ImagenLateral"] = CLUB_ROOT . $datos_config_facturacion["ImagenLateral"];
        $configuracion["PrecargarFechaHoyBuscador"] = $datos_config_facturacion["PrecargarFechaHoyBuscador"];

        $configuracion["PermiteSeleccionarVarias"] = $datos_config_facturacion["PermiteSeleccionarVarias"];
        $configuracion["MostrarDecimal"] = $datos_config_facturacion["MostrarDecimal"];
        $configuracion["TextoSeleccionarDeseleccionar"] = $datos_config_facturacion["TextoSeleccionarDeseleccionar"];
        $configuracion["TextoIntroSeleccionarVariasPago"] = $datos_config_facturacion["TextoIntroSeleccionarVariasPago"];

        $configuracion["OcultarBuscadorFechasHistorico"] = $datos_config_facturacion["OcultarBuscadorFechasHistorico"];
        $configuracion["OcultarBuscadorFechasPendientesPago"] = $datos_config_facturacion["OcultarBuscadorFechasPendientesPago"];

        array_push($response, $configuracion);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $configuracion;

        return $respuesta;
    }



    /* ... WORLD TRADE CENTER ... */
    public function get_facturas_WTC($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");

        $RutaExtractos = EXTRACTOSWORLTRADECENTER_DIR;

        // self::borrar_extracto_tmp();

        $FechaNombre = date("Ym");

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("_", $archivo_carpeta);

                                    $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[0]);
                                    $accion_socio_estado_pdf = str_replace(".pdf", "", $array_nombre_archivo[0]);
                                    $array_nombre_archivo_estado = explode("-", $accion_socio_estado_pdf);
                                    $accion_socio_estado_pdf = $array_nombre_archivo_estado[0] . "-" . $array_nombre_archivo_estado[1];
                                    //$NombreArchivo = $AccionSocio."-".$AccionPadre."-".$FechaNombre;
                                    $NombreArchivo = $AccionSocio . "-" . $AccionPadre;

                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($NombreArchivo == $accion_socio_estado_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);


        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = str_replace("-f", "", $fecha);
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["IDSocio"] = $IDSocio;

            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_WTC($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();

        $nombre_archivo = substr($IDFactura, 0, 3) . "_" . substr($IDFactura, 3);
        $logo = $dbo->getFields("Club", "FotoLogoApp", "IDClub = $IDClub");

        $ruta_local = EXTRACTOSTMP_ROOT .  $IDFactura;
        $ruta_ext = EXTRACTOSTMP_DIR .  $IDFactura;

        $detalle_producto = '  
            <tbody>
                <br><br>
                <!--tr>
                    <td>
                        <img src="' . CLUB_ROOT . $logo . '" width="300" height="100">
                    </td>
                </tr-->
                <tr>
                    <td>
                        <iframe src="http://docs.google.com/gview?url=' . $ruta_local . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                    </td>
                </tr>
            </tbody>';



        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tr>
                    <td>
                    <table border="0" width="100%">
                        ' . $detalle_producto . '
                    </table>
                    </td>
                </tr>
            </table>';

        $factura["WebViewInterno"] = "S";
        $factura["IDFactura"] = $IDFactura;

        $cuerpo_factura = '<!doctype html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <title>Detalle Factura</title>
                                <style>
                                .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size:12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                                }

                                .tabla th {
                                padding: 5px;
                                font-size: 16px;
                                background-color: #83aec0;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #558FA6;
                                border-bottom-color: #558FA6;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                                }

                                .tabla .modo1 {
                                font-size: 12px;
                                font-weight:bold;
                                background-color: #e2ebef;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                                }
                                .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #A4C4D0;
                                border-bottom-color: #A4C4D0;
                                text-align:right;
                                }

                                .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight:bold;
                                text-align: left;
                                background-color: #e2ebef;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #A4C4D0;
                                border-bottom-color: #A4C4D0;
                                }

                                .tabla .modo2 {
                                font-size: 12px;
                                font-weight:bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align:center;
                                }
                                .tabla .modo2 td {
                                padding: 5px;
                                }
                                .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight:bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align:left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                                }
                                </style>
                            </head>
                            <body>
                            ';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $detalle_forma_pago . "<br>" . $boton;
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN WORLD TRADE CENTER ... */

    /* ... HEBRAICA ... */

    public function get_facturas_hebraica($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        require LIBDIR . "SIMWebServiceHebraica.inc.php";
        $dbo = SIMDB::get();
        $response = array();
        $response_categoria = array();

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        // BUSCAMOS EL PREDIO DEL VERDADERO

        $Predio = $dbo->getFields("Socio", "Predio", "Accion = '$datos_socio[AccionPadre]' AND IDClub = $IDClub");
        $id_persona = $Predio;

        if ($IDSocio == 5533)
            $id_persona = 45911;

        $Extractos = SIMWebServiceHebraica::estado_cuenta_cliente($id_persona);

        $Valor = 0;
        $TotalCredito = 0;

        foreach ($Extractos[items] as $id => $InfoExtracto) :
            $Divisa = $InfoExtracto[cd_divisa];
            $Valor += $InfoExtracto[nm_debito_reexpresado]; //Se cambio nm_debito por nm_debito_reexpresado
            $TotalCredito += $InfoExtracto[nm_credito_reexpresado]; //Se cambio nm_credito por nm_credito_reexpresado


        endforeach;
        $credito = abs($TotalCredito);
        $Valor = ($Valor - $credito);

        $nombre_categoria = "Estado de Cuenta";
        $Factura[NumeroFactura] = "Extracto";
        $Factura[IDFactura] = $id_persona;
        $Factura[IDClub] = $IDClub;
        $Factura[Fecha] = date("Y-m-d");
        $Factura[ValorFactura] = $Divisa . number_format((float)$Valor, 2, '.', ',');

        $response_campos = array();
        $datos_campos["Texto"] = "";
        $datos_campos["Color"] = "";
        array_push($response_campos, $datos_campos);

        $Factura["Campos"] = $response_campos;
        $array_categoria_factura[$nombre_categoria][] = $Factura;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["ValorTotal"] = $Divisa . number_format((float)$Valor, 2, '.', ',');
        $datos_facturas["ValorTotalLabel"] = "Saldo Actual";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PermiteSeleccionarVarias"] = "N";
        $datos_facturas["MostrarDecimal"] = "N";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";

        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_facturas_hebraica($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();
        require LIBDIR . "SIMWebServiceHebraica.inc.php";

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        // BUSCAMOS EL PREDIO DEL VERDADERO

        $Predio = $dbo->getFields("Socio", "Predio", "Accion = '$datos_socio[AccionPadre]' AND IDClub = $IDClub");

        $id_persona = $Predio;

        if ($IDSocio == 5533)
            $id_persona = 45911;

        $Extractos = SIMWebServiceHebraica::estado_cuenta_cliente($id_persona);

        $Accion = $datos_socio[AccionPadre];
        $Nombre = $datos_socio[Nombre] . " " . $datos_socio[Apellido];
        $Identificacion = $datos_socio[NumeroDocumento];
        $Correo = $datos_socio[CorreoElectronico];

        $detalle_producto .=
            "<tbody>
        <tr>    
            <th>Codigo Transacción</th>
            <th>Tipo Transacción</th>
            <th>Fecha</th>
            <th>Fecha Vencimiento</th>
            <th>Nombre del Miembro</th>
            <th>Observación</th>
            <th>Debito</th>
            <th>Credito</th>            
        </tr>";


        foreach ($Extractos[items] as $id => $InfoExtracto) :
            $detalle_producto .= "
                <tr class=modo1>                    
                    <td>" . $InfoExtracto[cd_transaccion] . "</td>
                    <td>" . $InfoExtracto[ds_tipo_transaccion] . "</td>
                    <td>" . $InfoExtracto[fe_transaccion] . "</td>
                    <td>" . $InfoExtracto[fe_vencimiento] . "</td>
                    <td>" . $InfoExtracto[ds_nombre_fiscal] . "</td>
                    <td>" . $InfoExtracto[ds_movimiento] . "</td>
                    <td>" . number_format((float)$InfoExtracto[nm_debito_reexpresado], 2, '.', ',') . "</td>
                    <td>" . number_format((float)$InfoExtracto[nm_credito_reexpresado], 2, '.', ',') . "</td>                    
                </tr>";

            $TotalDebito += $InfoExtracto[nm_debito_reexpresado]; //Se cambio nm_debito por nm_debito_reexpresado
            $TotalCredito += $InfoExtracto[nm_credito_reexpresado]; //Se cambio nm_credito por nm_credito_reexpresado

        endforeach;

        $detalle_producto .= "</tbody>";

        $cuerpo_factura =
            '<!doctype html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Detalle Factura</title>
                    <style>
                        .tabla {
                            font-family: Verdana, Arial, Helvetica, sans-serif;
                            font-size: 12px;
                            text-align: center;
                            width: 95%;
                            align: center;
                        }


                        .tabla th {
                            padding: 5px;
                            font-size: 12px;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #FFFFFF;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            text-transform: uppercase;
                        }

                        .tabla .modo1 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #cfcfca;
                            background-repeat: repeat-x;
                            color: #34484E;
                            font-family: "Trebuchet MS", Arial;
                        }

                        .tabla .modo1 td {
                            padding: 5px;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #cfcfca;
                            border-bottom-color: #cfcfca;
                            text-align: center;
                        }

                        .tabla .modo1 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            text-align: right;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                        }

                        .tabla .modo2 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #4f4d4d;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                        }

                        .tabla .modo2 td {
                            padding: 5px;
                        }

                        .tabla .modo2 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #fdfdf1;
                            background-repeat: repeat-x;
                            color: #990000;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #EBE9BC;
                            border-bottom-color: #EBE9BC;
                        }

                        .boton_personalizado{
                            text-decoration: none;
                            padding: 10px;
                            font-weight: 600;
                            font-size: 20px;
                            color: #ffffff;
                            background-color: #8f8f8c;
                            border-radius: 6px;
                            border: 2px solid #cfcfca;
                        }

                        .boton_personalizado:hover{
                        color: #cfcfca;
                        background-color: #ffffff;
                        }

                    </style>
                </head>
                <body>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Nombre Socio: <font color=#ffffff>' . $Nombre . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Acción: <font color=#ffffff>' . $Accion . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Identificación: <font color=#ffffff>' . $Identificacion . '</font></td>
                </tr>               
                <tr class="modo2">
                    <td>Correo: <font color=#ffffff>' . $Correo . '</font></td>
                </tr>               
                                                            
            </tbody>
        </table>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $totales =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Total Debito: <font color=#ffffff>' . $TotalDebito . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Total Credito: <font color=#ffffff>' . $TotalCredito . '</font></td>
                </tr>                                           
            </tbody>
        </table>';


        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" .  $totales . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    /* ... FIN HEBRAICA ... */

    /* ... PLAYA AZUL ... */
    public function get_factura_Playa_Azul($IDClub, $IDSocio)
    {
        require LIBDIR . "SIMWebServicePlayaAzul.inc.php";
        $dbo = SIMDB::get();
        $response = array();
        $response_categoria = array();

        if ($IDSocio == 636938)
            $IDSocio = 639445;

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $Accion = $DatosSocio[AccionPadre];
        $Deuda = SIMWebServicePlayaAzul::Deuda($Accion);

        $Total = 0;

        if (!empty($Deuda[response])) :

            foreach ($Deuda[response] as $id => $DatoDeuda) :
                if (!empty($DatoDeuda)) :
                    foreach ($DatoDeuda as $id => $DatosDeuda) :
                        $nombre_categoria = $DatosDeuda[Detalle];

                        $factura["IDClub"] = $IDClub;
                        $factura["IDFactura"] = $DatosDeuda[Documento];
                        $factura["NumeroFactura"] = $DatosDeuda[Accion];
                        $factura["Color"] = "";

                        $Fecha = explode("T", $DatosDeuda[Fecha]);
                        $factura["Fecha"] = $Fecha[0];
                        $factura["ColorFecha"] = "";
                        $factura["ValorFactura"] = "$ " . number_format((float)$DatosDeuda[Valor], 2, '.', ',');
                        // $factura["ValorFactura"] = "$ " . $DatosDeuda[Valor];

                        $Total += $DatosDeuda[Valor];

                        $response_campos = array();

                        $Texto = $DatosDeuda[Comentario];
                        $Color = "#090a09";

                        $datos_campos["Texto"] = $Texto;
                        $datos_campos["Color"] = $Color;

                        array_push($response_campos, $datos_campos);

                        $factura["Campos"] = $response_campos;

                        $array_categoria_factura[$nombre_categoria][] = $factura;
                    endforeach;
                    $NoHay = 0;
                else :
                    $NoHay = 1;
                endif;
            endforeach;
        endif;

        if ($NoHay == 1) :
            $nombre_categoria = "No hay deudas actuales";

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "";
            $factura["NumeroFactura"] = "Cuenta";
            $factura["Color"] = "";

            $factura["Fecha"] = "";
            $factura["ColorFecha"] = "";
            $factura["ValorFactura"] = "$ 0";
            // $factura["ValorFactura"] = "$ " . $DatosDeuda[Valor];

            $Total += $DatosDeuda[Valor];

            $response_campos = array();

            $Texto = $DatosDeuda[Comentario];
            $Color = "#090a09";

            $datos_campos["Texto"] = $Texto;
            $datos_campos["Color"] = $Color;

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;
        endif;


        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["ValorTotal"] = "$" . number_format((float)$Total, 2, '.', ',');
        $datos_facturas["ValorTotalLabel"] = "Total Deuda";
        // $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = $TextoDetalle;
        $datos_facturas["PermiteSeleccionarVarias"] = "N";
        $datos_facturas["MostrarDecimal"] = "N";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";

        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_playa_azul($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();
        require LIBDIR . "SIMWebServicePlayaAzul.inc.php";

        if ($IDSocio == 636938)
            $IDSocio = 639445;

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $Accion = $DatosSocio[AccionPadre];
        $Nombre = $DatosSocio[Nombre] . " " . $DatosSocio[Apellido];
        $Identificacion = $DatosSocio[NumeroDocumento];
        $Deuda = SIMWebServicePlayaAzul::Deuda($Accion);

        $detalle_producto .=
            "<tbody>
        <tr>    
            <th>Documento</th>
            <th>Detalle</th>
            <th>Comentario</th>
            <th>Valor</th>
        </tr>";

        foreach ($Deuda[response] as $id => $DatoDeuda) :
            foreach ($DatoDeuda as $id => $DatosDeuda) :

                if ($DatosDeuda[Documento] == $IDFactura) :

                    $AccionDeuda = $DatosDeuda[Accion];

                    $detalle_producto .= "
                    <tr class=modo1>                    
                        <td>" . $DatosDeuda[Documento] . "</td>
                        <td>" . $DatosDeuda[Detalle] . "</td>
                        <td>" . $DatosDeuda[Comentario] . "</td>
                        <td>$" . number_format((float)$DatosDeuda[Valor], 2, '.', ',') . "</td>
                    </tr>";

                endif;

            endforeach;
        endforeach;

        $detalle_producto .= "</tbody>";

        $cuerpo_factura =
            '<!doctype html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Detalle Factura</title>
                    <style>
                        .tabla {
                            font-family: Verdana, Arial, Helvetica, sans-serif;
                            font-size: 12px;
                            text-align: center;
                            width: 95%;
                            align: center;
                        }


                        .tabla th {
                            padding: 5px;
                            font-size: 12px;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #FFFFFF;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            text-transform: uppercase;
                        }

                        .tabla .modo1 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #cfcfca;
                            background-repeat: repeat-x;
                            color: #34484E;
                            font-family: "Trebuchet MS", Arial;
                        }

                        .tabla .modo1 td {
                            padding: 5px;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #cfcfca;
                            border-bottom-color: #cfcfca;
                            text-align: center;
                        }

                        .tabla .modo1 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            text-align: right;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                        }

                        .tabla .modo2 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #4f4d4d;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                        }

                        .tabla .modo2 td {
                            padding: 5px;
                        }

                        .tabla .modo2 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #fdfdf1;
                            background-repeat: repeat-x;
                            color: #990000;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #EBE9BC;
                            border-bottom-color: #EBE9BC;
                        }

                        .boton_personalizado{
                            text-decoration: none;
                            padding: 10px;
                            font-weight: 600;
                            font-size: 20px;
                            color: #ffffff;
                            background-color: #8f8f8c;
                            border-radius: 6px;
                            border: 2px solid #cfcfca;
                        }

                        .boton_personalizado:hover{
                        color: #cfcfca;
                        background-color: #ffffff;
                        }

                    </style>
                </head>
                <body>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Nombre Socio: <font color=#ffffff>' . $Nombre . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Acción: <font color=#ffffff>' . $AccionDeuda . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Identificación: <font color=#ffffff>' . $Identificacion . '</font></td>
                </tr>               
                                                            
            </tbody>
        </table>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN PLAYA AZUL ... */

    /* ... COMUNIDAD MINI ... */
    // PARA COMINIDAD MINI SE USA EN UN PRINCIPIO PARA MOSTRAR LOS BENEFICIOS Y PRECIO DE LA SUSCRIPCIÓN

    public function get_factura_comunidad_mini($IDClub, $IDSocio)
    {
        $dbo = SIMDB::get();
        $response = array();
        $response_campos = array();
        $response_categoria = array();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");


        if (trim($DatosSocio[TipoSocio]) == "Plan Básico") :
            $Texto = "Pasa al Plan Plus";
            $TextoDetalle = "Beneficios";
            $TotalTexto = "Valor";
            $Color = "#459c28";
            $Valor = 60000;
            $nombre_categoria = "Suscripcion Plus";
        else :
            $Texto = "Ya estas en el Plan Plus";
            $TextoDetalle = "--";
            $TotalTexto = "--";
            $Color = "#2549c2";
            $nombre_categoria = "Ya estas en el Plan Plus";
        endif;

        $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = $IDSocio;
        $factura["NumeroFactura"] = $DatosSocio[TipoSocio];
        $factura["Color"] = "#2549c2";
        $factura["Fecha"] = "";
        $factura["ColorFecha"] = "";
        $factura["ValorFactura"] = "$" . $Valor;

        $Total += $Valor;

        $datos_campos["Texto"] = $Texto;
        $datos_campos["Color"] = $Color;

        array_push($response_campos, $datos_campos);

        $factura["Campos"] = $response_campos;

        $array_categoria_factura[$nombre_categoria][] = $factura;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["ValorTotal"] = "$" . $Total;
        $datos_facturas["ValorTotalLabel"] = $TotalTexto;
        // $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = $TextoDetalle;
        $datos_facturas["PermiteSeleccionarVarias"] = "N";
        $datos_facturas["MostrarDecimal"] = "N";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";

        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_comunidad_mini($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $Nombre = $DatosSocio[Nombre] . " " . $DatosSocio[Apellido];
        $Plan = $NumeroFactura;
        $Identificacion = $DatosSocio[NumeroDocumento];
        $Pagar = 60000;

        $cuerpo_factura =
            '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: center;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

        $detalle_producto .=
            "<tbody>
                <tr>    
                    <th>BENEFICIOS PLAN PLUS</th>                    
                </tr>
                <tr class=modo1>    
                    <td>Modulo Beneficios</td>                    
                </tr>
                <tr class=modo1>    
                    <td>Directorio Socios</td>                    
                </tr>
                <tr class=modo1>    
                    <td>Galeria</td>                    
                </tr>
                <tr class=modo1>    
                    <td>Taller</td>                    
                </tr>
                <tr class=modo1>    
                    <td>Modulo de Preguntas Frecuentes</td>                    
                </tr>
                <tr>    
                    <th>Precio</th>                    
                </tr>
                <tr class=modo1>    
                    <td>$" . $Pagar . "</td>                    
                </tr>
            </tbody>";

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Nombre Socio: <font color=#ffffff>' . $Nombre . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Plan Actual: <font color=#ffffff>' . $Plan . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Identificación: <font color=#ffffff>' . $Identificacion . '</font></td>
                </tr>               
                                                            
            </tbody>
        </table>';

        $boton =
            '<form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_payu.php" method="post">
                <table align="center">                       
                <tr>
                    <td align="center">
                    Debito desde cuenta corriente/ahorros.<br>
                    <input type="image" src="' . URLROOT . 'plataform/assets/img/logopayu.png" alt="Submit">
                    </td>
                </tr>
                </table>                                                      

                <input type="hidden" name="Valor" id="Valor" value="' . $Pagar . '">
                <input type="hidden" name="IDClub" id="IDClub" value="' . $IDClub . '">               
                <input type="hidden" name="Descripcion" id="Descripcion" value="Suscripcion Comunidad MINI">
            </form>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN COMUNIDAD MINI ... */
    /* ... IZCARAGUA ... */
    public function get_factura_izcaragua($IDClub, $IDSocio)
    {
        require LIBDIR . "SIMWebServiceIzcaragua.inc.php";
        $dbo = SIMDB::get();
        $response = array();
        $response_categoria = array();

        if ($IDSocio == 612350)
            $IDSocio = 616606;

        $Deuda = SIMWebServiceIzcaragua::ServicioDeudaSocio($IDSocio);

        $Total = 0;

        $nombre_categoria = "Deudas";

        foreach ($Deuda as $id => $DatosDeuda) :

            $Texto = "";

            if ((string)trim($DatosDeuda[deuda]) == 'TRUE') :
                $Color  = "#e01010";
                $Texto = "Tiene deuda con el club";
                $TextoDetalle = "Ver Deuda";
                $TotalTexto = "Total Deuda";
            else :
                $Texto = "No tiene deuda con el club";
                $Color  = "#459c28";
                $TextoDetalle = "--";
                $TotalTexto = "--";
            endif;

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $IDSocio;
            $factura["NumeroFactura"] = $DatosDeuda[co_cli];
            $factura["Color"] = $Color;
            $factura["Fecha"] = "";
            $factura["ColorFecha"] = "";
            $factura["ValorFactura"] = "Bs " . number_format((float)$DatosDeuda[monto], 0, '', '');

            $Total += $DatosDeuda[monto];

            $response_campos = array();

            $datos_campos["Texto"] = $Texto;
            $datos_campos["Color"] = $Color;

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["ValorTotal"] = "Bs" . $Total;
        $datos_facturas["ValorTotalLabel"] = $TotalTexto;
        // $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = $TextoDetalle;
        $datos_facturas["PermiteSeleccionarVarias"] = "N";
        $datos_facturas["MostrarDecimal"] = "N";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";

        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_izcaragua($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();
        $response_categoria = array();
        require LIBDIR . "SIMWebServiceIzcaragua.inc.php";

        $IDSocio = $IDFactura;

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $Nombre = $DatosSocio[Nombre] . " " . $DatosSocio[Apellido];
        $Accion = $DatosSocio[Accion];
        $Identificacion = $DatosSocio[NumeroDocumento];

        $Deuda = SIMWebServiceIzcaragua::ServicioDeudaSocio($IDSocio);

        $detalle_producto .=
            "<tbody>
            <tr>    
                <th>Accion Deuda</th>
                <th>Deuda Total</th>
            </tr>";

        foreach ($Deuda as $id => $DatosDeuda) :

            $Monto = $DatosDeuda[monto];
            $Accion = $DatosDeuda[co_cli];

            $detalle_producto .= "
            <tr class=modo1>                    
                <td>" . $Accion . "</td>
                <td>Bs " . $Monto . "</td>
            </tr>";

        endforeach;

        $detalle_producto .= "</tbody>";

        $cuerpo_factura =
            '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Nombre Socio: <font color=#ffffff>' . $Nombre . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Acción: <font color=#ffffff>' . $Accion . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Identificación: <font color=#ffffff>' . $Identificacion . '</font></td>
                </tr>               
                                                            
            </tbody>
        </table>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $Saldos . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN IZCARAGUA ... */
    /* ... VALLE ARRIBA ... */
    public function get_factura_valle_arriba($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = SIMDB::get();
        $response = array();
        $response_categoria = array();
        require LIBDIR . "SIMWebServiceValleArriba.inc.php";

        if ($IDSocio == 602143) :
            $IDSocio = 611614;
        endif;

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");
        $Accion = $datos_socio[AccionPadre];

        $DatosFacturas = SIMWebServiceValleArriba::Cuentas($Accion);

        $nombre_categoria = "Estado de Cuentas";

        foreach ($DatosFacturas as $ids => $datosresponse) :

            $Periodo = $datosresponse[Periodo];
            $Total = $datosresponse[TotalCargos];

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $Accion;
            $factura["NumeroFactura"] = $datosresponse[RifCedula];
            $factura["Color"] = "#000000";
            $factura["Fecha"] = $Periodo;
            $factura["ColorFecha"] = "#000000";
            $factura["ValorFactura"] = "$" . $datosresponse[TotalSaldo];

            $response_campos = array();

            $datos_campos["Texto"] = "Fecha Limite: " . $datosresponse[FechaLimite];
            $datos_campos["Color"] = "#555754";

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["ValorTotal"] = "$" . $Total;
        $datos_facturas["ValorTotalLabel"] = "Total Cargos";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "Ver Detalle";
        $datos_facturas["PermiteSeleccionarVarias"] = "N";
        $datos_facturas["MostrarDecimal"] = "N";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";

        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function detalle_factura_valle_arriba($IDClub, $IDSocio, $IDFactura, $NumeroFactura)
    {
        $dbo = SIMDB::get();
        $response = array();

        require LIBDIR . "SIMWebServiceValleArriba.inc.php";

        $DatosFacturas = SIMWebServiceValleArriba::Cuentas($IDFactura);

        $detalle_producto .=
            "<tbody>
            <tr>
                <th>Codigo</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>";

        foreach ($DatosFacturas as $ids => $datosresponse) :

            $Periodo = $datosresponse[Periodo];
            $Nombre = $datosresponse[Nombres];
            $TotalCargos = $datosresponse[TotalCargos];
            $TotalAbonos = $datosresponse[TotalAbonos];
            $TotalSaldo = $datosresponse[TotalSaldo];
            $Accion = $datosresponse[Accion];
            $Identificacion = $datosresponse[RifCedula];
            $Limite = $datosresponse[FechaLimite];

            foreach ($datosresponse[Detalles] as $ids => $dato) :

                $Codigo = $dato[Codigo];
                $Descripcion = $dato[Descripcion];
                $Monto = $dato[Monto];

                $detalle_producto .= "
                <tr class=modo1>
                    <td>" . $Codigo . "</td>
                    <td>" .  $Descripcion . "</td>
                    <td>$" . $Monto . "</td>
                </tr>";

            endforeach;
        endforeach;

        $detalle_producto .= "</tbody>";

        $cuerpo_factura =
            '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Nombre Socio: <font color=#ffffff>' . $Nombre . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Acción: <font color=#ffffff>' . $Accion . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Identificación: <font color=#ffffff>' . $Identificacion . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Periodo: <font color=#ffffff>' . $Periodo . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Fecha Limite: <font color=#ffffff>' . $Limite . '</font></td>
                </tr>                                               
            </tbody>
        </table>';

        $Totales =
            '<table border="0"width="100%">
            <tbody>                                       
                <tr class="modo2">
                    <td>Total Cargos: <font color=#ffffff>$' . $TotalCargos . '</font></td>
                </tr>                            
                <tr class="modo2">
                    <td>Total Abonos: <font color=#ffffff>$' . $TotalAbonos . '</font></td>
                </tr>                            
                <tr class="modo2">
                    <td>Total Saldo: <font color=#ffffff>$' . $TotalSaldo . '</font></td>
                </tr>                                                        
            </tbody>
        </table>';

        $Saldos =
            '<table border="0"cellpadding="0"cellspacing="0"class="tabla">
            <tr>
                <td>
                    ' . $Totales . '
                </td>
            </tr>
        </table>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $Saldos . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN VALLE ARRIBA ... */

    /* ...COUNTRY BARANQUILLA ... */
    public function get_factura_barranquilla($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 358681)
            $IDSocio = 358668;

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $AccionPadre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSBARRANQUILLA_DIR;

        // self::borrar_extracto_tmp();

        $FechaNombre = date("Ym");

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("_", $archivo_carpeta);

                                    $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[0]);
                                    $accion_socio_estado_pdf = str_replace(".pdf", "", $array_nombre_archivo[0]);
                                    $array_nombre_archivo_estado = explode("-", $accion_socio_estado_pdf);
                                    $accion_socio_estado_pdf = $array_nombre_archivo_estado[0] . "-" . $array_nombre_archivo_estado[1];
                                    //$NombreArchivo = $AccionSocio."-".$AccionPadre."-".$FechaNombre;
                                    $NombreArchivo = $AccionSocio . "-" . $AccionPadre;

                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($NombreArchivo == $accion_socio_estado_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;

                                    if ("F" . $AccionSocio == $accion_socio_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = "FACTURA-" . date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);


        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $pos = strpos($archivo_extracto, "FACTURA");
            if ($pos === false) :
                $pos = strpos($archivo_extracto, "OTRA");
                if ($pos === false) :
                    $nombre_categoria = "Extractos";
                else :
                    $nombre_categoria = "Facturas";
                endif;
            else :
                $nombre_categoria = "Facturas";
            endif;


            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = str_replace("-f", "", $fecha);
            $factura["Fecha"] = $fecha_extracto;
            if ($nombre_categoria == "Facturas") {
                $factura["ValorFactura"] = "Factura";
            }
            if ($nombre_categoria == "Extractos") {
                $factura["ValorFactura"] = "Extracto";
            }

            $factura["IDSocio"] = $IDSocio;

            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_barranquilla($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $nombre_archivo = substr($IDFactura, 0, 3) . "_" . substr($IDFactura, 3);
        $logo = $dbo->getFields("Club", "FotoLogoApp", "IDClub = $IDClub");

        $ruta_local = EXTRACTOSTMP_ROOT .  $IDFactura;
        $ruta_ext = EXTRACTOSTMP_DIR .  $IDFactura;

        $detalle_producto = '   <tbody>
                                    <br><br>
                                    <!--tr>
                                        <td>
                                            <img src="' . CLUB_ROOT . $logo . '" width="300" height="100">
                                        </td>
                                    </tr-->
                                    <tr>
                                        <td>
                                            <iframe src="http://docs.google.com/gview?url=' . $ruta_local . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                        </td>
                                    </tr>
                                </tbody>';



        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cuerpo_factura =
            '<!doctype html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Detalle Factura</title>
                <style>
                    .tabla {
                        font-family: Verdana, Arial, Helvetica, sans-serif;
                        font-size: 12px;
                        text-align: center;
                        width: 95%;
                        align: center;
                    }


                    .tabla th {
                        padding: 5px;
                        font-size: 12px;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #FFFFFF;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        text-transform: uppercase;
                    }

                    .tabla .modo1 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #cfcfca;
                        background-repeat: repeat-x;
                        color: #34484E;
                        font-family: "Trebuchet MS", Arial;
                    }

                    .tabla .modo1 td {
                        padding: 5px;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #cfcfca;
                        border-bottom-color: #cfcfca;
                        text-align: left;
                    }

                    .tabla .modo1 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        text-align: left;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                    }

                    .tabla .modo2 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #4f4d4d;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                    }

                    .tabla .modo2 td {
                        padding: 5px;
                    }

                    .tabla .modo2 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #fdfdf1;
                        background-repeat: repeat-x;
                        color: #990000;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #EBE9BC;
                        border-bottom-color: #EBE9BC;
                    }

                    .boton_personalizado{
                        text-decoration: none;
                        padding: 10px;
                        font-weight: 600;
                        font-size: 20px;
                        color: #ffffff;
                        background-color: #8f8f8c;
                        border-radius: 6px;
                        border: 2px solid #cfcfca;
                    }

                    .boton_personalizado:hover{
                    color: #cfcfca;
                    background-color: #ffffff;
                    }

                </style>
            </head>
            <body>';

        $cuerpo_factura .=  $detalle_factura;
        $cuerpo_factura .= '</body></html>';

        $factura["WebViewInterno"] = "S";
        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN COUNTRY BARRANQUILLA ... */


    /* BELLAVISTA */
    public function get_factura_Bellavista($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo)
    {
        require(LIBDIR . "SIMWebServiceBellavista.inc.php");
        $dbo = &SIMDB::get();

        $response = array();
        $response_categoria = array();

        if ($IDSocio == 350585)
            $IDSocio = 556288;

        $CardCode = $dbo->getFields("Socio", "AccionPadre", "IDSocio = $IDSocio");

        $DATOS = SIMWebServiceBellavista::GetOrders($CardCode);
        $nombre_categoria = "ORDENES";

        foreach ($DATOS->Result->Partida as $ids => $dato) :

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $IDSocio;
            $factura["NumeroFactura"] = (string)$dato->DocNum;
            $factura["Fecha"] = date("Y-m-d");
            $factura["ValorFactura"] = "$" . number_format((float)$dato->Price, 3, ',', '.');

            $array_categoria_factura[$nombre_categoria][] = $factura;
        endforeach;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        if ($Dispositivo == "iOS") {
            $datos_facturas["BuscadorFechas"] = false;
        } else {
            $datos_facturas["BuscadorFechas"] = "N";
        }
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_Bellavista($IDClub, $IDFactura, $NumeroFactura)
    {
        require(LIBDIR . "SIMWebServiceBellavista.inc.php");
        $dbo = &SIMDB::get();

        $response = array();

        $CardCode = $dbo->getFields("Socio", "AccionPadre", "IDSocio = $IDFactura");

        $DATOS = SIMWebServiceBellavista::GetOrders($CardCode);

        $detalle_producto .=
            "<tbody>
            <tr>
                <th>Documento</th>
                <th>Codigo Item</th>
                <th>Descripción</th>
                <th>Precio</th>
            </tr>";
        foreach ($DATOS->Result->Partida as $ids => $dato) :

            if ((string)$dato->DocNum == $NumeroFactura) :
                $detalle_producto .=
                    "<tr class=modo1>
                    <td>" . (string)$dato->DocNum . "</td>
                    <td>" . (string)$dato->ItemCode . "</td>
                    <td>" . $dato->Dscription . "</td>
                    <td>$" . number_format((float)$dato->Price, 3, ',', '.') . "</td>
                </tr>";
            endif;
        endforeach;
        $detalle_producto .= "</tbody>";



        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cuerpo_factura =
            '<!doctype html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Detalle Factura</title>
                <style>
                    .tabla {
                        font-family: Verdana, Arial, Helvetica, sans-serif;
                        font-size: 12px;
                        text-align: center;
                        width: 95%;
                        align: center;
                    }


                    .tabla th {
                        padding: 5px;
                        font-size: 12px;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #FFFFFF;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        text-transform: uppercase;
                    }

                    .tabla .modo1 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #cfcfca;
                        background-repeat: repeat-x;
                        color: #34484E;
                        font-family: "Trebuchet MS", Arial;
                    }

                    .tabla .modo1 td {
                        padding: 5px;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #cfcfca;
                        border-bottom-color: #cfcfca;
                        text-align: left;
                    }

                    .tabla .modo1 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        text-align: left;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                    }

                    .tabla .modo2 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #4f4d4d;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                    }

                    .tabla .modo2 td {
                        padding: 5px;
                    }

                    .tabla .modo2 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #fdfdf1;
                        background-repeat: repeat-x;
                        color: #990000;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #EBE9BC;
                        border-bottom-color: #EBE9BC;
                    }

                    .boton_personalizado{
                        text-decoration: none;
                        padding: 10px;
                        font-weight: 600;
                        font-size: 20px;
                        color: #ffffff;
                        background-color: #8f8f8c;
                        border-radius: 6px;
                        border: 2px solid #cfcfca;
                    }

                    .boton_personalizado:hover{
                    color: #cfcfca;
                    background-color: #ffffff;
                    }

                </style>
            </head>
            <body>';

        $cuerpo_factura .=  $detalle_factura;
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* BELLAVISTA */

    /* RANCHO SAN FRANCISCO */
    public function get_factura_RSF($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo)
    {
        require(LIBDIR . "SIMWebServiceRanchoSF.inc.php");
        $dbo = &SIMDB::get();

        $response = array();
        $response_categoria = array();

        if ($IDSocio == 344643 || $IDSocio == 100162 || $IDSocio == 5533)
            $IDSocio = 112684;

        if (empty($FechaInicio))
            $FechaInicio = date("Ymd");

        if (empty($FechaFin))
            $FechaFin = date("Ymd");

        $tresmeses = date("Ymd", strtotime(date("Ymd") . "- 3 month"));

        if (strtotime($FechaFin) < strtotime($FechaInicio) || strtotime($FechaFin) > strtotime(date("Ymd"))) :
            $nombre_categoria = "LAS FECHAS SON INCORRECTAS";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "";
            $factura["NumeroFactura"] = "";
            $factura["Color"] = "#FF0000";
            $factura["Fecha"] = "";
            $factura["ValorFactura"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        elseif (strtotime($FechaInicio) < strtotime($tresmeses)) :
            $nombre_categoria = "LA FECHA INICIO DEBE SER MENOR A 3 MESES";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "";
            $factura["NumeroFactura"] = "";
            $factura["Color"] = "#FF0000";
            $factura["Fecha"] = "";
            $factura["ValorFactura"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        else :
            $DATA = SIMWebServiceRanchoSF::login();
            $DATOS = SIMWebServiceRanchoSF::consultaSinXml($DATA[access_token], $IDSocio, $FechaInicio, $FechaFin);

            $json = json_decode($DATOS[json], true);
            $Total = 0;

            if (!empty($json)) :
                foreach ($json as $ids => $dato) :

                    $response_campos = array();

                    $fecha =  date("Y-m-d", strtotime($dato[Fecha]));

                    $nombre_categoria = $dato[Caja];

                    if ($dato[Tipo] == "NotaCredito")
                        $colorNumero = "#990b1b";
                    else
                        $colorNumero = "#3a8f13";

                    $factura["IDClub"] = $IDClub;
                    $factura["IDFactura"] = $dato[Caja] . "|" . $dato[Cedula];
                    $factura["NumeroFactura"] = $dato[Numero];
                    $factura["Color"] = $colorNumero;
                    $factura["Fecha"] = $fecha;
                    $factura["ColorFecha"] = "#000000";
                    $factura["ValorFactura"] = "$" . number_format((float)$dato[Total], 2, '.', ',');

                    $datos_campos["Texto"] = $dato[Tipo];
                    $datos_campos["Color"] = "#555754";

                    array_push($response_campos, $datos_campos);

                    $factura["Campos"] = $response_campos;

                    $Total += $dato[Total];

                    $array_categoria_factura[$nombre_categoria][] = $factura;
                endforeach;
            else :

                $response_campos = array();

                $nombre_categoria = "NO HAY FACTURAS PARA LA FECHA";
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = "";
                $factura["NumeroFactura"] = "";
                $factura["Color"] = "#FF0000";
                $factura["Fecha"] = "";
                $factura["ColorFecha"] = "#00AA00";
                $factura["ValorFactura"] = "";

                $datos_campos["Texto"] = "$dato[Tipo]";
                $datos_campos["Color"] = "#00AA00";
                array_push($response_campos, $datos_campos);

                $factura["Campos"] = $response_campos;

                $Total += $dato[Total];

                $array_categoria_factura[$nombre_categoria][] = $factura;
            endif;
        endif;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        /* if ($Dispositivo == "iOS") {
            $datos_facturas["BuscadorFechas"] = true;
        } else { */
        $datos_facturas["BuscadorFechas"] = "S";
        // }

        $Total = number_format((float)$Total, 2, '.', ',');

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = $Total;
        $datos_facturas["ValorTotalLabel"] = "Total Cuentas";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PrecargarFechaHoyBuscador"] = "S";

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_RSF($IDClub, $IDFactura, $NumeroFactura)
    {
        require(LIBDIR . "SIMWebServiceRanchoSF.inc.php");
        $dbo = &SIMDB::get();

        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $DatosBusqueda = explode("|", $IDFactura);
        $Caja = $DatosBusqueda[0];
        $Cedula = $DatosBusqueda[1];

        $DATA = SIMWebServiceRanchoSF::login();
        $DATOS = SIMWebServiceRanchoSF::consultaXml($DATA[access_token], $Cedula, $NumeroFactura, $Caja);

        $json = json_decode($DATOS[json], true);

        $xml = simplexml_load_string($json["Xml"]);

        // print_r($xml);
        // exit;

        $fecha =  $xml->infoFactura->fechaEmision;

        $formapago = $xml->infoAdicional->campoAdicional[1];

        if ($Caja == "APORTES PATRIMONIALES" || $Caja == "CUOTAS MANTENIMIENTOS") :
            $formapago = "Cuentas x Cobrar";
        endif;

        $SubTotal = $xml->infoFactura->totalSinImpuestos;
        $Servicio = $xml->infoFactura->propina;
        $Descuento = $xml->infoFactura->totalDescuento;
        $TotaPagar = $xml->infoFactura->importeTotal;

        $detalle_producto .=
            "<tbody>
            <tr>
            <th>Producto</th>
            <th>Cant</th>
            <th>PVP</th>
            <th>Total</th>
        </tr>";

        foreach ($xml->detalles->detalle as $id => $detalle) :

            $Producto = $detalle->descripcion;
            $Cantidad = $detalle->cantidad;
            $PrecioUnitario = $detalle->precioUnitario;
            $TotalSinImpuestos = $detalle->precioTotalSinImpuesto;

            $detalle_producto .= "
            <tr class=modo1>
                <td>" . $Producto . "</td>
                <td>" . $Cantidad . "</td>
                <td>$" . number_format((float)$PrecioUnitario, 2, '.', ',') . "</td>
                <td>$" . number_format((float)$TotalSinImpuestos, 2, '.', ',') . "</td>
            </tr>";

        endforeach;

        $Iva = 0;
        foreach ($xml->infoFactura->totalConImpuestos->totalImpuesto as $id => $detalle) :
            $Iva += $detalle->valor;
        endforeach;

        $detalle_producto .= "</tbody>";

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Numero: <font color=#ffffff>' . $NumeroFactura . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Fecha: <font color=#ffffff>' . $fecha . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Caja: <font color=#ffffff>' . $Caja . '</font></td>
                </tr>
                <tr class="modo2">
                    <td>Forma de Pago: <font color=#ffffff>' . $formapago . '</font></td>
                </tr>
            </tbody>
        </table>';

        $subtotales =
            '<table border="0"cellpadding="0"cellspacing="0"class="tabla">
            <tr>
                <td>
                    <table border="0"width="100%">
                        <tbody>
                            <tr class=modo1>
                                <td>SubTotal</td>
                                <td>$' . number_format((float)$SubTotal, 2, '.', ',') . '</td>
                            </tr>
                            <tr class=modo1>
                                <td>Servicio</td>
                                <td>$' . number_format((float)$Servicio, 2, '.', ',') . '</td>
                            </tr>
                            <tr class=modo1>
                                <td>Descuento</td>
                                <td>$' . number_format((float)$Descuento, 2, '.', ',') . '</td>
                            </tr>
                            <tr class=modo1>
                                <td>Iva</td>
                                <td>$' . number_format((float)$Iva, 2, '.', ',') . '</td>
                            </tr>
                            <tr>
                                <th>Total Factura</th>
                                <th>$' . number_format((float)$TotaPagar, 2, '.', ',') . '</th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $boton =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                <a href="miclubranchosanfrancisco://show?module=pqr" class=boton_personalizado> Ir a PQR</a>
                </table>
                </td>
            </tr>
        </table>';

        $cuerpo_factura =
            '<!doctype html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Detalle Factura</title>
                <style>
                    .tabla {
                        font-family: Verdana, Arial, Helvetica, sans-serif;
                        font-size: 12px;
                        text-align: center;
                        width: 95%;
                        align: center;
                    }


                    .tabla th {
                        padding: 5px;
                        font-size: 12px;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #FFFFFF;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        text-transform: uppercase;
                    }

                    .tabla .modo1 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #cfcfca;
                        background-repeat: repeat-x;
                        color: #34484E;
                        font-family: "Trebuchet MS", Arial;
                    }

                    .tabla .modo1 td {
                        padding: 5px;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #cfcfca;
                        border-bottom-color: #cfcfca;
                        text-align: left;
                    }

                    .tabla .modo1 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        text-align: right;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                    }

                    .tabla .modo2 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #4f4d4d;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                    }

                    .tabla .modo2 td {
                        padding: 5px;
                    }

                    .tabla .modo2 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #fdfdf1;
                        background-repeat: repeat-x;
                        color: #990000;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #EBE9BC;
                        border-bottom-color: #EBE9BC;
                    }

                    .boton_personalizado{
                        text-decoration: none;
                        padding: 10px;
                        font-weight: 600;
                        font-size: 20px;
                        color: #ffffff;
                        background-color: #8f8f8c;
                        border-radius: 6px;
                        border: 2px solid #cfcfca;
                    }

                    .boton_personalizado:hover{
                    color: #cfcfca;
                    background-color: #ffffff;
                    }

                </style>
            </head>
            <body>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $subtotales . "<br>" .  $boton . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* FIN RANCHO SAN FRANCISCO */


    /* ... CLUB LAGUNITA ... */
    public function get_factura_lagunita($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $response_categoria = array();

        if ($IDSocio == 539794) {
            $IDSocio = 541791;
        }

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        // EXTRACTOS

        $sqlExtractos = "SELECT * FROM ExtractosLagunita WHERE IDSocio = $IDSocio";
        $qryExtractos = $dbo->query($sqlExtractos);

        $nombre_categoria = "Documentos Pendientes";

        while ($row = $dbo->fetchArray($qryExtractos)) {

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $row[IDExtractosLagunita];
            $factura["NumeroFactura"] = $row[documento];
            $factura["Fecha"] = $row[periodo];
            $factura["ValorFactura"] = "$" . $row[totalaPagar];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        // ESTADO DE CUENTA

        $sqlCuentas = "SELECT * FROM CuentasLagunita WHERE IDSocio = $IDSocio";
        $qryCuentas = $dbo->query($sqlCuentas);
        $cuentas = $dbo->fetchArray($qryCuentas);

        $nombre_categoria = "Estado de Cuenta";

        $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = $cuentas[IDCuentasLagunita];
        $factura["NumeroFactura"] = "Cuenta";
        $factura["Fecha"] = "";
        $factura["ValorFactura"] = "$" . $cuentas[Balance];

        $array_categoria_factura[$nombre_categoria][] = $factura;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_lagunita($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $detalle_producto = "<tbody>";

        if ($NumeroFactura == "Cuenta") {

            $sqlCuentas = "SELECT * FROM CuentasLagunita WHERE IDCuentasLagunita = $IDFactura";
            $qryCuentas = $dbo->query($sqlCuentas);
            $cuotas = $dbo->fetchArray($qryCuentas);

            $datos = json_decode($cuotas[Situacion], true);

            $detalle_producto .= "
                <tr>
                    <td>Concepto</td>
                    <td>Documento</td>
                    <td>Fecha</td>
                    <td>Cargo</td>
                    <td>Abono</td>
                </tr>";

            foreach ($datos as $id => $info) {

                $Concepto = $info[concepto];
                $Documento = $info[documento];
                $fecha = substr($info[fecha], 0, 10);
                $cargo = $info[cargo];
                $abono = $info[abono];

                $detalle_producto .= "
                <tr>
                    <td>" . $Concepto . "</td>
                    <td>" . $Documento . "</td>
                    <td>" . $fecha . "</td>
                    <td>$" . $cargo . "</td>
                    <td>$" . $abono . "</td>
                </tr>";
            }

            $detalle_producto .= "

            <tr></tr>
            <tr></tr>
            <tr>
                <td>Balance General</td>
                <td>$" . $cuotas[Balance] . "</td>
            </tr>
            <tr></tr>";
        } else {
            $sqlExtractos = "SELECT * FROM ExtractosLagunita WHERE IDExtractosLagunita = $IDFactura";
            $qryExtractos = $dbo->query($sqlExtractos);
            $dato = $dbo->fetchArray($qryExtractos);

            $detalle = $dato["detalle"];
            $periodo = $dato["periodo"];
            $fecha = substr($dato["fecha"], 0, 10);
            $fechaLimite = substr($dato["fechaLimitePago"], 0, 10);
            $documento = $dato["documento"];
            $valor = $dato["valor"];
            $saldoAnterior = $dato["saldoAnterior"];
            $totalPagos = $dato["totalPagos"];
            $totalPagar = $dato["totalaPagar"];
            $impuestoIGTF = (3 * $totalPagar / 100);
            $TotalConImpuesto = $totalPagar + $impuestoIGTF;

            $detalle_producto .= "
                        <tr></tr>
                        <tr>
                            <td>Detalle</td>
                            <td>" . $detalle . "</td>
                        </tr>
                        <tr>
                            <td>Periodo</td>
                            <td>" . $periodo . "</td>
                        </tr>
                        <tr>
                            <td>Fecha</td>
                            <td>" . $fecha . "</td>
                        </tr>
                        <tr>
                            <td>Fecha limite de pago</td>
                            <td>" . $fechaLimite . "</td>
                        </tr>
                        <tr>
                            <td>Documento</td>
                            <td>" . $documento . "</td>
                        </tr>
                        <tr>
                            <td>Valor</td>
                            <td>$" . $valor . "</td>
                        </tr>
                        <tr>
                            <td>Saldo Anterior</td>
                            <td>$" . $saldoAnterior . "</td>
                        </tr>
                        <tr>
                            <td>Total Pagos</td>
                            <td>$" . $totalPagos . "</td>
                        </tr>
                        <tr>
                            <td>Total a Pagar antes de Impuesto</td>
                            <td>$" . $totalPagar . "</td>
                        </tr>
                        <tr>
                            <td>Impuesto IGTF</td>
                            <td>$" . $impuestoIGTF . "</td>
                        </tr>
                        <tr>
                            <td>Total a Pagar con impuesto</td>
                            <td>$" . $TotalConImpuesto . "</td>
                        </tr>
                        <tr></tr>";

            $boton =
                '   <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                            <tr>
                                <td align="center">
                                <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'paypalGET.php" method="get">

                                    <input type="hidden" name="valor" id="valor" value="' . number_format((float)$TotalConImpuesto, 2, '.', ',')  . '">
                                    <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                                    <input type="hidden" name="extra1" id="extra1" value="Pagos Documento-' . $documento . '">
                                    <input type="hidden" name="IDSocio" id="IDSocio" value="' . $dato[IDSocio] . '">
                                    <input type="hidden" name="Modulo" id="Modulo" value="FacturasLagunita">
                                    <input type="image"  src="' . URLROOT . 'plataform/assets/img/logotipo_paypal_pagos.png" alt="Submit">

                                </form>
                                </td>
                            </tr>
                        </table>';
        }

        $detalle_producto .= "</tbody>";

        $datos_factura = '
                        <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                            <tr>
                                <td colspan="5" >
                                <table border="1" width="100%">
                                    ' . $detalle_producto . '
                                </table>
                                </td>
                            </tr>
                        </table>
                        ';

        $cuerpo_factura =
            '<!doctype html>
                    <html>
                        <head>
                            <meta charset="UTF-8">
                            <title>Detalle Factura</title>
                            <style>
                                .tabla {
                                    font-family: Verdana, Arial, Helvetica, sans-serif;
                                    font-size: 12px;
                                    text-align: center;
                                    width: 95%;
                                    align: center;
                                }


                                .tabla th {
                                    padding: 5px;
                                    font-size: 12px;
                                    background-color: #8f8f8c;
                                    background-repeat: repeat-x;
                                    color: #FFFFFF;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #8f8f8c;
                                    border-bottom-color: #8f8f8c;
                                    font-family: "Trebuchet MS", Arial;
                                    text-transform: uppercase;
                                }

                                .tabla .modo1 {
                                    font-size: 12px;
                                    font-weight: bold;
                                    background-color: #cfcfca;
                                    background-repeat: repeat-x;
                                    color: #34484E;
                                    font-family: "Trebuchet MS", Arial;
                                }

                                .tabla .modo1 td {
                                    padding: 5px;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #cfcfca;
                                    border-bottom-color: #cfcfca;
                                    text-align: right;
                                }

                                .tabla .modo1 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight: bold;
                                    text-align: right;
                                    background-color: #8f8f8c;
                                    background-repeat: repeat-x;
                                    color: #8f8f8c;
                                    font-family: "Trebuchet MS", Arial;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #8f8f8c;
                                    border-bottom-color: #8f8f8c;
                                }

                                .tabla .modo2 {
                                    font-size: 12px;
                                    font-weight: bold;
                                    background-color: #8f8f8c;
                                    background-repeat: repeat-x;
                                    color: #4f4d4d;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align: left;
                                }

                                .tabla .modo2 td {
                                    padding: 5px;
                                }

                                .tabla .modo2 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight: bold;
                                    background-color: #fdfdf1;
                                    background-repeat: repeat-x;
                                    color: #990000;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align: left;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #EBE9BC;
                                    border-bottom-color: #EBE9BC;
                                }

                                .boton_personalizado{
                                    text-decoration: none;
                                    padding: 10px;
                                    font-weight: 600;
                                    font-size: 20px;
                                    color: #ffffff;
                                    background-color: #8f8f8c;
                                    border-radius: 6px;
                                    border: 2px solid #cfcfca;
                                }

                                .boton_personalizado:hover{
                                color: #cfcfca;
                                background-color: #ffffff;
                                }

                            </style>
                        </head>
                        <body>';


        $cuerpo_factura .= $datos_factura . "<br>" .  $boton;
        $cuerpo_factura .= '

                </body>
                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CLUB LAGUNITA ... */

    /* ... CLUB INKA ... */
    public function get_factura_inka($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        require LIBDIR . "SIMWebServiceInkas.inc.php";

        $dbo = &SIMDB::get();

        $response = array();
        $response_categoria = array();

        if ($IDSocio == 517856) {
            $IDSocio = 517555;
        }

        $Response = SIMWebServiceInkas::obtener_token();
        $Token = $Response["response"];

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Filtro = $datos_socio["Accion"];

        $cuotas = SIMWebServiceInkas::cuenta_cuotas($Token, $Filtro);

        $nombre_categoria = "Cotizaciones y Otros";
        foreach ($cuotas["response"] as $cuota => $dato) {
            if (!in_array($dato["periodo"], $arrayPeriodoCuotas)) {
                $arrayPeriodoCuotas[] = $dato["periodo"];
            }

            if (!in_array($dato["moneda"], $arrayMoneda)) {
                $arrayMoneda[] = $dato["moneda"];
            }
        }

        foreach ($arrayPeriodoCuotas as $id => $periodo) {

            $año = substr($periodo, 0, 4);
            $Mes = substr($periodo, -2);

            foreach ($arrayMoneda as $id => $moneda) {
                $valor = 0;
                foreach ($cuotas["response"] as $cuota => $dato) {

                    if ($dato["moneda"] == $moneda && $dato["periodo"] == $periodo) {
                        $valor += $dato["total"];
                    }
                }

                if ($moneda == "SO") {
                    $monedaS = "S/";
                } else {
                    $monedaS = "$";
                }

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $Filtro . "-CUOTA-" . $moneda;
                $factura["NumeroFactura"] = $periodo;
                $factura["Fecha"] = $año . "-" . $Mes;
                $factura["ValorFactura"] = $monedaS . $valor;

                $array_categoria_factura[$nombre_categoria][] = $factura;
            }
        }

        $consumos = SIMWebServiceInkas::cuenta_consumos($Token, $Filtro);


        $nombre_categoria = "Consumos A&B";
        foreach ($consumos["response"] as $cuota => $dato) {
            if (!in_array($dato["periodo"], $arrayPeridodoConsumos)) {
                $arrayPeridodoConsumos[] = $dato["periodo"];
            }
        }

        foreach ($arrayPeridodoConsumos as $id => $periodo) {

            $año = substr($periodo, 0, 4);
            $Mes = substr($periodo, -2);

            $valor = 0;
            foreach ($consumos["response"] as $cuota => $dato) {

                if ($dato["periodo"] == $periodo) {
                    $valor += $dato["total"];
                }
            }

            if ($moneda == "SO") {
                $monedaS = "S/";
            } else {
                $monedaS = "$";
            }

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $Filtro . "-CONSUMO";
            $factura["NumeroFactura"] = $periodo;
            $factura["Fecha"] = $año . "-" . $Mes;
            $factura["ValorFactura"] = $monedaS . $valor;

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }


        if (empty($cuotas["response"]) && empty($consumos["response"])) :
            $nombre_categoria = "Usted no registra deuda a la fecha";

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "VACIO-VACIO";
            $factura["NumeroFactura"] = "";
            $factura["Color"] = "#FF0000";
            $factura["Fecha"] = "";
            $factura["ValorFactura"] = "";

            $array_categoria_factura[$nombre_categoria][] = $factura;
        endif;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_inka($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        require LIBDIR . "SIMWebServiceInkas.inc.php";

        $Response = SIMWebServiceInkas::obtener_token();
        $Token = $Response["response"];

        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $Datos = explode("-", $IDFactura);
        $Filtro = $Datos[0];

        $detalle_producto = "<tbody>";

        if ($Datos[1] == "CUOTA") {
            $cuotas = SIMWebServiceInkas::cuenta_cuotas($Token, $Filtro);

            foreach ($cuotas["response"] as $cuota => $dato) {

                if ($dato[periodo] == $NumeroFactura && $dato[moneda] == $Datos[2]) {

                    $año = substr($dato["periodo"], 0, 4);
                    $Mes = substr($dato["periodo"], -2);

                    $descripcion = $dato["descripcion"];
                    $periodo = $año . "-" . $Mes;
                    $TipoDeuda = $dato["tip_deuda"];
                    $concepto = $dato["concepto"];
                    $importe = $dato["importe"];
                    $mora = $dato["mora"];
                    $total = $dato["total"];

                    $moneda = $dato["moneda"];
                    if ($moneda == "SO") {
                        $moneda = "S/";
                    } else {
                        $moneda = "$";
                    }

                    $detalle_producto .= "<tr></tr>
                    <tr>
                        <td>Descripción</td>
                        <td>" . $descripcion . "</td>
                    </tr>
                    <tr>
                        <td>Periodo</td>
                        <td>" . $periodo . "</td>
                    </tr>
                    <tr>
                        <td>Tipo Deuda</td>
                        <td>" . $TipoDeuda . "</td>
                    </tr>
                    <tr>
                        <td>Concepto</td>
                        <td>" . $concepto . "</td>
                    </tr>
                    <tr>
                        <td>Importe</td>
                        <td>" . $moneda . $importe . "</td>
                    </tr>
                    <tr>
                        <td>Mora</td>
                        <td>" . $moneda . $mora . "</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>" . $moneda . $total . "</td>
                    </tr><tr></tr>";
                }
            }
        } elseif ($Datos[1] == "CONSUMO") {

            $consumos = SIMWebServiceInkas::cuenta_consumos($Token, $Filtro);

            foreach ($consumos["response"] as $cuota => $dato) {

                if ($dato[periodo] == $NumeroFactura) {

                    $año = substr($dato["periodo"], 0, 4);
                    $Mes = substr($dato["periodo"], -2);

                    $descripcion = $dato["descripcion"];
                    $periodo = $año . "-" . $Mes;
                    $TipoDeuda = $dato["tip_deuda"];
                    $concepto = $dato["concepto"];
                    $importe = $dato["importe"];
                    $mora = $dato["mora"];
                    $total = $dato["total"];

                    $moneda = $dato["moneda"];

                    $moneda = "S/";

                    $detalle_producto .= "<tr></tr>
                     <tr>
                         <td>Descripción</td>
                         <td>" . $descripcion . "</td>
                     </tr>
                     <tr>
                         <td>Periodo</td>
                         <td>" . $periodo . "</td>
                     </tr>
                     <tr>
                         <td>Tipo Deuda</td>
                         <td>" . $TipoDeuda . "</td>
                     </tr>
                     <tr>
                         <td>Concepto</td>
                         <td>" . $concepto . "</td>
                     </tr>
                     <tr>
                         <td>Importe</td>
                         <td>" . $moneda . $importe . "</td>
                     </tr>
                     <tr>
                         <td>Mora</td>
                         <td>" . $moneda . $mora . "</td>
                     </tr>
                     <tr>
                         <td>Total</td>
                         <td>" . $moneda . $total . "</td>
                     </tr><tr></tr>";
                }
            }
        } else {
            $detalle_producto .= "<tr></tr>
                     <tr>
                         <td>Descripción</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Periodo</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Tipo Deuda</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Concepto</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Importe</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Mora</td>
                         <td>--</td>
                     </tr>
                     <tr>
                         <td>Total</td>
                         <td>--</td>
                     </tr><tr></tr>";
        }

        $detalle_producto .= "</tbody>";

        $datos_factura = '
                        <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                            <tr>
                                <td colspan="5" >
                                    <table border="0" width="100%">
                                        El proceso de actualización del estado de cuenta podrá tomar dos días hábiles en hacerse efectivo, período necesario para confirmar movimientos de cuenta y cancelaciones en sistemas.
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" >
                                <table border="0" width="100%">
                                    ' . $detalle_producto . '
                                </table>
                                </td>
                            </tr>
                        </table>
                        ';

        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Factura</title>
                                    <style>
                                    .tabla {
                                    font-family: Verdana, Arial, Helvetica, sans-serif;
                                    font-size:12px;
                                    text-align: center;
                                    width: 95%;
                                    align: center;
                                    }

                                    .tabla th {
                                    padding: 5px;
                                    font-size: 12px;
                                    background-color: #83aec0;
                                    background-repeat: repeat-x;
                                    color: #FFFFFF;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #558FA6;
                                    border-bottom-color: #558FA6;
                                    font-family: "Trebuchet MS", Arial;
                                    text-transform: uppercase;
                                    }

                                    .tabla .modo1 {
                                    font-size: 10px;
                                    font-weight:bold;
                                    background-color: #e2ebef;
                                    background-repeat: repeat-x;
                                    color: #34484E;
                                    font-family: "Trebuchet MS", Arial;
                                    }
                                    .tabla .modo1 td {
                                    padding: 5px;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #A4C4D0;
                                    border-bottom-color: #A4C4D0;
                                    text-align:right;
                                    }

                                    .tabla .modo1 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight:bold;
                                    text-align: left;
                                    background-color: #e2ebef;
                                    background-repeat: repeat-x;
                                    color: #34484E;
                                    font-family: "Trebuchet MS", Arial;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #A4C4D0;
                                    border-bottom-color: #A4C4D0;
                                    }

                                    .tabla .modo2 {
                                    font-size: 12px;
                                    font-weight:bold;
                                    background-color: #fdfdf1;
                                    background-repeat: repeat-x;
                                    color: #990000;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align:center;
                                    }
                                    .tabla .modo2 td {
                                    padding: 5px;
                                    }
                                    .tabla .modo2 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight:bold;
                                    background-color: #fdfdf1;
                                    background-repeat: repeat-x;
                                    color: #990000;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align:left;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #EBE9BC;
                                    border-bottom-color: #EBE9BC;
                                    }

                                    .boton_personalizado{
                                    text-decoration: none;
                                    padding: 10px;
                                    font-weight: 600;
                                    font-size: 20px;
                                    color: #ffffff;
                                    background-color: #1883ba;
                                    border-radius: 6px;
                                    border: 2px solid #0016b0;
                                    }

                                    </style>
                                    </head>
                                    <body>';

        $cuerpo_factura .= $datos_factura;
        $cuerpo_factura .= '

                </body>
                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CLUB INKA ... */

    /* ... EL RINCON ... */
    public function get_factura_ftp($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 479765)
            $IDSocio = 620324;

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $AccionSocio = substr($AccionSocio, 0, 4);
        $RutaExtractos = EXTRACTOSRINCON_DIR;

        //$AccionSocio="0003";

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("_", $archivo_carpeta);
                                    $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[1]);
                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        //echo EXTRACTOSTMP_ROOT.$nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        /*
        //Consulto movimientos
        $sql_movimiento = "Select MONTH(Fecha) as Mes, YEAR(Fecha) as Year
        From SocioMovimiento
        Where IDClub = '".$IDClub."' and Accion = '".$AccionSocio."' group by month(Fecha)";
        $result_movimiento = $dbo->query($sql_movimiento);
        while($row_movimiento = $dbo->fetchArray($result_movimiento)):

        $nombre_categoria ="Consumos";
        $fecha_extracto = SIMResources::$meses[((int)$row_movimiento["Mes"]-1)] . " de " .  $row_movimiento["Year"] . " ";
        $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = "Movimiento|".$row_movimiento["Year"]."|".$row_movimiento["Mes"]."|".$AccionSocio;
        $factura["NumeroFactura"] = $row_movimiento["Year"];
        $factura["Fecha"] = $fecha_extracto;
        $factura["ValorFactura"] = "Consumos";
        $factura["Almacen"] = "";
        $array_categoria_factura [$nombre_categoria][]     = $factura;
        endwhile;
         */

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_app($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $pos = strpos($IDFactura, "Movimiento");
        if ($pos === false) { //Muestra Extracto
            $nombre_archivo = substr($IDFactura, 0, 3) . "_" . substr($IDFactura, 3);
            //$ruta_local = EXTRACTOS_ROOT.$NumeroFactura."/".$nombre_archivo;
            $ruta_local = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . '/5169_IMG_0400.PNG">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                <img src="' . URLROOT . '/admin/images/icons/pdf.gif">

                                                <a href="' . $ruta_local . '">
                                                    Pulse aca para ver el Extracto.
                                                </a>
                                                <br><br>
                                                <a href="https://www.zonapagos.com/t_Clubelrincondecajica">
                                                <img src="' . URLROOT . 'plataform/assets/img/btnpagopereira.png">
                                                </a>

                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';
        } else {

            $array_datos = explode("|", $IDFactura);
            $Year = $array_datos[1];
            $Mes = $array_datos[2];
            $AccionSocio = $array_datos[3];

            //detalle movimientos
            $sql_movimiento = "Select *
                                       From SocioMovimiento
                                     Where IDClub = '" . $IDClub . "' and Accion = '" . $AccionSocio . "'
                                     and month(Fecha) = '" . $Mes . "' and Year(Fecha) = '" . $Year . "'";
            $result_movimiento = $dbo->query($sql_movimiento);
            while ($row_movimiento = $dbo->fetchArray($result_movimiento)) :

                $fila_movimiento .= '<tr>
						                                                            <td>
						                                                                ' . $row_movimiento["PuntoVenta"] . '
						                                                            </td>
						                                                            <td>
						                                                                ' . $row_movimiento["Producto"] . '
						                                                            </td>
						                                                            <td>
						                                                                ' . $row_movimiento["Cantidad"] . '
						                                                            </td>
						                                                            <td>
						                                                                ' . number_format($row_movimiento["ValorProducto"], 0, '', '.') . '
						                                                            </td>
						                                                            <td>
						                                                                ' . $row_movimiento["Fecha"] . '
						                                                            </td>
						                                                            <td>
						                                                                ' . $row_movimiento["NumeroFactura"] . '
						                                                            </td>
						                                                            <td>
						                                                                ' . number_format($row_movimiento["Propina"]) . '
						                                                            </td>
						                                                            <td>
						                                                                ' . number_format($row_movimiento["TotalFactura"]) . '
						                                                            </td>
						                                                        </tr>';
            endwhile;

            $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>
                                    <style>
                                                    .tabla {
                                                    font-family: Verdana, Arial, Helvetica, sans-serif;
                                                    font-size:8px;
                                                    text-align: center;
                                                    width: 98%;
                                                    align: center;
                                                    }

                                                    .tabla th {
                                                    padding: 5px;
                                                    font-size: 8px;
                                                    background-color: #83aec0;
                                                    background-repeat: repeat-x;
                                                    color: #FFFFFF;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #558FA6;
                                                    border-bottom-color: #558FA6;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-transform: uppercase;
                                                    }

                                                    .tabla .modo1 {
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #e2ebef;
                                                    background-repeat: repeat-x;
                                                    color: #34484E;
                                                    font-family: "Trebuchet MS", Arial;
                                                    }
                                                    .tabla .modo1 td {
                                                    padding: 5px;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #A4C4D0;
                                                    border-bottom-color: #A4C4D0;
                                                    text-align:right;
                                                    }

                                                    .tabla .modo1 th {
                                                    background-position: left top;
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    text-align: left;
                                                    background-color: #e2ebef;
                                                    background-repeat: repeat-x;
                                                    color: #34484E;
                                                    font-family: "Trebuchet MS", Arial;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #A4C4D0;
                                                    border-bottom-color: #A4C4D0;
                                                    }

                                                    .tabla .modo2 {
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #fdfdf1;
                                                    background-repeat: repeat-x;
                                                    color: #990000;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-align:center;
                                                    }
                                                    .tabla .modo2 td {
                                                    padding: 5px;
                                                    }
                                                    .tabla .modo2 th {
                                                    background-position: left top;
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #fdfdf1;
                                                    background-repeat: repeat-x;
                                                    color: #990000;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-align:left;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #EBE9BC;
                                                    border-bottom-color: #EBE9BC;
                                                    }
                                                    </style>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . '/5169_IMG_0400.PNG">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table class="tabla">
                                                        <tbody>
                                                        <tr>
                                                            <th>
                                                                Pto Vta
                                                            </th>
                                                            <th>
                                                                Prod.
                                                            </th>
                                                            <th>
                                                                Cant.
                                                            </th>
                                                            <th>
                                                                Valor
                                                            </th>
                                                            <th>
                                                                Fecha
                                                            </th>
                                                            <th>
                                                                Num Fac
                                                            </th>
                                                            <th>
                                                                Propina
                                                            </th>
                                                            <th>
                                                                Total
                                                            </th>
                                                        </tr>
                                                        ' . $fila_movimiento . '

                                                        </tbody>
                                                    </table>

                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';
        }

        $factura["WebViewInterno"] = "S";
        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN EL RINCON ... */

    /* ... GUN ... */
    public function get_factura_ftp_gun($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSGUN_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("_", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[1];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        } else {
            echo "no existe";
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $IDSocio;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["WebViewInterno"] = "S";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        //Consulto los movimientos si no tiene fecha traigo los del dia
        if (empty($FechaInicio)) {
            $fecha = date('Y-m-j');
            $nuevafecha = strtotime('-15 day', strtotime($fecha));
            $nuevafecha = date('Y-m-j', $nuevafecha);
            $FechaInicio = $nuevafecha;
            $FechaFin = date("Y-m-d");
        }
        //$array_movimientos=SIMWebServiceZeus::consulta_movimientov2($IDClub,$IDSocio,$FechaInicio,$FechaFin);

        foreach ($array_movimientos as $id_movimiento => $datos_movimiento) :
            $nombre_categoria = "Movimientos";
            $fecha_extracto = "Fecha ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "Movimientos|" . $FechaInicio . "|" . $FechaFin . "|" . $IDSocio;
            $factura["NumeroFactura"] = "fecha vencimiento: " . $datos_movimiento["Vencimiento"];

            $array_descripcion = explode("/", $datos_movimiento["Descripcion"]);
            $ultimo_elemento = end($array_descripcion);
            $Descripcion = substr($ultimo_elemento, 2);
            $findme = 'TURCOS';
            $pos = strpos($Descripcion, $findme);
            if ($pos === false) {
            } else {
                $Descripcion = "BANOS TURCOS";
            }

            $factura["Fecha"] = utf8_encode($Descripcion);
            $factura["ValorFactura"] = "$" . $datos_movimiento["Valor"];
            $factura["Almacen"] = "";
            $factura["Url"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "S";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_gun($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $response = array();
        $datos_consulta = explode("|", $IDFactura);
        if ($datos_consulta[0] == "Movimientos") {
            $contador = 0;
            $array_movimientos = SIMWebServiceZeus::consulta_movimientov2($IDClub, $datos_consulta[3], $datos_consulta[1], $datos_consulta[2]);
            foreach ($array_movimientos as $id_movimiento => $datos_movimiento) :
                $array_descripcion = explode("/", $datos_movimiento["Descripcion"]);
                $ultimo_elemento = end($array_descripcion);
                $Descripcion = substr($ultimo_elemento, 2);

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $valores_movimiento .= "<tr style='font-size:12px' bgcolor='" . $fondo . "' >
						                                <td>" . $datos_movimiento["Fecha"] . "</td>
						                                <td>" . $Descripcion . "</td>
						                                <td>" . $datos_movimiento["Vencimiento"] . "</td>
						                                <td>" . "$" . $datos_movimiento["Valor"] . "</td>
						                                </tr>";
                $contador++;
            endforeach;

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                             <html>
                                             <head>
                                             <meta charset="UTF-8">
                                             <title>Detalle Extracto</title>

                                             </head>
                                             <body>
                                                 <table align="center">
                                                     <tr>
                                                         <td align="center">
                                                         <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td align="center">

                                                        <table>
                                                            <tr style="font-size:12px" bgcolor="#467aa7">
                                                                <td>Fecha</td>
                                                                <td>Descripcion</td>
                                                                <td>Vencimiento</td>
                                                                <td>Valor</td>
                                                            </tr>
                                                            ' . $valores_movimiento . '
                                                        </table>
                                                         </td>
                                                     </tr>
                                                 </table>
                                             </body>
                                         </html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = 0;
            $factura["BotonPago"] = "N";
            $factura["WebViewInterno"] = "N";
            $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
            $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
            $extracto["Accion"] = $datos_socio["Accion"];
            $extracto["Action"] = "https://www.gunclub.com.co/pago-app/";

            array_push($response, $factura);
        } else {

            $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>

                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                      <!--<a href="https://www.gunclub.com.co/pago-app/' . $parametros . '">-->
                                                                         <a href="https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432">
                                                                         <img src="https://www.miclubapp.com/img/iconos/pagargun.png">
                                                                     </a>
                                                                 </td>
                                                             </tr>
                                                         </table>
                                                     </body>
                                                 </html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = 0;
            $factura["BotonPago"] = "N";
            $factura["WebViewInterno"] = "S";
            $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
            $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
            $extracto["Accion"] = $datos_socio["Accion"];
            //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
            $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

            array_push($response, $factura);
        }

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN GUN ... */

    /* ... LA SABANA ... */
    public function get_factura_sabana($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {

        $dbo = &SIMDB::get();

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        if ($IDSocio == 197929) {
            $IDSocio = 198611;
        }

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        $documento_socio = "";
        $accion_socio = $datos_socio["AccionPadre"];
        $secuencia_socio = "00";

        /*
        $Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_SABANA, USUARIOZEUS_SABANA, CLAVEZEUS_SABANA);

        $array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_SABANA, $Token, $documento_socio, $accion_socio, $secuencia_socio);
        $valor = str_replace(',', '.', $array_estado->item->saldocartera);

        // print_r($array_estado);

        $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = $IDSocio;
        $factura["NumeroFactura"] = $accion_socio;
        $factura["Fecha"] = date("Y-m-d");
        $factura["ValorFactura"] = "$" . number_format($valor, 0, '', '.');

        $array_categoria_factura[$nombre_categoria][] = $factura;
        */

        /* $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = $IDSocio;
        $factura["NumeroFactura"] = $accion_socio;
        $factura["Fecha"] = date("Y-m-d");
        $factura["ValorFactura"] = "Pagar";
        $array_categoria_factura[$nombre_categoria][] = $factura; */

        //FIN Extractos pdf
        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSSABANA_DIR;

        //$AccionSocio="0003";

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("-", $archivo_carpeta);
                                    //$accion_socio_pdf = str_replace(".pdf", "", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        //echo EXTRACTOSTMP_ROOT.$nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;


        //FIN Extractos pdf

        $response = array();
        $response_categoria = array();

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_sabana($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();
        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $pos = strpos($IDFactura, ".pdf");
        if ($pos === false) { //Muestra Extracto

            if ($IDFactura == 197929) {
                $IDFactura = 198611;
            }

            if ($TipoApp == "Empleado") {
                $datos_socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDFactura . "' ", "array");
            } else {

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDFactura . "' ", "array");

                $documento_socio = "";
                $accion_socio = $datos_socio["AccionPadre"];
                $secuencia_socio = "00";

                //$Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_SABANA, USUARIOZEUS_SABANA, CLAVEZEUS_SABANA);
                //$array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_SABANA, $Token, $documento_socio, $accion_socio, $secuencia_socio);
                $valor = str_replace(',', '.', $array_estado->item->saldocartera);
            }

            $detalle_producto = "<tbody>
                              <td>$" . number_format($valor, 0, ',', '.') . "</td>
                          </tr></tbody>";

            $datos_factura = '<br>
                          <br><br>
                          <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                              <tr>
                                  <td colspan="5" >
                                   <table border="0" width="100%">
                                      <tr>
                                          <th>Valor</th>
                                      </tr>
                                      ' . $detalle_producto . '
                                  </table>
                                  </td>
                              </tr>
                          </table>
                          <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'zonapagosv2.php" method="post">
                              <table align="center">
                                  <tr>
                                      <td align="center">
                                          <!-- Pagar completo?<br>
                                          <input type="radio" name="Completo" value="S" checked>Si
                                          <input type="radio" name="Completo" value="N">No -->

                                          <br><label> Valor a pagar </label>
                                          <input type="text" name="valorPago" placeholder = "Valor a pagar"/>
                                      </td>
                                  </tr>
                                  <tr>
                                  </tr>
                                  <tr>
                                      <td align="center">
                                      Debito desde cuenta corriente/ahorros.<br>
                                      <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                      </td>
                                  </tr>
                              </table>
                              <input type="hidden" name="descripcion" id="descripcion" value="Pago Extracto Mi Club">
                              <input type="hidden" name="valor" id="valor" value="' . $valor . '">
                              <input type="hidden" name="extra2" id="extra2" value="' . $datos_club["IDClub"] . '">
                              <input type="hidden" name="IDSocio" id="IDSocio" value="' . $IDFactura . '">
                              <input type="hidden" name="emailComprador" id="emailComprador" value="' . $datos_socio["CorreoElectronico"] . '">
                              <input type="hidden" name="refVenta" id="refVenta" value="' . $IDFactura . '">
                              <input type="hidden" name="Modulo" id="Modulo" value="Consumos">
                              <input type="hidden" name="Completo" value="N">
                          </form>

                          ';

            $cuerpo_factura = '<!doctype html>
                                      <html>
                                      <head>
                                      <meta charset="UTF-8">
                                      <title>Detalle Factura</title>
                                      <style>
                                      .tabla {
                                      font-family: Verdana, Arial, Helvetica, sans-serif;
                                      font-size:12px;
                                      text-align: center;
                                      width: 95%;
                                      align: center;
                                      }

                                      .tabla th {
                                      padding: 5px;
                                      font-size: 12px;
                                      background-color: #83aec0;
                                      background-repeat: repeat-x;
                                      color: #FFFFFF;
                                      border-right-width: 1px;
                                      border-bottom-width: 1px;
                                      border-right-style: solid;
                                      border-bottom-style: solid;
                                      border-right-color: #558FA6;
                                      border-bottom-color: #558FA6;
                                      font-family: "Trebuchet MS", Arial;
                                      text-transform: uppercase;
                                      }

                                      .tabla .modo1 {
                                      font-size: 10px;
                                      font-weight:bold;
                                      background-color: #e2ebef;
                                      background-repeat: repeat-x;
                                      color: #34484E;
                                      font-family: "Trebuchet MS", Arial;
                                      }
                                      .tabla .modo1 td {
                                      padding: 5px;
                                      border-right-width: 1px;
                                      border-bottom-width: 1px;
                                      border-right-style: solid;
                                      border-bottom-style: solid;
                                      border-right-color: #A4C4D0;
                                      border-bottom-color: #A4C4D0;
                                      text-align:right;
                                      }

                                      .tabla .modo1 th {
                                      background-position: left top;
                                      font-size: 12px;
                                      font-weight:bold;
                                      text-align: left;
                                      background-color: #e2ebef;
                                      background-repeat: repeat-x;
                                      color: #34484E;
                                      font-family: "Trebuchet MS", Arial;
                                      border-right-width: 1px;
                                      border-bottom-width: 1px;
                                      border-right-style: solid;
                                      border-bottom-style: solid;
                                      border-right-color: #A4C4D0;
                                      border-bottom-color: #A4C4D0;
                                      }

                                      .tabla .modo2 {
                                      font-size: 12px;
                                      font-weight:bold;
                                      background-color: #fdfdf1;
                                      background-repeat: repeat-x;
                                      color: #990000;
                                      font-family: "Trebuchet MS", Arial;
                                      text-align:center;
                                      }
                                      .tabla .modo2 td {
                                      padding: 5px;
                                      }
                                      .tabla .modo2 th {
                                      background-position: left top;
                                      font-size: 12px;
                                      font-weight:bold;
                                      background-color: #fdfdf1;
                                      background-repeat: repeat-x;
                                      color: #990000;
                                      font-family: "Trebuchet MS", Arial;
                                      text-align:left;
                                      border-right-width: 1px;
                                      border-bottom-width: 1px;
                                      border-right-style: solid;
                                      border-bottom-style: solid;
                                      border-right-color: #EBE9BC;
                                      border-bottom-color: #EBE9BC;
                                      }

                                      .boton_personalizado{
                                      text-decoration: none;
                                      padding: 10px;
                                      font-weight: 600;
                                      font-size: 20px;
                                      color: #ffffff;
                                      background-color: #1883ba;
                                      border-radius: 6px;
                                      border: 2px solid #0016b0;
                                      }

                                      </style>
                                      </head>
                                      <body>';

            $cuerpo_factura .= $datos_factura;
            $cuerpo_factura .= '

                  </body>
                  </html>';
        } else {
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");


            $response = array();
            $datos_consulta = explode("|", $IDFactura);

            //$datos_socio["NumeroDocumento"]="19417661";

            $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                                       <html>
                                                       <head>
                                                       <meta charset="UTF-8">
                                                       <title>Detalle Extracto</title>

                                                       </head>
                                                       <body>
                                                           <table align="center">
                                                               <tr>
                                                                   <td align="center">
                                                                   <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                   </td>
                                                               </tr>
                                                               <tr>
                                                                   <td align="center">
                                                                   <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                   <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                   </a><br><br>
                                                                   </td>
                                                               </tr>
                                                               <tr>
                                                                   <td>
                                                                       <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                   </td>
                                                               </tr>

                                                               <tr>
                                                                   <td align="center">   <br>
                                                                         <!--<a href="https://www.zonapagos.com/t_Clubcampestresabana">-->
                                                                         <a href="https://portalpagos.davivienda.com/#/comercio/6263/CLUB%20CAMPESTRE%20LA%20SABANA">                                                                         
                                                                          <img src="' . URLROOT . 'plataform/assets/img/logo_90pse.png">
                                                                         </a>

                                                                   </td>
                                                               </tr>
                                                           </table>
                                                       </body>
                                                   </html>';
        }





        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $valor;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN LA SABANA ... */

    /* ... ARRAYANES COLOMBIA ... */
    /* public function get_factura_ftp_arrayanes($IDClub, $IDSocio, $FechaInicio, $FechaFin, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        if ($TipoApp == "Empleado") {
            $AccionSocio = $dbo->getFields("Usuario", "NumeroDocumento", "IDUsuario = '" . $IDSocio . "'");
        } else {
            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        }

        $RutaExtractos = EXTRACTOSARRAYANES_DIR;

        //$AccionSocio="001";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {

                                    if ($archivo == "empleados") {
                                        //echo $RutaExtractos.$archivo."/".$archivo_carpeta;
                                        //exit;

                                    }

                                    //SubCarpeta
                                    if (is_dir($RutaExtractos . $archivo . "/" . $archivo_carpeta)) {
                                        if ($carpeta_mes_sub = opendir($RutaExtractos . $archivo . "/" . $archivo_carpeta)) {
                                            while (($archivo_carpeta_sub = readdir($carpeta_mes_sub)) !== false) {
                                                if ($archivo_carpeta_sub != '.' && $archivo_carpeta_sub != '..' && $archivo_carpeta_sub != '.htaccess') {
                                                    $array_nombre_archivo_sub = explode(".", $archivo_carpeta_sub);
                                                    $accion_socio_pdf = $array_nombre_archivo_sub[0];
                                                    if ($AccionSocio == $accion_socio_pdf) :
                                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta . "/" . $archivo_carpeta_sub;
                                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                                        copy($origen_copy, $destino_copy);
                                                        $array_pdf[$archivo] = $nombre_encriptado;
                                                    endif;
                                                }
                                            }
                                        }
                                    }

                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            if ($TipoApp == "Empleado") {
                $nombre_categoria = "Certificado";
                $fecha_extracto = "Consulte su Certificado de Ingresos y Retenciones";
            } else {
                $nombre_categoria = "Extractos";
                $fecha_extracto = "Consulte su último extracto aquí: " . SIMResources::$meses[((int) substr($fecha, 5, 2) - 1)] . " de " . substr($fecha, 0, 4);
            }
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_arrayanes($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        if ($TipoApp == "Empleado") {
            $datos_socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $NumeroFactura . "' ", "array");
        } else {
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
            $Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_ARRAYANES, USUARIOZEUS_ARRAYANES, CLAVEZEUS_ARRAYANES);
            $array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_ARRAYANES, $Token, $datos_socio["NumeroDocumento"], $accion_socio, $secuencia_socio);
            $valor = str_replace(',', '.', $array_estado->item->saldocartera);
        }

        $response = array();
        $datos_consulta = explode("|", $IDFactura);

        //$datos_socio["NumeroDocumento"]="19417661";

        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
        $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>

                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                         Saldo en cartera: $' . $valor . '<br>
                                                                        <a href="' . URLROOT . 'credibanco.php?IDSocio=' . $datos_socio["IDSocio"] . '&valor=' . $valor . '&extra2=' . $IDClub . '&Modulo=Extracto">
                                                                             <img src="https://www.miclubapp.com/img/iconos/pagargun.png">
                                                                         </a>
                                                                 </td>
                                                             </tr>
                                                         </table>
                                                     </body>
                                                 </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = 0;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
        $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
        $extracto["Accion"] = $datos_socio["Accion"];
        //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
        $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } */

    public function get_factura_arrayanes_Zeus($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $TipoApp = "")
    {

        $dbo = &SIMDB::get();


        if ($IDSocio == 45115 || $IDSocio == 5533)
            $IDSocio = 31919;

        if ($TipoApp == "Empleado") {
            $AccionSocio = $dbo->getFields("Usuario", "NumeroDocumento", "IDUsuario = '" . $IDSocio . "'");
            $TituloBoton = "certificado";
        } else {
            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            $TituloBoton = "Extracto";
        }

        $urlendpoint = URLZEUS_ARRAYANESPOS;
        $usuariuozeus = USUARIOZEUS_ARRAYANESPOS;
        $clavezeus = CLAVEZEUS_ARRAYANESPOS;

        $response = array();
        $response_categoria = array();

        $RutaExtractos = EXTRACTOSARRAYANES_DIR;

        // PROCESO EXTRACTO
        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {

                                    if ($archivo == "empleados") {
                                        //echo $RutaExtractos.$archivo."/".$archivo_carpeta;
                                        //exit;

                                    }

                                    //SubCarpeta
                                    if (is_dir($RutaExtractos . $archivo . "/" . $archivo_carpeta)) {
                                        if ($carpeta_mes_sub = opendir($RutaExtractos . $archivo . "/" . $archivo_carpeta)) {
                                            while (($archivo_carpeta_sub = readdir($carpeta_mes_sub)) !== false) {
                                                if ($archivo_carpeta_sub != '.' && $archivo_carpeta_sub != '..' && $archivo_carpeta_sub != '.htaccess') {
                                                    $array_nombre_archivo_sub = explode(".", $archivo_carpeta_sub);
                                                    $accion_socio_pdf = $array_nombre_archivo_sub[0];
                                                    if ($AccionSocio == $accion_socio_pdf) :
                                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta . "/" . $archivo_carpeta_sub;
                                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                                        copy($origen_copy, $destino_copy);
                                                        $array_pdf[$archivo] = $nombre_encriptado;
                                                    endif;
                                                }
                                            }
                                        }
                                    }

                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            if ($TipoApp == "Empleado") {
                $nombre_categoria = "Certificado";
                $fecha_extracto = "Por decreto 1625 de 2016 el presente certificado no exige firma autógrafa.";
            } else {
                $nombre_categoria = "Se encuentra consultando el extracto del mes inmediatamente anterior a la fecha actual";
                $fecha_extracto = "Consulte su último extracto aquí: " . SIMResources::$meses[((int) substr($fecha, 5, 2) - 1)] . " de " . substr($fecha, 0, 4);
            }
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "OTRO|" . $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = $TituloBoton;
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        // PROCESO CONSUMOS ZEUS

        if (empty($FechaInicio))
            $FechaInicio = date("Ymd");
        else
            $FechaInicio = date("Ymd", strtotime($FechaInicio));

        if (empty($FechaFin))
            $FechaFin = date("Ymd");
        else
            $FechaFin = date("Ymd", strtotime($FechaFin));

        $Accion = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");

        if (strtotime($FechaFin) < strtotime($FechaInicio) || strtotime($FechaFin) > strtotime(date("Ymd"))) :
            $nombre_categoria = "LAS FECHAS SON INCORRECTAS";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "";
            $factura["NumeroFactura"] = "";
            $factura["Color"] = "#FF0000";
            $factura["Fecha"] = "";
            $factura["ValorFactura"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        else :


            if ($TipoApp != "Empleado") {

                $Token = SIMWebServiceZeus::obtener_token_club_curl($urlendpoint, $usuariuozeus, $clavezeus);
                $DATOS = SIMWebServiceZeus::movimientos($urlendpoint, $Token, $Accion, $FechaInicio, $FechaFin);

                // print_r($DATOS);
                // exit;
                $nombre_categoria = "Consumos";
                $Total = 0;

                if (!empty($DATOS->socio)) :
                    foreach ($DATOS->socio[0]->cuentas->cuenta as $ids => $consumos) :

                        $response_campos = array();
                        $total = 0;

                        foreach ($consumos->detalleconsumos->consumos as $id => $detalle) :
                            $total += $detalle->total;
                            $fecha = $detalle->fecharegis;
                        endforeach;

                        $cuenta = str_replace("-", "", $consumos->idcuenta);

                        $factura["IDClub"] = $IDClub;
                        $factura["IDFactura"] = "ZEUS|0|" . $Accion . "|" . $FechaInicio . "|" . $FechaFin;
                        $factura["NumeroFactura"] = (string)$cuenta;
                        $factura["Color"] = "";
                        $factura["Fecha"] = (string)$fecha;
                        $factura["ColorFecha"] = "";
                        $factura["ValorFactura"] = "$" . number_format((float)$total, 2, '.', ',');

                        $datos_campos["Texto"] = "";
                        $datos_campos["Color"] = "";

                        array_push($response_campos, $datos_campos);

                        $factura["Campos"] = $response_campos;

                        $Total += $total;

                        $array_categoria_factura[$nombre_categoria][] = $factura;
                    endforeach;

                    foreach ($DATOS->socio[1]->cuentas->cuenta as $ids => $consumos) :

                        $response_campos = array();
                        $total = 0;

                        foreach ($consumos->detalleconsumos->consumos as $id => $detalle) :
                            $total += $detalle->total;
                            $fecha = $detalle->fecharegis;
                        endforeach;
                        $cuenta = str_replace("-", "", $consumos->idcuenta);

                        $factura["IDClub"] = $IDClub;
                        $factura["IDFactura"] = "ZEUS|1|" . $Accion . "|" . $FechaInicio . "|" . $FechaFin;
                        $factura["NumeroFactura"] = (string)$cuenta;
                        $factura["Color"] = "";
                        $factura["Fecha"] = (string)$fecha;
                        $factura["ColorFecha"] = "";
                        $factura["ValorFactura"] = "$" . number_format((float)$total, 2, '.', ',');

                        $datos_campos["Texto"] = "";
                        $datos_campos["Color"] = "";

                        array_push($response_campos, $datos_campos);

                        $factura["Campos"] = $response_campos;

                        $Total += $total;

                        $array_categoria_factura[$nombre_categoria][] = $factura;
                    endforeach;
                else :

                    $response_campos = array();

                    $nombre_categoria = "NO HAY FACTURAS PARA LA FECHA";
                    $factura["IDClub"] = $IDClub;
                    $factura["IDFactura"] = "";
                    $factura["NumeroFactura"] = "";
                    $factura["Color"] = "#FF0000";
                    $factura["Fecha"] = "";
                    $factura["ColorFecha"] = "#00AA00";
                    $factura["ValorFactura"] = "";

                    $datos_campos["Texto"] = "$dato[Tipo]";
                    $datos_campos["Color"] = "#00AA00";
                    array_push($response_campos, $datos_campos);

                    $factura["Campos"] = $response_campos;

                    $Total += $dato[Total];

                    $array_categoria_factura[$nombre_categoria][] = $factura;
                endif;
            }
        endif;

        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        if ($Dispositivo == "iOS") {
            $datos_facturas["BuscadorFechas"] = "S";
        } else {
            $datos_facturas["BuscadorFechas"] = "S";
        }

        $Total = number_format((float)$Total, 2, '.', ',');

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = $Total;
        $datos_facturas["ValorTotalLabel"] = "Total Consumos";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PrecargarFechaHoyBuscador"] = "S";

        array_push($response, $datos_facturas);

        $respuesta["message"] = "Factura";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_arrayanes_zeus($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        $datos = explode("|", $IDFactura);
        $Tipo = $datos[0];
        $IDFactura = $datos[1];

        if ($Tipo == "ZEUS") :

            $urlendpoint = URLZEUS_ARRAYANESPOS;
            $usuariuozeus = USUARIOZEUS_ARRAYANESPOS;
            $clavezeus = CLAVEZEUS_ARRAYANESPOS;



            $id = (int)$datos[1];
            $Accion = $datos[2];
            $FechaInicio = $datos[3];
            $FechaFin = $datos[4];

            $Token = SIMWebServiceZeus::obtener_token_club_curl($urlendpoint, $usuariuozeus, $clavezeus);
            $DATOS = SIMWebServiceZeus::movimientos($urlendpoint, $Token, $Accion, $FechaInicio, $FechaFin);

            $response = array();

            $detalle_producto .=
                "<tbody>
                    <tr>
                        <th>Nombre</th>
                        <th>Cant</th>
                        <th>Valor</th>
                        <th>Total</th>
                        <th>Nombre Caja</th>
                        <th>Fecha Factura</th>
                        <th>Propina</th>
                    </tr>";

            foreach ($DATOS->socio[$id]->cuentas->cuenta as $ids => $consumos) :

                $cuenta = str_replace("-", "", $consumos->idcuenta);
                if (trim((string)$cuenta) == trim($NumeroFactura)) :
                    foreach ($consumos->detalleconsumos->consumos as $id => $detalle) :

                        $total = $detalle->cantidad * $detalle->valor;

                        $Nombre = $detalle->nombre;
                        $Cantidad = $detalle->cantidad;
                        $Valor = number_format((float)$detalle->valor, 2, '.', ',');
                        $Total = number_format((float)$total, 2, '.', ',');
                        $NombreCaja = $detalle->nombrecaja;
                        $FechaFactura = date("Y-m-d", strtotime($detalle->fechafactura));
                        $Propina = number_format((float)$detalle->propina, 2, '.', ',');

                        $detalle_producto .= "
                                <tr class=modo1>
                                    <td>" . $Nombre . "</td>
                                    <td>" . $Cantidad . "</td>
                                    <td>$" . $Valor . "</td>
                                    <td>$" . $Total . "</td>
                                    <td>" . $NombreCaja . "</td>
                                    <td>" . $FechaFactura . "</td>
                                    <td>$" . $Propina . "</td>
                                </tr>";

                    endforeach;

                    $FormaPago = $consumos->pagos->pago->formapago;
                endif;
            endforeach;

            $detalle_producto .= "</tbody>";

            $cabeza_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                    <tr class="modo2">
                        <td><font color=#ffffff>Acción Socio: ' . $Accion . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td><font color=#ffffff>Cuenta: ' . $NumeroFactura . '</font></td>
                    </tr>
                </tbody>
            </table>';

            $detalle_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tr>
                    <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                    </td>
                </tr>
            </table>';

            $formpago =
                '<table border="0"cellpadding="0"cellspacing="0"class="tabla">
                <tr>
                    <td>
                        <table border="0"width="100%">
                            <tbody>
                                <tr>
                                    <th>Forma de Pago</th>
                                </tr>
                                <tr class=modo1>
                                    <td>' . $FormaPago . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>';


            $cuerpo_factura =
                '<!doctype html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Detalle Factura</title>
                    <style>
                        .tabla {
                            font-family: Verdana, Arial, Helvetica, sans-serif;
                            font-size: 12px;
                            text-align: center;
                            width: 95%;
                            align: center;
                        }


                        .tabla th {
                            padding: 5px;
                            font-size: 12px;
                            background-color: #ff0303;
                            background-repeat: repeat-x;
                            color: #FFFFFF;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            text-transform: uppercase;
                        }

                        .tabla .modo1 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #ffffff;
                            background-repeat: repeat-x;
                            color: #34484E;
                            font-family: "Trebuchet MS", Arial;
                        }

                        .tabla .modo1 td {
                            padding: 5px;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #cfcfca;
                            border-bottom-color: #cfcfca;
                            text-align: left;
                        }

                        .tabla .modo1 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            text-align: right;
                            background-color: #8f8f8c;
                            background-repeat: repeat-x;
                            color: #8f8f8c;
                            font-family: "Trebuchet MS", Arial;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #8f8f8c;
                            border-bottom-color: #8f8f8c;
                        }

                        .tabla .modo2 {
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #ff0303;
                            background-repeat: repeat-x;
                            color: #4f4d4d;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                        }

                        .tabla .modo2 td {
                            padding: 5px;
                        }

                        .tabla .modo2 th {
                            background-position: left top;
                            font-size: 12px;
                            font-weight: bold;
                            background-color: #fdfdf1;
                            background-repeat: repeat-x;
                            color: #990000;
                            font-family: "Trebuchet MS", Arial;
                            text-align: left;
                            border-right-width: 1px;
                            border-bottom-width: 1px;
                            border-right-style: solid;
                            border-bottom-style: solid;
                            border-right-color: #EBE9BC;
                            border-bottom-color: #EBE9BC;
                        }

                        .boton_personalizado{
                            text-decoration: none;
                            padding: 10px;
                            font-weight: 600;
                            font-size: 20px;
                            color: #ffffff;
                            background-color: #8f8f8c;
                            border-radius: 6px;
                            border: 2px solid #cfcfca;
                        }

                        .boton_personalizado:hover{
                        color: #cfcfca;
                        background-color: #ffffff;
                        }

                    </style>
                </head>
                <body>';

            $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $formpago . "<br>";
            $cuerpo_factura .= '</body></html>';

        else :
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

            if ($TipoApp == "Empleado") {
                $datos_socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $NumeroFactura . "' ", "array");
            } else {
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
                //$Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_ARRAYANES, USUARIOZEUS_ARRAYANES, CLAVEZEUS_ARRAYANES);
                //$array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_ARRAYANES, $Token, $datos_socio["NumeroDocumento"], $accion_socio, $secuencia_socio);
                //$valor = str_replace(',', '.', $array_estado->item->saldocartera);
            }

            $response = array();
            $datos_consulta = explode("|", $IDFactura);



            $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                <html>
                                    <head>
                                        <meta charset="UTF-8">
                                        <title>Detalle Extracto</title>
                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td align="center">
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">';

            if ($TipoApp != "Empleado") {
                $cuerpo_factura .= '<img src="' . URLROOT . 'img/iconos/descargaextracto.png">';
            } else {
                $cuerpo_factura .= 'Descargar Certificado';
            }

            $cuerpo_factura .= '</a><br><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </td>
                                            </tr>';


            if ($TipoApp != "Empleado") {
                $cuerpo_factura .= '<tr>
                                                <td align="center">
                                                        Saldo en cartera: $' . $valor . '<br>
                                                        <a href="' . URLROOT . 'credibanco.php?IDSocio=' . $datos_socio["IDSocio"] . '&valor=' . $valor . '&extra2=' . $IDClub . '&Modulo=Extracto">
                                                            <img src="https://www.miclubapp.com/img/iconos/pagargun.png">
                                                        </a>
                                                </td>';
            } else {
                $cuerpo_factura .= ' <td align="center">
                                                Por decreto 1625 de 2016 el presente certificado no exige firma autógrafa.:<br>                                                        
                                                </td>';
            }


            $cuerpo_factura .= '
                                            </tr>
                                        </table>
                                    </body>
                                </html>';

            $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
            $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
            $extracto["Accion"] = $datos_socio["Accion"];
            //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
            $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

        endif;

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (float) $Total;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        array_push($response, $factura);

        $respuesta["message"] = "DETALLE";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN ARRAYANES COLOMBIA ... */
    /*COUNTRY CLUB MEDELIN DETALLE FACTURA */
    public function get_detalle_factura_country_medellin($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio, $Tipo)
    {


        $dbo = &SIMDB::get();
        $WebInterno = "S";
        $datos_club = $dbo->fetchAll(
            "Club",
            " IDClub = '" . $IDClub . "' ",
            "array"
        );


        $response = array();


        $detalle_producto .=
            "<tbody>
            <tr>
 
                <th>Concepto</th>
                <th>Valor</th>
                <th>Cantidad</th>
 
                <th> Total </th>
            </tr>";

        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio' LIMIT 1";
        $datos = $dbo->query($datos_socios);
        while ($row = $dbo->fetchArray($datos)) {
            $token = $row["TokenCountryMedellin"];
        }
        if ($Tipo == "cuentasabiertas") {
            $familiares = "true";

            $cadena = $NumeroFactura;
            $separador = "-";
            $separada = explode($separador, $cadena);
            $separada1 = json_encode($separada);
            $separada2 = json_decode($separada1);



            $codigoMesa =  $separada2[0];
            $sala =  $separada2[1];

            /*
$cadena = $NumeroFactura;
$separador = "-";
$separada = explode($separador, $cadena);
$separada1= json_encode($separada);
$separada2= json_decode($separada1);
echo $separada2[1];

*/


            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarMesasAbiertas($familiares, $codigoMesa, $sala, $token);

            $rows = array();
            $nombre_categoria = "Cartera";
            $resultado = json_decode($resultado1, true);



            $codigo = $resultado['ConsumoMesa']["mesa"];
            $nombre = $resultado['ConsumoMesa']["nombresCliente"];
            $ubicacion = $resultado['ConsumoMesa']["descripcionUbicacion"];
            $fecha = $resultado['ConsumoMesa']["fecha"];
            $permitepagar = $resultado['ConsumoMesa']["permitePagar"];
            $valor = $resultado['ConsumoMesa']["valorPago"];
            $propina = $resultado['ConsumoMesa']["propina"];
            $detalles = $resultado['ConsumoMesa']["detalle"]["DetalleConsumo"];



            $fecha = date("Y-m-d");

            $detalle = $resultado['ConsumoMesa']["detalle"]["DetalleConsumo"];


            //cuento cuantos detallesConsumo tiene cada socio, que serian cuantos productos consumio u pidio.
            $cantidad = count($detalle);
            $cantidad1 = count($detalle . ["valor"]);
            $cantidad2 = count($detalle);
            if ($cantidad == 4) {
                $cantidad = 1;
            }

            if ($cantidad  != 1) {

                foreach ($detalle as $lista) {

                    $detalle_producto .= "
            
                            <tr class=modo1>
           
                                                                                                                  
                                
 
                                <td>" . $lista["descripcionProducto"]  .   "</td>
                                <td>" . $lista["valor"]  . "</td>
                                <td>" . $lista["cantidad"]  . "</td>
                     
 
                                <td>$" . number_format($lista["cantidad"]  * $lista["valor"], 0, ',', '.') . "</td>
                            </tr>";
                }
            } else {

                $detalle = $resultado['ConsumoMesa']["detalle"];

                //     foreach($resultado['ConsumoMesa'][$k]["detalle"] as $lista) {

                foreach ($detalle as $lista) {

                    $detalle_producto .= "
            
                            <tr class=modo1>
           
                                                                                                                  
                                
 
                                <td>" . $lista["descripcionProducto"]  .   "</td>
                                <td>" . $lista["valor"]  . "</td>
                                <td>" . $lista["cantidad"]  . "</td>
                     
 
                                <td>$" . number_format($lista["cantidad"]  * $lista["valor"], 0, ',', '.') . "</td>
                            </tr>";
                }
            }



            $detalle_producto .= "</tbody>";


            if (($resultado['ConsumoMesa']["permitePagar"]) == "true") {
                $ubicacion = $resultado['ConsumoMesa']["descripcionUbicacion"];
                $propina = $resultado['ConsumoMesa']["propina"];
                $totalpago = $resultado['ConsumoMesa']["valorPago"];
                $totalfin = ($totalpago - $propina);
            }

            $cabeza_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                    <tr class="modo2">
                        <td>Socio : <font color=#ffffff>' . $resultado['ConsumoMesa']["nombresCliente"] . '</font></td>
                    </tr>
                       <tr class="modo2">
                        <td>Ubicacion: <font color=#ffffff>'  . $ubicacion . $cantidad1 . $cantidad2 . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Fecha: <font color=#ffffff>' . $fecha .  '</font></td>
                    </tr>
                    
                   
                    <tr class="modo2">
                        <td>Valor Total: <font color=#ffffff>$ ' . number_format($totalfin) . '</font></td>
                    </tr>
                </tbody>
            </table>';
        } else {

            $familiares = "true";

            $fechaInicial = "01-01-2017";
            $fecha = date("d-m-Y");
            $fechaFinal = $fecha;
            $numeroConsumo = $NumeroFactura;
            $pagina = "1";


            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarConsumos($familiares, $numeroConsumo, $fechaInicial, $fechaFinal, $pagina, $token);
            //servicio que retorna el array del wifi de country medellin
            //$redeswifi = SIMWebServiceCountryMedellin::App_ConsultarRedes($token); 
            $rows = array();
            $nombre_categoria = "Historial De Consumos";
            $resultado = json_decode($resultado1, true);
            $ubicacion = $resultado['consumos']['Consumo']['descripcionUbicacion'];

            $cantidad = count($resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"]["descripcionProducto"]);
            if ($cantidad == 0) {
                $cantidad = count($resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"]);
            }
            if ($cantidad == 1) {

                foreach ($resultado['consumos']['Consumo']["detalle"] as $lista) {

                    $detalle_producto .= "
            
                            <tr class=modo1>
           
                                                                                                                  
                                
 
                                <td>" . $lista["descripcionProducto"]  .  "</td>
                                <td>" . $lista["valor"]  . "</td>
                                <td>" . $lista["cantidad"]   . "</td>
                     
 
                                <td>$" . number_format($lista["cantidad"] * $lista["valor"], 0, ',', '.') . "</td>
                            </tr>";
                }
            } else {
                for ($i = 0; $i < $cantidad; $i++) {



                    $detalle_producto .= "
            
                            <tr class=modo1>

           
                                                                                                                    
                                
 

                                <td>" . $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"][$i]["descripcionProducto"]  . "</td>
                                <td>" . $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"][$i]["valor"]  . "</td>
                                <td>" . $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"][$i]["cantidad"]   . "</td>

                     
 
                                <td>$" . number_format($resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"][$i]["cantidad"] * $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"][$i]["valor"], 0, ',', '.') . "</td>

                            </tr>";
                }
            }





            $detalle_producto .= "</tbody>";


            $cliente = $resultado['consumos']['Consumo']["nombresCliente"];
            $fecha = $resultado['consumos']['Consumo']["fecha"];
            $tipo_pago = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["tipoFormaPago"];
            //  $valor_pago = $resultado['consumos']['Consumo'][$j]["detalle"]["DetalleConsumo"]["valor"];
            //  $propina = $resultado['consumos']['Consumo'][$j]["detalle"]["DetalleConsumo"]["propina"];
            $valor_entregado = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["entregado"];





            $cabeza_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">

                <tbody>
                    <tr class="modo2">
                        <td>Socio : <font color=#ffffff>' .  $cliente . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Fecha: <font color=#ffffff>'  . $fecha . '</font></td>
                    </tr>
                       <tr class="modo2">
                        <td>Ubicacion: <font color=#ffffff>'  . $ubicacion . '</font></td>
                    </tr>
                     <tr class="modo2">
                        <td>Tipo de Pago: <font color=#ffffff>' . $tipo_pago . '</font></td>
                    </tr>
                   
                    <tr class="modo2">
                        <td>Valor Total: <font color=#ffffff>$ ' . number_format($valor_entregado) . '</font></td>
                    </tr>
                </tbody>
            </table>';
        }






        $cuerpo_factura =
            '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                    <tr>
                        <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                        </td>
                    </tr>
                </table>';

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");


        /* $boton =
            '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_ecollect.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="ValorPagar" id="ValorPagar" value="' . $Pagar . '">
                            <input type="hidden" name="IDClub" id="IDClub" value="' . $datos_club["IDClub"] . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $NumeroFactura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>

                        ';*/


        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
        $cuerpo_factura .= '</body></html>';



        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = $WebInterno;
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }


    /* ... CARMEL ... */
    public function get_factura_ftp_carmel($IDClub, $IDSocio, $FechaInicio, $FechaFin, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSCARMEL_DIR;

        //$AccionSocio="0003";

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("-", $archivo_carpeta);
                                    //$accion_socio_pdf = str_replace(".pdf", "", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        //echo EXTRACTOSTMP_ROOT.$nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $Indice = $archivo . "-" . $archivo_carpeta;
                                        $array_pdf[$Indice] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";
            $array_indice = explode("-", $fecha);
            $fecha_extracto = SIMResources::$meses[((int) substr($array_indice[0], 4, 2) - 1)] . " de " . substr($array_indice[0], 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_carmel($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");


        $response = array();
        $datos_consulta = explode("|", $IDFactura);

        //$datos_socio["NumeroDocumento"]="19417661";

        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
        $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>

                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                         Saldo en cartera: $' . $valor . '<br>
                                                                        <a href="' . URLROOT . 'credibanco.php?IDSocio=' . $datos_socio["IDSocio"] . '&valor=' . $valor . '&extra2=' . $IDClub . '&Modulo=Extracto">
                                                                             <img src="https://www.miclubapp.com/img/iconos/pagargun.png">
                                                                         </a>
                                                                 </td>
                                                             </tr>
                                                         </table>
                                                     </body>
                                                 </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = 0;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
        $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
        $extracto["Accion"] = $datos_socio["Accion"];
        //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
        $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CARMEL  ... */


    /* ... LA SABANA ... */
    public function get_factura_ftp_sabana($IDClub, $IDSocio, $FechaInicio, $FechaFin, $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSSABANA_DIR;

        //$AccionSocio="0003";

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode("-", $archivo_carpeta);
                                    //$accion_socio_pdf = str_replace(".pdf", "", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    //Comparo si el archivo pertenece al socio a consultar
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        //echo EXTRACTOSTMP_ROOT.$nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_sabana_new($IDClub, $IDFactura, $NumeroFactura, $TipoApp = "")
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");


        $response = array();
        $datos_consulta = explode("|", $IDFactura);

        //$datos_socio["NumeroDocumento"]="19417661";

        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
        $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>

                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                         </table>
                                                     </body>
                                                 </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = 0;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";

        $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
        $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
        $extracto["Accion"] = $datos_socio["Accion"];
        //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
        $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN LA SABANA  ... */

    /* ... SINDAMANOY ... */
    public function get_factura_ftp_sindamanoy($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 209359)
            $IDSocio = 348479;

        $AccionSocio = $dbo->getFields("Socio", "Predio", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSSINDAMANOY_DIR;

        //$AccionSocio="001";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $mes_carp = ((int) substr($fecha, 4, 2) - 1);
            //SIMResources::$meses[];
            $fecha_extracto = "Consulte su último extracto aquí: " . SIMResources::$meses[$mes_carp] . " de " . substr($fecha, 0, 4);
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_sindamanoy($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                <img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
                                                Descargar extracto
                                                </a><br><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN SINDAMANOY ... */

    /* ... PAYANDE ... */
    public function get_factura_ftp_payande($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        //$AccionSocio = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'" );
        $DocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSPAYANDE_DIR;

        //$AccionSocio="001";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Cedula Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($DocumentoSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Consumos";
            $fecha_extracto = "Consumos: " . SIMResources::$meses[((int) substr($fecha, 5, 2) - 1)] . " de " . substr($fecha, 0, 4);
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Consumo";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_payande($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
        $response = array();
        $datos_consulta = explode("|", $IDFactura);

        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
        $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>
                                                    <style type="text/css">
                                                  .boton_personalizado{
                                                    text-decoration: none;
                                                    padding: 10px;
                                                    font-weight: 600;
                                                    font-size: 20px;
                                                    color: #ffffff;
                                                    background-color: #3EC156;
                                                    border-radius: 6px;
                                                    border: 2px solid #0016b0;
                                                  }
                                                  .boton_personalizado:hover{
                                                    color: #1883ba;
                                                    background-color: #ffffff;
                                                  }
                                                </style>
                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                            <tr>
                                                                 <td align="center">
                                                                <form name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_payu.php" method="post">
                                                                    Digite el valor a pagar:
                                                                    <input type="number" name="Valor" id="Valor" ><br>
                                                                    <input type="hidden" name="IDClub" id="IDClub" value="27" >
                                                                    <input type="hidden" name="IDSocio" id="IDSocio" value="' . $NumeroFactura . '" >
                                                                    <input type="submit" name="pagar" id="pagar" Value="Pagar" class="boton_personalizado">
                                                                    <br><img src="' . URLROOT . 'plataform/assets/img/logopayu.png">
                                                                </form>

                                                                 </td>
                                                             </tr>

                                                         </table>
                                                     </body>
                                                 </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = 0;
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";
        $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
        $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
        $extracto["Accion"] = $datos_socio["Accion"];
        //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
        $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN PAYANDE ... */

    /* ... ANAPOIMA ... */
    public function get_factura_ftp_anapoima($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $NumeroDocumento = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSANAPOIMA_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($NumeroDocumento == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "ESTADO CUENTA";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Estado Cuenta";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_anapoima($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                <img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
                                                Descargar extracto
                                                </a><br>

                                                <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </td>
                                            </tr>

                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN ANAPOIMA ... */

    /* ... RANCHO ... */
    public function get_factura_ftp_r($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSRANCHO_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(";", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[1];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_rancho($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                <img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
                                                Descargar extracto
                                                </a><br><br>
                                                <a href="https://secure.payzen.lat/vads-site/Club_Campestre_El_Rancho">
                                                <img src="' . URLROOT . 'plataform/assets/img/btnpagopereira.png">
                                                </a>
                                                <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </td>
                                            </tr>

                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN RANCHO ... */

    /* ... BTCC ... */
    public function get_factura_ftp_btcc($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $array_accion = explode("-", $AccionSocio);
        if (empty($array_accion[1]) || $array_accion[1] == 1) {
            $AccionSocio = $array_accion[0];
        }

        $RutaExtractos = EXTRACTOSBTCC_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {

                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {

                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess' && $archivo != 'Donacion') {

                                    //Capturo Accion Socio
                                    $caracteres_nom = strlen($archivo_carpeta);
                                    switch ($caracteres_nom) {
                                        case '9':
                                            $accion_socio_pdf = "00" . substr($archivo_carpeta, 0, 1);
                                            break;
                                        case '10':
                                            $accion_socio_pdf = "0" . substr($archivo_carpeta, 0, 2);
                                            break;
                                        case '11':
                                            $accion_socio_pdf = substr($archivo_carpeta, 0, 3);
                                            break;
                                        default:
                                            $accion_socio_pdf = substr($archivo_carpeta, 0, 1);
                                            break;
                                    }
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        //Fundacion
        $RutaDonacion = EXTRACTOSBTCC_DIR . "/Donacion/";
        if (is_dir($RutaDonacion)) {
            if ($dir = opendir($RutaDonacion)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaDonacion . $archivo)) {
                        if ($carpeta_mes = opendir($RutaDonacion . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    $accion_socio_pdf = substr($archivo_carpeta, 0, 3);

                                    //Capturo Accion Socio
                                    $caracteres_nom = strlen($archivo_carpeta);
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        $ano_pdf = substr($archivo_carpeta, 3, 4);
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaDonacion . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf_donacion[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf_donacion);

        foreach ($array_pdf_donacion as $fecha => $archivo_extracto) :
            $nombre_categoria = "Certificado Donacion";
            $donacion["IDClub"] = $IDClub;
            $donacion["IDFactura"] = $archivo_extracto;
            $donacion["NumeroFactura"] = "Donacion";
            $donacion["Fecha"] = $ano_pdf;
            $donacion["ValorFactura"] = $ano_pdf;
            $donacion["Almacen"] = "";
            $donacion["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $donacion;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_btcc($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        if ($NumeroFactura == "Donacion") {
            $btnpago = "";
        } else {
            $btnpago = '<a href="https://www.zonapagos.com/t_Btcc">
                    <img src="' . URLROOT . 'plataform/assets/img/btnpagopereira.png">
                    </a>';
        }

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                ' . $btnpago . '
                                                <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </td>
                                            </tr>

                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN BTCC ... */

    /* ... POLO ... */
    public function get_factura_ftp_polo($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");

        if (!empty($FechaInicio)) :
            $condicion_fecha = " and Fecha >= '" . $FechaInicio . "'";
        endif;

        if (!empty($FechaFin)) :
            $condicion_fecha .= " and Fecha <= '" . $FechaFin . "'";
        endif;

        $sql_PagosPendientes = "SELECT * FROM SocioPagosPendientesPoloClub Where IDSocio = '" . $IDSocio . "' and Documento like '%TOTAL%' " .  $condicion_fecha . " group by IDSocio,Fecha Order By IDSocioPagosPendientesPoloClub DESC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        if ($dbo->rows($qry_PagosPendientes) > 0) {
            $response = array();
            $message = $dbo->rows($qry_PagosPendientes) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_PagosPendientes)) {
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = ($r["Fecha"]);
                $factura["NumeroFactura"] = ($r["Accion"]);
                $factura["Fecha"] = $r["Fecha"];
                $factura["ValorFactura"] = "$" . number_format(($r["Valor"]), 0, ",", ".");
                $array_categoria_factura["Factura"][] = $factura;
            }

            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            if ($Dispositivo == "iOS") {
                $datos_facturas["BuscadorFechas"] = false;
            } else {
                $datos_facturas["BuscadorFechas"] = "N";
            }

            $datos_facturas["Categorias"] = $response_categoria;
            $datos_facturas["TipoFormaPago"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;

        // $RutaExtractos = EXTRACTOSPOLO_DIR;

        // //$AccionSocio="001-0241-7-1";

        // self::borrar_extracto_tmp();

        // if (is_dir($RutaExtractos)) {
        //     if ($dir = opendir($RutaExtractos)) {
        //         while (($archivo = readdir($dir)) !== false) {
        //             if (is_dir($RutaExtractos . $archivo)) {
        //                 if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {

        //                     while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
        //                         if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess' && $archivo != 'Donacion') {

        //                             $array_nombre_arch = explode("-", $archivo_carpeta);

        //                             $accion_socio_pdf = $array_nombre_arch[0];

        //                             if ($AccionSocio == $accion_socio_pdf) :
        //                                 //$array_pdf[$archivo]=$archivo_carpeta;
        //                                 //lo copio con nombre encriptado para tenerlo temporalmente
        //                                 $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
        //                                 $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
        //                                 $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
        //                                 copy($origen_copy, $destino_copy);
        //                                 $array_pdf[$archivo] = $nombre_encriptado;
        //                             endif;
        //                         }
        //                     }
        //                 }
        //                 closedir($carpeta_mes);
        //             }
        //         }
        //         closedir($dir);
        //     }
        // }

        // krsort($array_pdf);

        // foreach ($array_pdf as $fecha => $archivo_extracto) :
        //     $nombre_categoria = "Extractos";
        //     $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
        //     $factura["IDClub"] = $IDClub;
        //     $factura["IDFactura"] = $archivo_extracto;
        //     $factura["NumeroFactura"] = $fecha;
        //     $factura["Fecha"] = $fecha_extracto;
        //     $factura["ValorFactura"] = "Extracto";
        //     $factura["Almacen"] = "";
        //     $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
        //     $array_categoria_factura[$nombre_categoria][] = $factura;

        // endforeach;

        // $response = array();
        // $response_categoria = array();
        // foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

        //     $datos_factura_categoria["Nombre"] = $nombre_categoria;
        //     $datos_factura_categoria["Facturas"] = $facturas;
        //     array_push($response_categoria, $datos_factura_categoria);
        // endforeach;

        // $datos_facturas["BuscadorFechas"] = "N";
        // $datos_facturas["Categorias"] = $response_categoria;

        // array_push($response, $datos_facturas);

        // $respuesta["message"] = $message;
        // $respuesta["success"] = true;
        // $respuesta["response"] = $response;

        // // cerrar la conexión ftp
        // ftp_close($conn_id);

        // return $respuesta;
    }



    public function get_factura_vencidas_pereira($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");


        if (!empty($FechaInicio)) :
            $condicion_fecha = " and Fecha >= '" . $FechaInicio . "'";
        endif;

        if (!empty($FechaFin)) :
            $condicion_fecha .= " and Fecha <= '" . $FechaFin . "'";
        endif;

        $sql_PagosPendientes = "SELECT * FROM CarteraVencida Where IDSocio = '" . $IDSocio . "' " . $condicion_fecha . " group by Fecha Order By  IDCarteraVencida  DESC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        if ($dbo->rows($qry_PagosPendientes) > 0) {
            $response = array();
            $message = $dbo->rows($qry_PagosPendientes) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_PagosPendientes)) {
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = ($r["IDCarteraVencida"]);
                $factura["NumeroFactura"] = ($r["Accion"]);
                $factura["NumeroFactura"] = ($r["Factura"]);
                $factura["Fecha"] = $r["Fecha"];
                $factura["ValorFactura"] = "$" . number_format(($r["Valor"]), 0, ",", ".");
                $array_categoria_factura["Factura"][] = $factura;
            }

            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            if ($Dispositivo == "iOS") {
                $datos_facturas["BuscadorFechas"] = false;
            } else {
                $datos_facturas["BuscadorFechas"] = "N";
            }

            $datos_facturas["Categorias"] = $response_categoria;
            $datos_facturas["TipoFormaPago"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;

        // $RutaExtractos = EXTRACTOSPOLO_DIR;

        // //$AccionSocio="001-0241-7-1";

        // self::borrar_extracto_tmp();

        // if (is_dir($RutaExtractos)) {
        //     if ($dir = opendir($RutaExtractos)) {
        //         while (($archivo = readdir($dir)) !== false) {
        //             if (is_dir($RutaExtractos . $archivo)) {
        //                 if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {

        //                     while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
        //                         if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess' && $archivo != 'Donacion') {

        //                             $array_nombre_arch = explode("-", $archivo_carpeta);

        //                             $accion_socio_pdf = $array_nombre_arch[0];

        //                             if ($AccionSocio == $accion_socio_pdf) :
        //                                 //$array_pdf[$archivo]=$archivo_carpeta;
        //                                 //lo copio con nombre encriptado para tenerlo temporalmente
        //                                 $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
        //                                 $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
        //                                 $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
        //                                 copy($origen_copy, $destino_copy);
        //                                 $array_pdf[$archivo] = $nombre_encriptado;
        //                             endif;
        //                         }
        //                     }
        //                 }
        //                 closedir($carpeta_mes);
        //             }
        //         }
        //         closedir($dir);
        //     }
        // }

        // krsort($array_pdf);

        // foreach ($array_pdf as $fecha => $archivo_extracto) :
        //     $nombre_categoria = "Extractos";
        //     $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
        //     $factura["IDClub"] = $IDClub;
        //     $factura["IDFactura"] = $archivo_extracto;
        //     $factura["NumeroFactura"] = $fecha;
        //     $factura["Fecha"] = $fecha_extracto;
        //     $factura["ValorFactura"] = "Extracto";
        //     $factura["Almacen"] = "";
        //     $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
        //     $array_categoria_factura[$nombre_categoria][] = $factura;

        // endforeach;

        // $response = array();
        // $response_categoria = array();
        // foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

        //     $datos_factura_categoria["Nombre"] = $nombre_categoria;
        //     $datos_factura_categoria["Facturas"] = $facturas;
        //     array_push($response_categoria, $datos_factura_categoria);
        // endforeach;

        // $datos_facturas["BuscadorFechas"] = "N";
        // $datos_facturas["Categorias"] = $response_categoria;

        // array_push($response, $datos_facturas);

        // $respuesta["message"] = $message;
        // $respuesta["success"] = true;
        // $respuesta["response"] = $response;

        // // cerrar la conexión ftp
        // ftp_close($conn_id);

        // return $respuesta;
    }





    public function get_detalle_factura_polo($IDClub, $IDFactura, $NumeroFactura, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        // $ruta_ext = EXTRACTOSPOLO_DIR . "/" . $IDFactura;
        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>
<style>*{margin:0;padding:0;box-content:border-box}td{text-align:center}.text-factura{font-size:80%}td{border-bottom:2px solid #e7e7e7;border-right:1px solid #e7e7e7;}</style>
                                    </head>
                                    <body>
                                        <table style="width:100%" border="0">
                                            <tr>
                                                <td colspan="4">
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                            <td colspan="4" style="text-align:center">' . $datos_socio['Accion'] . ' / ' . $datos_socio['Nombre'] . ' ' . $datos_socio['Apellido'] . '</td>
                                            </tr>
                                            <tr>
                                                    <!--<td><b>Acci&oacute;n</b></td>-->
                                                    <td><b>FECHA</b></td>
                                                    <td><b>CUOTAS, SERVICIOS Y CONSUMOS</b></td>
                                                    <td><b>VALOR</b></td>
                                            </tr>';

        $sql_PagosPendientes = "SELECT * FROM SocioPagosPendientesPoloClub Where IDSocio = '" . $IDSocio . "' AND Fecha = '" . $IDFactura . "' Order By IDSocioPagosPendientesPoloClub ASC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        if ($dbo->rows($qry_PagosPendientes) > 0) {
            while ($PagosPendientes = $dbo->fetchArray($qry_PagosPendientes)) {
                $cuerpo_factura .= '<tr>
    <!--<td class="text-factura">' . $PagosPendientes['Accion'] . '</td>-->
    <td class="text-factura">' . $PagosPendientes['Fecha'] . '</td>
    <td class="text-factura">' . $PagosPendientes['Documento'] . '</td>
    <td class="text-factura valor">$' . number_format(($PagosPendientes["Valor"]), 0, ",", ".") . '</td>

</tr>';
            }
        }

        $cuerpo_factura .= '</table>
</body>
</html>';

        // $cuerpo_factura = '<!doctype html>
        //                             <html>
        //                             <head>
        //                             <meta charset="UTF-8">
        //                             <title>Detalle Extracto</title>

        //                             </head>
        //                             <body>
        //                                 <table align="center">
        //                                     <tr>
        //                                         <td>
        //                                         <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
        //                                         </td>
        //                                     </tr>
        //                                     <tr>
        //                                         <td valign="middle">
        //                                         ' . $btnpago . '
        //                                         <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
        //                                         </td>
        //                                     </tr>

        //                                 </table>

        //                             </body>
        //                         </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"] = "";
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);
        $message = '';
        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN POLO CLUB ... */

    /* ... GUAYMARAL... */
    public function get_factura_ftp_g($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $DocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSGUAYMARAL_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    //$array_nombre_archivo=explode("_",$archivo_carpeta);
                                    $array_nombre_archivo = explode("-", $archivo_carpeta);
                                    $documento_socio_pdf = $array_nombre_archivo[0];
                                    if ($DocumentoSocio == $documento_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        } else {
            echo "no existe";
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_guaymaral($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="300" height="100">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                <img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
                                                Descargar extracto
                                                </a><br><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                <a href="https://www.zonapagos.com/t_Clubcampestreguaymaral">
                                                <img src="' . URLROOT . 'plataform/assets/img/btnpago.png">
                                                </a>
                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN GUAYMARAL ... */


    /* ... SAN ANDRES... */
    public function get_factura_ftp_s($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $DocumentoSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $array_accion = explode("-", $DocumentoSocio);
        $DocumentoSocio = $array_accion[0]; //anterior
        // $DocumentoSocio = $array_accion[1]; //nueva validacion para los titulares 00
        $RutaExtractos = EXTRACTOSSANANDRES_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    //$array_nombre_archivo=explode("_",$archivo_carpeta);
                                    $array_nombre_archivo = explode("-", $archivo_carpeta);
                                    $documento_socio_pdf = $array_nombre_archivo[0];
                                    //SOLO PARA LOS SOCIO TITULARES Y SON LOS QUE TERMINAN POR LA ACCION 00
                                    if ($array_accion[1] == "00") {
                                        if ($DocumentoSocio == $documento_socio_pdf) :


                                            //$array_pdf[$archivo]=$archivo_carpeta;
                                            //lo copio con nombre encriptado para tenerlo temporalmente
                                            $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                            $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                            $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                            copy($origen_copy, $destino_copy);
                                            $array_pdf[$archivo] = $nombre_encriptado;
                                        endif;
                                    }
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        } else {
            echo "no existe";
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_san_andres($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $response = array();
        $datos_consulta = explode("|", $IDFactura);
        if ($datos_consulta[0] == "Movimientos") {
            $contador = 0;
            $array_movimientos = SIMWebServiceZeus::consulta_movimientov2($IDClub, $datos_consulta[3], $datos_consulta[1], $datos_consulta[2]);
            foreach ($array_movimientos as $id_movimiento => $datos_movimiento) :
                $array_descripcion = explode("/", $datos_movimiento["Descripcion"]);
                $ultimo_elemento = end($array_descripcion);
                $Descripcion = substr($ultimo_elemento, 2);

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $valores_movimiento .= "<tr style='font-size:12px' bgcolor='" . $fondo . "' >
						                                <td>" . $datos_movimiento["Fecha"] . "</td>
						                                <td>" . $Descripcion . "</td>
						                                <td>" . $datos_movimiento["Vencimiento"] . "</td>
						                                <td>" . "$" . $datos_movimiento["Valor"] . "</td>
						                                </tr>";
                $contador++;
            endforeach;

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                             <html>
                                             <head>
                                             <meta charset="UTF-8">
                                             <title>Detalle Extracto</title>

                                             </head>
                                             <body>
                                                 <table align="center">
                                                     <tr>
                                                         <td align="center">
                                                         <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td align="center">

                                                        <table>
                                                            <tr style="font-size:12px" bgcolor="#467aa7">
                                                                <td>Fecha</td>
                                                                <td>Descripcion</td>
                                                                <td>Vencimiento</td>
                                                                <td>Valor</td>
                                                            </tr>
                                                            ' . $valores_movimiento . '
                                                        </table>
                                                         </td>
                                                     </tr>
                                                 </table>
                                             </body>
                                         </html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = 0;
            $factura["BotonPago"] = "N";
            $factura["WebViewInterno"] = "N";
            $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
            $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
            $extracto["Accion"] = $datos_socio["Accion"];
            $extracto["Action"] = "https://www.gunclub.com.co/pago-app/";

            array_push($response, $factura);
        } else {

            $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");
            $parametros = "?Nombre=" . utf8_encode($datos_socio["Nombre"]) . "&Apellido=" . utf8_encode($datos_socio["Apellido"]) . "&Accion=" . $datos_socio["Accion"];
            $cuerpo_factura = '<!doctype html>
                                                     <html>
                                                     <head>
                                                     <meta charset="UTF-8">
                                                     <title>Detalle Extracto</title>

                                                     </head>
                                                     <body>
                                                         <table align="center">
                                                             <tr>
                                                                 <td align="center">
                                                                 <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="134">
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">
                                                                 <a href="' . EXTRACTOSTMP_ROOT . $IDFactura . '">
                                                                 <img src="' . URLROOT . 'img/iconos/descargaextracto.png">
                                                                 </a><br><br>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td>
                                                                     <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <td align="center">                                                                      
                                                                         <a href="#">
                                                                         <img src="https://www.miclubapp.com/img/iconos/pagargun.png">
                                                                     </a>
                                                                 </td>
                                                             </tr>
                                                         </table>
                                                     </body>
                                                 </html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["IDFactura"] = $IDFactura;
            $factura["TotalPagar"] = 0;
            $factura["BotonPago"] = "N";
            $factura["WebViewInterno"] = "S";
            $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
            $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
            $extracto["Accion"] = $datos_socio["Accion"];
            //$extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
            $extracto["Action"] = "https://www.psepagos.co/psehostingui/showticketoffice.aspx?id=9432";

            array_push($response, $factura);
        }

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN SAN ANDRES ... */

    /* ... FARALLONES ... */
    public function get_factura_ftp_f($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $fecha_hoy = date("Y-m-d");
        $array_pdf[$fecha_hoy] = "Pago Farallones";
        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Pagos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "Farallones";
            $factura["NumeroFactura"] = $IDSocio;
            $factura["Fecha"] = $fecha_hoy;
            $factura["ValorFactura"] = "Pago no presencial";
            $factura["Almacen"] = "";
            $factura["Url"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_farallones($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $NumeroFactura . "' ", "array");

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <a href="https://www.miclubapp.com/farallonespago/index.php?Nom=' . base64_encode($datos_socio["Nombre"]) . '&Ape=' . base64_encode($datos_socio["Apellido"]) . '&Accion=' . base64_encode($datos_socio["Accion"]) . '&Doc=' . base64_encode($datos_socio["NumeroDocumento"]) . '">
                                                <img src="' . URLROOT . 'plataform/assets/img/btnpagofarallones2.jpg">
                                                </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="background-color:#2758BA">
                                                <a href="http://190.242.109.24:8080/ZeusEPagos">
                                                <img src="' . URLROOT . 'plataform/assets/img/LOGO_SISTEMAepagos.png">

                                                </a>
                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN FARALLONES ... */





    /* ... PEREIRA ... */
    public function get_factura_pereira($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $IDModulo = "")
    {
        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");

        if ($IDSocio == 53113) {
            $IDSocio = 49739;
        }

        if ($IDSocio == 344649 || $IDSocio == 10684) :
            $IDSocio = 52995;
        endif;

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = $IDSocio ", "array");

        $Cedula = $datos_socio[NumeroDocumento];
        if ($IDSocio == 53113) {
            //$Cedula = 10131111;
        }


        if(empty($IDModulo)){
            $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($Cedula);
            
            /*
            $nombre_categoria = "Extractos";
            $fecha_extracto = date("Y");
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "Extraxto|".$Cedula;
            $factura["NumeroFactura"] = "Zona de pagos";
            $factura["Fecha"] = date("Y");
            $factura["ValorFactura"] = "Pulse para continuar ";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
            */
    
            //Consulto movimientos 
    
            // CONSUMOS NUEVA VERSION
            $consumos = SIMWebServiceCampestrePereira::Consumos($Cedula);
    
            // Socios habilitados para ver los consumos
            $arr_SociosConsumos = array(51228, 52089, 49836, 49957, 52970, 52836, 50883, 52995);
    
            if (!isset($consumos['message']) && in_array($IDSocio, $arr_SociosConsumos)) {
                $valor_mayor = 0;
                //Resumen
                foreach ($consumos as $id_consumo => $datos_consumo) {
                    $NumeroConsumo = $datos_consumo["id"];
                    $FechaConsumo = str_replace("T", " ", $datos_consumo['horaConsumo']);
                    // $FechaConsumo = $datos_consumo["FechaRegis"];
                    $Accion = $datos_consumo["accionId"];
                    $Documento = $datos_consumo["terceroId"];
                    $Consecutivo = $datos_consumo["consecutivo"];
                    $Subtotal = $datos_consumo["precioVenta"];
                    // $Subtotal = $datos_consumo["precioVenta"] + $datos_consumo['valorImpuesto'];
                    if ((int) $Subtotal >= $valor_mayor) {
                        //$ValorTotal=$Subtotal;
                        //$valor_mayor=$Subtotal;
                    }
                    $ValorTotal += (int)$Subtotal;
                    $nombre_categoria = "Consumos";
                    $fecha_extracto = $FechaConsumo;
                    $factura["IDClub"] = $IDClub;
                    //$factura["IDFactura"] = "Movimiento|".$NumeroConsumo;
                    $factura["IDFactura"] = "Movimiento|" . $Consecutivo . "|" . $Dispositivo;
                    $factura["NumeroFactura"] = $Consecutivo;
                    $factura["Fecha"] = $fecha_extracto;
                    $factura["ValorFactura"] = "$" . number_format($ValorTotal, 0, ',', '.');
                    $factura["Almacen"] = "";
    
                    $factura["ID"] = "25";
    
                    $factura["Color"] = "";
                    $factura["ColorFecha"] = "";
    
                    $array_categoria_factura[$nombre_categoria][] = $factura;
                    unset($SubTotal);
                    unset($ValorTotal);
                }
            }
            // FIN CONSUMOS NUEVA VERSION
            $Total = 0;

            //separo los consumos en el modulo normal
            if (empty($IDModulo)) :
                $nombre_categoria = "Consumos";
                foreach ($DATOS as $id => $campos) :

                    $factura["IDClub"] = $IDClub;
                    $factura["ID"] = "25";
                    $factura["IDFactura"] = "Cartera|" . $Cedula;
                    $factura["NumeroFactura"] = $campos[factura];

                    $factura["Color"] = "";
                    $factura["Fecha"] = substr($campos[fecha], 0, 10);
                    $factura["ColorFecha"] = "";
                    $factura["ValorFactura"] = "$" . number_format($campos[valor], 0, ',', '.');
                    $factura["ValorNumeroFactura"] = $campos[valor];

                    $datos_campos["Texto"] = "";
                    $datos_campos["Color"] = "";

                    $Total += $campos[valor];

                    array_push(
                        $response_campos,
                        $datos_campos
                    );

                    $factura["Campos"] = $response_campos;

                    $array_categoria_factura[$nombre_categoria][] = $factura;

                endforeach;
            endif;
        }
        else{

            // if($IDSocio == 52995 || $IDSocio == 51379 || $IDSocio == 52836 || $IDSocio == 51228 || $IDSocio == 49957 || $IDSocio == 50883 || $IDSocio == 52970 || $IDSocio == 52089 || $IDSocio == 49836):
                $nombre_categoria = "Cartera";

                //Traigo las facturas del socio desde la tabla CarteraVencida  
    
                $dbo = &SIMDB::get();
                //separo las carteras vencidas en el modulo infinito
                if (!empty($IDModulo)) :
                    $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
                    $sql_PagosPendientes = "SELECT * FROM CarteraVencida Where IDSocio = '" . $IDSocio . "' AND Accion = '" . $datos_socio['Accion'] . "' AND FechaPago = 0000-00-00  Order By  IDCarteraVencida  DESC";
                    $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);
    
                    $rows = array();
                    while ($row = mysqli_fetch_array($qry_PagosPendientes))
                        $rows[] = $row;
                    foreach ($rows as $row) {
    
                        $factura["IDClub"] = $row["IDClub"];
                        //NECESITO PASAR ESTE A LA FUNCION VARIAS FACTURAS
    
                        $id =   $row["IDCarteraVencida"];
                        $factura["NumeroFactura"] = $id;
                        $factura["Color"] = "";
                        $factura["Fecha"] =  $row["Factura"] . " " . $row["Fecha"];
                        $factura["ColorFecha"] = "";
                        $factura["ValorFactura"] = "$" . number_format($row["Valor"], 0, ',', '.');
                        $factura["ValorNumeroFactura"] = $row["Valor"];
    
                        $datos_campos["Texto"] = "";
                        $datos_campos["Color"] = "";
    
                        $Total += $row["Valor"];
    
                        array_push(
                            $response_campos,
                            $datos_campos
                        );
    
                        $factura["Campos"] = $response_campos;
    
                        $array_categoria_factura[$nombre_categoria][] = $factura;
                    }
                endif; //fin modulo cartera infinito
    
                
            // endif;

        }
        

        

       







        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = "$" . number_format($Total, 0, ',', '.');
        $datos_facturas["ValorTotalLabel"] = "Total a Pagar";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PermiteSeleccionarVarias"] = "S";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";
        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_pereira($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio)
    {
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");
        $dbo = &SIMDB::get();
        $WebInterno = "S";
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $response = array();
        $datos_consulta = explode("|", $IDFactura);
        $ConsecutivoFactura = $datos_consulta[1];
        $NumeroDocumento = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocio");
        if ($IDSocio == 5533) {
            $Cedula = 10131111;
        }
        if ($datos_consulta[0] == "Movimiento") {
            $WebInterno = "S";

            // CONSUMOS NUEVA VERSION
            $consumos = SIMWebServiceCampestrePereira::Consumos($NumeroDocumento);
            if (!isset($consumos['message'])) {
                foreach ($consumos as $datos_detalle) {
                    if ($datos_detalle['consecutivo'] == $ConsecutivoFactura) {

                        $detalle_producto = "";
                        $NumeroConsumo = $datos_detalle["id"];
                        $FechaConsumo = str_replace('T', ' ', $datos_detalle["horaConsumo"]);
                        $Accion = $datos_detalle["accionId"];
                        $Documento = $datos_detalle["terceroId"];
                        $Producto = $datos_detalle["nombreProducto"];
                        $Cantidad = $datos_detalle["cantidad"];
                        $Valor = $datos_detalle["precioPublico"];
                        $Descuento = $datos_detalle["descuento"];
                        $Iva = (int)$datos_detalle["valorImpuesto"];
                        $SumaIva += $Iva;
                        // $Subtotal = $datos_detalle["precioPublico"] + $Iva;
                        $Subtotal = $datos_detalle["precioPublico"];
                        $Subtotalitem = $Cantidad * $Valor;
                        $ValorTotal += (int)$Subtotal;


                        $detalle_producto .= "<tbody>
                                                            <tr class='modo1'>
                                                            <td align='left'>" . $Producto . "</td>
                                                            <td align='center'>" . $Cantidad . "</td>
                                                            <!--<td>$" . number_format($Valor, 0, ',', '.') . "</td>-->
                                                            <td>$" . number_format($Descuento, 0, ',', '.') . "</td>
                                                            <td>$" . number_format($Iva, 0, ',', '.') . "</td>
                                                            <td>$" . number_format($Valor, 0, ',', '.') . "</td>
                                                        </tr></tbody>";
                        $encabezado = "<tbody><tr class='modo2'>
                                                            <td>Numero:</td><td>" . $NumeroConsumo . "</td>
                                                            <td>Fecha:</td><td>" . substr($FechaConsumo, 0, 10) . "</td>
                                                            <td>Accion:</td><td>" . $Accion . "</td>
                                                            </tr></tbody>";

                        $datos_factura = '<br>
                                                            <!--<a class="boton_personalizado" href="' . URLROOT . 'ecollect_historico.php?IDSocio=' . $NumeroFactura . '">Ver Transacciones anteriores</a>-->
                                                            <br><br>
                                                            <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                                                ' . $encabezado . '
                                                                <tr>
                                                                    <td colspan="5">
                                                                    <table border="0" width="100%">
                                                                        <tr style=""background:red>
                                                                            <th>Producto</th>
                                                                            <th>Cant.</th>
                                                                            <!--<th>Valor</td>-->
                                                                            <th>Dto</th>
                                                                            <th>Iva</th>
                                                                            <th>Valor</th>
                                                                        </tr>
                                                                        ' . $detalle_producto . '
                                                                        <tr>
                                                                            <td colspan=3></td>
                                                                            <th>IVA</th>
                                                                            <th>' . number_format($SumaIva, 0, ',', '.') . '</th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan=3></td>
                                                                            <th>Gran Total</th>
                                                                            <th>' . number_format($ValorTotal, 0, ',', '.') . '</th>
                                                                        </tr>
                                                                    </table>
                                                                    </td>
                                                                </tr>
                                                            </table>';

                        //Consulto siguiente numero de factura
                        $sql = $dbo->query("SELECT MAX(ConsecutivoFactura) as NumeroMayor FROM PagoCredibanco WHERE IDClub = 15 ");
                        while ($row = $dbo->fetchArray($sql)) {
                            if (empty($row["NumeroMayor"]) || $row["NumeroMayor"] == 0) {
                                $row["NumeroMayor"] = 108;
                            }
                            $SiguienteNumeroFac = (int)$row["NumeroMayor"] + 1;
                        }

                        $botonPago =
                            '<form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                            <table align="center">
                                <tr>
                                    <td>
                                    Incluir 10% de propina?
                                    <input type="radio" name="Propina" value="S"  >S
                                    <input type="radio" name="Propina" value="N" checked>N
                                    </td>
                                </tr>
                                    <tr>
                                        <td align="center">
                                        Debito desde cuenta corriente/ahorros y tarjeta de credito.<br>
                                        <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                        </td>
                                    </tr>
                                </table>                                                     

                            <input type="hidden" name="valor" id="valor" value="' . $ValorTotal . '">
                            <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $Documento . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $Accion . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="ref" id="ref" value="' . $SiguienteNumeroFac . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $IDFactura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="ConsumosPereira">
                        </form>';

                        $cuerpo_factura = '<!doctype html>
                                                                                <html>
                                                                                <head>
                                                                                <meta charset="UTF-8">
                                                                                <title>Detalle Factura</title>
                                                                                <style>
                                                                                .tabla {
                                                                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                                                                font-size:12px;
                                                                                text-align: center;
                                                                                width: 95%;
                                                                                align: center;
                                                                                }

                                                                                .tabla th {
                                                                                padding: 5px;
                                                                                font-size: 12px;
                                                                                background-color: #83aec0;
                                                                                background-repeat: repeat-x;
                                                                                color: #FFFFFF;
                                                                                border-right-width: 1px;
                                                                                border-bottom-width: 1px;
                                                                                border-right-style: solid;
                                                                                border-bottom-style: solid;
                                                                                border-right-color: #558FA6;
                                                                                border-bottom-color: #558FA6;
                                                                                font-family: "Trebuchet MS", Arial;
                                                                                text-transform: uppercase;
                                                                                }

                                                                                .tabla .modo1 {
                                                                                font-size: 10px;
                                                                                font-weight:bold;
                                                                                background-color: #e2ebef;
                                                                                background-repeat: repeat-x;
                                                                                color: #34484E;
                                                                                font-family: "Trebuchet MS", Arial;
                                                                                }
                                                                                .tabla .modo1 td {
                                                                                padding: 5px;
                                                                                border-right-width: 1px;
                                                                                border-bottom-width: 1px;
                                                                                border-right-style: solid;
                                                                                border-bottom-style: solid;
                                                                                border-right-color: #A4C4D0;
                                                                                border-bottom-color: #A4C4D0;
                                                                                text-align:right;
                                                                                }

                                                                                .tabla .modo1 th {
                                                                                background-position: left top;
                                                                                font-size: 12px;
                                                                                font-weight:bold;
                                                                                text-align: left;
                                                                                background-color: #e2ebef;
                                                                                background-repeat: repeat-x;
                                                                                color: #34484E;
                                                                                font-family: "Trebuchet MS", Arial;
                                                                                border-right-width: 1px;
                                                                                border-bottom-width: 1px;
                                                                                border-right-style: solid;
                                                                                border-bottom-style: solid;
                                                                                border-right-color: #A4C4D0;
                                                                                border-bottom-color: #A4C4D0;
                                                                                }

                                                                                .tabla .modo2 {
                                                                                font-size: 12px;
                                                                                font-weight:bold;
                                                                                background-color: #fdfdf1;
                                                                                background-repeat: repeat-x;
                                                                                color: #990000;
                                                                                font-family: "Trebuchet MS", Arial;
                                                                                text-align:center;
                                                                                }
                                                                                .tabla .modo2 td {
                                                                                padding: 5px;
                                                                                }
                                                                                .tabla .modo2 th {
                                                                                background-position: left top;
                                                                                font-size: 12px;
                                                                                font-weight:bold;
                                                                                background-color: #fdfdf1;
                                                                                background-repeat: repeat-x;
                                                                                color: #990000;
                                                                                font-family: "Trebuchet MS", Arial;
                                                                                text-align:left;
                                                                                border-right-width: 1px;
                                                                                border-bottom-width: 1px;
                                                                                border-right-style: solid;
                                                                                border-bottom-style: solid;
                                                                                border-right-color: #EBE9BC;
                                                                                border-bottom-color: #EBE9BC;
                                                                                }

                                                                                .boton_personalizado{
                                                                                text-decoration: none;
                                                                                padding: 10px;
                                                                                font-weight: 600;
                                                                                font-size: 20px;
                                                                                color: #ffffff;
                                                                                background-color: #1883ba;
                                                                                border-radius: 6px;
                                                                                border: 2px solid #0016b0;
                                                                              }

                                                                                </style>
                                                                                </head>
                                                                                <body>';

                        $cuerpo_factura .= $datos_factura;
                        $cuerpo_factura .= $botonPago;
                        $cuerpo_factura .= '

                                                            </body>

                                                                            </html>';
                    }
                }
            }
            // FIN CONSUMOS NUEVA VERSION
        } else {
            if ($Dispositivo == "iOS") {
                $WebInterno = "S";
            } else {
                $WebInterno = "N";
            }

            $cuerpo_factura = '<!doctype html>
                                        <html>
                                        <head>
                                        <meta charset="UTF-8">
                                        <title>Detalle Extracto</title>

                                        </head>
                                        <body>
                                            <table align="center">
                                                <tr>
                                                    <td>
                                                    <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <!--<a href="https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html">-->
                                                    <a href="https://ecommerce.credibanco.com/vpos2/MM/transactionStart20.do">
                                                    <img src="' . URLROOT . 'plataform/assets/img/btnpagopereira.png">
                                                    </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </body>
                                    </html>';
        }

        if ($datos_consulta[0] == "Cartera") :
            $detalle_producto .=
                "<tbody>
            <tr>
                <th>Concepto</th>
                <th>Valor</th>
            </tr>";

            $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($datos_consulta[1]);
            foreach ($DATOS as $id => $campos) :
                if ($campos[factura] == $NumeroFactura) :
                    $fecha = substr($campos[fecha], 0, 10);
                    $vencimiento = substr($campos[vencimiento], 0, 10);
                    $valor = "$" . number_format($campos[valor], 0, ',', '.');
                    $Pagar = $campos[valor];

                    foreach ($campos[conceptos] as $id2 => $concepto) :
                        $detalle_producto .= "
                        <tr class=modo1>
                            <td>" . $concepto[concepto] . "</td>
                            <td>$" . number_format($concepto[valor], 0, ',', '.') . "</td>
                        </tr>";
                    endforeach;
                    break;
                endif;
            endforeach;


            $detalle_producto .= "</tbody>";

            $cabeza_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                    <tr class="modo2">
                        <td>Numero Factura: <font color=#ffffff>' . $NumeroFactura . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Fecha: <font color=#ffffff>' . $fecha . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Total: <font color=#ffffff>' . $valor . '</font></td>
                    </tr>
                </tbody>
            </table>';


            $cuerpo_factura =
                '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

            $detalle_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                    <tr>
                        <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                        </td>
                    </tr>
                </table>';

            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");


            //Consulto siguiente numero de factura
            $sql = $dbo->query("SELECT MAX(ConsecutivoFactura) as NumeroMayor FROM PagoCredibanco WHERE IDClub = 15 ");
            while ($row = $dbo->fetchArray($sql)) {
                if (empty($row["NumeroMayor"]) || $row["NumeroMayor"] == 0) {
                    $row["NumeroMayor"] = 108;
                }
                $SiguienteNumeroFac = (int)$row["NumeroMayor"] + 1;
            }
            $boton =
                '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros y tarjeta de credito.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="valor" id="valor" value="' . $Pagar . '">
                            <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="ref" id="ref" value="' . $SiguienteNumeroFac . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $NumeroFactura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>';


            $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
            $cuerpo_factura .= '</body></html>';

        endif;

        if ($datos_consulta[0] != "Cartera" && $datos_consulta[0] != "Movimiento") :
            $detalle_producto .=
                "<tbody>
            <tr>
                <th>Concepto</th>
                <th>Valor</th>
            </tr>";

            $sql_valor = "SELECT * FROM CarteraVencida Where IDCarteraVencida IN  ( $NumeroFactura  ) ";
            $r_valor = $dbo->query($sql_valor);

            $rows = array();
            while ($row = mysqli_fetch_array($r_valor))
                $rows[] = $row;
            foreach ($rows as $row) {

                $fecha = substr($row["Fecha"], 0, 10);

                $valor = "$" . number_format($row["Valor"], 0, ',', '.');
                $Pagar = $rows["Valor"];
                $detalle_producto .= "
                            <tr class=modo1>

                               
                                <td>" . $row["Factura"] . "</td>
                                <td>$" . number_format($row["Valor"], 0, ',', '.') . "</td>
                            </tr>";
            }



            $detalle_producto .= "</tbody>";



            $cabeza_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                    <tr class="modo2">
                        <td>Numero Factura: <font color=#ffffff>' . $row["Factura"] . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Fecha: <font color=#ffffff>' . $fecha . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Total: <font color=#ffffff>' . $valor . '</font></td>
                    </tr>
                </tbody>
            </table>';


            $cuerpo_factura =
                '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;

                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

            $detalle_factura =
                '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                    <tr>
                        <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                        </td>
                    </tr>
                </table>';

            $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");



            $boton =
                '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_ecollect.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="ValorPagar" id="ValorPagar" value="' . $Pagar . '">
                            <input type="hidden" name="IDClub" id="IDClub" value="' . $datos_club["IDClub"] . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $NumeroFactura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>

                        ';


            $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
            $cuerpo_factura .= '</body></html>';

        endif;



        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = $WebInterno;
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_varias_factura_pereira($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio)
    {
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");
        $dbo = &SIMDB::get();
        $WebInterno = "S";
        $datos_club = $dbo->fetchAll(
            "Club",
            " IDClub = '$IDClub' ",
            "array"
        );
        $response = array();
        $Cedula = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio=$IDSocio");

        if ($datos_consulta[0] == "Movimiento") {
            $WebInterno = "S";
            $server = DBHOST_PEREIRA;
            $error_conectar = "N";

            // CONSUMOS NUEVA VERSION
            $arr_numeroFactura = explode(
                '|',
                $NumeroFactura
            );
            $consumos = SIMWebServiceCampestrePereira::Consumos($Cedula);
            if (!isset($consumos['message'])) {

                $ValorMayor = 0;

                foreach ($consumos as $id_consumo => $datos_consumo) {
                    $detalle_producto = "";
                    foreach ($datos_consumo as $id_detalle => $datos_detalle) {
                        $NumeroConsumo = $datos_detalle["ConsumoId"];
                        $FechaConsumo = $datos_detalle["FechaRegis"];
                        $Accion = $datos_detalle["AccionId"];
                        $Documento = $datos_detalle["TerceroId"];
                        $Producto = $datos_detalle["Nombre"];
                        $Cantidad = $datos_detalle["Cantidad"];
                        $Valor = $datos_detalle["Valor"];
                        $Descuento = $datos_detalle["Descuento"];
                        $Iva = $datos_detalle["Iva"];
                        $SumaIva += $Iva;
                        $Subtotal = $datos_detalle["Valor"];
                        $Subtotalitem = $Cantidad * $Valor;
                        if ((int) $Subtotal >= $ValorMayor) {
                            //$ValorTotal=$Subtotal;
                            //$ValorMayor=$Subtotal;
                        }
                        $ValorTotal += $Subtotal;
                        $detalle_producto .=
                            "<tbody>
                            <tr class='modo1'>
                                <td align='left'>" . $Producto . "</td>
                                <td align='center'>" . $Cantidad . "</td>
                                <!--<td>$" . number_format($Valor, 0, ',', '.') . "</td>-->
                                <td>$" . number_format($Descuento, 0, ',', '.') . "</td>
                                <td>$" . number_format($Iva, 0, ',', '.') . "</td>
                                <td>$" . number_format($Valor, 0, ',', '.') . "</td>
                            </tr>
                        </tbody>";
                    }
                    $encabezado =
                        "<tbody>
                        <tr class='modo2'>
                            <td>Numero:</td><td>" . $NumeroConsumo . "</td>
                            <td>Fecha:</td><td>" . substr($FechaConsumo, 0, 10) . "</td>
                            <td>Accion:</td><td>" . $Accion . "</td>
                        </tr>
                    </tbody>";

                    $datos_factura =
                        '<br>
                    <a class="boton_personalizado" href="' . URLROOT . 'ecollect_historico.php?IDSocio=' . $NumeroFactura . '">Ver Transacciones anteriores</a>
                    <br><br>
                    <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                        ' . $encabezado . '
                        <tr>
                            <td colspan="5">
                            <table border="0" width="100%">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                    <!--<th>Valor</td>-->
                                    <th>Dto</th>
                                    <th>Iva</th>
                                    <th>Valorrr</th>
                                </tr>
                                ' . $detalle_producto . '
                                <tr>
                                    <td colspan=3></td>
                                    <th>IVA</th>
                                    <th>' . number_format($SumaIva, 0, ',', '.') . '</th>
                                </tr>
                                <tr>
                                    <td colspan=3></td>
                                    <th>Gran Total</th>
                                    <th>' . number_format($ValorTotal, 0, ',', '.') . '</th>
                                </tr>
                            </table>
                            </td>
                        </tr>
                    </table>
                    <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_ecollect.php" method="post">
                        <table align="center">
                        <tr>
                            <td>
                            Incluir 10% de propina?
                            <input type="radio" name="Propina" value="S"  >S
                            <input type="radio" name="Propina" value="N" checked>N
                            </td>
                        </tr>
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="ValorPagar" id="ValorPagar" value="' . $ValorTotal . '">
                        <input type="hidden" name="IDClub" id="IDClub" value="' . $datos_club["IDClub"] . '">
                        <input type="hidden" name="IDSocio" id="IDSocio" value="' . $NumeroFactura . '">
                        <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $Documento . '">
                        <input type="hidden" name="Accion" id="Accion" value="' . $Accion . '">
                        <!--<input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">-->
                        <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                        <input type="hidden" name="Modulo" id="Modulo" value="ConsumosPereira">
                    </form>';

                    $cuerpo_factura =
                        '<!doctype html>
                        <html>
                        <head>
                            <meta charset="UTF-8">
                            <title>Detalle Factura</title>
                            <style>
                                .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size:12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                                }

                                .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #83aec0;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #558FA6;
                                border-bottom-color: #558FA6;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                                }

                                .tabla .modo1 {
                                font-size: 10px;
                                font-weight:bold;
                                background-color: #e2ebef;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                                }
                                .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #A4C4D0;
                                border-bottom-color: #A4C4D0;
                                text-align:right;
                                }

                                .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight:bold;
                                text-align: left;
                                background-color: #e2ebef;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #A4C4D0;
                                border-bottom-color: #A4C4D0;
                                }

                                .tabla .modo2 {
                                font-size: 12px;
                                font-weight:bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align:center;
                                }
                                .tabla .modo2 td {
                                padding: 5px;
                                }
                                .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight:bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align:left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                                }

                                .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #1883ba;
                                border-radius: 6px;
                                border: 2px solid #0016b0;
                                }

                            </style>
                        </head>
                        <body>';

                    $cuerpo_factura .= $datos_factura;
                    $cuerpo_factura .= '</body></html>';
                }
            }
        }

        $comas = strpos($IDFactura, ",");
        if ($comas === false) :
            $ArregloIDFactura[] = $IDFactura;
        else :
            $ArregloIDFactura = explode(
                ",",
                $IDFactura
            );
        endif;

        $comas = strpos($NumeroFactura, ",");
        if ($comas === false) :
            $ArregloNumeroFactura[] = $NumeroFactura;
        else :
            $ArregloNumeroFactura = explode(",", $NumeroFactura);
        endif;

        if ($IDSocio == 5533) :
            $IDSocio = 52995;
        endif;

        $cuerpo_factura =
            '<!doctype html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Detalle Factura</title>
                <style>
                    .tabla {
                        font-family: Verdana, Arial, Helvetica, sans-serif;
                        font-size: 12px;
                        text-align: center;
                        width: 95%;
                        align: center;
                    }


                    .tabla th {
                        padding: 5px;
                        font-size: 12px;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #FFFFFF;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        text-transform: uppercase;
                    }

                    .tabla .modo1 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #cfcfca;
                        background-repeat: repeat-x;
                        color: #34484E;
                        font-family: "Trebuchet MS", Arial;
                    }

                    .tabla .modo1 td {
                        padding: 5px;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #cfcfca;
                        border-bottom-color: #cfcfca;
                        text-align: right;
                    }

                    .tabla .modo1 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        text-align: right;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                    }

                    .tabla .modo2 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #4f4d4d;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                    }

                    .tabla .modo2 td {
                        padding: 5px;
                    }

                    .tabla .modo2 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #fdfdf1;
                        background-repeat: repeat-x;
                        color: #990000;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #EBE9BC;
                        border-bottom-color: #EBE9BC;
                    }

                    .boton_personalizado{
                        text-decoration: none;
                        padding: 10px;
                        font-weight: 600;
                        font-size: 20px;
                        color: #ffffff;
                        background-color: #8f8f8c;
                        border-radius: 6px;
                        border: 2px solid #cfcfca;
                    }

                    .boton_personalizado:hover{
                    color: #cfcfca;
                    background-color: #ffffff;
                    }

                </style>
            </head>
            <body>';

        $detalle_producto .=
            "<tbody>
        <tr>
            <th>Nombre</th>
            <th>Factura</th>
            <th>Valor</th> 
        </tr>";

        $total = 0;

        $Factura = "";
        //CEDULA
        $Id_Tercero = substr($IDFactura, 8);
        //FACTURA
        $ArregloNumeroFactura[$id1];
        //FACTURA
        if ($datos_consulta[0] != "Cartera") {

            $sql_valor = "SELECT * FROM CarteraVencida Where IDCarteraVencida IN  ( $NumeroFactura  ) ";
            $r_valor = $dbo->query($sql_valor);

            $rows = array();
            while ($row = mysqli_fetch_array($r_valor))
                $rows[] = $row;
            foreach ($rows as $row) {

                $valor = "$" . number_format($row["Valor"], 0, ',', '.');
                $Pagar += $row["Valor"];
                $total += $row["Valor"];
                $detalle_producto .= "
                            <tr class=modo1>

                                <td>" . $row["NombreTercero"] . "</td>
                                <td>" . $row["Factura"] . "</td>
                                <td>$" . number_format($row["Valor"], 0, ',', '.') . "</td>
                            </tr>";
            }
        }


        foreach ($ArregloIDFactura as $id1 => $IDFactura) :
            $datos_consulta = explode("|", $IDFactura);
            if ($datos_consulta[0] == "Cartera") :
                $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($datos_consulta[1]);

                foreach ($DATOS as $id => $campos) :
                    if ($campos[factura] == $ArregloNumeroFactura[$id1]) :

                        $Factura .= $ArregloNumeroFactura[$id1] . "|" . $campos[valor] . "|" . $campos[cuota] . "/";

                        $fecha = substr($campos[fecha], 0, 10);
                        $vencimiento = substr($campos[vencimiento], 0, 10);
                        $valor = "$" . number_format($campos[valor], 0, ',', '.');
                        $Pagar += $campos[valor];
                        $total += $campos[valor];
                        foreach ($campos[conceptos] as $id2 => $concepto) :
                            $detalle_producto .= "
                            <tr class=modo1>
                                <td>" . $campos[factura] . "</td>
                                <td>" . $concepto[concepto] . "</td>
                                <td>$" . number_format($concepto[valor], 0, ',', '.') . "</td>
                            </tr>";
                        endforeach;
                        break;
                    endif;
                endforeach;
            elseif (!$r_valor) : //si la consulta obtiene resultados entonces son facturas iguales

                $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($datos_consulta[1]);
                $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($datos_consulta[1]);

                $carteras_vencidas = str_replace(',', '","', $NumeroFactura);
                $carteras_vencidas = '"' . $carteras_vencidas . '"';
                $sql_valor = "SELECT * FROM CarteraVencida Where IDCarteraVencida IN  ( $carteras_vencidas ) ";
                $r_valor = $dbo->query($sql_valor);

                $rows = array();
                while ($row = mysqli_fetch_array($r_valor))
                    $rows[] = $row;
                foreach ($rows as $row) {

                    $valor = "$" . number_format($row["Valor"], 0, ',', '.');
                    $Pagar += $row["Valor"];
                    $total += $row["Valor"];
                    $detalle_producto .= "
                            <tr class=modo1>

                                <td>" . $row["NombreTercero"] . "</td>
                                <td>" . $row["Factura"] . "</td>
                                <td>$" . number_format($row["Valor"], 0, ',', '.') . "</td>
                            </tr>";
                }


                foreach ($DATOS as $id => $campos) :
                    if ($campos[factura] == $ArregloNumeroFactura[$id1]) :

                        $Factura .= $ArregloNumeroFactura[$id1] . "|" . $campos[valor] . "|" . $campos[cuota] . "/";

                        $fecha = substr($campos[fecha], 0, 10);
                        $vencimiento = substr($campos[vencimiento], 0, 10);
                        $valor = "$" . number_format($campos[valor], 0, ',', '.');
                        $Pagar += $campos[valor];
                        $total += $campos[valor];
                        foreach ($campos[conceptos] as $id2 => $concepto) :
                            $detalle_producto .= "
                            <tr class=modo1>
                                <td>" . $campos[factura] . "</td>
                                <td>" . $concepto[concepto] . "</td>
                                <td>$" . number_format($concepto[valor], 0, ',', '.') . "</td>
                            </tr>";
                        endforeach;
                        break;
                    endif;
                endforeach;

            endif;
        endforeach;

        $detalle_producto .= "</tbody>";

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '
                </table>
                </td>
            </tr>
        </table>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Facturas: <font color=#ffffff>' . $NumeroFactura . '</font></td>
                </tr>              
                <tr class="modo2">
                    <td>Total Facturas: <font color=#ffffff>' . "$" . number_format($total, 0, ',', '.') . '</font></td>
                </tr>
            </tbody>
        </table>';

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");
        //Consulto siguiente numero de factura
        $sql = $dbo->query("SELECT MAX(ConsecutivoFactura) as NumeroMayor FROM PagoCredibanco WHERE IDClub = 15 ");
        while ($row = $dbo->fetchArray($sql)) {
            if (
                empty($row["NumeroMayor"]) || $row["NumeroMayor"] == 0
            ) {
                $row["NumeroMayor"] = 109;
            }
            $SiguienteNumeroFac = (int)$row["NumeroMayor"] + 1;
        }
        $boton =
            '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros y tarjeta de credito.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="valor" id="valor" value="' . $Pagar . '">
                            <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="ref" id="ref" value="' . $SiguienteNumeroFac . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $Factura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = $WebInterno;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN PEREIRA ... */

    /* ...  COUNTRY MEDELLIN ... */
    public function get_factura_country_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo)
    {
        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        $FiltrarGastosFamiliares = SIMNet::req("FiltrarGastosFamiliares");

        $familiares = "false";
        if ($FiltrarGastosFamiliares != "N") {
            $familiares = "true";
        }


        $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio' LIMIT 1";
        $datos = $dbo->query($datos_socios);
        while ($row = $dbo->fetchArray($datos)) {
            $token = $row["TokenCountryMedellin"];
        }

        $codigoMesa = "-1";
        $sala = "-1";

        require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

        $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarMesasAbiertas($familiares, $codigoMesa, $sala, $token);

        $rows = array();
        $nombre_categoria = "";
        $resultado = json_decode($resultado1, true);
        /*
$cadena = "200-2001";
$separador = "-";
$separada = explode($separador, $cadena);
$separada1= json_encode($separada);
$separada2= json_decode($separada1);
echo $separada2[1];

*/
        $cantidad_registro = count($resultado['ConsumoMesa']);
        if ($cantidad_registro == 10) :
            $cantidad_registro = 1;
        endif;
        for ($i = 0; $i < $cantidad_registro; $i++) {
            //SACAMOS LOS DATOS QUE USAREMOS PARA GENERAR LA FACTURA
            if ($cantidad_registro == 1) :
                $codigo = $resultado['ConsumoMesa']["mesa"] . "-" . $resultado['ConsumoMesa']["sala"];
                $cod_cliente = $resultado['ConsumoMesa']["codCliente"];
                $ubicacion = $resultado['ConsumoMesa']["descripcionUbicacion"];
                $fecha = $resultado['ConsumoMesa']["fecha"];
                $permitepagar = $resultado['ConsumoMesa']["permitePagar"];
                $valor = $resultado['ConsumoMesa']["valorPago"];
                $propina = $resultado['ConsumoMesa']["propina"];
                $cliente = $resultado['ConsumoMesa']["nombresCliente"];
                $valor = $valor - $propina;
            else :
                $codigo = $resultado['ConsumoMesa'][$i]["mesa"] . "-" . $resultado['ConsumoMesa'][$i]["sala"];
                $cod_cliente = $resultado['ConsumoMesa'][$i]["codCliente"];
                $ubicacion = $resultado['ConsumoMesa'][$i]["descripcionUbicacion"];
                $fecha = $resultado['ConsumoMesa'][$i]["fecha"];
                $permitepagar = $resultado['ConsumoMesa'][$i]["permitePagar"];
                $valor = $resultado['ConsumoMesa'][$i]["valorPago"];
                $propina = $resultado['ConsumoMesa'][$i]["propina"];
                $cliente = $resultado['ConsumoMesa'][$i]["nombresCliente"];
                $valor = $valor - $propina;
            endif;
            $factura["IDClub"] = $row["IDClub"];


            $id =  $codigo;
            $factura["NumeroFactura"] =  $codigo;
            $factura["IDFactura"] = "cuentasabiertas";
            $factura["Color"] = "";
            $factura["Fecha"] = $ubicacion . "\n\n" . $cliente . "\n" . $fecha;
            $factura["ColorFecha"] = "";
            /* for($e=0; $e<count($resultado['ConsumoMesa'][$i]["detalle"]["DetalleConsumo"]); $e++) {
            
           $total += $resultado['ConsumoMesa'][$i]["detalle"]["DetalleConsumo"]["valor"];
            
            }*/
            if (($permitepagar) == "true") {
                $total = $valor;
            }
            $factura["ValorFactura"] = "$" . number_format($total, 0, ',', '.');
            $factura["ValorNumeroFactura"] = $total;

            $datos_campos["Texto"] = "";
            $datos_campos["Color"] = "";
            $Total += $row["Valor"];

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }


        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = "$" . number_format($Total, 0, ',', '.');
        $datos_facturas["ValorTotalLabel"] = "Total a Pagar";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PermiteSeleccionarVarias"] = "S";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";
        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }


    public function get_consumos_country_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $FiltrarGastosFamiliares)
    {
        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        $FiltrarGastosFamiliares = SIMNet::req("FiltrarGastosFamiliares");
        $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio' LIMIT 1";
        $datos = $dbo->query($datos_socios);
        while ($row = $dbo->fetchArray($datos)) {
            $token = $row["TokenCountryMedellin"];
        }
        //datos que se usaran en la nueva version  
        $familiares = "true";
        if ($FiltrarGastosFamiliares != "S") {
            $familiares = "false";
        }


        $fechaInicial = date('d-m-Y', strtotime($FechaInicio));
        $fechaFinal = date('d-m-Y', strtotime($FechaFin));


        $numeroConsumo = "-1";
        $pagina = "1";

        if ($fechaInicial > $fechaFinal) {

            $respuesta["message"] = "La fecha de inicio debe ser menor a la facha final";
            $respuesta["success"] = false;
            $respuesta["response"] = $response;
            return $respuesta;
        }


        require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

        $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarConsumos($familiares, $numeroConsumo, $fechaInicial, $fechaFinal, $pagina, $token);
        //servicio que retorna el array del wifi de country medellin
        //$redeswifi = SIMWebServiceCountryMedellin::App_ConsultarRedes($token); 
        $rows = array();
        $nombre_categoria = "";
        $resultado = json_decode($resultado1, true);

        $todo = count($resultado['consumos']['Consumo']);
        if ($todo == 8) {
            $todo = 1;
        }
        for ($i = 0; $i < $todo; $i++) {
            //SACAMOS LOS DATOS QUE USAREMOS PARA GENERAR LA FACTURA
            if ($todo == 8 or $todo == 1) {
                $codigo = $resultado['consumos']['Consumo']["numeroConsumo"];
                $numero_consumo = $resultado['consumos']['Consumo']["numeroConsumo"];
                $ubicacion = $resultado['consumos']['Consumo']["descripcionUbicacion"];
                $cliente = $resultado['consumos']['Consumo']["nombresCliente"];
                $fecha = $resultado['consumos']['Consumo']["fecha"];
                $producto = $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"]["descripcionProducto"];
                $valor_producto = $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"]["valor"];
                $cantidad_producto = $resultado['consumos']['Consumo']["detalle"]["DetalleConsumo"]["cantidad"];
                $tipo_pago = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["tipoFormaPago"];
                $valor_pago = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["valor"];
                $propina = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["propina"];
                $valor_entregado = $resultado['consumos']['Consumo']["formaspago"]["FormaPago"]["entregado"];
            } else {
                $codigo = $resultado['consumos']['Consumo'][$i]["numeroConsumo"];
                $numero_consumo = $resultado['consumos']['Consumo'][$i]["numeroConsumo"];
                $ubicacion = $resultado['consumos']['Consumo'][$i]["descripcionUbicacion"];
                $cliente = $resultado['consumos']['Consumo'][$i]["nombresCliente"];
                $fecha = $resultado['consumos']['Consumo'][$i]["fecha"];
                $producto = $resultado['consumos']['Consumo'][$i]["detalle"]["DetalleConsumo"]["descripcionProducto"];
                $valor_producto = $resultado['consumos']['Consumo'][$i]["detalle"]["DetalleConsumo"]["valor"];
                $cantidad_producto = $resultado['consumos']['Consumo'][$i]["detalle"]["DetalleConsumo"]["cantidad"];
                $tipo_pago = $resultado['consumos']['Consumo'][$i]["formaspago"]["FormaPago"]["tipoFormaPago"];
                $valor_pago = $resultado['consumos']['Consumo'][$i]["formaspago"]["FormaPago"]["valor"];
                $propina = $resultado['consumos']['Consumo'][$i]["formaspago"]["FormaPago"]["propina"];
                $valor_entregado = $resultado['consumos']['Consumo'][$i]["formaspago"]["FormaPago"]["entregado"];
            }
            $factura["IDClub"] = $row["IDClub"];
            $id =  $codigo;
            $factura["NumeroFactura"] =  $codigo;
            $factura["IDFactura"] =  "consumos";
            $factura["Color"] = "";
            $factura["Fecha"] =   $ubicacion . "\n\n" . $cliente . "\n" . $fecha;
            $factura["ColorFecha"] = "";
            $factura["ValorFactura"] = "$" . number_format($valor_entregado, 0, ',', '.');
            $factura["ValorNumeroFactura"] = $valor_entregado;

            $datos_campos["Texto"] = "";
            $datos_campos["Color"] = "";
            $Total += $row["Valor"];

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }


        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = "$" . number_format($Total, 0, ',', '.');
        $datos_facturas["ValorTotalLabel"] = "Total a Pagar";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PermiteSeleccionarVarias"] = "S";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";
        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    /* ... CABO TORTUGA ... */
    public function get_factura_cabo_tortuga($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo)
    {
        $dbo = &SIMDB::get();
        $IDSocio = SIMNet::req("IDSocio");
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");


        $nombre_categoria = "Cartera";

        //Traigo las facturas del socio desde la tabla CarteraVencida  

        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $sql_PagosPendientes = "SELECT * FROM CarteraCaboTortuga Where IDSocio = '" . $IDSocio . "'  Order By  IDCarteraCaboTortuga  DESC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        $rows = array();
        while ($row = mysqli_fetch_array($qry_PagosPendientes))
            $rows[] = $row;
        foreach ($rows as $row) {

            $factura["IDClub"] = $row["IDClub"];
            //NECESITO PASAR ESTE A LA FUNCION VARIAS FACTURAS

            $id = $row["IDCarteraCaboTortuga"];
            $factura["NumeroFactura"] = $id;
            $factura["Color"] = "";
            $factura["Fecha"] =  $row["Cliente"];
            $factura["ColorFecha"] = "";
            $factura["ValorFactura"] = "$" . number_format($row["ValorTotal"], 0, ',', '.');
            $factura["ValorNumeroFactura"] = $row["ValorTotal"];

            $datos_campos["Texto"] = "";
            $datos_campos["Color"] = "";

            $Total += $row["Valor"];

            array_push($response_campos, $datos_campos);

            $factura["Campos"] = $response_campos;

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }


        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $urlIMagen = "https://www.miclubapp.com/file/club/image.png";

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;
        $datos_facturas["ValorTotal"] = "$" . number_format($Total, 0, ',', '.');
        $datos_facturas["ValorTotalLabel"] = "Total a Pagar";
        $datos_facturas["ImagenLateral"] = $urlIMagen;
        $datos_facturas["TextoLateral"] = "";
        $datos_facturas["PermiteSeleccionarVarias"] = "S";
        $datos_facturas["TextoSeleccionarDeseleccionar"] = "Selecionar todas las facturas";
        $datos_facturas["TextoIntroSeleccionarVariasPago"] = "Seleccione las facturas que desea pagar";
        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    public function get_detalle_factura_cabo_tortuga($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio)
    {


        $dbo = &SIMDB::get();
        $WebInterno = "S";
        $datos_club = $dbo->fetchAll(
            "Club",
            " IDClub = '" . $IDClub . "' ",
            "array"
        );
        $response = array();


        $detalle_producto .=
            "<tbody>
            <tr>
                <th>Concepto</th>
                <th>1 A 30</th>
                <th>31 A 60</th>
                <th>61 A 90</th>
                <th>Mas De 90</th>
                <th>Valor </th>
            </tr>";

        $sql_valor = "SELECT * FROM CarteraCaboTortuga Where IDCarteraCaboTortuga IN  ( $NumeroFactura ) ";
        $r_valor = $dbo->query($sql_valor);

        $rows = array();
        while ($row = mysqli_fetch_array($r_valor))
            $rows[] = $row;
        foreach ($rows as $row) {

            $fecha = date("Y-m-d");

            $valor = "$" . number_format($row["ValorTotal"], 0, ',', '.');
            $Pagar = $rows["Valor"];
            $detalle_producto .= "
                            <tr class=modo1>

                               
                                <td>" . $row["Cliente"] . "</td>
                                <td>" . $row["1A30"] . "</td>
                                <td>" . $row["31A60"] . "</td>
                                <td>" . $row["61A90"] . "</td>
                                <td>" . $row["MasDe90"] . "</td>
                                <td>$" . number_format($row["ValorTotal"], 0, ',', '.') . "</td>
                            </tr>";
        }



        $detalle_producto .= "</tbody>";



        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                    <tr class="modo2">
                        <td>Factura : <font color=#ffffff>' . $row["Cliente"] . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Fecha: <font color=#ffffff>' . $fecha . '</font></td>
                    </tr>
                    <tr class="modo2">
                        <td>Total: <font color=#ffffff>' . $valor . '</font></td>
                    </tr>
                </tbody>
            </table>';


        $cuerpo_factura =
            '<!doctype html>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Detalle Factura</title>
                        <style>
                            .tabla {
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 12px;
                                text-align: center;
                                width: 95%;
                                align: center;
                            }


                            .tabla th {
                                padding: 5px;
                                font-size: 12px;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #FFFFFF;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                text-transform: uppercase;
                            }

                            .tabla .modo1 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #cfcfca;
                                background-repeat: repeat-x;
                                color: #34484E;
                                font-family: "Trebuchet MS", Arial;
                            }

                            .tabla .modo1 td {
                                padding: 5px;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #cfcfca;
                                border-bottom-color: #cfcfca;
                                text-align: right;
                            }

                            .tabla .modo1 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                text-align: right;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #8f8f8c;
                                font-family: "Trebuchet MS", Arial;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #8f8f8c;
                                border-bottom-color: #8f8f8c;
                            }

                            .tabla .modo2 {
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #8f8f8c;
                                background-repeat: repeat-x;
                                color: #4f4d4d;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                            }

                            .tabla .modo2 td {
                                padding: 5px;
                            }

                            .tabla .modo2 th {
                                background-position: left top;
                                font-size: 12px;
                                font-weight: bold;
                                background-color: #fdfdf1;
                                background-repeat: repeat-x;
                                color: #990000;
                                font-family: "Trebuchet MS", Arial;
                                text-align: left;
                                border-right-width: 1px;
                                border-bottom-width: 1px;
                                border-right-style: solid;
                                border-bottom-style: solid;
                                border-right-color: #EBE9BC;
                                border-bottom-color: #EBE9BC;
                            }

                            .boton_personalizado{
                                text-decoration: none;
                                padding: 10px;
                                font-weight: 600;
                                font-size: 20px;
                                color: #ffffff;
                                background-color: #8f8f8c;
                                border-radius: 6px;
                                border: 2px solid #cfcfca;
                            }

                            .boton_personalizado:hover{
                            color: #cfcfca;
                            background-color: #ffffff;
                            }

                        </style>
                    </head>
                    <body>';

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                    <tr>
                        <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                        </td>
                    </tr>
                </table>';

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");



        $boton =
            '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_ecollect.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="ValorPagar" id="ValorPagar" value="' . $Pagar . '">
                            <input type="hidden" name="IDClub" id="IDClub" value="' . $datos_club["IDClub"] . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $NumeroFactura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>

                        ';


        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
        $cuerpo_factura .= '</body></html>';



        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = $WebInterno;
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    public function get_detalle_varias_factura_cabo_tortuga($IDClub, $IDFactura,  $NumeroFactura, $Dispositivo, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $WebInterno = "S";
        $datos_club = $dbo->fetchAll(
            "Club",
            " IDClub = '$IDClub' ",
            "array"
        );
        $response = array();

        $comas = strpos($IDFactura, ",");
        if ($comas === false) :
            $ArregloIDFactura[] = $IDFactura;
        else :
            $ArregloIDFactura = explode(
                ",",
                $IDFactura
            );
        endif;

        $comas = strpos($NumeroFactura, ",");
        if ($comas === false) :
            $ArregloNumeroFactura[] = $NumeroFactura;
        else :
            $ArregloNumeroFactura = explode(",", $NumeroFactura);
        endif;


        $cuerpo_factura =
            '<!doctype html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Detalle Factura</title>
                <style>
                    .tabla {
                        font-family: Verdana, Arial, Helvetica, sans-serif;
                        font-size: 12px;
                        text-align: center;
                        width: 95%;
                        align: center;
                    }


                    .tabla th {
                        padding: 5px;
                        font-size: 12px;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #FFFFFF;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                        font-family: "Trebuchet MS", Arial;
                        text-transform: uppercase;
                    }

                    .tabla .modo1 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #cfcfca;
                        background-repeat: repeat-x;
                        color: #34484E;
                        font-family: "Trebuchet MS", Arial;
                    }

                    .tabla .modo1 td {
                        padding: 5px;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #cfcfca;
                        border-bottom-color: #cfcfca;
                        text-align: right;
                    }

                    .tabla .modo1 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        text-align: right;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #8f8f8c;

                        font-family: "Trebuchet MS", Arial;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #8f8f8c;
                        border-bottom-color: #8f8f8c;
                    }

                    .tabla .modo2 {
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #8f8f8c;
                        background-repeat: repeat-x;
                        color: #4f4d4d;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                    }


                    .tabla .modo2 td {
                        padding: 5px;
                    }

                    .tabla .modo2 th {
                        background-position: left top;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #fdfdf1;
                        background-repeat: repeat-x;
                        color: #990000;
                        font-family: "Trebuchet MS", Arial;
                        text-align: left;
                        border-right-width: 1px;
                        border-bottom-width: 1px;
                        border-right-style: solid;
                        border-bottom-style: solid;
                        border-right-color: #EBE9BC;
                        border-bottom-color: #EBE9BC;

                    }

                    .boton_personalizado{
                        text-decoration: none;
                        padding: 10px;
                        font-weight: 600;
                        font-size: 20px;
                        color: #ffffff;
                        background-color: #8f8f8c;
                        border-radius: 6px;
                        border: 2px solid #cfcfca;
                    }

                    .boton_personalizado:hover{
                    color: #cfcfca;
                    background-color: #ffffff;
                    }

                </style>

            </head>
            <body>';

        $detalle_producto .=
            "<tbody>
        <tr>
            <th>Factura</th>
            <th>1 A 30</th>
            <th>31 A 60</th>
            <th>61 A 90</th>
            <th>Mas De 90</th>
            <th>Valor</th> 
        </tr>";

        $total = 0;

        $Factura = "";
        //CEDULA
        $Id_Tercero = substr($IDFactura, 8);
        //FACTURA
        $ArregloNumeroFactura[$id1];
        //FACTURA
        if ($datos_consulta[0] != "Cartera") {

            $sql_valor = "SELECT * FROM CarteraCaboTortuga Where IDCarteraCaboTortuga IN  ( $NumeroFactura  ) ";
            $r_valor = $dbo->query($sql_valor);

            $rows = array();
            while ($row = mysqli_fetch_array($r_valor))
                $rows[] = $row;
            foreach ($rows as $row) {

                $valor = "$" . number_format($row["ValorTotal"], 0, ',', '.');
                $Pagar += $row["ValorTotal"];
                $total += $row["ValorTotal"];
                $fac .= $row["Cliente"];
                $detalle_producto .= "
                            <tr class=modo1>

                                <td>" . $row["Cliente"] . "</td>
                                <td>" . $row["1A30"] . "</td>
                                <td>" . $row["31A60"] . "</td>
                                <td>" . $row["61A90"] . "</td>
                                <td>" . $row["MasDe90"] . "</td>
                                                                                                
                                <td>$" . number_format($row["ValorTotal"], 0, ',', '.') . "</td>
                            </tr>";
            }
        }


        foreach ($ArregloIDFactura as $id1 => $IDFactura) :
            $datos_consulta = explode("|", $IDFactura);
            if ($datos_consulta[0] == "Cartera") :
                $DATOS = SIMWebServiceCampestrePereira::EstadoCuenta($datos_consulta[1]);

                foreach ($DATOS as $id => $campos) :
                    if ($campos[factura] == $ArregloNumeroFactura[$id1]) :

                        $Factura .= $ArregloNumeroFactura[$id1] . "|" . $campos[valor] . "|" . $campos[cuota] . "/";

                        $fecha = substr($campos[fecha], 0, 10);
                        $vencimiento = substr($campos[vencimiento], 0, 10);
                        $valor = "$" . number_format($campos[valor], 0, ',', '.');
                        $Pagar += $campos[valor];
                        $total += $campos[valor];
                        foreach ($campos[conceptos] as $id2 => $concepto) :
                            $detalle_producto .= "
                            <tr class=modo1>
                                <td>" . $campos[factura] . "</td>
                                <td>" . $concepto[concepto] . "</td>
                                <td>$" . number_format($concepto[valor], 0, ',', '.') . "</td>
                            </tr>";
                        endforeach;
                        break;
                    endif;
                endforeach;
            elseif (!$r_valor) :

                $cuerpo_factura .= "<br>SOLO SE PUEDEN SELECCIONAR FACTURAS DE LA MISMA CATEGORIA<br>";
                $cuerpo_factura .= '</body></html>';

                $factura["IDClub"] = $IDClub;
                $factura["CuerpoFactura"] = $cuerpo_factura;
                $factura["IDFactura"] = $IDFactura;
                $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
                $factura["BotonPago"] = "N";
                $factura["WebViewInterno"] = $WebInterno;

                array_push($response, $factura);

                $respuesta["message"] = "Solo son permitidas facturas de la misma categoria";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
                return $respuesta;

            endif;
        endforeach;

        $detalle_producto .= "</tbody>";

        $detalle_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tr>
                <td>
                <table border="0" width="100%">
                    ' . $detalle_producto . '

                </table>
                </td>
            </tr>
        </table>';

        $cabeza_factura =
            '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
            <tbody>
                <tr class="modo2">
                    <td>Facturas: <font color=#ffffff>' . $fac . '</font></td>
                </tr>              
                <tr class="modo2">
                    <td>Total Facturas: <font color=#ffffff>' . "$" . number_format($total, 0, ',', '.') . '</font></td>
                </tr>
            </tbody>
        </table>';

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = $IDSocio");
        //Consulto siguiente numero de factura
        $sql = $dbo->query("SELECT MAX(ConsecutivoFactura) as NumeroMayor FROM PagoCredibanco WHERE IDClub = 15 ");
        while ($row = $dbo->fetchArray($sql)) {
            if (
                empty($row["NumeroMayor"]) || $row["NumeroMayor"] == 0
            ) {
                $row["NumeroMayor"] = 109;
            }
            $SiguienteNumeroFac = (int)$row["NumeroMayor"] + 1;
        }
        $boton =
            '   <form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                            <table align="center">                       
                            <tr>
                                <td align="center">
                                Debito desde cuenta corriente/ahorros y tarjeta de credito.<br>
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                </td>
                            </tr>
                            </table>                                                      

                            <input type="hidden" name="valor" id="valor" value="' . $Pagar . '">
                            <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                            <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
                            <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
                            <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
                            <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
                            <input type="hidden" name="ref" id="ref" value="' . $SiguienteNumeroFac . '">
                            <input type="hidden" name="Factura" id="Factura" value="' . $Factura . '">
                            <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
                        </form>';
        // $boton =
        //     '<form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'redirect_ecollect.php" method="post">
        //     <table align="center">                       
        //     <tr>
        //         <td align="center">
        //         Debito desde cuenta corriente/ahorros y tarjeta de credito<br>
        //         <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
        //         </td>
        //     </tr>
        //     </table>                                                      

        //     <input type="hidden" name="ValorPagar" id="ValorPagar" value="' . $Pagar . '">
        //     <input type="hidden" name="IDClub" id="IDClub" value="' . $IDClub . '">
        //     <input type="hidden" name="IDSocio" id="IDSocio" value="' .  $IDSocio . '">
        //     <input type="hidden" name="NumeroDocumento" id="NumeroDocumento" value="' . $datos_consulta[1] . '">
        //     <input type="hidden" name="Accion" id="Accion" value="' . $AccionSocio . '">                    
        //     <input type="hidden" name="ConsumoId" id="ConsumoId" value="' . time() . '">
        //     <input type="hidden" name="Factura" id="Factura" value="' . $Factura . '">
        //     <input type="hidden" name="Modulo" id="Modulo" value="CarteraPereira">
        // </form>';

        $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $boton . "<br>";
        $cuerpo_factura .= '</body></html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = $WebInterno;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CABO TORTUGA ... */

    /* ... INVERMETROS ... */
    public function get_factura_invermetros($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 546139)
            $IDSocio = 564737;

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT SUM(Debito) as ValorPagar,SUM(Credito) as ValorCredito, month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha) Order by Fecha DESC";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
            $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-28";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $row_valor["ValorPagar"] += $row_cartera["Total"];
            }

            $row_valor["ValorPagar"] -= $row_valor["ValorCredito"];

            //Consulto si tiene Descuentos
            $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
            $r_desc = $dbo->query($sql_desc);
            $row_desc = $dbo->fetchArray($r_desc);
            if ((int) $row_desc["DescuentoClubHouse"] > 0) {
                $row_valor["ValorPagar"] -= $row_desc["DescuentoSerena"];
                $row_valor["ValorPagar"] -= $row_desc["DescuentoClubHouse"];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_valor["Mes"] - 1;
            $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
            $factura["Fecha"] = $row_valor["Anio"];
            $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
            $factura["Almacen"] = $row_valor["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_invermetros($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);



        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {

            if ((int) $row_valor["Debito"] > 0) {
                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                $valor_mostrar = $row_valor["Debito"];
                $valor_total += $row_valor["Debito"];
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
                $valor_total -= $row_valor["Credito"];
            }

            if ($array_valor[$row_valor["Nombre"]] > 0) {

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
                    <tr bgcolor=' . $fondo . '>
                        <td>' . $descripcion_concepto . '</td>
                    <td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
                    </tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }

        //Consulto si tiene saldo en Cartera
        $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
        $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-28";
        //Consulto si tiene saldo en Cartera
        $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
        //$sql_cartera="SELECT * FROM SocioSaldoCartera WHERE IDClub= '".$IDClub."' and Codigo='".$predio."' Order by IDSocioSaldoCartera DESC LIMIT 1";
        $r_cartera = $dbo->query($sql_cartera);
        $row_cartera = $dbo->fetchArray($r_cartera);
        if ((int) $row_cartera["Total"] > 0) {
            $valor_total += $row_cartera["Total"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Saldos anteriores</td>
                <td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
                </tr>';
        }

        //Consulto si tiene Descuentos
        $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
        $r_desc = $dbo->query($sql_desc);
        $row_desc = $dbo->fetchArray($r_desc);
        if ((int) $row_desc["DescuentoClubHouse"] > 0) {
            $valor_total_desc += $row_desc["DescuentoClubHouse"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentos por alivio covid-19 Agua serena 14%</td>
                <td>$' . number_format($row_desc["DescuentoSerena"], 0, '', '.') . '</td>
                </tr>
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentospor alivio covid-19 Club House 30%</td>
                <td>$' . number_format($row_desc["DescuentoClubHouse"], 0, '', '.') . '</td>
                </tr>
                ';
            $valor_total -= $row_desc["DescuentoSerena"];
            $valor_total -= $row_desc["DescuentoClubHouse"];
        }

        $detalle_cuenta .= '
            <tr bgcolor= #c47e7e >
                <td>Valor Total: </td>
            <td>$' . number_format($valor_total, 0, '', '.') . '</td>
            </tr>';

        $response = array();

        if ($IDClub == 71) :
            $Pago = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";
        elseif ($IDClub == 150) :
            $Pago = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        elseif ($IDClub == 158) :
            $Pago = "https://checkout.wompi.co/l/EsXvCH";
        endif;


        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <table align="center">
                                                <tr bgcolor="#24508e">
                                                    <td style="color:#FFF">Detalle</td>
                                                    <td style="color:#FFF">Valor</td>
                                                </tr>
                                                ' . $detalle_cuenta . '
                                            </table>
                                            <br>
                                            <span style="font-size:10px;" >Nota: Por favor verificar los valores contra la cuenta de cobro.</span>
                                            <br>
                                            <a href=' . $Pago . '>
                                            <img src="' . URLROOT . 'plataform/assets/img/pagaaqui.png">
                                            </a>
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";



        $factura["Action"] = $Pago;

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        //$factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su Referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        $WebInterno = "N";
        $factura["WebViewInterno"] = $WebInterno;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... INVERMETROS ... */

    /* ... MALLORCA ... */
    /*  public function get_factura_mallorca($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();


        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT SUM(Debito) as ValorPagar,SUM(Credito) as ValorCredito, month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
            $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-28";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $row_valor["ValorPagar"] += $row_cartera["Total"];
            }

            $row_valor["ValorPagar"] -= $row_valor["ValorCredito"];

            //Consulto si tiene Descuentos
            $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
            $r_desc = $dbo->query($sql_desc);
            $row_desc = $dbo->fetchArray($r_desc);
            if ((int) $row_desc["DescuentoClubHouse"] > 0) {
                $row_valor["ValorPagar"] -= $row_desc["DescuentoSerena"];
                $row_valor["ValorPagar"] -= $row_desc["DescuentoClubHouse"];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_valor["Mes"] - 1;
            $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
            $factura["Fecha"] = $row_valor["Anio"];
            $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
            $factura["Almacen"] = $row_valor["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_mallorca($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {

            if ((int) $row_valor["Debito"] > 0) {
                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                $valor_mostrar = $row_valor["Debito"];
                $valor_total += $row_valor["Debito"];
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
                $valor_total -= $row_valor["Credito"];
            }

            if ($array_valor[$row_valor["Nombre"]] > 0) {

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
                    <tr bgcolor=' . $fondo . '>
                        <td>' . $descripcion_concepto . '</td>
                    <td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
                    </tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }

        //Consulto si tiene saldo en Cartera
        $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
        $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-28";
        //Consulto si tiene saldo en Cartera
        $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
        //$sql_cartera="SELECT * FROM SocioSaldoCartera WHERE IDClub= '".$IDClub."' and Codigo='".$predio."' Order by IDSocioSaldoCartera DESC LIMIT 1";
        $r_cartera = $dbo->query($sql_cartera);
        $row_cartera = $dbo->fetchArray($r_cartera);
        if ((int) $row_cartera["Total"] > 0) {
            $valor_total += $row_cartera["Total"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Saldos anteriores</td>
                <td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
                </tr>';
        }

        //Consulto si tiene Descuentos
        $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
        $r_desc = $dbo->query($sql_desc);
        $row_desc = $dbo->fetchArray($r_desc);
        if ((int) $row_desc["DescuentoClubHouse"] > 0) {
            $valor_total_desc += $row_desc["DescuentoClubHouse"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentos por alivio covid-19 Agua serena 14%</td>
                <td>$' . number_format($row_desc["DescuentoSerena"], 0, '', '.') . '</td>
                </tr>
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentospor alivio covid-19 Club House 30%</td>
                <td>$' . number_format($row_desc["DescuentoClubHouse"], 0, '', '.') . '</td>
                </tr>
                ';
            $valor_total -= $row_desc["DescuentoSerena"];
            $valor_total -= $row_desc["DescuentoClubHouse"];
        }

        $detalle_cuenta .= '
            <tr bgcolor= #c47e7e >
                <td>Valor Total: </td>
            <td>$' . number_format($valor_total, 0, '', '.') . '</td>
            </tr>';

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <table align="center">
                                                <tr bgcolor="#24508e">
                                                    <td style="color:#FFF">Detalle</td>
                                                    <td style="color:#FFF">Valor</td>
                                                </tr>
                                                ' . $detalle_cuenta . '
                                            </table>
                                            <br>
                                            <span style="font-size:10px;" >Nota: Por favor verificar los valores contra la cuenta de cobro.</span>
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "S";
        $factura["Action"] = "https://checkout.wompi.co/l/tmZ5ML?_ga=2.234867614.1842038240.1634667512-246521021.1633627857&_gac=1.174261782.1634667512.EAIaIQobChMIocedgYvX8wIVWtKzCh1fpgBCEAAYASAAEgKAgPD_BwE";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su Referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } */
    /* ... FIN MALLORCA ... */

    /* ... PUERTAS DEL SOL ... */
    /* public function get_factura_puertas_del_sol($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT SUM(Debito) as ValorPagar,SUM(Credito) as ValorCredito, month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
            $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-28";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $row_valor["ValorPagar"] += $row_cartera["Total"];
            }

            //echo $row_valor["ValorCredito"]. " ";
            //$row_valor["ValorPagar"]-=$row_valor["ValorCredito"];

            //Consulto si tiene Descuentos
            $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
            $r_desc = $dbo->query($sql_desc);
            $row_desc = $dbo->fetchArray($r_desc);
            if ((int) $row_desc["DescuentoClubHouse"] > 0) {
                $row_valor["ValorPagar"] -= $row_desc["DescuentoSerena"];
                $row_valor["ValorPagar"] -= $row_desc["DescuentoClubHouse"];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_valor["Mes"] - 1;
            $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
            $factura["Fecha"] = date("Y");
            $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
            $factura["Almacen"] = $row_valor["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_puertas_del_sol($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $mostrar = "N";
            if ((int) $row_valor["Debito"] > 0) {
                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                $valor_mostrar = $row_valor["Debito"];
                $valor_total += $row_valor["Debito"];
                $mostrar = "S";
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
                //$valor_total-=$row_valor["Credito"];
                $mostrar = "N";
            }

            //if(    $array_valor[$row_valor["Nombre"]]>0){
            if ($mostrar == "S") {

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
                    <tr bgcolor=' . $fondo . '>
                        <td>' . $descripcion_concepto . '</td>
                    <td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
                    </tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }

        //Consulto si tiene saldo en Cartera
        $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
        $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-28";
        //Consulto si tiene saldo en Cartera
        $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
        //$sql_cartera="SELECT * FROM SocioSaldoCartera WHERE IDClub= '".$IDClub."' and Codigo='".$predio."' Order by IDSocioSaldoCartera DESC LIMIT 1";
        $r_cartera = $dbo->query($sql_cartera);
        $row_cartera = $dbo->fetchArray($r_cartera);
        if ((int) $row_cartera["Total"] > 0) {
            $valor_total += $row_cartera["Total"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Saldos anteriores</td>
                <td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
                </tr>';
        }

        //Consulto si tiene Descuentos
        $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
        $r_desc = $dbo->query($sql_desc);
        $row_desc = $dbo->fetchArray($r_desc);
        if ((int) $row_desc["DescuentoClubHouse"] > 0) {
            $valor_total_desc += $row_desc["DescuentoClubHouse"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentos por alivio covid-19 Agua serena 14%</td>
                <td>$' . number_format($row_desc["DescuentoSerena"], 0, '', '.') . '</td>
                </tr>
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentospor alivio covid-19 Club House 30%</td>
                <td>$' . number_format($row_desc["DescuentoClubHouse"], 0, '', '.') . '</td>
                </tr>
                ';
            $valor_total -= $row_desc["DescuentoSerena"];
            $valor_total -= $row_desc["DescuentoClubHouse"];
        }

        $detalle_cuenta .= '
            <tr bgcolor= #c47e7e >
                <td>Valor Total: </td>
            <td>$' . number_format($valor_total, 0, '', '.') . '</td>
            </tr>';

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <table align="center">
                                                <tr bgcolor="#24508e">
                                                    <td style="color:#FFF">Detalle</td>
                                                    <td style="color:#FFF">Valor</td>
                                                </tr>
                                                ' . $detalle_cuenta . '

                                            </table>
                                            <br>
                                            <span style="font-size:10px;" >Nota: Por favor verificar los valores contra la cuenta de cobro.</span>
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "S";
        $factura["Action"] = "https://checkout.wompi.co/l/EsXvCH";
        $WebInterno = "N";
        $factura["WebViewInterno"] = $WebInterno;

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_total, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } */
    /* ... FIN PUERTAS DEL SOL ... */

    /* ... ENTRELOMAS ... */
    public function get_factura_entrelomas($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT SUM(Debito) as ValorPagar,SUM(Credito) as ValorCredito, month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
            $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-28";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $row_valor["ValorPagar"] += $row_cartera["Total"];
            }

            //echo $row_valor["ValorCredito"]. " ";
            //$row_valor["ValorPagar"]-=$row_valor["ValorCredito"];

            //Consulto si tiene Descuentos
            $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
            $r_desc = $dbo->query($sql_desc);
            $row_desc = $dbo->fetchArray($r_desc);
            if ((int) $row_desc["DescuentoClubHouse"] > 0) {
                $row_valor["ValorPagar"] -= $row_desc["DescuentoSerena"];
                $row_valor["ValorPagar"] -= $row_desc["DescuentoClubHouse"];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_valor["Mes"] - 1;
            $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
            $factura["Fecha"] = date("Y");
            $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
            $factura["Almacen"] = $row_valor["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_entrelomas($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $mostrar = "N";
            if ((int) $row_valor["Debito"] > 0) {
                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                $valor_mostrar = $row_valor["Debito"];
                $valor_total += $row_valor["Debito"];
                $mostrar = "S";
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
                //$valor_total-=$row_valor["Credito"];
                $mostrar = "N";
            }

            //if(    $array_valor[$row_valor["Nombre"]]>0){
            if ($mostrar == "S") {

                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
                    <tr bgcolor=' . $fondo . '>
                        <td>' . $descripcion_concepto . '</td>
                    <td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
                    </tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }

        //Consulto si tiene saldo en Cartera
        $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
        $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-28";
        //Consulto si tiene saldo en Cartera
        $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
        //$sql_cartera="SELECT * FROM SocioSaldoCartera WHERE IDClub= '".$IDClub."' and Codigo='".$predio."' Order by IDSocioSaldoCartera DESC LIMIT 1";
        $r_cartera = $dbo->query($sql_cartera);
        $row_cartera = $dbo->fetchArray($r_cartera);
        if ((int) $row_cartera["Total"] > 0) {
            $valor_total += $row_cartera["Total"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Saldos anteriores</td>
                <td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
                </tr>';
        }

        //Consulto si tiene Descuentos
        $sql_desc = "SELECT * FROM SocioDescuento WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioDescuento DESC LIMIT 1";
        $r_desc = $dbo->query($sql_desc);
        $row_desc = $dbo->fetchArray($r_desc);
        if ((int) $row_desc["DescuentoClubHouse"] > 0) {
            $valor_total_desc += $row_desc["DescuentoClubHouse"];
            $detalle_cuenta .= '
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentos por alivio covid-19 Agua serena 14%</td>
                <td>$' . number_format($row_desc["DescuentoSerena"], 0, '', '.') . '</td>
                </tr>
                <tr bgcolor=' . $fondo . '>
                    <td>Descuentospor alivio covid-19 Club House 30%</td>
                <td>$' . number_format($row_desc["DescuentoClubHouse"], 0, '', '.') . '</td>
                </tr>
                ';
            $valor_total -= $row_desc["DescuentoSerena"];
            $valor_total -= $row_desc["DescuentoClubHouse"];
        }

        $detalle_cuenta .= '
            <tr bgcolor= #c47e7e >
                <td>Valor Total: </td>
            <td>$' . number_format($valor_total, 0, '', '.') . '</td>
            </tr>';

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <table align="center">
                                                <tr bgcolor="#24508e">
                                                    <td style="color:#FFF">Detalle</td>
                                                    <td style="color:#FFF">Valor</td>
                                                </tr>
                                                ' . $detalle_cuenta . '

                                            </table>
                                            <br>
                                            <span style="font-size:10px;" >Nota: Por favor verificar los valores contra la cuenta de cobro.</span>
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "S";
        $factura["Action"] = "https://www.davivienda.com/wps/portal/personas/nuevo/personas/aqui_puedo/pagar_facilmente/pse/";
        $WebInterno = "N";
        $factura["WebViewInterno"] = $WebInterno;

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_total, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN ENTRELOMAS ... */




    /* ... CONJUNTO FONTANAR ... */
    public function get_factura_fontanar($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $array_predio = explode(" ", $datos_socio["Predio"]);
        $predio = end($array_predio);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        //$sql_valor="SELECT SUM(Debito) as ValorPagar,month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '".$IDClub."' and Nit='".$predio."' Group by year(Fecha),month(Fecha)";
        $sql_meses = "SELECT Detalle,month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
        $r_meses = $dbo->query($sql_meses);
        while ($row_meses = $dbo->fetchArray($r_meses)) {
            $sql_valores = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha) = '" . $row_meses["Mes"] . "' and year(Fecha) = '" . $row_meses["Anio"] . "' ";
            $r_valores = $dbo->query($sql_valores);
            $valor_mes = 0;
            while ($row_valores = $dbo->fetchArray($r_valores)) {
                //Para los demas conjuntos depende la fecha se cobra un valor para administacion
                $dia_hoy = (int) date("d");
                if ($dia_hoy <= 15) {
                    if ((int) $row_valores["Porcret"] > 0) {
                        $columna_sumar = "Porcret";
                    } else {
                        $columna_sumar = "Debito";
                    }
                } else {
                    $columna_sumar = "Cencosto";
                }

                $valor_acumulado += (int) $row_valores[$columna_sumar];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_meses["Mes"] - 1;
            $factura["IDFactura"] = $row_meses["Anio"] . "-" . $row_meses["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_meses["Anio"];
            $factura["Fecha"] = date("Y");
            $factura["ValorFactura"] = "$" . number_format($valor_acumulado, 0, '', '.');
            $factura["Almacen"] = $row_meses["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_fontanar($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $datos_socio["Predio"] . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {

            if ((int) $row_valor["Debito"] > 0) {

                $dia_hoy = (int) date("d");
                if ($row_valor["Nombre"] == "CUOTAS DE ADMINISTRACION") {
                    if ($dia_hoy <= 15) {
                        if ((int) $row_valores["Porcret"] > 0) {
                            $columna_sumar = "Porcret";
                        } else {
                            $columna_sumar = "Debito";
                        }
                    } else {
                        $columna_sumar = "Cencosto";
                    }
                } else {
                    $columna_sumar = "Debito";
                }

                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor[$columna_sumar];
                $valor_mostrar = $row_valor[$columna_sumar];
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
            }

            if ($array_valor[$row_valor["Nombre"]] > 0) {

                $valor_total += $row_valor["Debito"];
                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
                    <tr bgcolor=' . $fondo . '>
                        <td>' . $descripcion_concepto . '</td>
                    <td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
                    </tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }
        $detalle_cuenta .= '
            <tr bgcolor= #c47e7e >
                <td>Valor Total: </td>
            <td>$' . number_format($valor_total, 0, '', '.') . '</td>
            </tr>';

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <table align="center">
                                                <tr bgcolor="#24508e">
                                                    <td style="color:#FFF">Detalle</td>
                                                    <td style="color:#FFF">Valor</td>
                                                </tr>
                                                ' . $detalle_cuenta . '
                                            </table>
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CONJUNTO FONTANAR ... */

    /* ... URAKI ... */
    public function get_factura_uraki($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $DocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = CORRESPONDENCIA_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $documento_socio_pdf = $array_nombre_archivo[0];
                                    if ($DocumentoSocio == $documento_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".TXT";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        } else {
            echo "no existe";
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Desprendibles Nomina";
            $fecha_extracto = substr($fecha, 0, 4);
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $IDSocio;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Desprendible de Nomina";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_factura_mi_club($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio)) {

            if (!empty($FechaInicio)) :
                $condicion_fecha = " and Fecha >= '" . $FechaInicio . "'";
            endif;

            if (!empty($FechaFin)) :
                $condicion_fecha .= " and Fecha <= '" . $FechaFin . "'";
            endif;

            $sql_facturas = "SELECT * FROM SocioFactura Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' " . $condicion_fecha . "  Order By Fecha Desc";
            $qry_factura = $dbo->query($sql_facturas);

            if ($dbo->rows($qry_factura) > 0) {
                $response = array();
                $message = $dbo->rows($qry_factura) . " Encontrados";
                while ($r = $dbo->fetchArray($qry_factura)) {
                    $factura["IDClub"] = $IDClub;
                    $factura["IDFactura"] = $r["IDSocioFactura"];
                    $factura["NumeroFactura"] = trim($r["Numero"]);
                    $factura["Fecha"] = $r["Fecha"];
                    $factura["ValorFactura"] = "$" . number_format(($r["ValorPagar"]), 0, ",", ".");
                    $array_categoria_factura["Factura"][] = $factura;
                }

                $response_categoria = array();
                foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                    $datos_factura_categoria["Nombre"] = $nombre_categoria;
                    $datos_factura_categoria["Facturas"] = $facturas;
                    array_push($response_categoria, $datos_factura_categoria);
                endforeach;

                if ($Dispositivo == "iOS") {
                    $datos_facturas["BuscadorFechas"] = true;
                } else {
                    $datos_facturas["BuscadorFechas"] = "S";
                }

                $datos_facturas["Categorias"] = $response_categoria;
                $datos_facturas["TipoFormaPago"] = $response_categoria;

                array_push($response, $datos_facturas);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "15. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_detalle_factura_mi_club($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $sql_facturas = "SELECT * FROM SocioFactura Where  IDSocioFactura = '" . $IDFactura . "' Order by Fecha ASC";
        $qry_factura = $dbo->query($sql_facturas);
        if ($dbo->rows($qry_factura) > 0) {
            $response = array();
            $message = $dbo->rows($qry_factura) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_factura)) {
                $cuerpo_factura = "";
                $cabeza_factura = "";
                $detalle_factura = "";
                $detalle_forma_pago = "";
                //Encabezado Factura
                $cabeza_factura = '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                <tbody>
                                <tr class="modo2">
                                    <td>MI Club</td>
                                </tr>
                                <tr class="modo2">
                                    <td>Nit. 8000000-7</td>
                                </tr>
                                <tr class="modo2">
                                    <td>Bogota - Colombia</td>
                                </tr>
                                <tr class="modo2">
                                    <td>4556677</td>
                                </tr>
                                <tr class="modo2">
                                    <td>' . SIMUtil::tiempo($r["Fecha"]) . '</td>
                                </tr>
                                </tbody>
                            </table>';

                //Consulto el detalle de la factura
                $detalle_factura = '
                            <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                <tbody>
                                <tr>

                                    <th>Fecha.</th>
                                    <th>Detalle</th>
                                    <th>Valor</th>
                                </tr>
                            ';
                $sql_detalle = "SELECT * FROM SocioFacturaDetalle Where IDSocioFactura = '" . $r["IDSocioFactura"] . "'";
                $qry_detalle = $dbo->query($sql_detalle);
                while ($row_detalle = $dbo->fetchArray($qry_detalle)) {
                    $detalle_factura .= '
                                <tr class="modo1">
                                    <td>' . $row_detalle["Fecha"] . '</td>
                                    <td>$' . $row_detalle["Detalle"] . '</td>
                                    <td>$' . number_format($row_detalle["Valor"], 0, ",", ".") . '</td>
                                </tr>
                                ';
                }

                $detalle_factura .= '
                                </tbody>
                            </table>
                            ';

                $cuerpo_factura = '<!doctype html>
                                            <html>
                                            <head>
                                            <meta charset="UTF-8">
                                            <title>Detalle Factura</title>
                                            <style>
                                            .tabla {
                                            font-family: Verdana, Arial, Helvetica, sans-serif;
                                            font-size:12px;
                                            text-align: center;
                                            width: 95%;
                                            align: center;
                                            }

                                            .tabla th {
                                            padding: 5px;
                                            font-size: 16px;
                                            background-color: #83aec0;
                                            background-repeat: repeat-x;
                                            color: #FFFFFF;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #558FA6;
                                            border-bottom-color: #558FA6;
                                            font-family: "Trebuchet MS", Arial;
                                            text-transform: uppercase;
                                            }

                                            .tabla .modo1 {
                                            font-size: 12px;
                                            font-weight:bold;
                                            background-color: #e2ebef;
                                            background-repeat: repeat-x;
                                            color: #34484E;
                                            font-family: "Trebuchet MS", Arial;
                                            }
                                            .tabla .modo1 td {
                                            padding: 5px;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #A4C4D0;
                                            border-bottom-color: #A4C4D0;
                                            text-align:right;
                                            }

                                            .tabla .modo1 th {
                                            background-position: left top;
                                            font-size: 12px;
                                            font-weight:bold;
                                            text-align: left;
                                            background-color: #e2ebef;
                                            background-repeat: repeat-x;
                                            color: #34484E;
                                            font-family: "Trebuchet MS", Arial;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #A4C4D0;
                                            border-bottom-color: #A4C4D0;
                                            }

                                            .tabla .modo2 {
                                            font-size: 12px;
                                            font-weight:bold;
                                            background-color: #fdfdf1;
                                            background-repeat: repeat-x;
                                            color: #990000;
                                            font-family: "Trebuchet MS", Arial;
                                            text-align:center;
                                            }
                                            .tabla .modo2 td {
                                            padding: 5px;
                                            }
                                            .tabla .modo2 th {
                                            background-position: left top;
                                            font-size: 12px;
                                            font-weight:bold;
                                            background-color: #fdfdf1;
                                            background-repeat: repeat-x;
                                            color: #990000;
                                            font-family: "Trebuchet MS", Arial;
                                            text-align:left;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #EBE9BC;
                                            border-bottom-color: #EBE9BC;
                                            }
                                            </style>
                                            </head>
                                            <body>
                                            ';

                $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $detalle_forma_pago;
                $cuerpo_factura .= '


                        </body>
                                        </html>';

                $ruta_pdf = "202944_202944.pdf";
                if (file_exists(DIRROOT . "../file/extractos/guaymaral/" . $ruta_pdf)) {
                    $cuerpo_factura = '
                                                                    <table align="center">
                                                                    <tr>
                                                                        <td>
                                                                        <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                                                        </td>
                                                                    </tr>
                                                                        <!--
                                                                        <tr>
                                                                            <td>
                                                                            <a href="' . URLROOT . "/file/extractos/guaymaral/" . $ruta_pdf . '">
                                                                            Descargar extracto
                                                                            <img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
                                                                            </a>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <iframe src="http://docs.google.com/gview?url=' . URLROOT . "/file/extractos/guaymaral/" . $ruta_pdf . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                                            </td>
                                                                        </tr>
                                                                        -->
                                                                        <tr>
                                                                            <td>
                                                                            <br>
                                                                            <br>Valor a Pagar: $1.200.000<br><br>
                                                                            <a href="https://www.miclubapp.com/file/noticia/578304_Comunicado_Vuelta_a_los_campos_COVID_19_VF.pdf">Ver documento</a>
                                                                            <br>
                                                                            <a href="https:/www.zonapagos.com/t_Clubcampestreguaymaral">
                                                                            <img src="' . URLROOT . 'plataform/assets/img/btnpago.png">
                                                                            </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>';
                }

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $r["IDSocioFactura"];
                $factura["NumeroFactura"] = $r["nmro_fctra"];
                $factura["Fecha"] = $r["fcha_rgstro"];
                $factura["ValorFactura"] = "$" . number_format($r["vlor_vnta"], 0, ",", ".");
                $factura["Almacen"] = $r["nmbre_almcen"];
                $factura["CuerpoFactura"] = $cuerpo_factura;
                $factura["BotonPago"] = "S";
                $factura["WebViewInterno"] = "S";
                $extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
                $factura["Referencia"] = $r["Referencia"];
                $factura["Observacion"] = $r["Observacion"];
                $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
                $factura["ValorFactura"] = "$" . number_format(($r["ValorPagar"]), 0, ",", ".");
                $factura["Almacen"] = "";
                $factura["TipoPago"] = "WebView";
                $factura["ObligatorioCodigoPago"] = "S";
                $factura["TextoConfirmacionPago"] = "El codigo es obligatorio";

                $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $r["Referencia"] . " por un valor de: " . $factura["ValorFactura"];

                array_push($response, $factura);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        }

        return $respuesta;
    }
    /* ... FIN URAKI ... */

    /* ... SIGMA ... */
    public function get_factura_sigma($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $DocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSANAPOIMA_DIR;

        //$AccionSocio="001-0241-7-1";

        self::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $documento_socio_pdf = $array_nombre_archivo[0];
                                    if ($DocumentoSocio == $documento_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        } else {
            echo "no existe";
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Facturacion";
            $fecha_extracto = substr($fecha, 0, 4);
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $IDSocio;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Estado de cuenta";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_sigma($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="150">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="middle">
                                                <iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                                                </a><br><br>
                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN SIGMA ... */

    /* ... MEDELLIN ... */
    public function get_factura_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        //$IDSocio=82704;
        //Solo los titulares
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        if (substr($datos_socio["Accion"], -2) == "00") { // Es el titular
            $AccionSocio = substr($datos_socio["Accion"], 0, -2);
            $consulta = " codigo_derecho = '" . $AccionSocio . "' ";
        } else {
            $AccionSocio = $datos_socio["Accion"];
            $consulta = " tso_codigo_cliente = '" . $AccionSocio . "' ";
        }

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $server = DBHOST_MEDELLIN;
        // Connect to Sql server CASMPRESTRE MEDELLIN

        try {
            $hostname = $server;
            $port = "";
            $dbname = DBNAME_MEDELLIN;
            $username = DBUSER_MEDELLIN;
            $pw = DBPASS_MEDELLIN;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
            exit;
        }

        $sql_consumo = "SELECT
                                    CAST(codigo_cliente AS INTEGER) AS codigo_cliente,
                                CAST(nombre_cliente AS TEXT) AS nombre_cliente,
                                    CAST(ident_cliente AS TEXT) AS ident_cliente,
                                    CAST(seccion AS INTEGER) AS seccion,
                                    CAST(concepto AS TEXT) AS concepto,
                                CAST(nombre_pagador AS TEXT) AS nombre_pagador,
                                    CAST(nit_pagador  AS TEXT) AS nit_pagador,
                                    CAST(cargo AS INTEGER) AS cargo,
                                    CAST(documento AS TEXT) AS documento,
                                    CAST(fecha AS DATETIME) AS fecha,
                                    CAST(area AS TEXT) AS area,
                                    CAST(cargo AS INTEGER) AS cargo
                                FROM [vapp_estados_cuenta]
                                    WHERE codigo_derecho = '" . $AccionSocio . "' ";

        $sql = $dbh->query($sql_consumo);

        while ($row_valor = $sql->fetch()) {
            $ValorTotal += (int) $row_valor["cargo"];
            $array_fecha = explode(" ", $row_valor["fecha"]);
        }

        $factura["IDClub"] = $IDClub;
        $valor_mes = (int) $row_valor["codigo_cliente"];
        $valor_mes = $row_valor["fecha"];
        $factura["IDFactura"] = $IDSocio;
        $factura["NumeroFactura"] = $datos_socio["IDSocio"];
        $factura["Fecha"] = $array_fecha[0];
        $factura["ValorFactura"] = "$" . number_format($ValorTotal, 0, '', '.');
        $factura["Almacen"] = utf8_decode($row_valor["Detalle"]);


        $array_categoria_factura[$nombre_categoria][] = $factura;

        //Consulto los movimientos si no tiene fecha traigo los del dia
        $AccionSocio = substr($datos_socio["Accion"], 0, -2);

        $sql = $dbh->query("SELECT
                                CAST(numero_factura AS TEXT) AS numero_factura,
                                CAST(fecha AS DATETIME) AS fecha,
                                CAST(lugar AS TEXT) AS lugar,
                                CAST(valor  AS INTEGER) AS valor,
                                CAST(codigo_derecho AS INTEGER) AS codigo_derecho,
                                CAST(tso_codigo_cliente AS INTEGER) AS tso_codigo_cliente,
                                CAST(codigo_cliente  AS INTEGER) AS codigo_cliente,
                                CAST(nombre_cajero AS TEXT) AS nombre_cajero
                            FROM vapp_enc_documento WHERE $consulta  ORDER BY fecha DESC ");

        while ($row = $sql->fetch()) {
            $nombre_categoria = "Historico de consumos";
            $fecha_extracto = "Fecha ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $row["numero_factura"] . "|Movimiento";
            $factura["NumeroFactura"] = "Factura: " . $row["numero_factura"];
            $ultimo_elemento = end($array_descripcion);
            //$factura["Fecha"] = utf8_encode($row["lugar"]);
            $factura["Fecha"] = $row["lugar"];
            $factura["ValorFactura"] = "$" . number_format($row["valor"], 0, '', '.');
            $factura["Almacen"] = $row["fecha"];
            $factura["Url"] = "url";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_medellin($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        $server = DBHOST_MEDELLIN;
        // Connect to Sql server CASMPRESTRE MEDELLIN
        try {
            $hostname = $server;
            $port = "";
            $dbname = DBNAME_MEDELLIN;
            $username = DBUSER_MEDELLIN;
            $pw = DBPASS_MEDELLIN;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
            exit;
        }

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $array_factura = explode("|", $IDFactura);
        if ($array_factura[1] != "Movimiento") {

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDFactura . "' ", "array");
            //$IDSocio=82704;
            if (substr($datos_socio["Accion"], -2) == "00") // Es el titular
            {
                $AccionSocio = substr($datos_socio["Accion"], 0, -2);
            } else {
                $AccionSocio = $datos_socio["Accion"];
            }

            $sql_detalle = "SELECT
                                                                 CAST(codigo_cliente AS INTEGER) AS codigo_cliente,
                                                               CAST(nombre_cliente AS TEXT) AS nombre_cliente,
                                                                 CAST(ident_cliente AS TEXT) AS ident_cliente,
                                                                 CAST(seccion AS INTEGER) AS seccion,
                                                                 CAST(concepto AS TEXT) AS concepto,
                                                               CAST(nombre_pagador AS TEXT) AS nombre_pagador,
                                                                 CAST(nit_pagador  AS TEXT) AS nit_pagador,
                                                                 CAST(cargo AS INTEGER) AS cargo,
                                                                 CAST(documento AS TEXT) AS documento,
                                                                 CAST(fecha AS DATETIME) AS fecha,
                                                                 CAST(area AS TEXT) AS area,
                                                                 CAST(cargo AS INTEGER) AS cargo,
                                                                 CAST(titular AS INTEGER) AS titular
                                                               FROM [vapp_estados_cuenta]
                                                                 WHERE codigo_derecho = $AccionSocio and  cargo>0 Order by titular asc ";

            $sql = $dbh->query($sql_detalle);

            $contador = 1;

            while ($row = $sql->fetch()) {
                $ValorTotal += (int) $row["cargo"];
                $array_fecha = explode(" ", $row["fecha"]);
                $array_cliente[utf8_encode($row["nombre_cliente"])] += (int) $row["cargo"];
                $NombreCliente = utf8_encode($row["nombre_cliente"]);
                $Titular = $row["titular"];
                $Area = $row["area"];
                $Concepto = $row["concepto"];
                $Documento = $row["documento"];
                $Fecha = $array_fecha[0] . ' ' . $array_fecha[1] . ' ' . $array_fecha[2];
                $array_detalle_cliente[$NombreCliente][$Area][$Concepto][$Documento] = $Fecha . "|" . (int) $row["cargo"];
            }

            //krsort($array_detalle_cliente);
            $tabla_detalle = "<table class='tabla'><tbody><tr><th colspan=3>Nombre</th><th>Doc.</th><th>Fecha</th><th>Cargo Mes</th></tr>";
            foreach ($array_detalle_cliente as $nombre_cliente => $array_area) {
                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $tabla_detalle .= '<tr bgcolor=' . $fondo . '>';
                $tabla_detalle .= '<td colspan=6 align=left style="font-weight:bold;">' . $nombre_cliente . '</td>';
                $tabla_detalle .= '</tr>';
                //echo "<br>" . $nombre_cliente;
                foreach ($array_area as $nombre_area => $array_concepto) {
                    $tabla_detalle .= '<tr bgcolor=' . $fondo . '>';
                    $tabla_detalle .= '<td>&nbsp;</td><td colspan=5 align=left style="color:#094713;font-weight:bold;">' . $nombre_area . '</td>';
                    $tabla_detalle .= '</tr>';
                    //echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_area;
                    foreach ($array_concepto as $nombre_concepto => $array_documento) {
                        //$tabla_detalle.='<tr bgcolor='.$fondo.'>';
                        //$tabla_detalle.='<td>&nbsp;</td><td>&nbsp;</td><td colspan=4>'.utf8_encode($nombre_concepto).'</td>';
                        //$tabla_detalle.='</tr>';
                        //echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_concepto .":";
                        foreach ($array_documento as $nombre_documento => $valor) {
                            $array_datos_detalle = explode("|", $valor);
                            $tabla_detalle .= '<tr bgcolor=' . $fondo . '>';
                            $tabla_detalle .= '<td>&nbsp;</td><td>&nbsp;</td><td align=left>' . utf8_encode($nombre_concepto) . '</td><td>' . $nombre_documento . '</td><td>' . $array_datos_detalle[0] . '</td><td align=right>$' . number_format($array_datos_detalle[1], 0, '', '.') . '</td>';
                            $tabla_detalle .= '</tr>';
                            //echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $nombre_documento.":".$valor;
                        }
                    }
                }
                $contador++;
            }
            $tabla_detalle .= "<tr><th colspan=5>TOTAL</th><th>$" . number_format($ValorTotal, 0, '', '.') . "</th></tr>";
            $tabla_detalle .= "<tbody></table>";

            $tabla_resumen = "<table class='tabla'><tbody<tr><th>Nombre</th><th>Cargo Mes</th></tr>";
            foreach ($array_cliente as $nombre => $valor) {
                $tabla_resumen .= "<tr><td>" . $nombre . "</td><td align='right'>$" . number_format($valor, 0, ',', '.') . "</td></tr>";
            }
            $tabla_resumen .= "</tbody></table>";
        } else {
            $sql_detalle_mov = "SELECT
                                        CAST(numero_factura AS TEXT) AS numero_factura,
                                        CAST(fecha AS DATETIME) AS fecha,
                                        CAST(lugar AS TEXT) AS lugar,
                                        CAST(valor  AS INTEGER) AS valor,
                                        CAST(codigo_derecho AS INTEGER) AS codigo_derecho,
                                        CAST(tso_codigo_cliente AS INTEGER) AS tso_codigo_cliente,
                                        CAST(codigo_cliente  AS INTEGER) AS codigo_cliente,
                                        CAST(nombre_cajero AS TEXT) AS nombre_cajero
                                       FROM vapp_enc_documento WHERE numero_factura = '" . $array_factura[0] . "'  ";
            $sql = $dbh->query($sql_detalle_mov);
            $row = $sql->fetch();

            // Resumen movimiento
            $tabla_resumen .= "<table class='tabla'><tbody";
            $tabla_resumen .= "<tr><th>Factura</th><td>" . $row["numero_factura"] . "</td></tr>";
            $tabla_resumen .= "<tr><th>Fecha</th><td>" . $row["fecha"] . "</td></tr>";
            $tabla_resumen .= "<tr><th>Lugar</th><td>" . utf8_encode($row["lugar"]) . "</td></tr>";
            $tabla_resumen .= "<tr><th>Valor</th><td>$" . number_format($row["valor"], 0, ',', '.') . "</td></tr>";
            $tabla_resumen .= "<tr><th>Cajero</th><td>" . utf8_encode($row["nombre_cajero"]) . "</td></tr>";
            $tabla_resumen .= "</tbody></table>";

            //Detalle de la factura
            $sql = $dbh->query("SELECT
                                    CAST(cantidad_consumo AS INTEGER) AS cantidad_consumo,
                                    CAST(numero_factura AS TEXT) AS numero_factura,
                                    CAST(codigo_cliente AS INTEGER) AS codigo_cliente,
                                    CAST(fecha_cuenta AS DATETIME) AS fecha_cuenta,
                                    CAST(desc_producto_pos  AS TEXT) AS desc_producto_pos,
                                    CAST(Valor AS INTEGER) AS Valor
                                    FROM vapp_det_documento WHERE numero_factura= '" . $array_factura[0] . "'  ");

            $tabla = "<table>
                                        <tr>
                                            <td>numero_factura</td>
                                            <td>codigo_cliente</td>
                                            <td>fecha_cuenta</td>
                                            <td>desc_producto_pos</td>
                                            <td>Valor</td>
                                        </tr>";

            $tabla_detalle .= "<table class='tabla'><tbody>";
            $tabla_detalle .= "<tr>";
            $tabla_detalle .= "<th>Cantidad</th>";
            $tabla_detalle .= "<th>Producto</th>";
            $tabla_detalle .= "<th>Valor</th></tr>";

            while ($row = $sql->fetch()) {
                $tabla_detalle .= "<tr>
                            <td>" . number_format($row["cantidad_consumo"], 0, ',', '.') . "</td>
                            <td>" . utf8_encode($row["desc_producto_pos"]) . "</td>
                            <td>$" . number_format($row[Valor], 0, ',', '.') . "</td>
                        </tr>";
            }
            $tabla_detalle .= "</tbody></table>";

            //Foma de pago
            $consulta_forma_pago = "SELECT
                                    CAST(numero_factura AS TEXT) AS numero_factura,
                                    CAST(tso_codigo_cliente AS INTEGER) AS tso_codigo_cliente,
                                    CAST(desc_tipo_pago AS TEXT) AS desc_tipo_pago,
                                    CAST(consec_pago_cuenta AS INTEGER) AS consec_pago_cuenta,
                                    CAST(valor AS INTEGER) AS valor,
                                    CAST(propina AS INTEGER) AS propina
                                     FROM vapp_pag_documento WHERE numero_factura= '" . $array_factura[0] . "' ";
            $sql_forma_pago = $dbh->query($consulta_forma_pago);

            $tabla_forma .= "<table class='tabla'><tbody>
                                        <tr>
                                            <th>Forma de pago</th>
                                            <th>Valor</th>
                                            <th>Propina</th>
                                        </tr>";

            while ($row_forma_pago = $sql_forma_pago->fetch()) {
                $tabla_forma .= "<tr>
                            <td>" . utf8_encode($row_forma_pago["desc_tipo_pago"]) . "</td>
                            <td>$" . number_format($row_forma_pago[valor], 0, ',', '.') . "</td>
                            <td>$" . number_format($row_forma_pago[propina], 0, ',', '.') . "</td>
                        </tr>";
            }
            $tabla_forma .= "</tbody></table>";

            $tabla_detalle .= $tabla_forma;
        }

        $response = array();
        $cuerpo_factura = '<!doctype html>
                              <html>
                              <head>
                              <meta charset="UTF-8">
                              <title>Detalle ESTADO CUENTA</title>
                              <style>
                              .tabla {
                              font-family: Verdana, Arial, Helvetica, sans-serif;
                              font-size:12px;
                              text-align: center;
                              width: 95%;
                              align: center;
                              }

                              .tabla th {
                              padding: 5px;
                              font-size: 16px;
                              background-color: #83aec0;
                              background-repeat: repeat-x;
                              color: #FFFFFF;
                              border-right-width: 1px;
                              border-bottom-width: 1px;
                              border-right-style: solid;
                              border-bottom-style: solid;
                              border-right-color: #558FA6;
                              border-bottom-color: #558FA6;
                              font-family: "Trebuchet MS", Arial;
                              text-transform: uppercase;
                              }

                              .tabla .modo1 {
                              font-size: 12px;
                              font-weight:bold;
                              background-color: #e2ebef;
                              background-repeat: repeat-x;
                              color: #34484E;
                              font-family: "Trebuchet MS", Arial;
                              }
                              .tabla .modo1 td {
                              padding: 5px;
                              border-right-width: 1px;
                              border-bottom-width: 1px;
                              border-right-style: solid;
                              border-bottom-style: solid;
                              border-right-color: #A4C4D0;
                              border-bottom-color: #A4C4D0;
                              text-align:right;
                              }

                              .tabla .modo1 th {
                              background-position: left top;
                              font-size: 12px;
                              font-weight:bold;
                              text-align: left;
                              background-color: #e2ebef;
                              background-repeat: repeat-x;
                              color: #34484E;
                              font-family: "Trebuchet MS", Arial;
                              border-right-width: 1px;
                              border-bottom-width: 1px;
                              border-right-style: solid;
                              border-bottom-style: solid;
                              border-right-color: #A4C4D0;
                              border-bottom-color: #A4C4D0;
                              }

                              .tabla .modo2 {
                              font-size: 12px;
                              font-weight:bold;
                              background-color: #fdfdf1;
                              background-repeat: repeat-x;
                              color: #990000;
                              font-family: "Trebuchet MS", Arial;
                              text-align:center;
                              }
                              .tabla .modo2 td {
                              padding: 5px;
                              }
                              .tabla .modo2 th {
                              background-position: left top;
                              font-size: 12px;
                              font-weight:bold;
                              background-color: #fdfdf1;
                              background-repeat: repeat-x;
                              color: #990000;
                              font-family: "Trebuchet MS", Arial;
                              text-align:left;
                              border-right-width: 1px;
                              border-bottom-width: 1px;
                              border-right-style: solid;
                              border-bottom-style: solid;
                              border-right-color: #EBE9BC;
                              border-bottom-color: #EBE9BC;
                              }
                              </style>
                              </head>
                              <body>
                                  <table align="left" width="100%" class="tabla">
                                      <tbody>
                                      <tr>
                                          <td>
                                          <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width=300 height=83>
                                          </td>
                                      </tr>
                                      <tr>
                                      <td>
                                      ' . $tabla_resumen . '
                                      </td>
                                      </tr>
                                      <tr>
                                      <td>
                                      ' . $tabla_detalle . '
                                      </td>
                                      </tr>
                                      <tbody>
                                  </table>
                              </body>
                          </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $extracto["Action"] = "";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];
        $WebInterno = "S";
        $factura["WebViewInterno"] = $WebInterno;

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN MEDELLIN ... */

    /* ... CLUB COLOMBIA ... */
    public function get_factura_colombia($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {

        $dbo = &SIMDB::get();

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        //$documento_socio="14940740";
        //$accion_socio="2396";
        //$secuencia_socio="00";
        $documento_socio = $datos_socio["NumeroDocumento"];
        $accion_socio == $datos_socio["Accion"];
        $secuencia_socio = "00";

        $Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_CLUBCLOLOMBIA, USUARIOZEUS_CLUBCOLOMBIA, CLAVEZEUS_CLUBCOLOMBIA);
        $array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_CLUBCLOLOMBIA, $Token, $documento_socio, $accion_socio, $secuencia_socio);
        $valor = str_replace(',', '.', $array_estado->item->saldocartera);

        $factura["IDClub"] = $IDClub;
        $valor_mes = (int) $row_valor["Mes"] - 1;
        $factura["IDFactura"] = $valor . "-" . $IDSocio;
        $factura["NumeroFactura"] = date("Y-m-d");
        $factura["Fecha"] = "Saldo a la fecha";
        $factura["ValorFactura"] = "$" . number_format($valor, 2, '', '.');
        $factura["Almacen"] = $row_valor["Detalle"];

        $array_categoria_factura[$nombre_categoria][] = $factura;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_colombia($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[1] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $valor = $array_dato_factura["0"];
        $valor = "$" . number_format($valor, 2, '', '.');

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            El saldo de cartera a la fecha es de: <br>' . $valor . '<br>
                                            <a href="' . URLROOT . 'credibanco.php?IDSocio=' . $datos_socio["IDSocio"] . '&valor=' . $valor . '&extra2=' . $IDClub . '&Modulo=Extracto">
                                            <!--<a href="https://www.clubcolombia.org/facturacion.php">-->
                                            <img src="' . URLROOT . 'plataform/assets/img/pagaaqui.png">
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";
        $extracto["Action"] = "";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = $valor;
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN CLUB COLOMBIA ... */

    /* ... ITALIANO ... */
    public function get_factura_italiano($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {

        $dbo = &SIMDB::get();

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        $documento_socio = $datos_socio["NumeroDocumento"];
        $accion_socio == "";
        $secuencia_socio = "";

        $Token = SIMWebServiceZeus::obtener_token_club(URLZEUS_ITALIANO, USUARIOZEUS_ITALIANO, CLAVEZEUS_ITALIANO);
        $array_estado = SIMWebServiceZeus::estado_socio(URLZEUS_CLUBCLOLOMBIA, $Token, $documento_socio, $accion_socio, $secuencia_socio);
        $valor = str_replace(',', '.', $array_estado->item->saldocartera);

        $factura["IDClub"] = $IDClub;
        $valor_mes = (int) $row_valor["Mes"] - 1;
        $factura["IDFactura"] = $valor . "-" . $IDSocio;
        $factura["NumeroFactura"] = date("Y-m-d");
        $factura["Fecha"] = "Saldo a la fecha";
        $factura["ValorFactura"] = "$" . number_format($valor, 2, '', '.');
        $factura["Almacen"] = $row_valor["Detalle"];

        $array_categoria_factura[$nombre_categoria][] = $factura;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_italiano($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[1] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $valor = $array_dato_factura["0"];
        $valor = "$" . number_format($valor, 2, '', '.');

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                <meta charset="UTF-8">
                                <title>Detalle ESTADO CUENTA</title>
                                </head>
                                <body>
                                    <table align="left" width="100%">
                                        <tr>
                                            <td>
                                            <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            El saldo de cartera a la fecha es de: <br>' . $valor . '<br>
                                            <a href="https://www.clubcolombia.org/facturacion.php">
                                            <img src="' . URLROOT . 'plataform/assets/img/btnpago.png">
                                            </td>
                                        </tr>
                                    </table>
                                </body>
                            </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "N";
        $extracto["Action"] = "";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = $valor;
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN ITALIANO ... */

    /* ... ARRAYANES ECUADOR ... */
    public function get_factura_arrayanes_ec($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {

        $dbo = &SIMDB::get();

        //$IDSocio=144383;

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $nombre_categoria = "Consumos";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT * FROM FacturaConsumo WHERE IDClub= '" . $IDClub . "' and IDSocio='" . $IDSocio . "' and Estado = 'PendientePago' ORDER BY IDFacturaConsumo DESC";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $row_valor["IDFacturaConsumo"];
            $factura["NumeroFactura"] = $row_valor["NumeroDocumentoFactura"];
            $factura["Fecha"] = $row_valor["FechaTrCr"];
            $factura["ValorFactura"] = "$" . number_format($row_valor["Total"], 2, ',', '.');
            $factura["Almacen"] = $row_valor["Estado"];
            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_arrayanes_ec($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_factura = $dbo->fetchAll("FacturaConsumo", " IDFacturaConsumo = '" . $IDFactura . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $array_datos_producto = explode("*", $datos_factura["Detalle"]);
        foreach ($array_datos_producto as $producto) {
            $array_producto = explode(",", $producto);
            $detalle_cuenta .= '
                    <tr bgcolor= #fff >
                        <td>' . $array_producto[1] . '</td>
                        <td align="center">' . $array_producto[2] . '</td>
                        <td>' . $array_producto[3] . '</td>
                    </tr>';
        }

        $detalle_cuenta .= '
                <tr bgcolor= #c47e7e >
                    <td></td>
                    <td>Iva: </td>
                    <td>$' . number_format($datos_factura["Iva"], 2, ',', '.') . '</td>
                </tr>
                <tr bgcolor= #c47e7e >
                    <td></td>
                    <td>Servicio: </td>
                    <td>$' . number_format($datos_factura["Servicio"], 2, ',', '.') . '</td>
                </tr>
                <tr bgcolor= #c47e7e >
                    <td></td>
                    <td>Valor Total: </td>
                    <td>$' . number_format($datos_factura["Total"], 2, ',', '.') . '</td>
                </tr>';

        //$texto_fac=str_replace("\r","<br>",$datos_factura["TextoRecibo"]);
        $texto_fac = $datos_factura["TextoRecibo"];

        $texto_fac = str_replace("ƒ", "&nbsp;", $texto_fac);
        $texto_fac = str_replace("Æ?", "&nbsp;", $texto_fac);

        if ($datos_factura["Estado"] != "Pagada") {
            $formas_pago = '
                            <tr>
                            <td align="center">
                                PAGAR CON
                                <a href="https://miclubapp.com/placetopaygeneral.php?id=' . $IDFactura . '&IDClub=' . $IDClub . '" target="_self">
                                <img src="' . URLROOT . 'plataform/assets/img/Logos_PlacetoPay.png" width=300 height=85>
                                </a>
                                <a href="https://miclubapp.com/ptp_historico.php?IDSocio=' . $datos_factura["IDSocio"] . '&IDClub=' . $IDClub . '" target="_blank">
                                Ver hist&oacute;rico de transacciones
                                </a>
                            </td>
                        </tr>';

            $formas_pago .= '
                        <tr>
                            <td align="center">
                                <!--<a href="https://miclubapp.com/payphone.php?id=' . $IDFactura . '&IDClub=' . $IDClub . '">-->
                                <img src="' . URLROOT . 'plataform/assets/img/payphone.jpg" width=200 height=122 >
                                <!--</a>-->
                                <form action="https://www.miclubapp.com/payphone.php" name="payphone" id="payphone" method="post">
                                    <input type="text" style="font-family: sans-serif;font-size: 18px;font-weight: 400;color: #ffffff;background: #889ccf;margin: 0 0 25px;overflow: hidden;padding: 20px;" name="Celular" id="Celular" placeholder="Digita tu numero de celular" required>
                                    <input type="hidden" name="IDFactura" value="' . $IDFactura . '">
                                    <input type="hidden" name="IDClub" value="' . $IDClub . '">
                                    <input type="submit" value="Enviar">
                                </form>
                            </td>
                        </tr>';
        }

        $response = array();
        $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Factura</title>
                                    </head>
                                    <body>
                                        <table align="left" width="100%">
                                            <tr>
                                                <td >
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                <table align="center">
                                                    <!--
                                                    <tr bgcolor="#24508e">

                                                        <td style="color:#FFF; font-size:24px; ">Producto</td>
                                                        <td style="color:#FFF;font-size:24px;">Cantidad</td>
                                                            <td style="color:#FFF;font-size:24px;">Valor</td>
                                                    </tr>
                                                    -->

                                                    <tr>
                                                    <td>
                                                    ' . $texto_fac . '
                                                    </td>
                                                    </tr>
                                                </table>
                                                <br>
                                                ESTADO FACTURA: ' . $datos_factura["Estado"] . '
                                                </td>
                                            </tr>
                                            ' . $formas_pago . '
                                        </table>
                                    </body>
                                </html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";
        $WebInterno = "S";
        $factura["WebViewInterno"] = $WebInterno;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN ARRAYANES ECUADOR ... */

    /* ... LAGARTOS ... */
    public function get_factura_lagartos($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $AccionSocio = substr($AccionSocio, 0, 4);

        //$AccionSocio="0125";
        $mes_actual = date("Y-m-") . "01";
        $sql_extracto = "Select IDSocioExtracto,MONTH(Fecha) as Mes, YEAR(Fecha) as Year, Valor,Fecha  From SocioExtracto Where Accion = '" . $AccionSocio . "' and Fecha >= '" . $mes_actual . "'";
        $r_extracto = $dbo->query($sql_extracto);
        while ($row_extracto = $dbo->fetchArray($r_extracto)) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($row_extracto["Fecha"], 4, 2) - 1)] . " de " . substr($row_extracto["Fecha"], 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $row_extracto["IDSocioExtracto"];
            $factura["NumeroFactura"] = $row_extracto["Year"];
            $factura["Fecha"] = $row_extracto["Fecha"];
            $factura["ValorFactura"] = $row_extracto["Valor"];
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        endwhile;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_lagartos($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        $datos_extracto = $dbo->fetchAll("SocioExtracto", " IDSocioExtracto = '" . $IDFactura . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $response = array();
        $pos = strpos($IDFactura, "Movimiento");
        if ($pos === false) { //Muestra Extracto
            $nombre_archivo = substr($IDFactura, 0, 3) . "_" . substr($IDFactura, 3);
            $ruta_local = EXTRACTOS_ROOT . $NumeroFactura . "/" . $nombre_archivo;

            $cuerpo_factura = '<!doctype html>
                                    <html>
                                    <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Extracto</title>

                                    </head>
                                    <body>
                                        <table align="center">
                                            <tr>
                                                <td>
                                                <img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:#0E1671; font-size:18px;">
                                                <br>
                                                <b>Valor Cancelar:</b> $' . number_format($datos_extracto["Valor"], 0, ',', '.') . '
                                                <br><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                <!--<a href="https://www.psepagos.co/PSEHostingUI/ShowTicketOffice.aspx?ID=2350">-->
                                                <a href="https://www.avalpaycenter.com/wps/portal/portal-de-pagos/web/pagos-aval/resultado-busqueda/realizar-pago?idConv=00009471&origen=buscar">
                                                <img src="' . URLROOT . 'plataform/assets/img/btnpago.png">
                                                </a>
                                                </td>
                                            </tr>
                                        </table>

                                    </body>
                                </html>';
        }

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $extracto["Action"] = "https://www.psepagos.co/PSEHostingUI/ShowTicketOffice.aspx?ID=2350";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
    /* ... FIN LAGARTOS ... */

    /* ... LA PRADERA ... */
    public function get_factura($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo = "", $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 619190)
            $IDSocio = 66592;

        // Connect to Sql server
        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas por el momento";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDSocio)) {
            $response = array();
            //Consulto la accion del socio
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if (!empty($datos_socio["AccionPadre"])) : // Es beneficiario
                //$condicion_nucleo = " and (AccionPadre = '".$datos_socio["AccionPadre"]."' or Accion = '".$datos_socio["AccionPadre"]."')";
                //$condicion_accion = "nmro_accion = '".$datos_socio["Accion"]."'";
                $condicion_accion = "nmro_accion = '" . trim($datos_socio["AccionPadre"]) . "'";
            else : // es Cabeza familia
                $sql_accion_nucleo = "Select Accion From Socio Where AccionPadre = '" . $datos_socio["Accion"] . "' and IDClub = '" . $IDClub . "'";
                $qry_accion_nucleo = $dbo->query($sql_accion_nucleo);
                while ($row_accion_nucleo = $dbo->fetchArray($qry_accion_nucleo)) :
                    $array_accion_nucleo[] = "nmro_accion = '" . $row_accion_nucleo["Accion"] . "'";
                endwhile;
                if (count($array_accion_nucleo) > 0) :
                    $acciones_nucleo = implode(" or ", $array_accion_nucleo);
                    $condicion_accion = "(" . $acciones_nucleo . " or nmro_accion = '" . $datos_socio["Accion"] . "' " . ")";
                else :
                    $condicion_accion = "( nmro_accion = '" . trim($datos_socio["Accion"]) . "' " . ")";
                endif;
            endif;

            $nmro_accion = $datos_socio["Accion"];
            //$nmro_accion = 538;
            //$condicion_accion = "nmro_accion = '699'";

            if (!empty($FechaInicio)) :
                $condicion_fecha = " and fcha_rgstro >= '" . $FechaInicio . "'";
            else :
                $FechaInicio = date("Y-m-d");
                $condicion_fecha = " and fcha_rgstro >= '" . $FechaInicio . "'";
            endif;

            if (!empty($FechaFin)) :
                $condicion_fecha .= " and fcha_rgstro <= '" . $FechaFin . "'";
            else :
                $FechaFin = date("Y-m-d");
                $condicion_fecha .= " and fcha_rgstro <= '" . $FechaFin . "'";
            endif;

            if ($IDSocio != 205635) :
                $condicionEstado = " AND cdgo_estdo = 'C' ";
            endif;

            // SACAMOS LAS FACTRAS DE SISCLUB EL SISTEMA DE LA PRADERA

            $sql_facturas = "SELECT TOP 100 * FROM v_ventas WHERE $condicion_accion  $condicion_fecha $condicionEstado Order By fcha_rgstro Desc";
            //$sql_facturas = "SELECT TOP 100 * FROM v_ventas WHERE fcha_rgstro >= '2022-11-10' and fcha_rgstro <= '2022-11-10'  Order By fcha_rgstro Desc";
            $qry_factura = $dbh->query($sql_facturas);
            while ($r = $qry_factura->fetch()) {
                $datos[] = $r;
            }
            $message = count($datos) . " Encontrados";
            foreach ($datos as $r) {
                //averiguo la forma de pago                  


                if (trim($r["nmro_fctra"]) == "") :
                    $NumFactura = $r["nmro_almcen"] . "|" . trim($r["nmro_fctra"]);
                else :
                    $NumFactura = trim($r["nmro_fctra"]);
                endif;

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $r["nmro_rgstro"] . "|" . $IDSocio . "|Facturas";
                $factura["NumeroFactura"] = $NumFactura;
                $factura["Fecha"] = $r["fcha_rgstro"];
                $factura["ValorFactura"] = "$" . number_format(($r["vlor_vnta"] + (int) $r["vlor_prpna"]), 0, ",", ".");
                $factura["Almacen"] = utf8_encode($r["nmro_almcen"]);

                if ($r[cdgo_estdo] == "C") :

                    $sql_forma_pago = "SELECT * FROM  v_frm_pgo Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                    $qry_forma_pago = $dbh->query($sql_forma_pago);

                    while ($row_forma_pago = $qry_forma_pago->fetch()) {
                        $array_categoria_factura[$row_forma_pago["nmbre_frma"]][] = $factura;
                    }

                else :

                    $NombreCategoria = "Facturas Abiertas";
                    $array_categoria_factura[$NombreCategoria][] = $factura;
                endif;
            }

            // FINN FACTURAS

            // EXTRAEMOS LOS EXTRACTOS DE LOS SOCIOS QUE LOS TENGAN

            if ($TipoApp == "Socio") :
                $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");
                $RutaExtractos = EXTRACTOSLAPRADERA_DIR;
            else :
                $AccionSocio = $dbo->getFields("Usuario", "NumeroDocumento", "IDUsuario = '" . $IDSocio . "'");
                $RutaExtractos = EXTRACTOSLAPRADERAEMPLEADOS_DIR;
            endif;

            if (is_dir($RutaExtractos)) {
                if ($dir = opendir($RutaExtractos)) {
                    while (($archivo = readdir($dir)) !== false) {
                        if (is_dir($RutaExtractos . $archivo)) {
                            if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                                while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                    if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                        //Capturo Accion Socio
                                        $array_nombre_archivo = explode("_", $archivo_carpeta);

                                        $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[0]);

                                        //Comparo si el archivo pertenece al socio a consultar
                                        if ($AccionSocio == $accion_socio_pdf) :
                                            $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                            $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                            $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                            copy($origen_copy, $destino_copy);
                                            $array_pdf[$archivo] = $nombre_encriptado;
                                        endif;
                                        // BUSCAMOS EL DOCUMENTO QUE TIENE LA FACTURA DEL SOCIO
                                        $factura_accion_socio_pdf = str_replace("-f.pdf", "", $array_nombre_archivo[0]);

                                        if ($AccionSocio == $factura_accion_socio_pdf) :
                                            $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                            $nombre_encriptado = "FACTURA-" . date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                            $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                            copy($origen_copy, $destino_copy);
                                            $array_pdf[$archivo] = $nombre_encriptado;
                                        endif;
                                    }
                                }
                            }
                            closedir($carpeta_mes);
                        }
                    }
                    closedir($dir);
                }
            }

            krsort($array_pdf);

            foreach ($array_pdf as $fecha => $archivo_extracto) :

                $nombre_categoria = "Extractos";

                $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $archivo_extracto . "|" . $IDSocio . "|Extracto";
                $factura["NumeroFactura"] = $fecha;
                $factura["Fecha"] = $fecha_extracto;
                $factura["ValorFactura"] = "Extracto";
                $factura["IDSocio"] = $IDSocio;

                $array_categoria_factura[$nombre_categoria][] = $factura;

            endforeach;

            // FIN EXTRACTOS

            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;


            $datos_facturas["BuscadorFechas"] = "S";


            $datos_facturas["Categorias"] = $response_categoria;




            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "15. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        unset($dbh);
        unset($stmt);
        unset($stmt2);
        return $respuesta;
    }

    public function get_detalle_factura($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        // Connect to Sql server
        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas por el momento";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDFactura)) {

            $datosfactura = explode("|", $IDFactura);
            $IDFactura = $datosfactura[0];
            $IDSocio = $datosfactura[1];
            $Tipo = $datosfactura[2];

            $Pos = strpos($NumeroFactura, "|");
            if ($Pos === false) :
                $condicion = "nmro_fctra = '$NumeroFactura'";
            else :
                $datosNumfactura = explode("|", $NumeroFactura);
                $NumeroFactura = $datosNumfactura[1];
                $Almacen = $datosNumfactura[0];

                $condicion = "nmro_almcen = '$Almacen'";
            endif;


            $response = array();

            if ($Tipo == "Facturas") :

                $sql_facturas = "SELECT TOP 1 * FROM v_ventas Where $condicion and nmro_rgstro = '" . $IDFactura . "' Order by fcha_rgstro desc";
                $qry_factura = $dbh->query($sql_facturas);

                $message = " Encontrados";
                while ($r = $qry_factura->fetch()) {

                    // print_r($r);
                    $cuerpo_factura = "";
                    $cabeza_factura = "";
                    $detalle_factura = "";
                    $detalle_forma_pago = "";
                    //Encabezado Factura
                    $cabeza_factura = '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                        <tbody>
                                            <tr class="modo2">
                                            <td>La Pradera de Potosi Club Residencial</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>Nit. 832001216-7</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>Km 19 v&iacute;a La Calera - Sopo</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>8757777</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>' . substr($r["fcha_rgstro"], 0, 12) . '</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>' . $r["nmro_accion"] . ' - ' . utf8_encode($r["nmbre_scio"]) . '</td>
                                            </tr>
                                            <tr class="modo2">
                                            <td>' . $r["nmbre_almcen"] . '</td>
                                            </tr>
                                        </tbody>
                                        </table>';

                    //Consulto el detalle de la factura
                    $detalle_factura = '
                                        <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                        <tbody>
                                            <tr>
                                            <th>Item</th>
                                            <th>Cant.</th>
                                            <th>Vr uni.</th>
                                            <th>Vr total</th>
                                            </tr>
                                        ';

                    $sql_detalle = "SELECT * FROM v_dtlle_venta Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                    $r_detalle = $dbh->query($sql_detalle);
                    while ($row_detalle = $r_detalle->fetch()) {
                        $detalle_factura .= '
                                            <tr class="modo1">
                                            <td>' . utf8_encode($row_detalle["nmbre_prdcto"]) . '</td>
                                            <td>' . number_format($row_detalle["cntdad"], 0, ",", ".") . '</td>
                                            <td>$' . number_format($row_detalle["vlor_untrio"], 0, ",", ".") . '</td>
                                            <td>$' . number_format($row_detalle["vlor_ttal"], 0, ",", ".") . '</td>
                                            </tr>
                                            ';
                    }
                    if ((int) $r["vlor_prpna"] > 0) :
                        $detalle_factura .= '
                                                                    <tr class="modo1">
                                                                    <td>Sub Total:</td>
                                                                    <td>1</td>
                                                                    <td>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</td>
                                                                    <td>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</td>
                                                                    </tr>
                                                                    ';
                    endif;

                    $detalle_factura .= '
                                            <tr>
                                            <th colspan="3">Sub Total: </th>
                                            <th>$' . number_format($r["vlor_vnta"], 0, ",", ".") . '</th>
                                            </tr>';

                    if ((int) $r["vlor_prpna"] > 0) :
                        $detalle_factura .= '
                                                                        <tr>
                                                                        <th colspan="3">Propina: </th>
                                                                        <th>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</th>
                                                                        </tr>';
                    endif;

                    $detalle_factura .= '
                                            <tr>
                                            <th colspan="3">Total Venta</th>
                                            <th>$' . number_format(($r["vlor_vnta"] + (int) $r["vlor_prpna"]), 0, ",", ".") . '</th>
                                            </tr>
                                        </tbody>
                                        </table>
                                        ';
                    //Consulto la forma de pago d la factura
                    $detalle_forma_pago = '
                                        <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                        <tbody>
                                            <tr>
                                            <th colspan="2">Forma de Pago</th>
                                            </tr>
                                        ';
                    $sql_forma_pago = "SELECT * FROM  v_frm_pgo Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                    $r_forma_pago = $dbh->query($sql_forma_pago);
                    while ($row_forma_pago = $r_forma_pago->fetch()) {
                        $detalle_forma_pago .= '
                                            <tr class="modo1">
                                            <td>' . utf8_encode($row_forma_pago["nmbre_frma"]) . '</td>
                                            <td>' . number_format($row_forma_pago["vlor_pgar"], 0, ",", ".") . '</td>
                                            </tr>
                                            ';
                    }

                    $detalle_forma_pago .= '
                                        </tbody>
                                        </table>
                                        ';

                    if ($r[cdgo_estdo] == 'A') :

                        $MaxFactura = "SELECT cnsctvo_dian AS NumeroFactura FROM almcnes WHERE nmro_almcen = '$r[nmro_almcen]'";
                        $qryMaxFactura = $dbh->query($MaxFactura);
                        $row = $qryMaxFactura->fetch();

                        $TotalPagar = $r["vlor_vnta"];
                        $NumeroFactura = $row[NumeroFactura] + 1;
                        $Propina = (0.1 * (float)$TotalPagar);

                        $boton =
                            '<form name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                            <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                <tr class="modo2">
                                    <td align="center">
                                        PAGAR                    
                                    </td>
                                </tr>
                                <tr class="modo2">
                                    <td align="center">
                                        Propina (Sugerido 10%)
                                        <input type="text" name="Propina" value="' . $Propina . '" >                            
                                    </td>
                                </tr>
                                <!-- <tr class="modo2">
                                    <td align="center">
                                        Total A Pagar
                                        <input type="number" name="valor" value="' . $TotalPagar . '" >                            
                                    </td>
                                </tr> -->
                                <tr class="modo2">
                                    <td align="center">
                                        Tipo de Pago<br>
                                        <input type="radio" name="TipoPago" value="CargoSocio"> Cargo a Socio.<br>                        
                                        <input type="radio" name="TipoPago" value="Credibanco"> Tarjeta de credito/debito                         
                                    </td>
                                </tr>
                                <tr class="modo2">
                                    <td align="center">          
                                        <input type="hidden" name="valor" id="valor" value="' . $TotalPagar . '">
                                        <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                                        <input type="hidden" name="extra1" id="extra1" value="' . $IDFactura . '">
                                        <input type="hidden" name="IDSocio" id="IDSocio" value="' . $IDSocio . '">
                                        <input type="hidden" name="ref" id="ref" value="' . $NumeroFactura . '">
                                        <input type="hidden" name="Almacen" id="Almacen" value="' . $r[nmro_almcen] . '">
                                        <input type="hidden" name="Modulo" id="Modulo" value="FacturasPraderaPotosi">                                                                        
                                        <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                                        <input type="submit" value="Enviar">
                                    </td>
                                </tr>                        
                            </table>
                        </form>';

                    endif;

                    $factura["WebViewInterno"] = "S";
                    $factura["IDFactura"] = $r["nmro_rgstro"];
                    $factura["NumeroFactura"] = $r["nmro_fctra"];
                    $factura["Fecha"] = $r["fcha_rgstro"];
                    $factura["ValorFactura"] = "$" . number_format($r["vlor_vnta"], 0, ",", ".");
                    $factura["Almacen"] = $r["nmbre_almcen"];
                }
            else :

                $nombre_archivo = substr($IDFactura, 0, 3) . "_" . substr($IDFactura, 3);
                $logo = $dbo->getFields("Club", "FotoLogoApp", "IDClub = $IDClub");

                $ruta_local = EXTRACTOSTMP_ROOT .  $IDFactura;
                $ruta_ext = EXTRACTOSTMP_DIR .  $IDFactura;

                $detalle_producto = '  
                <tbody>
                    <br><br>
                    <!--tr>
                        <td>
                            <img src="' . CLUB_ROOT . $logo . '" width="300" height="100">
                        </td>
                    </tr-->
                    <tr>
                        <td>
                            <iframe src="http://docs.google.com/gview?url=' . $ruta_local . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
                        </td>
                    </tr>
                </tbody>';

                /*  $boton =
                    '<form target="_self" name="frmpago" id="frmpago" action="' . URLROOT . 'CredibancoVApi.php" method="post">
                    <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                        <tr class="modo2">
                            <td align="center">
                                PAGAR EXTRACTO                 
                            </td>
                        </tr>                        
                        <tr class="modo2">
                            <td align="center">
                                Total A Pagar, porfavor especificar<br>
                                <input type="number" name="valor" value="">                            
                            </td>
                        </tr>                        
                        <tr class="modo2">
                            <td align="center">          
                                <input type="hidden" name="extra2" id="extra2" value="' . $IDClub . '">
                                <input type="hidden" name="extra1" id="extra1" value="' . $IDFactura . '">
                                <input type="hidden" name="IDSocio" id="IDSocio" value="' . $IDSocio . '">
                                <input type="hidden" name="ref" id="ref" value="' . $NumeroFactura . '">                                
                                <input type="hidden" name="Modulo" id="Modulo" value="ExtractoPradera">                                
                                <input type="image" src="' . URLROOT . 'plataform/assets/img/logo_90pse.png" alt="Submit">
                            </td>
                        </tr>                        
                    </table>
                </form>'; */



                $detalle_factura =
                    '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                    <tr>
                        <td>
                        <table border="0" width="100%">
                            ' . $detalle_producto . '
                        </table>
                        </td>
                    </tr>
                </table>';

                $factura["WebViewInterno"] = "S";
                $factura["IDFactura"] = $IDFactura;

            endif;

            $cuerpo_factura = '<!doctype html>
                                <html>
                                <head>
                                    <meta charset="UTF-8">
                                    <title>Detalle Factura</title>
                                    <style>
                                    .tabla {
                                    font-family: Verdana, Arial, Helvetica, sans-serif;
                                    font-size:12px;
                                    text-align: center;
                                    width: 95%;
                                    align: center;
                                    }

                                    .tabla th {
                                    padding: 5px;
                                    font-size: 16px;
                                    background-color: #83aec0;
                                    background-repeat: repeat-x;
                                    color: #FFFFFF;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #558FA6;
                                    border-bottom-color: #558FA6;
                                    font-family: "Trebuchet MS", Arial;
                                    text-transform: uppercase;
                                    }

                                    .tabla .modo1 {
                                    font-size: 12px;
                                    font-weight:bold;
                                    background-color: #e2ebef;
                                    background-repeat: repeat-x;
                                    color: #34484E;
                                    font-family: "Trebuchet MS", Arial;
                                    }
                                    .tabla .modo1 td {
                                    padding: 5px;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #A4C4D0;
                                    border-bottom-color: #A4C4D0;
                                    text-align:right;
                                    }

                                    .tabla .modo1 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight:bold;
                                    text-align: left;
                                    background-color: #e2ebef;
                                    background-repeat: repeat-x;
                                    color: #34484E;
                                    font-family: "Trebuchet MS", Arial;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #A4C4D0;
                                    border-bottom-color: #A4C4D0;
                                    }

                                    .tabla .modo2 {
                                    font-size: 12px;
                                    font-weight:bold;
                                    background-color: #fdfdf1;
                                    background-repeat: repeat-x;
                                    color: #990000;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align:center;
                                    }
                                    .tabla .modo2 td {
                                    padding: 5px;
                                    }
                                    .tabla .modo2 th {
                                    background-position: left top;
                                    font-size: 12px;
                                    font-weight:bold;
                                    background-color: #fdfdf1;
                                    background-repeat: repeat-x;
                                    color: #990000;
                                    font-family: "Trebuchet MS", Arial;
                                    text-align:left;
                                    border-right-width: 1px;
                                    border-bottom-width: 1px;
                                    border-right-style: solid;
                                    border-bottom-style: solid;
                                    border-right-color: #EBE9BC;
                                    border-bottom-color: #EBE9BC;
                                    }
                                    </style>
                                </head>
                                <body>
                                ';

            $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $detalle_forma_pago . "<br>" . $boton;
            $cuerpo_factura .= '</body></html>';

            $factura["IDClub"] = $IDClub;
            $factura["CuerpoFactura"] = $cuerpo_factura;
            $factura["WebViewInterno"] = "S";

            array_push($response, $factura);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }
        return $respuesta;
    }

    /*  public function get_factura_pradera($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo = "")
    {
        $dbo = &SIMDB::get();

        if ($IDSocio == 61803)
            $IDSocio = 66511;

        // Connect to Sql server
        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDSocio)) {
            $response = array();
            //Consulto la accion del socio
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if (!empty($datos_socio["AccionPadre"])) : // Es beneficiario
                $condicion_accion = "nmro_accion = '" . $datos_socio["AccionPadre"] . "'";
            else : // es Cabeza familia
                $sql_accion_nucleo = "Select Accion From Socio Where AccionPadre = '" . $datos_socio["Accion"] . "' and IDClub = '" . $IDClub . "'";
                $qry_accion_nucleo = $dbo->query($sql_accion_nucleo);
                while ($row_accion_nucleo = $dbo->fetchArray($qry_accion_nucleo)) :
                    $array_accion_nucleo[] = "nmro_accion = '" . $row_accion_nucleo["Accion"] . "'";
                endwhile;
                if (count($array_accion_nucleo) > 0) :
                    $acciones_nucleo = implode(" or ", $array_accion_nucleo);
                    $condicion_accion = "(" . $acciones_nucleo . " or nmro_accion = '" . $datos_socio["Accion"] . "' " . ")";
                else :
                    $condicion_accion = "( nmro_accion = '" . $datos_socio["Accion"] . "' " . ")";
                endif;
            endif;

            $nmro_accion = $datos_socio["Accion"];


            if (!empty($FechaInicio)) :
                $condicion_fecha = " and fcha_rgstro >= '" . $FechaInicio . "'";
            endif;

            if (!empty($FechaFin)) :
                $condicion_fecha .= " and fcha_rgstro <= '" . $FechaFin . "'";
            endif;

            $sql_facturas = "SELECT TOP 100 * FROM v_ventas Where  " . $condicion_accion . " " . $condicion_fecha . "  Order By fcha_rgstro Desc";
            $qry_factura = $dbh->query($sql_facturas);
            while ($r = $qry_factura->fetch()) {
                $datos[] = $r;
            }

            $message = count($datos) . " Encontrados";
            foreach ($datos as $r) {
                //averiguo la forma de pago

                $sql_forma_pago = "SELECT * FROM  v_frm_pgo Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                $qry_forma_pago = $dbh->query($sql_forma_pago);

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $r["nmro_rgstro"];
                $factura["NumeroFactura"] = trim($r["nmro_fctra"]);
                $factura["Fecha"] = $r["fcha_rgstro"];
                $factura["ValorFactura"] = "$" . number_format(($r["vlor_vnta"] + (int) $r["vlor_prpna"]), 0, ",", ".");
                $factura["Almacen"] = utf8_encode($r["nmbre_almcen"]);

                while ($row_forma_pago = $qry_forma_pago->fetch()) {

                    $array_categoria_factura[$row_forma_pago["nmbre_frma"]][] = $factura;
                }
            }
            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            if ($Dispositivo == "iOS") {
                $datos_facturas["BuscadorFechas"] = true;
            } else {
                $datos_facturas["BuscadorFechas"] = "S";
            }

            $datos_facturas["Categorias"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "15. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        unset($dbh);
        unset($stmt);
        unset($stmt2);
        return $respuesta;
    } */
    /*


 */
    public function get_detalle_factura_vencidas_pereira($IDClub, $IDSocio, $IDFactura)
    {
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");

        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");


        if (!empty($FechaInicio)) :
            $condicion_fecha = " and Fecha >= '" . $FechaInicio . "'";
        endif;

        if (!empty($FechaFin)) :
            $condicion_fecha .= " and Fecha <= '" . $FechaFin . "'";
        endif;

        $sql_PagosPendientes = "SELECT * FROM CarteraVencida Where IDSocio = '" . $IDSocio . "'  and IDCarteraVencida='" . $IDFactura . "' Order By  IDCarteraVencida  DESC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        if ($dbo->rows($qry_PagosPendientes) > 0) {
            $response = array();
            $message = $dbo->rows($qry_PagosPendientes) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_PagosPendientes)) {
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = ($r["Fecha"]);
                $factura["NumeroFactura"] = ($r["Accion"]);
                $factura["NumeroFactura"] = ($r["Factura"]);
                $factura["Fecha"] = $r["Fecha"];
                $factura["ValorFactura"] = "$" . number_format(($r["Valor"]), 0, ",", ".");
                $array_categoria_factura["Factura"][] = $factura;
            }



            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            if ($Dispositivo == "iOS") {
                $datos_facturas["BuscadorFechas"] = false;
            } else {
                $datos_facturas["BuscadorFechas"] = "N";
            }

            $datos_facturas["Categorias"] = $response_categoria;
            $datos_facturas["TipoFormaPago"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
    /* ... FIN LA FACTURAS VENCIDAS PEREIRA ... */

    public function get_detalle_factura_pradera($IDClub, $IDFactura, $IDSocio)
    {
        $IDSocio = SIMNet::req("IDSocio");
        require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");

        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $IDSocio . "'");


        if (!empty($FechaInicio)) :
            $condicion_fecha = " and Fecha >= '" . $FechaInicio . "'";
        endif;

        if (!empty($FechaFin)) :
            $condicion_fecha .= " and Fecha <= '" . $FechaFin . "'";
        endif;

        $sql_PagosPendientes = "SELECT `IDCarteraVencida`, `IDClub`, `IDSocio`, `Accion`, `IDTercero`, `NombreTercero`, `Factura`, `Tc`, `NombreTipoCartera`, MAX(`Fecha`) AS Fecha, `FechaPago`, SUM(`Valor`) as Valor, `Dias`, `Vendedor`, `NombreVendedor`, `CiudadResidencia`, `Tel_Recidencia`, `DireccionRecidencia`, `Zona`, `Region`, `CiudadCorrespondencia`, `TelCorrespondencia`, `DireccionCorrespondencia`, `Celular` FROM CarteraVencida Where IDSocio = '" . $IDSocio . "' Group by IDSocio Order By  IDCarteraVencida  DESC";
        $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

        if ($dbo->rows($qry_PagosPendientes) > 0) {
            $response = array();
            $message = $dbo->rows($qry_PagosPendientes) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_PagosPendientes)) {
                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = ($r["Fecha"]);
                $factura["Accion"] = ($r["Accion"]);
                $factura["IDTercero"] = ($r["IDTercero"]);
                $factura["NombreTercero"] = ($r["NombreTercero"]);
                $factura["Fecha"] = $r["Fecha"];
                $factura["ValorFacturaTotal"] = "$" . number_format(($r["Valor"]), 0, ",", ".");
                $array_categoria_factura["Factura"][] = $factura;
            }



            $response_categoria = array();
            foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                $datos_factura_categoria["Nombre"] = $nombre_categoria;
                $datos_factura_categoria["Facturas"] = $facturas;
                array_push($response_categoria, $datos_factura_categoria);
            endforeach;

            if ($Dispositivo == "iOS") {
                $datos_facturas["BuscadorFechas"] = false;
            } else {
                $datos_facturas["BuscadorFechas"] = "N";
            }

            $datos_facturas["Categorias"] = $response_categoria;
            $datos_facturas["TipoFormaPago"] = $response_categoria;

            array_push($response, $datos_facturas);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;

        /*
        
    
        $dbo = &SIMDB::get();
        // Connect to Sql server
        try {
            $hostname = DBHOST_PRADERA;
            $port = "";
            $dbname = DBNAME_PRADERA;
            $username = DBUSER_PRADERA;
            $pw = DBPASS_PRADERA;
            $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            //echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de facturas por el momento";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDFactura)) {

            $sql_facturas = "SELECT TOP 1 * FROM v_ventas Where  nmro_fctra = '" . $NumeroFactura . "' and nmro_rgstro = '" . $IDFactura . "' Order by fcha_rgstro desc";
            $qry_factura = $dbh->query($sql_facturas);

            $response = array();
            $message = " Encontrados";
            while ($r = $qry_factura->fetch()) {
                $cuerpo_factura = "";
                $cabeza_factura = "";
                $detalle_factura = "";
                $detalle_forma_pago = "";
                //Encabezado Factura
                $cabeza_factura = '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                      <tbody>
                                        <tr class="modo2">
                                          <td>La Pradera de Potosi Club Residencial</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>Nit. 832001216-7</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>Km 19 v&iacute;a La Calera - Sopo</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>8757777</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>' . substr($r["fcha_rgstro"], 0, 12) . '</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>' . $r["nmro_accion"] . ' - ' . utf8_encode($r["nmbre_scio"]) . '</td>
                                        </tr>
                                        <tr class="modo2">
                                          <td>' . $r["nmbre_almcen"] . '</td>
                                        </tr>
                                      </tbody>
                                    </table>';

                //Consulto el detalle de la factura
                $detalle_factura = '
                                    <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                      <tbody>
                                        <tr>
                                          <th>Item</th>
                                          <th>Cant.</th>
                                          <th>Vr uni.</th>
                                          <th>Vr total</th>
                                        </tr>
                                    ';

                $sql_detalle = "SELECT * FROM v_dtlle_venta Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                $r_detalle = $dbh->query($sql_detalle);
                while ($row_detalle = $r_detalle->fetch()) {
                    $detalle_factura .= '
                                         <tr class="modo1">
                                          <td>' . utf8_encode($row_detalle["nmbre_prdcto"]) . '</td>
                                          <td>' . number_format($row_detalle["cntdad"], 0, ",", ".") . '</td>
                                          <td>$' . number_format($row_detalle["vlor_untrio"], 0, ",", ".") . '</td>
                                          <td>$' . number_format($row_detalle["vlor_ttal"], 0, ",", ".") . '</td>
                                        </tr>
                                        ';
                }
                if ((int) $r["vlor_prpna"] > 0) :
                    $detalle_factura .= '
						                                        <tr class="modo1">
						                                          <td>Sub Total:</td>
						                                          <td>1</td>
						                                          <td>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</td>
						                                          <td>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</td>
						                                        </tr>
						                                        ';
                endif;

                $detalle_factura .= '
                                         <tr>
                                          <th colspan="3">Sub Total: </th>
                                          <th>$' . number_format($r["vlor_vnta"], 0, ",", ".") . '</th>
                                        </tr>';

                if ((int) $r["vlor_prpna"] > 0) :
                    $detalle_factura .= '
						                                             <tr>
						                                              <th colspan="3">Propina: </th>
						                                              <th>$' . number_format($r["vlor_prpna"], 0, ",", ".") . '</th>
						                                            </tr>';
                endif;

                $detalle_factura .= '
                                         <tr>
                                          <th colspan="3">Total Venta</th>
                                          <th>$' . number_format(($r["vlor_vnta"] + (int) $r["vlor_prpna"]), 0, ",", ".") . '</th>
                                        </tr>
                                      </tbody>
                                    </table>
                                    ';
                //Consulto la forma de pago d la factura
                $detalle_forma_pago = '
                                    <table border="0" cellpadding="0" cellspacing="0" class="tabla">
                                      <tbody>
                                        <tr>
                                          <th colspan="2">Forma de Pago</th>
                                        </tr>
                                    ';
                $sql_forma_pago = "SELECT * FROM  v_frm_pgo Where nmro_rgstro = '" . $r["nmro_rgstro"] . "' and nmro_almcen = '" . $r["nmro_almcen"] . "'";
                $r_forma_pago = $dbh->query($sql_forma_pago);
                while ($row_forma_pago = $r_forma_pago->fetch()) {
                    $detalle_forma_pago .= '
                                        <tr class="modo1">
                                          <td>' . utf8_encode($row_forma_pago["nmbre_frma"]) . '</td>
                                          <td>' . number_format($row_forma_pago["vlor_pgar"], 0, ",", ".") . '</td>
                                        </tr>
                                        ';
                }

                $detalle_forma_pago .= '
                                     </tbody>
                                    </table>
                                    ';

                $cuerpo_factura = '<!doctype html>
                                                    <html>
                                                    <head>
                                                    <meta charset="UTF-8">
                                                    <title>Detalle Factura</title>
                                                    <style>
                                                    .tabla {
                                                    font-family: Verdana, Arial, Helvetica, sans-serif;
                                                    font-size:12px;
                                                    text-align: center;
                                                    width: 95%;
                                                    align: center;
                                                    }

                                                    .tabla th {
                                                    padding: 5px;
                                                    font-size: 16px;
                                                    background-color: #83aec0;
                                                    background-repeat: repeat-x;
                                                    color: #FFFFFF;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #558FA6;
                                                    border-bottom-color: #558FA6;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-transform: uppercase;
                                                    }

                                                    .tabla .modo1 {
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #e2ebef;
                                                    background-repeat: repeat-x;
                                                    color: #34484E;
                                                    font-family: "Trebuchet MS", Arial;
                                                    }
                                                    .tabla .modo1 td {
                                                    padding: 5px;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #A4C4D0;
                                                    border-bottom-color: #A4C4D0;
                                                    text-align:right;
                                                    }

                                                    .tabla .modo1 th {
                                                    background-position: left top;
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    text-align: left;
                                                    background-color: #e2ebef;
                                                    background-repeat: repeat-x;
                                                    color: #34484E;
                                                    font-family: "Trebuchet MS", Arial;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #A4C4D0;
                                                    border-bottom-color: #A4C4D0;
                                                    }

                                                    .tabla .modo2 {
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #fdfdf1;
                                                    background-repeat: repeat-x;
                                                    color: #990000;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-align:center;
                                                    }
                                                    .tabla .modo2 td {
                                                    padding: 5px;
                                                    }
                                                    .tabla .modo2 th {
                                                    background-position: left top;
                                                    font-size: 12px;
                                                    font-weight:bold;
                                                    background-color: #fdfdf1;
                                                    background-repeat: repeat-x;
                                                    color: #990000;
                                                    font-family: "Trebuchet MS", Arial;
                                                    text-align:left;
                                                    border-right-width: 1px;
                                                    border-bottom-width: 1px;
                                                    border-right-style: solid;
                                                    border-bottom-style: solid;
                                                    border-right-color: #EBE9BC;
                                                    border-bottom-color: #EBE9BC;
                                                    }
                                                    </style>
                                                    </head>
                                                    <body>
                                                    ';

                $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $detalle_forma_pago;
                $cuerpo_factura .= '</body>
                                                </html>';

                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $r["nmro_rgstro"];
                $factura["NumeroFactura"] = $r["nmro_fctra"];
                $factura["Fecha"] = $r["fcha_rgstro"];
                $factura["ValorFactura"] = "$" . number_format($r["vlor_vnta"], 0, ",", ".");
                $factura["Almacen"] = $r["nmbre_almcen"];
                $factura["CuerpoFactura"] = $cuerpo_factura;
                $factura["WebViewInterno"] = "N";
                array_push($response, $factura);
            }

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }
        return $respuesta;
         */
    }
    /* ... FIN LA PRADERA ... */


    public function get_factura_zeus($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $nombre_categoria = "Extractos";
        $fecha_extracto = "Extracto Disponible";
        $factura["IDClub"] = $IDClub;
        $factura["IDFactura"] = "Extracto" . $IDSocio;
        $factura["NumeroFactura"] = $fecha;
        $factura["Fecha"] = date("Y-m");
        $factura["ValorFactura"] = "Extracto";
        $factura["Almacen"] = "";
        $array_categoria_factura[$nombre_categoria][] = $factura;

        //Movimientos
        $nombre_categoria = "Movimientos";

        $Mes_hasta = (int) date("m") - 1;
        $Mes_desde = $Mes_hasta - 5;
        if ((int) $Mes_hasta <= 0) {
            $Mes_hasta = 0;
        }

        if ((int) $Mes_desde <= 0) {
            $Mes_desde = 0;
        }

        for ($i = $Mes_hasta; $i >= $Mes_desde; $i--) :
            $fecha_extracto = "Movimiento Disponible";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = "Movimiento" . $i . "Movimiento" . $IDSocio;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = SIMResources::$meses[(int) $i];
            $factura["ValorFactura"] = "Movimientos";
            $factura["Almacen"] = "";
            $array_categoria_factura[$nombre_categoria][] = $factura;
        endfor;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function borrar_extracto_tmp()
    {

        $RutaExtractosTMP = EXTRACTOSTMP_DIR;

        if (is_dir($RutaExtractosTMP)) {
            if ($dir = opendir($RutaExtractosTMP)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractosTMP . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractosTMP . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {

                                    $pos = strpos($archivo_carpeta, ".pdf");
                                    if ($pos === false) {
                                    } else {
                                        $array_nombre_archivo = explode("-", $archivo_carpeta);
                                        if (count($array_nombre_archivo) > 1) :
                                            $fecha_hora_archivo = strtotime($array_nombre_archivo[0]);
                                            //10 minutos antes
                                            $fecha = date('Y-m-j H:i:s');
                                            $nuevafecha = strtotime('-10 minutes', strtotime($fecha));
                                            if ($fecha_hora_archivo <= $nuevafecha && !empty($archivo_carpeta)) :
                                                $ruta_borrar = EXTRACTOSTMP_DIR . $archivo_carpeta;
                                                unlink($ruta_borrar);
                                            endif;
                                        endif;
                                    }
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }
        return $respuesta;
    }

    public function get_factura_mi_club_pagos($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio)) {

            if (!empty($FechaInicio)) :
                $condicion_fecha = " and FechaTrCr >= '" . $FechaInicio . "'";
            endif;

            if (!empty($FechaFin)) :
                $condicion_fecha .= " and FechaTrCr <= '" . $FechaFin . "'";
            endif;

            $sql_PagosPendientes = "SELECT * FROM SocioPagosPendientes Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' " . $condicion_fecha . "  Order By IDSocioPagosPendientes Desc";
            $qry_PagosPendientes = $dbo->query($sql_PagosPendientes);

            if ($dbo->rows($qry_PagosPendientes) > 0) {
                $response = array();
                $message = $dbo->rows($qry_PagosPendientes) . " Encontrados";
                while ($r = $dbo->fetchArray($qry_PagosPendientes)) {
                    $factura["IDClub"] = $IDClub;
                    $factura["IDFactura"] = $r["IDSocioPagosPendientes"];
                    $factura["NumeroFactura"] = trim($r["IDSocioPagosPendientes"]);
                    $factura["Fecha"] = $r["PagueseAntesDe"];
                    $factura["ValorFactura"] = "$" . number_format(($r["TotalPagar"]), 0, ",", ".");
                    $array_categoria_factura["Factura"][] = $factura;
                }

                $response_categoria = array();
                foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
                    $datos_factura_categoria["Nombre"] = $nombre_categoria;
                    $datos_factura_categoria["Facturas"] = $facturas;
                    array_push($response_categoria, $datos_factura_categoria);
                endforeach;

                if ($Dispositivo == "iOS") {
                    $datos_facturas["BuscadorFechas"] = false;
                } else {
                    $datos_facturas["BuscadorFechas"] = "N";
                }

                $datos_facturas["Categorias"] = $response_categoria;
                $datos_facturas["TipoFormaPago"] = $response_categoria;

                array_push($response, $datos_facturas);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "15. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_detalle_factura_mi_club_pagos($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll(
            "Club",
            " IDClub = '" . $IDClub . "' ",
            "array"
        );

        $sql_SocioPagosPendientes = "SELECT * FROM SocioPagosPendientes Where  IDSocioPagosPendientes = '" . $IDFactura . "' Order by FechaTrCr ASC";
        $qry_SocioPagosPendientes = $dbo->query($sql_SocioPagosPendientes);
        if ($dbo->rows($qry_SocioPagosPendientes) > 0) {
            $response = array();
            $message = $dbo->rows($qry_SocioPagosPendientes) . " Encontrados";
            while ($r = $dbo->fetchArray($qry_SocioPagosPendientes)) {
                $cuerpo_factura = "";
                $cabeza_factura = "";
                $detalle_factura = "";
                $detalle_forma_pago = "";
                //Encabezado Factura
                $cabeza_factura = '<table cellpadding="0" cellspacing="0" class="tabla">
                                <tbody>
                                <tr class="modo2">
                                    <td>
                                        <img src="' . CLUB_ROOT . 'header-detalle-pagos.png">
                                    </td>
                                </tr>
                                </tbody>
                            </table>';

                //Consulto el detalle de la factura
                $detalle_factura = '
                <table cellpadding="0" cellspacing="0" class="tabla">
                <tbody>
                                <tr>
                                    <td class="header">P&Aacute;GUESE ANTES DE</td>
                                    <td class="td">' . $r["PagueseAntesDe"] . '</td>
                                </tr>
                                <tr>
                                    <td class="header">SALDO ANTERIOR EXTRACTO</td>
                                    <td class="td">$' . number_format($r["SaldoAnterior"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="header">TOTAL PAGOS</td>
                                    <td class="td">$' . number_format($r["TotalPagos"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="header">TOTAL CONSUMOS</td>
                                    <td class="td">$' . number_format($r["TotalCompras"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="header">CUOTA SOSTENIMIENTO</td>
                                    <td class="td">$' . number_format($r["CuotaSostenimiento"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="header">COBRO PREDIAL</td>
                                    <td class="td">$' . number_format($r["CobroPredial"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="header">NOTAS CR&Eacute;DITO</td>
                                    <td class="td">$' . number_format($r["NotasCredito"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="headerTotal">TOTAL A PAGAR</td>
                                    <td class="tdTotal">$' . number_format($r["TotalPagar"], 0, ",", ".") . '</td>
                                </tr>
                                <tr>
                                    <td class="link" colspan="2">
                                        <a href="https://www.zonapagos.com/t_countryclubbd/pagos.asp"> Click aquí para pagar <a/>
                                        <br>
                                        <br>
                                        <p>
                                            Los pagos realizados después de la fecha de corte, se verán reflejados en el próximo estado de cuenta
                                        </p>
                                    </td>
                                </tr>
                            ';


                $detalle_factura .= '
                                </tbody>
                            </table>
                            ';

                $cuerpo_factura = '<!doctype html>
                                            <html>
                                            <head>
                                            <meta charset="UTF-8">
                                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                            <title>Detalle Factura</title>
                                            <style>
                                            * {
                                                margin: 0%;
                                                padding: 0%;
                                                box-sizing: border-box;
                                            }
                                            .tabla {
                                            font-family: Verdana, Arial, Helvetica, sans-serif;
                                            text-align: center;
                                            width: 100%;
                                            }
                                            tbody{
                                                background-color: #a40d2e;
                                            }
                                            .tabla .header {
                                            padding-top: 5px;
                                            padding-bottom: 5px;
                                            font-size: 12px;
                                            background-color: #a40d2e;
                                            background-repeat: repeat-x;
                                            color: #FFFFFF;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #558FA6;
                                            border-bottom-color: #558FA6;
                                            text-transform: uppercase;
                                            }
                                            .tabla .headerTotal{
                                                background-color: #c69a00;
                                                color: #fff;
                                                font-size: 12px;
                                            }

                                            .td, .tdTotal {
                                            padding: 5px;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            background-color: #fff;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #A4C4D0;
                                            border-bottom-color: #A4C4D0;
                                            text-align:right;
                                            font-size: 12px;
                                            }
                                            .tdTotal{
                                                font-size: 12px;
                                                font-weight: 600;
                                            }

                                            .tabla .modo1 th {
                                            background-position: left top;
                                            font-size: 12px;
                                            font-weight:bold;
                                            text-align: left;
                                            background-color: #e2ebef;
                                            background-repeat: repeat-x;
                                            color: #34484E;
                                            font-family: "Trebuchet MS", Arial;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #A4C4D0;
                                            border-bottom-color: #A4C4D0;
                                            }

                                            .tabla .modo2 {
                                            font-size: 12px;
                                            font-weight:bold;
                                            background-color:trasparent;
                                            background-repeat: repeat-x;
                                            color: #c69a00;
                                            font-family: "Trebuchet MS", Arial;
                                            text-align:center;
                                            }
                                            .tabla .modo2 td {
                                            padding: 5px;
                                            }
                                            .tabla .modo2 th {
                                            background-position: left top;
                                            font-size: 12px;
                                            font-weight:bold;
                                            background-color: #fdfdf1;
                                            background-repeat: repeat-x;
                                            color: #990000;
                                            font-family: "Trebuchet MS", Arial;
                                            text-align:left;
                                            border-right-width: 1px;
                                            border-bottom-width: 1px;
                                            border-right-style: solid;
                                            border-bottom-style: solid;
                                            border-right-color: #EBE9BC;
                                            border-bottom-color: #EBE9BC;
                                            }
                                            .link {
                                                background-color: #fff;
                                                padding: 3%;
                                                font-size: 12px;
                                                word-break: break-word;

                                            }
                                            img{
                                                width: 100%
                                            }
                                            </style>
                                            </head>
                                            <body>
                                            ';

                $cuerpo_factura .= $cabeza_factura . "<br>" . $detalle_factura . "<br>" . $detalle_forma_pago;
                $cuerpo_factura .= '


                        </body>
                                        </html>';



                $factura["IDClub"] = $IDClub;
                $factura["IDFactura"] = $r["IDSocioPagosPendientes"];
                $factura["NumeroFactura"] = $r["IDSocioPagosPendientes"];
                $factura["Fecha"] = $r["FechaTrCr"];
                $factura["ValorFactura"] = "$" . number_format($r["TotalPagar"], 0, ",", ".");
                // $factura["Almacen"] = $r["nmbre_almcen"];
                $factura["CuerpoFactura"] = $cuerpo_factura;
                $factura["BotonPago"] = "N";
                $factura["WebViewInterno"] = "S";
                $extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
                $factura["Referencia"] = $r["IDSocioPagosPendientes"];
                $factura["Observacion"] = $r["NotasCredito"];
                $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
                // $factura["ValorFactura"] = "$" . number_format(($r["ValorPagar"]), 0, ",", ".");
                $factura["Almacen"] = "";
                $factura["TipoPago"] = "WebView";
                $factura["ObligatorioCodigoPago"] = "S";
                $factura["TextoConfirmacionPago"] = "El codigo es obligatorio";

                $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $r["IDSocioPagosPendientes"] . " por un valor de: " . $factura["ValorFactura"];

                array_push($response, $factura);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $message = "No hay pagos pendientes";
            $respuesta["message"] = $message;
            $respuesta["success"] = false;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    }

    // NUEVAS

    public function get_factura_ftp2($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $host = '190.66.22.93';
        $user = 'miclubapp';
        $pass = 'Miclub2017';

        //$AccionSocio="0001";

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");

        /// set up basic connection
        $conn_id = ftp_connect($host, "64007");

        //Comprobar que la conexión ha tenido éxito
        if (!$conn_id) {
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        // login with username and password
        $login_result = ftp_login($conn_id, $user, $pass);

        // enabling passive mode
        ftp_pasv($conn_id, true);

        // Obtener los archivos contenidos en el directorio actual
        $files = ftp_nlist($conn_id, "");

        foreach ($files as $file) :
            if (ftp_size($conn_id, $file) == -1) : //Es directorio
                $array_directorio[] = $file;
                //Ingreso a ese directorio y consulto los archivos
                $directorio = ftp_nlist($conn_id, $file);
                foreach ($directorio as $archivo) :
                    $ruta_original = $archivo;
                    $archivo = str_replace($file . "/", "", $archivo);
                    //Capturo Accion Socio
                    $array_nombre_archivo = explode("_", $archivo);
                    $accion_socio_pdf = str_replace(".pdf", "", $array_nombre_archivo[1]);
                    //Comparo si el archivo pertenece al socio a consultar
                    if ($AccionSocio == $accion_socio_pdf) :

                        //$guardar_local = EXTRACTOS_DIR."/".$IDSocio.$file."extractor.pdf";
                        //$ruta_local = $IDSocio.$file."extractor.pdf";
                        $ruta_local = $ruta_original;
                        // descargar $server_file y guardarlo en $local_file
                        //ftp_get($conn_id, $guardar_local, $ruta_original, FTP_BINARY);

                        /*
                            if (ftp_get($conn_id, $guardar_local, $ruta_original, FTP_BINARY)) {
                            echo "Se ha guardado satisfactoriamente en $local_file <br>";
                            } else {
                            echo "Ha habido un problemaaa<br>";
                            }
                             */

                        $array_pdf[$file] = $ruta_local;

                    endif;
                endforeach;
            endif;
        endforeach;

        foreach ($array_pdf as $fecha => $archivo_extracto) :

            $nombre_categoria = "Extractos";

            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";

            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";

            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_zeus($IDClub, $IDFactura, $NumeroFactura)
    {

        //Valido si es el detalle del extracto o el de los movimientos
        $pos = strpos($IDFactura, "Extracto");
        if ($pos === false) { //Consulta movimientos
            $array_movimiento = explode("Movimiento", $IDFactura);
            $Mes = $array_movimiento[1];
            $IDSocio = $array_movimiento[2];
            if (strlen($Mes) <= 1) {
                $Mes = "0" . $Mes;
            }

            if ($IDSocio == "84887" || $IDSocio == "5533") :
                $IDSocio = "85176";
            endif;

            $respuesta = SIMWebServiceZeus::consulta_movimiento($IDClub, $IDSocio, $Mes);
        } else { // Consulta extractos
            $array_extracto = explode("Extracto", $IDFactura);
            $IDSocio = array_pop($array_extracto);

            if ($IDSocio == "84887" || $IDSocio == "5533") :
                $IDSocio = "85176";
            endif;

            $respuesta = SIMWebServiceZeus::consulta_extracto($IDClub, $IDSocio);
        }
        return $respuesta;
    }

    public function get_detalle_factura_app2($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();

        $host = '190.66.22.93';
        $user = 'miclubapp';
        $pass = 'Miclub2017';

        //$AccionSocio="0001";

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");

        /// set up basic connection
        $conn_id = ftp_connect($host, "64007");

        //Comprobar que la conexión ha tenido éxito
        if (!$conn_id) {
            $respuesta["message"] = "Lo sentimos no hay conexion a la base de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $carpeta_archivo_ftp = substr($IDFactura, 0, 6);
        $archivo_ftp = substr($IDFactura, 6, 3) . "_" . substr($IDFactura, 9);
        $ruta_archivo_ftp = $carpeta_archivo_ftp . "/" . $archivo_ftp;

        $guardar_local = EXTRACTOS_DIR . "/" . $IDClub . "_" . $carpeta_archivo_ftp . "_" . $archivo_ftp;
        $ruta_local = EXTRACTOS_ROOT . "/" . $IDClub . "_" . $carpeta_archivo_ftp . "_" . $archivo_ftp;

        // login with username and password
        $login_result = ftp_login($conn_id, $user, $pass);

        // enabling passive mode
        ftp_pasv($conn_id, true);

        ftp_get($conn_id, $guardar_local, $ruta_archivo_ftp, FTP_BINARY);

        // cerrar la conexión ftp
        ftp_close($conn_id);

        $response = array();

        $cuerpo_factura = '<!doctype html>
							<html>
							<head>
							<meta charset="UTF-8">
							<title>Detalle Extracto</title>

							</head>
							<body>
								<a href="' . $ruta_local . '">
								Pulse aca para ver el Extracto
								</a>
							</body>
						</html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_factura_mi_conjunto($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();
        $IDClub = 71;

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_socio["Predio"];
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $nombre_categoria = "Estado Cuenta";
        $fecha_extracto = date("Y");

        $sql_valor = "SELECT SUM(Debito) as ValorPagar,month(Fecha) as Mes,year(Fecha) as Anio,SMC.* FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' Group by year(Fecha),month(Fecha)";
        $r_valor = $dbo->query($sql_valor);
        while ($row_valor = $dbo->fetchArray($r_valor)) {
            $FechaInicioConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-01";
            $FechaFinConsulta = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-30";
            //Consulto si tiene saldo en Cartera
            $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
            $r_cartera = $dbo->query($sql_cartera);
            $row_cartera = $dbo->fetchArray($r_cartera);
            if ((int) $row_cartera["Total"] > 0) {
                $row_valor["ValorPagar"] += $row_cartera["Total"];
            }

            $factura["IDClub"] = $IDClub;
            $valor_mes = (int) $row_valor["Mes"] - 1;
            $factura["IDFactura"] = $row_valor["Anio"] . "-" . $row_valor["Mes"] . "-" . $IDSocio;
            $factura["NumeroFactura"] = SIMResources::$meses[$valor_mes] . " de " . $row_valor["Anio"];
            $factura["Fecha"] = date("Y");
            $factura["ValorFactura"] = "$" . number_format($row_valor["ValorPagar"], 0, '', '.');
            $factura["Almacen"] = $row_valor["Detalle"];

            $array_categoria_factura[$nombre_categoria][] = $factura;
        }

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :
            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_detalle_factura_mi_conjunto($IDClub, $IDFactura, $NumeroFactura)
    {
        $IDClub = 71;
        $dbo = &SIMDB::get();
        $array_dato_factura = explode("-", $IDFactura);
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $array_dato_factura[2] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $predio = str_replace("-", "", $datos_socio["Predio"]);

        $year_consulta = $array_dato_factura[0];
        $mes_consulta = $array_dato_factura[1];

        $sql_valor = "SELECT * FROM SocioMovimientoCuenta SMC WHERE IDClub= '" . $IDClub . "' and Nit='" . $predio . "' and month(Fecha)='" . $mes_consulta . "' and year(Fecha)='" . $year_consulta . "'";
        $r_valor = $dbo->query($sql_valor);
        $contador = 1;
        while ($row_valor = $dbo->fetchArray($r_valor)) {

            if ((int) $row_valor["Debito"] > 0) {
                $descripcion_concepto = $row_valor["Nombre"];
                $array_valor[$row_valor["Nombre"]] += $row_valor["Debito"];
                $valor_mostrar = $row_valor["Debito"];
            }

            if ((int) $row_valor["Credito"] > 0) {
                $descripcion_concepto = $row_valor["Detalle"];
                $array_valor[$row_valor["Nombre"]] -= $row_valor["Credito"];
                $valor_mostrar = "-" . $row_valor["Credito"];
            }

            if ($array_valor[$row_valor["Nombre"]] > 0) {

                $valor_total += $row_valor["Debito"];
                if ($contador % 2) {
                    $fondo = "#f7f7f7";
                } else {
                    $fondo = "#FFF";
                }

                $detalle_cuenta .= '
					<tr bgcolor=' . $fondo . '>
						<td>' . $descripcion_concepto . '</td>
					<td>$' . number_format($valor_mostrar, 0, '', '.') . '</td>
					</tr>';
                $contador++;
                $ref_pago = $row_valor["Codigo"];
                $valor_pagar += $row_valor["Debito"];
            }
            $array_valor_anterior[$row_valor["Nombre"]] = $array_valor[$row_valor["Nombre"]];
        }

        //Consulto si tiene saldo en Cartera
        $FechaInicioConsulta = $year_consulta . "-" . $mes_consulta . "-01";
        $FechaFinConsulta = $year_consulta . "-" . $mes_consulta . "-30";
        //Consulto si tiene saldo en Cartera
        $sql_cartera = "SELECT * FROM SocioSaldoCartera WHERE IDClub= '" . $IDClub . "' and Codigo='" . $predio . "' and FechaTrCr >='" . $FechaInicioConsulta . "' and FechaTrCr<='" . $FechaFinConsulta . "' Order by IDSocioSaldoCartera DESC LIMIT 1";
        $r_cartera = $dbo->query($sql_cartera);
        $row_cartera = $dbo->fetchArray($r_cartera);
        if ((int) $row_cartera["Total"] > 0) {
            $valor_total += $row_cartera["Total"];
            $detalle_cuenta .= '
				<tr bgcolor=' . $fondo . '>
					<td>Saldos anteriores</td>
				<td>$' . number_format($row_cartera["Total"], 0, '', '.') . '</td>
				</tr>';
        }

        $detalle_cuenta .= '
			<tr bgcolor= #c47e7e >
				<td>Valor Total: </td>
			<td>$' . number_format($valor_total, 0, '', '.') . '</td>
			</tr>';

        $response = array();
        $cuerpo_factura = '<!doctype html>
								<html>
								<head>
								<meta charset="UTF-8">
								<title>Detalle ESTADO CUENTA</title>
								</head>
								<body>
									<table align="left" width="100%">
										<tr>
											<td>
											<img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '">
											</td>
										</tr>
										<tr>
											<td>
											<table align="center">
												<tr bgcolor="#24508e">
													<td style="color:#FFF">Detalle</td>
													<td style="color:#FFF">Valor</td>
												</tr>
												' . $detalle_cuenta . '
											</table>
											</td>
										</tr>
									</table>
								</body>
							</html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "S";
        $extracto["Action"] = "https://www.e-collect.com/customers/Plus/ClubCampestrePlus.html";

        $factura["Referencia"] = $r["Referencia"];
        $factura["Observacion"] = $r["Observacion"];
        $factura["UrlWebView"] = $dbo->getFields("Club", "UrlPago", "IDClub = '" . $IDClub . "'");
        $factura["ValorFactura"] = "$" . number_format($r["ValorPagar"], 0, ",", ".");
        $factura["Almacen"] = "";
        $factura["TipoPago"] = "WebView";
        $factura["ObligatorioCodigoPago"] = "N";
        $factura["TextoConfirmacionPago"] = "El codigo es obligatorio, debe ser: " . $datos_socio["Predio"];

        $factura["EncabezadoWebView"] = "Su referencia de pago es: " . $datos_socio["Predio"] . " por un valor de: $" . number_format($valor_pagar, 0, " ", ".") . " después de realizar el pago registre ese valor a continuación en Codigo de pago";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_certificado($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $RutaExtractos = EXTRACTOSRANCHO_DIR . "/certificados/";

        //$AccionSocio="001-0241-7-1";

        SIMWebServiceFacturas::borrar_extracto_tmp();

        if (is_dir($RutaExtractos)) {
            if ($dir = opendir($RutaExtractos)) {
                while (($archivo = readdir($dir)) !== false) {
                    if (is_dir($RutaExtractos . $archivo)) {
                        if ($carpeta_mes = opendir($RutaExtractos . $archivo)) {
                            while (($archivo_carpeta = readdir($carpeta_mes)) !== false) {
                                if ($archivo_carpeta != '.' && $archivo_carpeta != '..' && $archivo_carpeta != '.htaccess') {
                                    //Capturo Accion Socio
                                    $array_nombre_archivo = explode(".", $archivo_carpeta);
                                    $accion_socio_pdf = $array_nombre_archivo[0];
                                    if ($AccionSocio == $accion_socio_pdf) :
                                        //$array_pdf[$archivo]=$archivo_carpeta;
                                        //lo copio con nombre encriptado para tenerlo temporalmente
                                        $origen_copy = $RutaExtractos . $archivo . "/" . $archivo_carpeta;
                                        $nombre_encriptado = date("YmdHis") . "-" . SIMUtil::generarPassword("6") . rand(0, 10000000) . ".pdf";
                                        $destino_copy = EXTRACTOSTMP_DIR . $nombre_encriptado;
                                        copy($origen_copy, $destino_copy);
                                        $array_pdf[$archivo] = $nombre_encriptado;
                                    endif;
                                }
                            }
                        }
                        closedir($carpeta_mes);
                    }
                }
                closedir($dir);
            }
        }

        krsort($array_pdf);

        foreach ($array_pdf as $fecha => $archivo_extracto) :
            $nombre_categoria = "Extractos";
            $fecha_extracto = SIMResources::$meses[((int) substr($fecha, 4, 2) - 1)] . " de " . substr($fecha, 0, 4) . " ";
            $factura["IDClub"] = $IDClub;
            $factura["IDFactura"] = $archivo_extracto;
            $factura["NumeroFactura"] = $fecha;
            $factura["Fecha"] = $fecha_extracto;
            $factura["ValorFactura"] = "Extracto";
            $factura["Almacen"] = "";
            $factura["Url"] = base64_encode(EXTRACTOSTMP_ROOT);
            $array_categoria_factura[$nombre_categoria][] = $factura;

        endforeach;

        $response = array();
        $response_categoria = array();
        foreach ($array_categoria_factura as $nombre_categoria => $facturas) :

            $datos_factura_categoria["Nombre"] = $nombre_categoria;
            $datos_factura_categoria["Facturas"] = $facturas;
            array_push($response_categoria, $datos_factura_categoria);
        endforeach;

        $datos_facturas["BuscadorFechas"] = "N";
        $datos_facturas["Categorias"] = $response_categoria;

        array_push($response, $datos_facturas);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // cerrar la conexión ftp
        ftp_close($conn_id);

        return $respuesta;
    }

    public function get_detalle_factura_uraki($IDClub, $IDFactura, $NumeroFactura)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $ruta_ext = EXTRACTOSTMP_ROOT . "/" . $IDFactura;

        $response = array();
        $cuerpo_factura = '<!doctype html>
								<html>
								<head>
								<meta charset="UTF-8">
								<title>Detalle Extracto</title>

								</head>
								<body>
									<table align="center">
										<tr>
											<td>
											<img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="150" height="150">
											</td>
										</tr>
										<tr>
											<td valign="middle">
											<iframe src="http://docs.google.com/gview?url=' . EXTRACTOSTMP_ROOT . $IDFactura . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
											</a><br><br>
											</td>
										</tr>
									</table>

								</body>
							</html>';

        $factura["IDClub"] = $IDClub;
        $factura["CuerpoFactura"] = $cuerpo_factura;
        $factura["IDFactura"] = $IDFactura;
        $factura["TotalPagar"] = (int) $datos_extracto["Valor"];
        $factura["BotonPago"] = "N";
        $factura["WebViewInterno"] = "S";
        $extracto["Action"] = "";

        array_push($response, $factura);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function set_codigo_pago($IDClub, $IDSocio, $IDFactura, $Codigo)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDFactura)) {

            $sql_pqr = $dbo->query("Update SocioFactura Set CodigoPago = '" . $Codigo . "', FechaCodigoPago = NOW() Where IDFacturaSocio = '" . $IDFactura . "'");

            //SIMUtil::noticar_pago($IDFactura);

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "122. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
}
