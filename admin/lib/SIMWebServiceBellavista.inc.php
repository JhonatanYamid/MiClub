<?php

class SIMWebServiceBellavista
{
    public function GetMembers($CardCode)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetMembers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS;

    }

    public function GetOrders($CardCode)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetOrders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=' . $CardCode . "&Referencia=",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);      
        $DATOS = simplexml_load_string($response);
        return $DATOS;
    }

    public function GetAlbercas($edadSocio, $CardCode, $Secuencia)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetItem_ALBERCAS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=' . $CardCode . '&edadSocio=' . $edadSocio . '&Secuencia=' . $Secuencia,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS;
    }

    public function GetCanchasTenis($edadSocio, $CardCode, $Secuencia)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetItem_CANCHAS_TENIS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=' . $CardCode . '&edadSocio=' . $edadSocio . '&Secuencia=' . $Secuencia,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS;
    }

    public function GetGreenFees($edadSocio, $CardCode, $Secuencia)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetItem_GREEN_FEES',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=' . $CardCode . '&edadSocio=' . $edadSocio . '&Secuencia=' . $Secuencia,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
       
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS;
    }

    public function GetCarrosGolf($edadSocio, $CardCode, $Secuencia)
    {
        $curl = curl_init();

        $llave = urlencode(LLAVE_SAP_BELLAVISTA);

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_SAP_BELLAVISTA . '/GetItem_RENTA_CARROS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'llave=' . $llave . '&CardCode=' . $CardCode . '&edadSocio=' . $edadSocio . '&Secuencia=' . $Secuencia,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS;
    }

    public function get_servicios_adicionales_bellavista($IDClub, $IDSocio, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

        $Accion = $DatosSocio[Accion];
        $DatosAccion = explode("-",$Accion);
        $CardCode = $DatosAccion[0];
        $Secuencia = "0".$DatosAccion[1];

        $message = "Adicionales";
        $Nombre_cat = "";
        $contador_cat = 0;
        $response = array();
        $response_carac_producto = array();
        $response_valores_carac = array();
        $response_lista_producto = array();

        $FechaNacimiento = $DatosSocio[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($FechaNacimiento), date_create($dia_actual));
        $edad = $edad_diff->format('%y');

        $IDSistemaExterno = $IDServicio . $IDClub;

        // BUSCAMOS SI YA ESXISTE EN LA BASE DE DATOS SI EXISTE GRENN FEES
        $id = $dbo->getFields("ServicioPropiedad", "IDServicioPropiedad", "IDSistemaExterno = $IDSistemaExterno");
        if (empty($id)):
            $Nombre = "Green Fees";

            // INSERTAR EN LAS TABLAS ServicioPropiedad y ServicioAdicional LOS DATOS PARA CONSULTA
            $insert = "INSERT INTO ServicioPropiedad (IDServicio, IDClub, IDSistemaExterno, Nombre, Tipo, Obligatorio, MaximoPermitido, Publicar)
		                        VALUES ('$IDServicio', '$IDClub', '$IDSistemaExterno','$Nombre','Radio','N','1','S')";
            $dbo->query($insert);
            $IDServicioPropiedad = $dbo->lastID();
        else:
            $IDServicioPropiedad = $id;
        endif;

        // CONSULTO CON LA EDAD DEL SOCIO PARA SABER QUE GREEN FEES TIENE ACTIVOS
        $GreenFee = SIMWebServiceBellavista::GetGreenFees($edad,$CardCode,$Secuencia);

       
        foreach ($GreenFee->Result->Articulos as $id => $Articulo):

            if($Articulo->PermisoReserva == "SI"):
                $id = $dbo->getFields("ServicioAdicional", "IDServicioAdicional", "IDSistemaExterno = $Articulo->ItemCode");
                if (empty($id)):
                    // INSERTO LOS ADICIONALS DE GREEN FEES
                    $ServicioAdicional = "INSERT INTO ServicioAdicional (IDServicioPropiedad, IDServicio, IDClub, IDSistemaExterno, Nombre, Valor, Stock, Publicar, ValidarEdad)
                                    VALUES ('$IDServicioPropiedad', '$IDServicio', '$IDClub', '$Articulo->ItemCode', '$Articulo->ItemName', '$Articulo->Price', 1000,'S','N')";

                    $dbo->query($ServicioAdicional);

                    $IDServicioAdicional = $dbo->lastID();
                else:
                    $IDServicioAdicional = $id;
                endif;

                $DatosAdicionales = $dbo->fetchAll("ServicioAdicional", "IDServicioAdicional = $IDServicioAdicional", "array");
                $DatosCaracteristica = $dbo->fetchAll("ServicioPropiedad", "IDServicioPropiedad = $DatosAdicionales[IDServicioPropiedad]", "array");

                if ($Nombre_cat != $DatosCaracteristica["Nombre"]) {
                    $Nombre_cat = $DatosCaracteristica["Nombre"];
                    if ($contador_cat > 0) {
                        array_push($response_carac_producto, $categoria_carac);
                        $response_valores_carac = array();
                    }

                    $categoria_carac["IDCaracteristica"] = $DatosCaracteristica["IDServicioPropiedad"];
                    $categoria_carac["TipoCampo"] = $DatosCaracteristica["Tipo"];
                    $categoria_carac["EtiquetaCampo"] = $DatosCaracteristica["Nombre"];
                    $categoria_carac["Obligatorio"] = $DatosCaracteristica["Obligatorio"];
                    $categoria_carac["CantidadMaximaSeleccion"] = $DatosCaracteristica["MaximoPermitido"];
                }

                $valores["IDCaracteristicaValor"] = $DatosAdicionales["IDServicioAdicional"];
                $valores["Opcion"] = $DatosAdicionales["Nombre"];
                $valores["Precio"] = $DatosAdicionales["Valor"];
                $valores["Stock"] = $DatosAdicionales["Stock"];
                $valores["Agotado"] = "N";
                array_push($response_valores_carac, $valores);
                $categoria_carac["Valores"] = $response_valores_carac;
                $contador_cat++;
            endif;

        endforeach;

        $IDSistemaExterno = $IDServicio . $IDClub . "2";

        // BUSCAMOS SI YA ESXISTE EN LA BASE DE DATOS CARROS DE GOLF CON UN ID DIFERENTE
        $id = $dbo->getFields("ServicioPropiedad", "IDServicioPropiedad", "IDSistemaExterno = $IDSistemaExterno");
        if (empty($id)):
            $Nombre = "Carros de Golf";

            // INSERTAR EN LAS TABLAS ServicioPropiedad y ServicioAdicional LOS DATOS PARA CONSULTA
            $insert = "INSERT INTO ServicioPropiedad (IDServicio, IDClub, IDSistemaExterno, Nombre, Tipo, Obligatorio, MaximoPermitido, Publicar)
		                        VALUES ('$IDServicio', '$IDClub', '$IDSistemaExterno','$Nombre','Radio','N','1','S')";
            $dbo->query($insert);
            $IDServicioPropiedad = $dbo->lastID();
        else:
            $IDServicioPropiedad = $id;
        endif;

        // CONSULTO CON LA EDAD DEL SOCIO PARA SABER QUE CARROS DE GOLF TIENE ACTIVOS
        $CarrosGolf = SIMWebServiceBellavista::GetCarrosGolf($edad,$CardCode,$Secuencia);

        foreach ($CarrosGolf->Result->Articulos as $id => $Articulo):

            if($Articulo->PermisoReserva == "SI"):
                $id = $dbo->getFields("ServicioAdicional", "IDServicioAdicional", "IDSistemaExterno = $Articulo->ItemCode");
                if (empty($id)):
                    // INSERTO LOS ADICIONALS DE GREEN FEES
                    $ServicioAdicional = "INSERT INTO ServicioAdicional (IDServicioPropiedad, IDServicio, IDClub, IDSistemaExterno, Nombre, Valor, Stock, Publicar, ValidarEdad)
                                    VALUES ('$IDServicioPropiedad', '$IDServicio', '$IDClub', '$Articulo->ItemCode', '$Articulo->ItemName', '$Articulo->Price', 1000,'S','N')";

                    $dbo->query($ServicioAdicional);

                    $IDServicioAdicional = $dbo->lastID();
                else:
                    $IDServicioAdicional = $id;
                endif;

                $DatosAdicionales = $dbo->fetchAll("ServicioAdicional", "IDServicioAdicional = $IDServicioAdicional", "array");
                $DatosCaracteristica = $dbo->fetchAll("ServicioPropiedad", "IDServicioPropiedad = $DatosAdicionales[IDServicioPropiedad]", "array");

                if ($Nombre_cat != $DatosCaracteristica["Nombre"]) {
                    $Nombre_cat = $DatosCaracteristica["Nombre"];
                    if ($contador_cat > 0) {
                        array_push($response_carac_producto, $categoria_carac);
                        $response_valores_carac = array();
                    }

                    $categoria_carac["IDCaracteristica"] = $DatosCaracteristica["IDServicioPropiedad"];
                    $categoria_carac["TipoCampo"] = $DatosCaracteristica["Tipo"];
                    $categoria_carac["EtiquetaCampo"] = $DatosCaracteristica["Nombre"];
                    $categoria_carac["Obligatorio"] = $DatosCaracteristica["Obligatorio"];
                    $categoria_carac["CantidadMaximaSeleccion"] = $DatosCaracteristica["MaximoPermitido"];
                }

                $valores["IDCaracteristicaValor"] = $DatosAdicionales["IDServicioAdicional"];
                $valores["Opcion"] = $DatosAdicionales["Nombre"];
                $valores["Precio"] = $DatosAdicionales["Valor"];
                $valores["Stock"] = $DatosAdicionales["Stock"];
                $valores["Agotado"] = "N";
                array_push($response_valores_carac, $valores);
                $categoria_carac["Valores"] = $response_valores_carac;
                $contador_cat++;
            endif;

        endforeach;

        if (count($response_valores_carac) > 0) {
            array_push($response_carac_producto, $categoria_carac);
        }

        if (count($response_carac_producto) > 0) {
            $response = $response_carac_producto;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    } // fin function

    public function setOrder($IDReservaGeneral)
    {
        $dbo = &SIMDB::get();

        $datos_reserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = $IDReservaGeneral");

        if (!empty($datos_reserva)):
            $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $datos_reserva[IDSocio]", "array");

            $DatosAccion = explode("-", $datos_socio[Accion]);
            $CardCode = $DatosAccion[0];
            $Secuencia = $DatosAccion[1];
            $CardName = $datos_reserva[NombreSocio];
            $Hoy = date("Y-m-d");

            $SLQAdicionales = "SELECT * FROM ReservaGeneralAdicional WHERE IDReservaGeneral = $IDReservaGeneral";
            $QRYAdicionales = $dbo->query($SLQAdicionales);

            while ($dato_adicional = $dbo->fetchArray($QRYAdicionales)):

                $SQLCantidad = "SELECT IDReservaGeneralAdicional FROM ReservaGeneralAdicional WHERE IDReservaGeneral = $IDReservaGeneral AND IDServicioAdicional = $dato_adicional[IDServicioAdicional]";
                $QRYCantidad = $dbo->query($SQLCantidad);
                $Cantidad = $dbo->rows($QRYCantidad);

                $Adicional = $dbo->fetchAll("ServicioAdicional", "IDServicioAdicional = $dato_adicional[IDServicioAdicional]", "array");
                $DETAIL .=
                    '<DETAIL>
                        <ITEMCODE>' . $Adicional[IDSistemaExterno] . '</ITEMCODE>
		                <ITEMDESCRIPTION>' . $Adicional[Nombre] . '</ITEMDESCRIPTION>
		                <QUANTITY>' . $Cantidad . '</QUANTITY>
		                <PRICE>' . $Adicional[Valor] . '</PRICE>
                    </DETAIL>';
            endwhile;

            $XML =
                '<DOCUMENTO xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	            <ORDER>
	                <HEADER>
	                    <CARDCODE>' . $CardCode . '</CARDCODE>
	                    <CARDNAME>' . $CardName . '</CARDNAME>
	                    <DOCDATE>' . $Hoy . '</DOCDATE>
	                    <COMMENTS>Orden creada por 109Apps con WS de SAP</COMMENTS>
	                    <Secuencia>' . $Secuencia . '</Secuencia>
	                </HEADER>
	                ' . $DETAIL . '
	                
	            </ORDER>
	        </DOCUMENTO>';

            $llave = urlencode(LLAVE_SAP_BELLAVISTA);
            $sOrderData = urlencode($XML);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => WS_SAP_BELLAVISTA . '/SetOrder',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'llave=' . $llave . '&sOrderData=' . $sOrderData,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $DATOS = simplexml_load_string($response);
            return $DATOS;
        else:
            return "LA RESERVA NO EXISTE";
        endif;
    }

    public function valida_albercas($IDSocio)
    {
        $dbo = SIMDB::get();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

        $Accion = $DatosSocio[Accion];
        $DatosAccion = explode("-",$Accion);
        $CardCode = $DatosAccion[0];
        $Secuencia = "0".$DatosAccion[1];

        $FechaNacimiento = $DatosSocio[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($FechaNacimiento), date_create($dia_actual));
        $edad = $edad_diff->format('%y');

        // SI PUEDE RESERVAR ALBERCAS
        $Albercas = SIMWebServiceBellavista::GetAlbercas($edad, $CardCode, $Secuencia);

        if($Albercas->Result->Articulos->PermisoReserva == "SI"):
            $respuesta["message"] = "PUEDE RESERVAR ALBERCAS";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        else:
            $respuesta["message"] = "Lo sentimos, no puede hacer reservas para albercas este dia.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function valida_tenis($IDSocio)
    {
        $dbo = SIMDB::get();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

        $Accion = $DatosSocio[Accion];
        $DatosAccion = explode("-",$Accion);
        $CardCode = $DatosAccion[0];
        $Secuencia = "0".$DatosAccion[1];

        $FechaNacimiento = $DatosSocio[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($FechaNacimiento), date_create($dia_actual));
        $edad = $edad_diff->format('%y');

        // SI PUEDE RESERVAR ALBERCAS
        $Albercas = SIMWebServiceBellavista::GetAlbercas($edad, $CardCode, $Secuencia);

        if($Albercas->Result->Articulos->PermisoReserva == "SI"):
            $respuesta["message"] = "PUEDE RESERVAR ALBERCAS";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        else:
            $respuesta["message"] = "Lo sentimos, no puede hacer reservas para tenis este dia.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_servicios_adicionales_bellavista_tennis($IDClub, $IDSocio, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

        $Accion = $DatosSocio[Accion];
        $DatosAccion = explode("-",$Accion);
        $CardCode = $DatosAccion[0];
        $Secuencia = "0".$DatosAccion[1];

        $message = "Adicionales";
        $Nombre_cat = "";
        $contador_cat = 0;
        $response = array();
        $response_carac_producto = array();
        $response_valores_carac = array();
        $response_lista_producto = array();

        $FechaNacimiento = $DatosSocio[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($FechaNacimiento), date_create($dia_actual));
        $edad = $edad_diff->format('%y');

        $IDSistemaExterno = $IDServicio . $IDClub;

        // BUSCAMOS SI YA ESXISTE EN LA BASE DE DATOS SI EXISTE GRENN FEES
        $id = $dbo->getFields("ServicioPropiedad", "IDServicioPropiedad", "IDSistemaExterno = $IDSistemaExterno");
        if (empty($id)):
            $Nombre = "Green Fees";

            // INSERTAR EN LAS TABLAS ServicioPropiedad y ServicioAdicional LOS DATOS PARA CONSULTA
            $insert = "INSERT INTO ServicioPropiedad (IDServicio, IDClub, IDSistemaExterno, Nombre, Tipo, Obligatorio, MaximoPermitido, Publicar)
		                        VALUES ('$IDServicio', '$IDClub', '$IDSistemaExterno','$Nombre','Radio','N','1','S')";
            $dbo->query($insert);
            $IDServicioPropiedad = $dbo->lastID();
        else:
            $IDServicioPropiedad = $id;
        endif;

        // CONSULTO CON LA EDAD DEL SOCIO PARA SABER QUE GREEN FEES TIENE ACTIVOS
        $GreenFee = SIMWebServiceBellavista::GetCanchasTenis($edad,$CardCode,$Secuencia);
       
        foreach ($GreenFee->Result->Articulos as $id => $Articulo):

            if($Articulo->PermisoReserva == "SI"):
                $id = $dbo->getFields("ServicioAdicional", "IDServicioAdicional", "IDSistemaExterno = $Articulo->ItemCode");
                if (empty($id)):
                    // INSERTO LOS ADICIONALS DE GREEN FEES
                    $ServicioAdicional = "INSERT INTO ServicioAdicional (IDServicioPropiedad, IDServicio, IDClub, IDSistemaExterno, Nombre, Valor, Stock, Publicar, ValidarEdad)
                                    VALUES ('$IDServicioPropiedad', '$IDServicio', '$IDClub', '$Articulo->ItemCode', '$Articulo->ItemName', '$Articulo->Price', 1000,'S','N')";

                    $dbo->query($ServicioAdicional);

                    $IDServicioAdicional = $dbo->lastID();
                else:
                    $IDServicioAdicional = $id;
                endif;

                $DatosAdicionales = $dbo->fetchAll("ServicioAdicional", "IDServicioAdicional = $IDServicioAdicional", "array");
                $DatosCaracteristica = $dbo->fetchAll("ServicioPropiedad", "IDServicioPropiedad = $DatosAdicionales[IDServicioPropiedad]", "array");

                if ($Nombre_cat != $DatosCaracteristica["Nombre"]) {
                    $Nombre_cat = $DatosCaracteristica["Nombre"];
                    if ($contador_cat > 0) {
                        array_push($response_carac_producto, $categoria_carac);
                        $response_valores_carac = array();
                    }

                    $categoria_carac["IDCaracteristica"] = $DatosCaracteristica["IDServicioPropiedad"];
                    $categoria_carac["TipoCampo"] = $DatosCaracteristica["Tipo"];
                    $categoria_carac["EtiquetaCampo"] = $DatosCaracteristica["Nombre"];
                    $categoria_carac["Obligatorio"] = $DatosCaracteristica["Obligatorio"];
                    $categoria_carac["CantidadMaximaSeleccion"] = $DatosCaracteristica["MaximoPermitido"];
                }

                $valores["IDCaracteristicaValor"] = $DatosAdicionales["IDServicioAdicional"];
                $valores["Opcion"] = $DatosAdicionales["Nombre"];
                $valores["Precio"] = $DatosAdicionales["Valor"];
                $valores["Stock"] = $DatosAdicionales["Stock"];
                $valores["Agotado"] = "N";
                array_push($response_valores_carac, $valores);
                $categoria_carac["Valores"] = $response_valores_carac;
                $contador_cat++;
            endif;

        endforeach;        

        if (count($response_valores_carac) > 0) {
            array_push($response_carac_producto, $categoria_carac);
        }

        if (count($response_carac_producto) > 0) {
            $response = $response_carac_producto;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    } // fin function

    public function get_servicios_adicionales_bellavista_albercas($IDClub, $IDSocio, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();

        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

        $Accion = $DatosSocio[Accion];
        $DatosAccion = explode("-",$Accion);
        $CardCode = $DatosAccion[0];
        $Secuencia = "0".$DatosAccion[1];

        $message = "Adicionales";
        $Nombre_cat = "";
        $contador_cat = 0;
        $response = array();
        $response_carac_producto = array();
        $response_valores_carac = array();
        $response_lista_producto = array();

        $FechaNacimiento = $DatosSocio[FechaNacimiento];
        $dia_actual = date("Y-m-d");
        $edad_diff = date_diff(date_create($FechaNacimiento), date_create($dia_actual));
        $edad = $edad_diff->format('%y');

        $IDSistemaExterno = $IDServicio . $IDClub;

        // BUSCAMOS SI YA ESXISTE EN LA BASE DE DATOS SI EXISTE GRENN FEES
        $id = $dbo->getFields("ServicioPropiedad", "IDServicioPropiedad", "IDSistemaExterno = $IDSistemaExterno");
        if (empty($id)):
            $Nombre = "Green Fees";

            // INSERTAR EN LAS TABLAS ServicioPropiedad y ServicioAdicional LOS DATOS PARA CONSULTA
            $insert = "INSERT INTO ServicioPropiedad (IDServicio, IDClub, IDSistemaExterno, Nombre, Tipo, Obligatorio, MaximoPermitido, Publicar)
		                        VALUES ('$IDServicio', '$IDClub', '$IDSistemaExterno','$Nombre','Radio','N','1','S')";
            $dbo->query($insert);
            $IDServicioPropiedad = $dbo->lastID();
        else:
            $IDServicioPropiedad = $id;
        endif;

        // CONSULTO CON LA EDAD DEL SOCIO PARA SABER QUE GREEN FEES TIENE ACTIVOS
        $GreenFee = SIMWebServiceBellavista::GetAlbercas($edad,$CardCode,$Secuencia);
       
        foreach ($GreenFee->Result->Articulos as $id => $Articulo):

            if($Articulo->PermisoReserva == "SI"):
                $id = $dbo->getFields("ServicioAdicional", "IDServicioAdicional", "IDSistemaExterno = $Articulo->ItemCode");
                if (empty($id)):
                    // INSERTO LOS ADICIONALS DE GREEN FEES
                    $ServicioAdicional = "INSERT INTO ServicioAdicional (IDServicioPropiedad, IDServicio, IDClub, IDSistemaExterno, Nombre, Valor, Stock, Publicar, ValidarEdad)
                                    VALUES ('$IDServicioPropiedad', '$IDServicio', '$IDClub', '$Articulo->ItemCode', '$Articulo->ItemName', '$Articulo->Price', 1000,'S','N')";

                    $dbo->query($ServicioAdicional);

                    $IDServicioAdicional = $dbo->lastID();
                else:
                    $IDServicioAdicional = $id;
                endif;

                $DatosAdicionales = $dbo->fetchAll("ServicioAdicional", "IDServicioAdicional = $IDServicioAdicional", "array");
                $DatosCaracteristica = $dbo->fetchAll("ServicioPropiedad", "IDServicioPropiedad = $DatosAdicionales[IDServicioPropiedad]", "array");

                if ($Nombre_cat != $DatosCaracteristica["Nombre"]) {
                    $Nombre_cat = $DatosCaracteristica["Nombre"];
                    if ($contador_cat > 0) {
                        array_push($response_carac_producto, $categoria_carac);
                        $response_valores_carac = array();
                    }

                    $categoria_carac["IDCaracteristica"] = $DatosCaracteristica["IDServicioPropiedad"];
                    $categoria_carac["TipoCampo"] = $DatosCaracteristica["Tipo"];
                    $categoria_carac["EtiquetaCampo"] = $DatosCaracteristica["Nombre"];
                    $categoria_carac["Obligatorio"] = $DatosCaracteristica["Obligatorio"];
                    $categoria_carac["CantidadMaximaSeleccion"] = $DatosCaracteristica["MaximoPermitido"];
                }

                $valores["IDCaracteristicaValor"] = $DatosAdicionales["IDServicioAdicional"];
                $valores["Opcion"] = $DatosAdicionales["Nombre"];
                $valores["Precio"] = $DatosAdicionales["Valor"];
                $valores["Stock"] = $DatosAdicionales["Stock"];
                $valores["Agotado"] = "N";
                array_push($response_valores_carac, $valores);
                $categoria_carac["Valores"] = $response_valores_carac;
                $contador_cat++;
            endif;

        endforeach;        

        if (count($response_valores_carac) > 0) {
            array_push($response_carac_producto, $categoria_carac);
        }

        if (count($response_carac_producto) > 0) {
            $response = $response_carac_producto;
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;

    } // fin function
}
