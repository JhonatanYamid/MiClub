<?php

class SIMWebServiceCountryMedellin
{



    //        public function ConfiguracionFacturacion($IDClub, $IDSocio)
    //     {
    //            $dbo = &SIMDB::get();
    //    $response = array();
    //    $datos_config_facturacion = $dbo->fetchAll("ConfiguracionFacturacion", " IDClub = '" . $IDClub . "' ", "array");


    //         $configuracion["IDClub"] = $IDClub; 
    //         $configuracion["MostrarFiltroFamiliares"] = $datos_config_facturacion["MostrarFiltroFamiliares"];
    //         $configuracion["FiltroFamiliaresLabel"] = $datos_config_facturacion["FiltroFamiliaresLabel"];
    //         $configuracion["MostrarSeccionesHistorial"] = $datos_config_facturacion["MostrarSeccionesHistorial"];
    //         $configuracion["SeccionesHistorialLabel"] = $datos_config_facturacion["SeccionesHistorialLabel"];
    //         $configuracion["SeccionesPendientesPagoLabel"] = $datos_config_facturacion["SeccionesPendientesPagoLabel"];
    //         $configuracion["PermitePaginar"] = $datos_config_facturacion["PermitePaginar"];

    //         $configuracion["BuscadorFechas"] = $datos_config_facturacion["BuscadorFechas"];
    //         $configuracion["ImagenLateral"] = $datos_config_facturacion["ImagenLateral"];
    //         $configuracion["PrecargarFechaHoyBuscador"] = $datos_config_facturacion["PrecargarFechaHoyBuscador"];

    //         $configuracion["PermiteSeleccionarVarias"] = $datos_config_facturacion["PermiteSeleccionarVarias"];
    //         $configuracion["MostrarDecimal"] = $datos_config_facturacion["MostrarDecimal"];
    //         $configuracion["TextoSeleccionarDeseleccionar"] = $datos_config_facturacion["TextoSeleccionarDeseleccionar"];
    //         $configuracion["TextoIntroSeleccionarVariasPago"] = $datos_config_facturacion["TextoIntroSeleccionarVariasPago"];

    //         array_push($response, $configuracion);

    //         $respuesta["message"] = $message;
    //         $respuesta["success"] = true;
    //         $respuesta["response"] = $response;

    //         return $respuesta;
    //     } 
    public function App_AutenticarUser($email, $Clave)
    {
        $curl = curl_init();

        //   $user = urlencode("702805");
        //  $pwd = urlencode("702805305");

        $POST = 'user=' . $email . '&pwd=' . $Clave;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_AutenticarUser',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $Token = $DATOS->token;
        $json = json_encode($DATOS);
        return $json;
    }



    public function App_ActualizarFotoPerfil($foto, $token)
    {
        $curl = curl_init();

        //   $user = urlencode("702805");
        //  $pwd = urlencode("702805305");

        $POST = 'Foto=' . $foto . '&pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ActualizarFotoPerfil',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $Token = $DATOS->token;
        $json = json_encode($DATOS);
        return $json;
    }


    public function App_ConsultarPerfil($token)
    {

        $curl = curl_init();
        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarPerfil',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }


    public function App_ActualizarEstadoUsuario($token, $tyc)
    {

        $curl = curl_init();
        $POST = 'pToken=' . $token . '&terminosaceptados=' . $tyc;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ActualizarEstadoUsuario',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }



    public function App_ConsultarDashboard($token)
    {

        $curl = curl_init();
        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarDashboard',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }

    public function App_ConsultarRedes($token)
    {

        $curl = curl_init();
        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarRedes',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }

    public function App_CambiarClave($ClaveAnterior, $Clave, $token)
    {
        $curl = curl_init();

        $POST = 'pClaveAnterior=' . $ClaveAnterior . '&pClaveNueva=' . $Clave . '&pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_CambiarClave',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }
    //actualizar perfil

    public function App_ActualizarPerfil($datos_campos, $Token)
    {
        $curl = curl_init();
        $pToken = $Token;
        //array que contiene las respuestas del form
        foreach ($datos_campos as $detalle_campo) {
            $IDCampo = $detalle_campo["IDCampoEditarSocio"];
            $valor = trim($detalle_campo["Valor"]);
            if ($IDCampo == "1235") {
                $estadoCivil = $valor;
            }
            if ($IDCampo == "1236") {
                $fechaAniversario = $valor;
            }
            if ($IDCampo == "1237") {
                $telefono = $valor;
            }
            if ($IDCampo == "1238") {
                $celular = $valor;
            }
            if ($IDCampo == "1239") {
                $direccion = $valor;
            }
            if ($IDCampo == "1240") {
                $profesion = $valor;
            }
            if ($IDCampo == "1241") {
                $nombreEmpresa = $valor;
            }
            if ($IDCampo == "1242") {
                $cargo = $valor;
            }
            if ($IDCampo == "1243") {
                $telefonoOficina = $valor;
            }
            if ($IDCampo == "1244") {
                $direccionOficina = $valor;
            }
            if ($IDCampo == "1245") {
                $direccionEnvio = $valor;
            }
            if ($IDCampo == "1246") {
                $correofacturacion = $valor;
            }
            if ($IDCampo == "1247") {
                $estudiante = $valor;
                if ($estudiante == "Si") {
                    $estudiante = "true";
                } else {
                    $estudiante = "false";
                }
            }
        }

        $wsdl = "http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx?WSDL";
        $oSoapClient = new nusoap_client($wsdl, true);
        $cadena =  '<App_ActualizarPerfil xmlns="http://tempuri.org/">
                              <pPerfil>
        <estadoCivil>' .  $estadoCivil  . '</estadoCivil>
        <fechaAniversario>' .  $fechaAniversario  . '</fechaAniversario>
        <telefono>' .  $telefono  . '</telefono>
        <celular>' .  $celular  . '</celular>
        <direccion>' .  $direccion  . '</direccion>
        <profesion>' .  $profesion  . '</profesion>
        <nombreEmpresa>' .  $nombreEmpresa  . '</nombreEmpresa>
        <cargo>' .  $cargo  . '</cargo>
        <telefonoOficina>' .  $telefonoOficina  . '</telefonoOficina>
        <direccionOficina>' .  $direccionOficina  . '</direccionOficina>
        <direccionEnvio>' . $direccionEnvio   . '</direccionEnvio>
        <estudiante>' . $estudiante . '</estudiante>
        <correofacturacion>' . $correofacturacion   . '</correofacturacion>
        
      </pPerfil>
      <pToken>' . $pToken . '</pToken>
    </App_ActualizarPerfil>';

        $respuestas = $oSoapClient->call("App_ActualizarPerfil", $cadena, "");
        if ($oSoapClient->getError()) {

            //echo "<br/><br/>Error al llamar el metodo<br/> ".$oSoapClient->getError();
            $respuesta["message"] = "CMR2. Hubo un problema al conectar con servicio externo, intente mas tarde";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Se han actualizado los datos correctamente!";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }


    //diligenciar formularios
    public function App_DiligenciarFormulario($IDEncuesta, $Respuestas, $Token)
    {
        $curl = curl_init();
        //llamo al formulario
        $detalles = self::App_AmpliarFormulario($Token, $IDEncuesta);
        $resultado1 = json_decode($detalles, true);

        //cuento la cantidad de campos
        $total = count($resultado1["campos"]["CampoFormulario2"]);

        $pToken = $Token;
        //array que contiene las respuestas del formulario
        $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
        $datos_respuesta = json_decode($Respuestas, true);

        foreach ($datos_respuesta as $detalle_respuesta) :

            for ($i = 0; $i < $total; $i++) {

                $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"];
                $cod_campo = $resultado1['campos']["CampoFormulario2"][$i]["codigoCampo"];
                $cantidad = count($opciones);

                for ($o = 0; $o <= $cantidad; $o++) {
                    $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["data"];
                    $label = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["label"];
                    if ($label == $detalle_respuesta["Valor"] and  $cod_campo == $detalle_respuesta["IDPregunta"]) {
                        $detalle_respuesta["Valor"] = $opciones;
                    }
                }
            }
            if ($detalle_respuesta["Valor"] == "Si") {
                $detalle_respuesta["Valor"] = "true";
            }
            if ($detalle_respuesta["Valor"] == "No") {
                $detalle_respuesta["Valor"] = "false";
            }
            $array_respuesta .= "
               <CampoDiligenciado>
                       <codigoCampo> $detalle_respuesta[IDPregunta]  </codigoCampo>
                       <valor>$detalle_respuesta[Valor]</valor>
               </CampoDiligenciado> ";
        endforeach;

        $wsdl = "http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx?WSDL";
        $oSoapClient = new nusoap_client($wsdl, true);
        /**/

        $cadena =  '<App_DiligenciarFormulario xmlns="http://tempuri.org/">
              <codigoFormulario>' . $IDEncuesta . '</codigoFormulario>
               <camposDiligenciados> 
                 ' . $array_respuesta . '
               </camposDiligenciados>
                    <pToken>' . $pToken . '</pToken>
               </App_DiligenciarFormulario>';

        $respuesta = $oSoapClient->call("App_DiligenciarFormulario", $cadena, "");
        if ($oSoapClient->getError()) {
            //echo "<br/><br/>Error al llamar el metodo<br/> ".$oSoapClient->getError();
            $respuesta["message"] = "CMR2. Hubo un problema al conectar con servicio externo, intente mas tarde";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            $IDReserva = 1;   //$respuesta["App_DiligenciarFormulario"]["PuestosReserva"]["idReserva"];
            if ((int)$IDReserva > 0) {
                $respuesta["message"] = "Se ha llenado el formulario correctamente!";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "CMR1. Hubo un problema al guardar el formulario intente mas tarde";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    }



    public function App_ConsultarTerminosCondiciones($IDClub)
    {
        $curl = curl_init();

        //   $user = urlencode("702805");
        //  $pwd = urlencode("702805305");

        $POST = 'IDClub=' . $IDClub;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarTerminosCondiciones',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json =  ($DATOS);
        return $json;
    }

    public function App_RecuperarClave($email)
    {
        $curl = curl_init();

        //   $user = urlencode("702805");
        //  $pwd = urlencode("702805305");

        $POST = 'pUsuario=' . $email;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_RecuperarClave',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }

    public function App_ConsultarMesasAbiertas($familiares, $codigoMesa, $sala, $token)
    {
        $curl = curl_init();

        $POST = 'familiares=' . $familiares . '&codigoMesa=' . $codigoMesa . '&sala=' . $sala . '&pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarMesasAbiertas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }

    public function App_ConsultarConsumos($familiares, $numeroConsumo, $fechaInicial, $fechaFinal, $pagina, $token)
    {
        $curl = curl_init();

        $POST = 'familiares=' . $familiares . '&numeroConsumo=' . $numeroConsumo . '&fechaInicial=' . $fechaInicial . '&fechaFinal=' . $fechaFinal . '&pagina=' . $pagina . '&pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarConsumos',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }

    public function App_ConsultarSectoresEconomicos($token, $id_club)
    {

        $dbo = &SIMDB::get();
        $curl = curl_init();
        $response = array();

        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarSectoresEconomicos',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $resultado1 = ($response);
        $array = $resultado1;
        $Orden = 1;

        $array = str_replace('<?xml version="1.0" encoding="utf-8"?>', "", $array);
        $array = str_replace('<string xmlns="http://tempuri.org/">', "", $array);
        $array = str_replace('</string>', "", $array);
        $array = str_replace('</string>', "", $array);
        $array = str_replace("[", "'", $array);
        $array = str_replace("]", "'", $array);
        $array = explode("},", $array);

        if (count($array) > 0) :
            $array_num = count($array);
            for ($k = 0; $k < $array_num; $k++) {


                $array[$k];

                $resultado = ($array[$k]);
                $a = $resultado;
                $array1 = explode(",", $a);
                $array1 = str_replace('{', "", $array1);
                $array1 = str_replace('}', "", $array1);

                $a = $array1[1];
                $b = $array1[2];
                $a = str_replace('"', "", $a);
                $a = str_replace("Descripcion:", "", $a);
                $b = str_replace('"', "", $b);
                $b = str_replace("SectorEconomicoID:", "", $b);

                $Nombre =  $a;
                $Descripcion =  $b;


                // BUSCAMOS SI LA CATEGORIA ESTA CREADA
                $SQLCategoria = "SELECT IDSeccionBeneficio FROM SeccionBeneficio  WHERE IDClub='$id_club' AND Nombre = '$Nombre'";
                $QRYCategoria = $dbo->query($SQLCategoria);
                // SI LA CATEGORIA NO EXISTE LA CREAMOS
                if ($dbo->rows($QRYCategoria) <= 0) :
                    $InsertCategoria = "INSERT INTO SeccionBeneficio (IDClub, Nombre, Descripcion, SoloIcono, Publicar, Orden) 
                                            VALUES ($id_club, '$Nombre', '$Descripcion', 'N', 'S', $Orden)";

                    $dbo->query($InsertCategoria);
                    $Orden++;
                endif;
            }

        endif;


        $response = array();
        $sql = "SELECT * FROM SeccionBeneficio  WHERE Publicar = 'S' and IDClub = '" . $id_club . "'  ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDCategoria"] = $r["Descripcion"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];
                $seccion["SoloIcono"] = $r["SoloIcono"];

                $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '8' and IDClub='" . $id_club . "' ", "array");
                $icono_modulo = $datos_modulo["Icono"];
                if (!empty($datos_modulo["Icono"])) :
                    $foto = MODULO_ROOT . $datos_modulo["Icono"];
                else :
                    $foto = "";
                endif;


                $seccion["Icono"] = $foto;
                array_push($response, $seccion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }


    //ver  DirectorioInteraccion por categorias
    public function App_ConsultarDirectorioInteraccion($token, $IDCategoria, $emprendedor, $misEmpresas)
    {
        $dbo = &SIMDB::get();
        if (empty($IDCategoria)) {
            $IDCategoria = -1;
        }

        $nombreEmpresa = "";
        $nombreContacto = "";
        $curl = curl_init();
        $POST = 'codigoSectorEconomico=' . $IDCategoria . '&misEmpresas=' . $misEmpresas . '&emprendedor=' . $emprendedor . '&pToken=' . $token . '&nombreEmpresa=' . $nombreEmpresa . '&nombreContacto=' . $nombreContacto;


        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarDirectorioInteraccion',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);


        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado['EmpresaDirectorio']);
        if ($items == 13) {
            $items = 1;
        }
        $response = array();
        if (count($resultado['EmpresaDirectorio']) > 0) {

            for ($k = 0; $k < $items; $k++) {
                $codigo = $resultado['EmpresaDirectorio'][$k]["codigoEmpresa"];
                $logo = $resultado['EmpresaDirectorio'][$k]["logoEmpresa"];
                $nombre = $resultado['EmpresaDirectorio'][$k]["nombre"];
                $descripcion = $resultado['EmpresaDirectorio'][$k]["descripcion"];
                $tel = $resultado['EmpresaDirectorio'][$k]["telefonoFijo"];
                $movil = $resultado['EmpresaDirectorio'][$k]["telefonoMovil"];
                $nombre_contacto = $resultado['EmpresaDirectorio'][$k]["nombreContacto"];
                $email = $resultado['EmpresaDirectorio'][$k]["emailContacto"];
                $sector_economico = $resultado['EmpresaDirectorio'][$k]["codigoSectorEconomico"];
                $direccion = $resultado['EmpresaDirectorio'][$k]["direccion"];
                $emprendedor = $resultado['EmpresaDirectorio'][$k]["emprendedor"];
                $terminos = $resultado['EmpresaDirectorio'][$k]["terminosAceptados"];
                $tipo_imagen = $resultado['EmpresaDirectorio'][$k]["tipoImagen"];

                if ($items == 1) {
                    $codigo = $resultado['EmpresaDirectorio']["codigoEmpresa"];
                    $logo = $resultado['EmpresaDirectorio']["logoEmpresa"];
                    $nombre = $resultado['EmpresaDirectorio']["nombre"];
                    $descripcion = $resultado['EmpresaDirectorio']["descripcion"];
                    $tel = $resultado['EmpresaDirectorio']["telefonoFijo"];
                    $movil = $resultado['EmpresaDirectorio']["telefonoMovil"];
                    $nombre_contacto = $resultado['EmpresaDirectorio']["nombreContacto"];
                    $email = $resultado['EmpresaDirectorio']["emailContacto"];
                    $sector_economico = $resultado['EmpresaDirectorio']["codigoSectorEconomico"];
                    $direccion = $resultado['EmpresaDirectorio']["direccion"];
                    $emprendedor = $resultado['EmpresaDirectorio']["emprendedor"];
                    $terminos = $resultado['EmpresaDirectorio']["terminosAceptados"];
                    $tipo_imagen = $resultado['EmpresaDirectorio']["tipoImagen"];
                }

                $beneficio["IDBeneficio"] = $codigo;
                $beneficio["IDCategoria"] =   $sector_economico;
                $beneficio["IDClub"] = "227";
                $beneficio["Nombre"] = $nombre;
                $beneficio["Introduccion"] =  $email;
                $beneficio["Descripcion"] = $nombrecontacto . "\n" . $direccion;
                $beneficio["DescripcionHtml"] = $descripcion;
                if ($tel == 0) {
                    $telefono = "";
                } else {
                    $telefono = $tel;
                }
                $PaginaWeb = "";


                $beneficio["PaginaWeb"] = $PaginaWeb;
                $beneficio["Telefono"] = $telefono;
                $beneficio["OcultarTelefono"] = "N";
                $beneficio["OcultarPaginaWeb"] = "N";
                $beneficio["OcultarMapa"] = "S";
                $beneficio["OcultarBotonRuta"] = "S";
                $beneficio["OcultarImagen"] = "N";
                $beneficio["OcultarUrlDetalle"] = "N";
                $beneficio["OcultarTelefonoDetalle"] = "N";
                $beneficio["MostrarCorreo"] = "S";
                $beneficio["Correo"] = $email;

                if (!empty($logo)) :
                    if (strstr(strtolower($logo), "http://")) {
                        $FotoPortada = $logo;
                    } else {
                        $FotoPortada = "";
                    }

                else :
                    $FotoPortada = "";
                endif;

                $beneficio["FotoPortada"] = $FotoPortada;


                $response_fotos = array();
                for ($i_foto = 1; $i_foto <= 1; $i_foto++) :
                    $campo_foto = "Foto" . $i_foto;
                    if (!empty($logo)) :
                        $array_dato_foto["Foto"] =  $logo;
                        array_push($response_fotos, $array_dato_foto);
                    endif;
                endfor;
                $beneficio["Fotos"] = $response_fotos;

                array_push($response, $beneficio);
            }
            //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {

            $respuesta["message"] =  "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }


        return $respuesta;
    }

    //GET FORMULARIOS SOCIOS

    public function App_ConsultarFormularios($token, $IDClub)
    {
        $dbo = &SIMDB::get();

        $curl = curl_init();
        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarFormularios',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);


        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Formulario]);
        $response = array();
        if (count($resultado['Formulario']) > 0) :

            for ($k = 0; $k < $items; $k++) {
                $codigo = $resultado['Formulario'][$k]["codigoFormulario"];

                if ($codigo == 1) :
                    $nombre = $resultado['Formulario'][$k]["nombreFormulario"];
                    $descripcion = $resultado['Formulario'][$k]["descripcionFormulario"];
                    $k1 = $k + 1;
                    if ((array) $descripcion  == $descripcion) {
                        $descripcion = "";
                    }

                    $fechaactual = date("Y-m-d");
                    //sumo 1 día
                    $fechafin = date("Y-m-d", strtotime($fecha_actual . "+ 2 days"));
                    //resto 1 día
                    /*
     // BUSCAMOS SI LA CATEGORIA ESTA CREADA
                    $sqlform = "SELECT CodigoFormulario FROM Encuesta  WHERE IDClub='8' AND CodigoFormulario = '$codigo'";
                    $queryform = $dbo->query($sqlform);
                    // SI LA CATEGORIA NO EXISTE LA CREAMOS
                    if($dbo->rows($queryform) <= 0):
                        $Insertform = "INSERT INTO Encuesta (CodigoFormulario, IDClub, DirigidoA, Nombre, Descripcion, Orden, SolicitarAbrirApp, UnaporSocio, FechaInicio, FechaFin,  RespuestaGuardar, Publicar) VALUES ('$codigo','8', 'S','$nombre', '$descripcion','$k1','N','S','$fechaactual','$fechafin','S','S')";

                        $dbo->query($Insertform);
                      
                    endif;      
                    
     */


                    /* $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
               if (!empty($IDSocio)) {
                    $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDSocio);
                }  */
                    $mostrar_encuesta = 1;
                    if ($mostrar_encuesta == 1) {
                        $encuesta["IDClub"] = $IDClub;
                        $encuesta["IDEncuesta"] = $codigo;
                        $encuesta["IDModulo"] = 0;
                        $encuesta["Nombre"] = $nombre;
                        $encuesta["Descripcion"] = $descripcion;
                        $encuesta["SolicitarAbrirApp"] = "N";
                        $encuesta["FechaInicio"] = $fechaactual;
                        $encuesta["FechaFin"] = $fechafin;
                        //  $encuesta["SegundaClave"] = $r["SegundaClave"]; 

                        $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '58' and IDClub='" . $IDClub . "' ", "array");
                        $icono_modulo = $datos_modulo["Icono"];
                        if (!empty($datos_modulo["Icono"])) :
                            $foto = MODULO_ROOT . $datos_modulo["Icono"];
                        else :
                            $foto = "";
                        endif;


                        $encuesta["Icono"] = $foto;
                        $encuesta["Respondida"] = "N";
                        //Verifico si el socio ya contesto la encuesta
                        /*    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM EncuestaRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDEncuesta"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    } */

                        //Pregunta
                        $pregunta = array();
                        $response_pregunta = array();

                        $detalles = self::App_AmpliarFormulario($token, $codigo);

                        $resultado1 = json_decode($detalles, true);

                        $total = count($resultado1["campos"]["CampoFormulario2"]);

                        for ($i = 0; $i < $total; $i++) {
                            $codigo = $resultado1['campos']["CampoFormulario2"][$i]["codigoCampo"];
                            $nombre =  $resultado1['campos']["CampoFormulario2"][$i]["nombreCampo"];
                            $requerido =  $resultado1['campos']["CampoFormulario2"][$i]["requerido"];
                            $tipo = $resultado1['campos']["CampoFormulario2"][$i]["tipoCampo"];
                            // $opciones= $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"];

                            $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"];
                            $cantidad = count($opciones);
                            $response_select = array();
                            for ($o = 0; $o < $cantidad; $o++) {
                                $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["data"];
                                $label = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["label"];


                                array_push($response_select, $label);
                            }
                            $opciones = implode("|", $response_select);





                            $i1 = $i + 1;
                            if ($requerido == "true") :
                                $obligatorio = "S";
                            else :
                                $obligatorio = "N";
                            endif;

                            $pregunta["IDPregunta"] = $codigo;

                            $pregunta["EtiquetaCampo"] = $nombre;
                            $pregunta["Obligatorio"] = $obligatorio;

                            $pregunta["Orden"] = $i1;
                            if ($tipo == "dropdown") {
                                $tipo  = "select";
                            }
                            if ($tipo == "cale") {
                                $tipo  = "date";
                            }
                            if ($tipo == "txtnum") {
                                $tipo  = "number";
                            }
                            if ($tipo == "txtarea") {
                                $tipo  = "textarea";
                            }
                            if ($tipo == "check") {
                                $tipo  = "radio";
                                $opciones = "Si|No";
                            }

                            $pregunta["Valores"] = $opciones;
                            $pregunta["TipoCampo"] = $tipo;

                            array_push($response_pregunta, $pregunta);
                        }

                        $encuesta["Preguntas"] = $response_pregunta;
                        array_push($response, $encuesta);
                    }

                endif;
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }

    public function App_ConsultarFormulariostodos($token, $IDClub)
    {
        $dbo = &SIMDB::get();

        $curl = curl_init();
        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarFormularios',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);


        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Formulario]);
        $response = array();
        if (count($resultado['Formulario']) > 0) :

            for ($k = 0; $k < $items; $k++) {
                $codigo = $resultado['Formulario'][$k]["codigoFormulario"];
                if ($codigo == 10) :
                    $nombre = $resultado['Formulario'][$k]["nombreFormulario"];
                    $descripcion = $resultado['Formulario'][$k]["descripcionFormulario"];
                    $k1 = $k + 1;
                    if ((array) $descripcion  == $descripcion) {
                        $descripcion = "";
                    }

                    $fechaactual = date("Y-m-d");
                    //sumo 1 día
                    $fechafin = date("Y-m-d", strtotime($fecha_actual . "+ 2 days"));
                    //resto 1 día
                    /*
     // BUSCAMOS SI LA CATEGORIA ESTA CREADA
                    $sqlform = "SELECT CodigoFormulario FROM Encuesta  WHERE IDClub='8' AND CodigoFormulario = '$codigo'";
                    $queryform = $dbo->query($sqlform);
                    // SI LA CATEGORIA NO EXISTE LA CREAMOS
                    if($dbo->rows($queryform) <= 0):
                        $Insertform = "INSERT INTO Encuesta (CodigoFormulario, IDClub, DirigidoA, Nombre, Descripcion, Orden, SolicitarAbrirApp, UnaporSocio, FechaInicio, FechaFin,  RespuestaGuardar, Publicar) VALUES ('$codigo','8', 'S','$nombre', '$descripcion','$k1','N','S','$fechaactual','$fechafin','S','S')";

                        $dbo->query($Insertform);
                      
                    endif;      
                    
     */


                    /* $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
               if (!empty($IDSocio)) {
                    $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDSocio);
                }  */
                    $mostrar_encuesta = 1;
                    if ($mostrar_encuesta == 1) {
                        $encuesta["IDClub"] = $IDClub;
                        $encuesta["IDEncuesta"] = $codigo;
                        $encuesta["IDModulo"] = 0;
                        $encuesta["Nombre"] = $nombre;
                        $encuesta["Descripcion"] = $descripcion;
                        $encuesta["SolicitarAbrirApp"] = "N";
                        $encuesta["FechaInicio"] = $fechaactual;
                        $encuesta["FechaFin"] = $fechafin;
                        //  $encuesta["SegundaClave"] = $r["SegundaClave"]; 

                        $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '58' and IDClub='" . $IDClub . "' ", "array");
                        $icono_modulo = $datos_modulo["Icono"];
                        if (!empty($datos_modulo["Icono"])) :
                            $foto = MODULO_ROOT . $datos_modulo["Icono"];
                        else :
                            $foto = "";
                        endif;


                        $encuesta["Icono"] = $foto;
                        $encuesta["Respondida"] = "N";
                        //Verifico si el socio ya contesto la encuesta
                        /*    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM EncuestaRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDEncuesta"] . "'";

                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    } */

                        //Pregunta
                        $pregunta = array();
                        $response_pregunta = array();

                        $detalles = self::App_AmpliarFormulario($token, $codigo);

                        $resultado1 = json_decode($detalles, true);

                        $total = count($resultado1["campos"]["CampoFormulario2"]);

                        for ($i = 0; $i < $total; $i++) {
                            $codigo = $resultado1['campos']["CampoFormulario2"][$i]["codigoCampo"];
                            $nombre =  $resultado1['campos']["CampoFormulario2"][$i]["nombreCampo"];
                            $requerido =  $resultado1['campos']["CampoFormulario2"][$i]["requerido"];
                            $tipo = $resultado1['campos']["CampoFormulario2"][$i]["tipoCampo"];
                            // $opciones= $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"];

                            $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"];
                            $cantidad = count($opciones);
                            $response_select = array();
                            for ($o = 0; $o < $cantidad; $o++) {
                                $opciones = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["data"];
                                $label = $resultado1['campos']["CampoFormulario2"][$i]["opciones"]["OpcionCampo"][$o]["label"];


                                array_push($response_select, $label);
                            }
                            $opciones = implode("|", $response_select);



                            if ($requerido == "true") :
                                $obligatorio = "S";
                            else :
                                $obligatorio = "N";
                            endif;

                            $pregunta["IDPregunta"] = $codigo;

                            $pregunta["EtiquetaCampo"] = $nombre;
                            $pregunta["Obligatorio"] = $obligatorio;

                            $pregunta["Orden"] = $i1;
                            if ($tipo == "dropdown") {
                                $tipo  = "select";
                            }
                            if ($tipo == "cale") {
                                $tipo  = "date";
                            }
                            if ($tipo == "txtnum") {
                                $tipo  = "number";
                            }
                            if ($tipo == "txtarea") {
                                $tipo  = "textarea";
                            }
                            if ($tipo == "check") {
                                $tipo  = "radio";
                                $opciones = "Si|No";
                            }

                            $pregunta["Valores"] = $opciones;
                            $pregunta["TipoCampo"] = $tipo;

                            array_push($response_pregunta, $pregunta);
                        }

                        $encuesta["Preguntas"] = $response_pregunta;
                        array_push($response, $encuesta);
                    }

                endif;
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }


    public function App_AmpliarFormulario($token, $codigo)
    {

        $curl = curl_init();


        $POST = 'pToken=' . $token . '&codigoFormulario=' . $codigo;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_AmpliarFormulario2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        return $json;
    }


    public function App_ConsultarNoticias($token)
    {

        $curl = curl_init();


        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarNoticias',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);


        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Categorias]);
        $response = array();
        if (count($resultado['Categorias']) > 0) :

            for ($k = 0; $k <= $items; $k++) {
                $nombre = $resultado['Categorias'][$k]["nombre"];

                $seccion["IDClub"] = $IDClub;
                $seccion["IDSeccion"] = $nombre;
                $seccion["Nombre"] = $nombre;
                $seccion["Descripcion"] = $nombre;
                array_push($response, $seccion);
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }

    public function App_ConsultarNoticiasTipo($token, $IDSeccion)
    {


        $curl = curl_init();


        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarNoticias',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
        if (empty($IDSeccion)) {

            $IDSeccion = "Entretenimiento";
        }
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Categorias]);
        $response = array();


        if (count($resultado['Categorias']) > 0) :

            for ($k = 0; $k <= $items; $k++) {
                $nombre = $resultado['Categorias'][$k]["nombre"];
                $lista = $resultado['Categorias'][$k]["noticias"];
                if ($nombre == $IDSeccion) {


                    $cantidad = count($resultado['Categorias'][$k]["noticias"]["Noticias"]);
                    // $cantidad2=count($resultado['Categorias'][$k]["noticias"]["Noticias"]);
                    if ($cantidad == 6) :
                        $cantidad = count($resultado['Categorias'][$k]["noticias"]["Noticias"]["imagen"]);
                    endif;

                    if ($cantidad == 0) :
                        $cantidad = 6;
                    endif;

                    if ($cantidad == 1) {
                        $cantidad = 1;
                        foreach ($resultado['Categorias'][$k]["noticias"] as $lista) {



                            //     foreach($resultado['ConsumoMesa'][$k]["detalle"] as $lista) {



                            $noticia["IDClub"] = $IDClub;
                            $noticia["IDSeccion"] = $IDSeccion;
                            $noticia["IDNoticia"] = $lista["idNoticia"];
                            $noticia["Titular"] =  $lista["titulo"];
                            $noticia["Introduccion"] =  "";
                            $idNoticia = $lista["idNoticia"];

                            $detalles = self::App_AmpliarNoticias($token, $idNoticia);


                            $noticia["Cuerpo"] = $detalles;


                            /*     if(!empty($Noticia->Contenido_URL_Video)):
                            $Video = '<p><iframe width="100%" height="315" src="'.$Noticia->Contenido_URL_Video.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></p>';
                                                        endif;*/

                            $Imagen1 = $lista["imagen_peq"];
                            $Imagen2 = $lista["imagen"];


                            $ImagenCuerpo = '<p><img src="' . $Imagen2 . '"></img></p>';


                            /* endif; */

                            //     $Cuerpo =  $Noticia->Capsula_Desc_Corta ."<br>" . $ImagenCuerpo . "<br>" . $Video . "<br>" . $Noticia->Contenido_Desc_Larga ;

                            //  $noticia["Cuerpo"] = $Cuerpo;

                            $noticia["Fecha"] = ""; // $lista["fecha"];


                            $noticia["Foto"] = $Imagen2;
                            // $noticia["Foto2"] = "DFFG";
                            $noticia["FotoPortada"] = /* URL_FEDEGOLF . */ $Imagen1;
                            $noticia["CantidadComentarios"] = 11;
                            $noticia["HeHechoLike"] = "1";

                            array_push($response, $noticia);
                        }
                    } else {
                        for ($i = 0; $i < $cantidad; $i++) {


                            //     foreach($resultado['ConsumoMesa'][$k]["detalle"] as $lista) {



                            $noticia["IDClub"] = $IDClub;
                            $noticia["IDSeccion"] = $IDSeccion;
                            $noticia["IDNoticia"] = $lista["Noticias"][$i]["idNoticia"];
                            $noticia["Titular"] =     $lista["Noticias"][$i]["titulo"];
                            $noticia["Introduccion"] = "";
                            $idNoticia =  $lista["Noticias"][$i]["idNoticia"];
                            $detalles = self::App_AmpliarNoticias($token, $idNoticia);



                            $noticia["Cuerpo"] = $detalles;





                            /*     if(!empty($Noticia->Contenido_URL_Video)):
                            $Video = '<p><iframe width="100%" height="315" src="'.$Noticia->Contenido_URL_Video.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></p>';
                                                        endif;*/

                            $Imagen1 = $lista["Noticias"][$i]["imagen_peq"];

                            $Imagen2 = $lista["Noticias"][$i]["imagen"];

                            $ImagenCuerpo = '<p><img src="' . $Imagen1 . '"></img></p>';
                            $noticia["Fecha"] = ""; //$lista["Noticias"][$i]["fecha"];

                            /* endif; */

                            //     $Cuerpo =  $Noticia->Capsula_Desc_Corta ."<br>" . $ImagenCuerpo . "<br>" . $Video . "<br>" . $Noticia->Contenido_Desc_Larga ;

                            //  $noticia["Cuerpo"] = $Cuerpo;




                            $noticia["Foto"] = $Imagen2;
                            //  $noticia["Foto2"] = "";
                            $noticia["FotoPortada"] = /* URL_FEDEGOLF . */ $Imagen1;
                            $noticia["CantidadComentarios"] = 11;
                            $noticia["HeHechoLike"] = "1";

                            array_push($response, $noticia);
                        }
                    }
                }
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }


    public function App_ConsultarNoticias2($token)
    {

        $curl = curl_init();


        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarNoticias',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Categorias]);
        $response = array();
        if (count($resultado['Categorias']) > 0) :

            for ($k = 0; $k <= $items; $k++) {
                if ($resultado['Categorias'][$k]["idCategorias"] == "2") {
                    $nombre = $resultado['Categorias'][$k]["nombre"];

                    $seccion["IDClub"] = $IDClub;
                    $seccion["IDSeccion"] = $nombre;
                    $seccion["Nombre"] = $nombre;
                    $seccion["Descripcion"] = $nombre;
                    array_push($response, $seccion);
                }
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }

    public function App_ConsultarNoticiasTipo2($token, $IDSeccion)
    {

        $curl = curl_init();


        $POST = 'pToken=' . $token;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarNoticias',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
        if (empty($IDSeccion)) {
            $IDSeccion = "De interes";
        }

        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);

        $resultado = json_decode($json, true);
        $items = count($resultado[Categorias]);
        $response = array();


        if (count($resultado['Categorias']) > 0) :

            for ($k = 0; $k <= $items; $k++) {
                $nombre = $resultado['Categorias'][$k]["nombre"];
                $lista = $resultado['Categorias'][$k]["noticias"];
                if ($nombre == $IDSeccion) {

                    $cantidad = count($resultado['Categorias'][$k]["noticias"]["Noticias"]);
                    // $cantidad2=count($resultado['Categorias'][$k]["noticias"]["Noticias"]);

                    if ($cantidad == 1) {
                        $cantidad = 1;
                        foreach ($resultado['Categorias'][$k]["noticias"] as $lista) {



                            //     foreach($resultado['ConsumoMesa'][$k]["detalle"] as $lista) {



                            $noticia["IDClub"] = $IDClub;
                            $noticia["IDSeccion"] = $IDSeccion;
                            $noticia["IDNoticia"] = $lista["idNoticia"];
                            $noticia["Titular"] =  $lista["titulo"];
                            $noticia["Introduccion"] = ""; // $lista["fecha"]." ".$lista["hora"];
                            $idNoticia = $lista["idNoticia"];
                            $detalles = self::App_AmpliarNoticias($token, $idNoticia);


                            $noticia["Cuerpo"] = $detalles;




                            /*     if(!empty($Noticia->Contenido_URL_Video)):
                            $Video = '<p><iframe width="100%" height="315" src="'.$Noticia->Contenido_URL_Video.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></p>';
                                                        endif;*/

                            $Imagen1 = $lista["imagen_peq"];
                            $Imagen2 = $lista["imagen"];


                            $ImagenCuerpo = '<p><img src="' . $Imagen2 . '"></img></p>';


                            /* endif; */

                            //     $Cuerpo =  $Noticia->Capsula_Desc_Corta ."<br>" . $ImagenCuerpo . "<br>" . $Video . "<br>" . $Noticia->Contenido_Desc_Larga ;

                            //  $noticia["Cuerpo"] = $Cuerpo;

                            $noticia["Fecha"] = ""; //$lista["fecha"];


                            $noticia["Foto"] = $Imagen2;
                            // $noticia["Foto2"] = "DFFG";
                            $noticia["FotoPortada"] = /* URL_FEDEGOLF . */ $Imagen1;
                            $noticia["CantidadComentarios"] = 11;
                            $noticia["HeHechoLike"] = "1";

                            array_push($response, $noticia);
                        }
                    } else {
                        for ($i = 0; $i < $cantidad; $i++) {


                            //     foreach($resultado['ConsumoMesa'][$k]["detalle"] as $lista) {



                            $noticia["IDClub"] = $IDClub;
                            $noticia["IDSeccion"] = $IDSeccion;
                            $noticia["IDNoticia"] = $lista["Noticias"][$i]["idNoticia"];
                            $noticia["Titular"] =  $lista["Noticias"][$i]["titulo"];
                            $noticia["Introduccion"] =  ""; // $lista["Noticias"][$i]["fecha"]." ".$lista["Noticias"][$i]["hora"];
                            $idNoticia =  $lista["Noticias"][$i]["idNoticia"];
                            $detalles = self::App_AmpliarNoticias($token, $idNoticia);


                            $noticia["Cuerpo"] = $detalles;







                            /*     if(!empty($Noticia->Contenido_URL_Video)):
                            $Video = '<p><iframe width="100%" height="315" src="'.$Noticia->Contenido_URL_Video.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></p>';
                                                        endif;*/

                            $Imagen1 = $lista["Noticias"][$i]["imagen_peq"];

                            $Imagen2 = $lista["Noticias"][$i]["imagen"];

                            $ImagenCuerpo = '<p><img src="' . $Imagen1 . '"></img></p>';
                            $noticia["Fecha"] = ""; // $lista["Noticias"][$i]["fecha"];

                            /* endif; */

                            //     $Cuerpo =  $Noticia->Capsula_Desc_Corta ."<br>" . $ImagenCuerpo . "<br>" . $Video . "<br>" . $Noticia->Contenido_Desc_Larga ;

                            //  $noticia["Cuerpo"] = $Cuerpo;




                            $noticia["Foto"] = $Imagen2;
                            //  $noticia["Foto2"] = "";
                            $noticia["FotoPortada"] = /* URL_FEDEGOLF . */ $Imagen1;
                            $noticia["CantidadComentarios"] = 11;
                            $noticia["HeHechoLike"] = "1";

                            array_push($response, $noticia);
                        }
                    }
                }
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        else :

            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }

    public function App_AmpliarNoticias($token, $idNoticia)
    {

        $curl = curl_init();


        $POST = 'pToken=' . $token . '&idNoticia=' . $idNoticia;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_AmpliacionNoticia',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);


        $resultado = str_replace("{", "", $response);
        $resultado1 = str_replace("}", "", $resultado);
        $resultado2 = str_replace("[", "", $resultado1);
        $resultado3 = str_replace("]", "", $resultado2);


        $array = explode('"contenido":', $resultado3);
        $contenido = str_replace('"', "", $array[1]);



        return $contenido;
    }

    public function ConsultarTipoSubtipo($IDServicio)
    {
        switch ($IDServicio) {
            case "46188": // Tenis
                $idTipoReserva = urlencode("1");
                $idSubTipoReserva = urlencode("1");
                break;
            case "46203": // Squash
                $idTipoReserva = urlencode("1");
                $idSubTipoReserva = urlencode("2");
                break;
            case "46131": // Belleza y bienestar
                $idTipoReserva = urlencode("2");
                $idSubTipoReserva = urlencode("1");
                break;
        }
        $array_tipo_subtipo["IDTiporeserva"] = $idTipoReserva;
        $array_tipo_subtipo["IDSubTiporeserva"] = $idSubTipoReserva;
        return $array_tipo_subtipo;
    }

    public function App_Turnos2($Fecha, $IDServicio, $Token)
    {

        $curl = curl_init();
        //$Token = SIMWebServiceCountryMedellin::App_AutenticarUser($user,$pwd);
        //$resp=json_decode($Token);
        //$Token=$resp->token;
        $array_tipo_subtipo = self::ConsultarTipoSubtipo($IDServicio);
        $pToken = urlencode($Token);
        $POST = 'Fecha=' . $Fecha . '&pToken=' . $pToken . '&idTipoReserva=' . $array_tipo_subtipo["IDTiporeserva"] . '&idSubTipoReserva=' . $array_tipo_subtipo["IDSubTiporeserva"];
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_Turnos2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = simplexml_load_string($response);
        return $DATOS[0];
    }

    public function App_UbicacionesDisponibles($Fecha, $Turno, $IDServicio, $Token)
    {
        $curl = curl_init();
        $array_tipo_subtipo = self::ConsultarTipoSubtipo($IDServicio);
        $pToken = urlencode($Token);
        $idTurno = $Turno;
        $POST = 'Fecha=' . $Fecha . '&pToken=' . $pToken . '&idTipoReserva=' . $array_tipo_subtipo["IDTiporeserva"] . '&idSubTipoReserva=' . $array_tipo_subtipo["IDSubTiporeserva"] . '&idTurno=' . $idTurno;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_UbicacionesDisponibles',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return $DATOS[0];
    }

    public function App_GenerarReserva($Fecha, $IDTurno, $IDUbicacion, $IDServicio, $IDTipoReserva, $Token)
    {


        $curl = curl_init();
        $pToken = urlencode($Token);
        $array_tipo_subtipo = self::ConsultarTipoSubtipo($IDServicio);

        if ($IDServicio == 46131) { // Belleza y bienestar            
            $idAsesor = urlencode($IDUbicacion);
            $IDUbicacion = "0";
            $IDSubtipo = $IDTipoReserva;
        } else {
            $idAsesor = urlencode("-1");
            $IDSubtipo = $array_tipo_subtipo["IDSubTiporeserva"];
        }

        $POST = "Fecha=$Fecha&idTurno=$IDTurno&idUbicacion=$IDUbicacion&pToken=$pToken&idAsesor=$idAsesor&idTipoReserva=" . $array_tipo_subtipo["IDTiporeserva"] . "&idSubTipoReserva=" . $IDSubtipo;

        /*
        $reservas[idReserva]=0;   
        $reservas[estado]="false";                            
        $reservas["mensaje"]=$POST;
        $reservas["response"] = $reservas["idReserva"];                        
        return $reservas;
        */

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_GenerarReserva',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        return (array)$DATOS;
    }

    public function App_GenerarReservaZonaInteractiva($Fecha, $HoraInicio, $HoraFin, $IDElemento, $IDServicio, $IDTipoReserva, $Token)
    {

        //Consulto el valor
        $respuesta_valor = self::App_LiquidarZonaInteractiva($Fecha, $HoraInicio, $HoraFin, $IDElemento, $IDServicio, $IDTipoReserva, $Token);
        if ($respuesta_valor == "ErrorConexion") {
            $respuesta["message"] = "Hubo un problema al liquidar la reserva, intente mas tarde";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } elseif ($respuesta_valor == "ErrorValor") {
            $respuesta["message"] = "Hubo un problema al consultar el valor de la reserva, intente mas tarde";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $curl = curl_init();
            $pToken = $Token;
            $wsdl = "http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx?WSDL";
            $oSoapClient = new nusoap_client($wsdl, true);
            $cadena =  '<App_GenerarReservaZonaInteractiva xmlns="http://tempuri.org/">
                    <pToken>' . $pToken . '</pToken>
                    <fecha>' . $Fecha . '</fecha>
                    <horainicial>' . $HoraInicio . '</horainicial>
                    <horafinal>' . $HoraFin . '</horafinal>
                    <!-- Optional -->
                    <puestos>
                        <!-- Optional -->
                        <Puestos>
                            <idPuesto>' . $IDElemento . '</idPuesto>
                            <tipoUsuario>Socio</tipoUsuario>
                        </Puestos>
                    </puestos>
                </App_GenerarReservaZonaInteractiva>';

            $respuesta = $oSoapClient->call("App_GenerarReservaZonaInteractiva", $cadena, "");
            if ($oSoapClient->getError()) {
                //echo "<br/><br/>Error al llamar el metodo<br/> ".$oSoapClient->getError();
                $respuesta["message"] = "CMR2. Hubo un problema al conectar con servicio externo, intente mas tarde";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } else {
                $IDReserva = $respuesta["App_GenerarReservaZonaInteractivaResult"]["PuestosReserva"]["idReserva"];
                if ((int)$IDReserva > 0) {
                    $respuesta["message"] = "Reserva creada correctamente: Nro: " . $IDReserva . " Valor: " . $respuesta_valor;
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "CMR1. Hubo un problema al guardar la reserva intente mas tarde";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            }
        }

        return $respuesta;
    }

    public function App_LiquidarZonaInteractiva($Fecha, $HoraInicio, $HoraFin, $IDElemento, $IDServicio, $IDTipoReserva, $Token)
    {
        $curl = curl_init();
        $pToken = $Token;
        $wsdl = "http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx?WSDL";
        $oSoapClient = new nusoap_client($wsdl, true);
        $cadena =  '<App_LiquidarZonaInteractiva xmlns="http://tempuri.org/">
				<pToken>' . $pToken . '</pToken>
				<fecha>' . $Fecha . '</fecha>
				<horainicial>' . $HoraInicio . '</horainicial>
				<horafinal>' . $HoraFin . '</horafinal>
				<!-- Optional -->
				<puestos>
					<!-- Optional -->
					<Puestos>
						<idPuesto>' . $IDElemento . '</idPuesto>
						<tipoUsuario>Socio</tipoUsuario>
					</Puestos>
				</puestos>
			</App_LiquidarZonaInteractiva>';

        $respuesta = $oSoapClient->call("App_LiquidarZonaInteractiva", $cadena, "");
        if ($oSoapClient->getError()) {
            $ValorLiquidacion = "ErrorConexion";
        } else {
            $IDPuesto = $respuesta["App_LiquidarZonaInteractivaResult"]["PuestoValor"]["idPuesto"];
            if ((int)$IDPuesto > 0) {
                $ValorLiquidacion = "$" . $respuesta["App_LiquidarZonaInteractivaResult"]["PuestoValor"]["valor"];
            } else {
                $ValorLiquidacion = "ErrorValor";
            }
        }
        return $ValorLiquidacion;
    }

    public function get_disponibilidad_elemento_servicio($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva, $HoraInicial = "", $HoraFinal = "")
    {
        $response = array();
        if ($IDServicio == 46131) { //Belleza y bienestar consume otro servicio            
            $response = self::App_DisponibilidadServiciosBB($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva);
        } elseif ($IDServicio == 46189) {
            $response = self::App_DisponibilidadServiciosCentro($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva, $HoraInicial, $HoraFinal);
        } else {
            $Turnos = self::App_Turnos2($Fecha, $IDServicio, $Token);
            $response = self::disponibilidad_general($Turnos, $IDServicio, $Token, $Fecha);
        }

        $respuesta[message] = "Encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;

        return $respuesta;
    }

    public function App_DisponibilidadServiciosCentro($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva, $HoraInicial, $HoraFinal)
    {
        $dbo = SIMDB::get();
        $HoraInicialEscogida = $HoraInicial;
        $Disponibilidad = array();
        $DisponibilidadFinal = array();
        $response = array();
        $Cuenta = 0;
        $response_disponibilidades = array();
        $curl = curl_init();
        $pToken = urlencode($Token);

        switch ($IDTipoReserva) {
            case "6110": // Individual
                $pTipoEspacio = 1;
                break;
            case "6111": //Doble
                $pTipoEspacio = 3;
                break;
            case "6112": // Sala cerrada
                $pTipoEspacio = 4;
                break;
            case "6113": // Mesa abierta
                $pTipoEspacio = 2;
                break;
            default:
                $pTipoEspacio = 1;
        }

        $POST = 'fecha=' . $Fecha . '&horainicial=' . $HoraInicialEscogida . '&horafinal=' . $HoraFinal . '&pToken=' . $pToken . '&pTipoEspacio=' . $pTipoEspacio;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_DisponibilidadZonaInteractiva3',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response_ws = curl_exec($curl);
        curl_close($curl);

        $DATOS = simplexml_load_string($response_ws);
        foreach ($DATOS[0] as $datos_servicio) {
            //print_r($datos_servicio);
            $HoraInicial = $HoraInicialEscogida;
            while (strtotime($HoraInicial) <= strtotime($HoraInicialEscogida)) {
                $orden++;
                $Disponible = "S";
                $Socio = "";
                $IDSocio = "";
                $IDReserva = "";
                $IDSocioBeneficiario = "";
                $LabelDisponible = "Disponible";
                $InfoDisponibilidad[Hora] = $HoraInicial;
                $InfoDisponibilidad[HoraFinal] = $HoraFinal;
                $InfoDisponibilidad[GMT] = "-05:00";
                $InfoDisponibilidad[Disponible] = $Disponible;
                $InfoDisponibilidad[Socio] = $Socio;
                $InfoDisponibilidad[IDSocio] = $IDSocio;
                $InfoDisponibilidad[ModalidadEsquiSocio] = "";
                $InfoDisponibilidad[IDReserva] = $IDReserva;
                $InfoDisponibilidad[IDSocioBeneficiario] = $IDSocioBeneficiario;
                $InfoDisponibilidad[MaximoPersonaTurno] = "0";
                $InfoDisponibilidad[NumeroInvitadoClub] = "0";
                $InfoDisponibilidad[NumeroInvitadoExterno] = "0";
                $InfoDisponibilidad[NumeroMinimoInvitadoClub] = "0";
                $InfoDisponibilidad[NumeroMinimoInvitadoExterno] = "0";
                $InfoDisponibilidad[IDDisponibilidad] = "";
                $InfoDisponibilidad[PermiteRepeticion] = "N";
                $InfoDisponibilidad[MedicionRepeticion] = "";
                $InfoDisponibilidad[FechaFinRepeticion] = "";
                $InfoDisponibilidad[Georeferenciacion] = "N";
                $InfoDisponibilidad[Latitud] = "0";
                $InfoDisponibilidad[Longitud] = "0";
                $InfoDisponibilidad[Rango] = "0";
                $InfoDisponibilidad[MensajeFueraRango] = "";
                $InfoDisponibilidad[LabelDisponible] = $LabelDisponible;
                $InfoDisponibilidad[IDElemento] = (string)$datos_servicio->idPuesto;
                $InfoDisponibilidad[NombreElemento] = "Puesto " . (string)$datos_servicio->espacio;
                $InfoDisponibilidad[IDUsuario] = "";
                $InfoDisponibilidad[PermiteReservarUsuario] = "N";
                $InfoDisponibilidad[ColorLetra] = "#000000";
                $InfoDisponibilidad[ColorFondo] = "#ffffff";
                $InfoDisponibilidad[Foto] = "";
                $InfoDisponibilidad[ModalidadElemento] = "";
                $InfoDisponibilidad[MaximoInvitadosSalon] = "0";
                $InfoDisponibilidad[OrdenElemento] = (string)$orden;
                $InfoDisponibilidad[PermiteListaEspera] = "N";
                $InfoDisponibilidad[LabelTituloHora] = "";
                $InfoDisponibilidad[MostrarBotonCumplida] = "S";
                $InfoDisponibilidad[IDAuxiliar] = "";
                $InfoDisponibilidad[MostrarBotonInscritos] = "N";
                $InfoDisponibilidad[LabelBotonInscritos] = "";
                $InfoDisponibilidad[Inscritos] = [];
                array_push($Disponibilidad, $InfoDisponibilidad);

                $HoraInicial = strtotime('+1 hour', strtotime($HoraInicial));
                $HoraInicial = date('H:i:s', $HoraInicial);
            }

            array_push($DisponibilidadFinal, $Disponibilidad);
        }


        $orden_letra = "";
        foreach ($Disponibilidad as $id_array => $datos_array) :
            $orden_letra = SIMResources::$abecedario_orden[$datos_array["OrdenElemento"]];
            $array_ordenado_hora[$orden_letra . "_" . $datos_array["Hora"] . $datos_array["IDElemento"]] = $datos_array;
        endforeach;



        if (count($array_ordenado_hora) > 0) {
            ksort($array_ordenado_hora);
        }


        $response_array_ordenado = array();

        if (count($array_ordenado_hora) <= 0) {
            $array_ordenado_hora = array();
        }


        foreach ($array_ordenado_hora as $id_array => $datos_array) :
            array_push($response_array_ordenado, $datos_array);
        endforeach;


        array_push($response_disponibilidades, $response_array_ordenado);


        $ConfigRespuesta[IDClub] = $IDClub;
        $ConfigRespuesta[IDServicio] = $IDServicio;
        $ConfigRespuesta[Fecha] = $Fecha;

        // Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
        if (!empty($UnElemento)) :
            $ConfigRespuesta["Disponibilidad"] = $response_array_ordenado;
        else :
            $ConfigRespuesta["Disponibilidad"] = $response_disponibilidades;
        endif;

        $ConfigRespuesta[name] = "";

        array_push($response, $ConfigRespuesta);
        return $response;
    }

    public function App_DisponibilidadServiciosBB($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva)
    {
        $Disponibilidad = array();
        $DisponibilidadFinal = array();
        $response = array();
        $Cuenta = 0;
        $response_disponibilidades = array();

        $curl = curl_init();
        $array_tipo_subtipo = self::ConsultarTipoSubtipo($IDServicio);
        $pToken = urlencode($Token);
        $POST = 'Fecha=' . $Fecha . '&pToken=' . $pToken . '&idSubTipoReserva=' . $IDTipoReserva;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_DisponibilidadServiciosBB',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response_ws = curl_exec($curl);

        curl_close($curl);

        $DATOS = simplexml_load_string($response_ws);
        foreach ($DATOS[0] as $datos_servicio) {
            $array_ubicaciones["idUbicacion"] = (string)$datos_servicio->idAsesor;
            $array_ubicaciones["Nombre"] = (string)$datos_servicio->nombre;
            $array_ubicaciones["estado"] = "disponible";
            $array_ubicaciones["mensaje_estado"] = "Libre";
            //print_r($array_ubicaciones);
            foreach ($datos_servicio->turnos->Turnos as $datos_turno) {
                $orden++;

                if ($Fecha == date("Y-m-d")) {
                    if ($datos_turno->hora_inicial >= date("H:i:s")) {
                        $Mostrar = "S";
                    } else {
                        $Mostrar = "N";
                    }
                } else {
                    $Mostrar = "S";
                }


                if ((((string)$datos_turno->Fecha >= date("H:i:s") && $Fecha == date("Y-m-d")) || $Cuenta <= 3) && $Mostrar == "S") {
                    if ((string)$datos_turno->estado == "disponible")
                        $Disponible = "S";
                    else
                        $Disponible = "N";

                    $Socio = "";
                    $IDSocio = "";
                    $IDReserva = "";
                    $IDSocioBeneficiario = "";
                    $LabelDisponible = (string)$datos_turno->estado;

                    $InfoDisponibilidad[Hora] = (string)$datos_turno->hora_inicial;
                    $InfoDisponibilidad[GMT] = "-05:00";
                    $InfoDisponibilidad[Disponible] = $Disponible;
                    $InfoDisponibilidad[Socio] = $Socio;
                    $InfoDisponibilidad[IDSocio] = $IDSocio;
                    $InfoDisponibilidad[ModalidadEsquiSocio] = "";
                    $InfoDisponibilidad[IDReserva] = $IDReserva;
                    $InfoDisponibilidad[IDSocioBeneficiario] = $IDSocioBeneficiario;
                    $InfoDisponibilidad[MaximoPersonaTurno] = "0";
                    $InfoDisponibilidad[NumeroInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroInvitadoExterno] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoExterno] = "0";
                    $InfoDisponibilidad[IDDisponibilidad] = "";
                    $InfoDisponibilidad[PermiteRepeticion] = "N";
                    $InfoDisponibilidad[MedicionRepeticion] = "";
                    $InfoDisponibilidad[FechaFinRepeticion] = "";
                    $InfoDisponibilidad[Georeferenciacion] = "N";
                    $InfoDisponibilidad[Latitud] = "0";
                    $InfoDisponibilidad[Longitud] = "0";
                    $InfoDisponibilidad[Rango] = "0";
                    $InfoDisponibilidad[MensajeFueraRango] = "";
                    $InfoDisponibilidad[LabelDisponible] = $LabelDisponible;
                    $InfoDisponibilidad[IDElemento] = (string)$datos_servicio->idAsesor;
                    $InfoDisponibilidad[NombreElemento] = (string)$datos_servicio->nombre;
                    $InfoDisponibilidad[IDUsuario] = "";
                    $InfoDisponibilidad[PermiteReservarUsuario] = "N";
                    $InfoDisponibilidad[ColorLetra] = "#000000";
                    $InfoDisponibilidad[ColorFondo] = "#ffffff";
                    $InfoDisponibilidad[Foto] = "";
                    $InfoDisponibilidad[ModalidadElemento] = "";
                    $InfoDisponibilidad[MaximoInvitadosSalon] = "0";
                    $InfoDisponibilidad[OrdenElemento] = (string)$orden;
                    $InfoDisponibilidad[PermiteListaEspera] = "N";
                    $InfoDisponibilidad[LabelTituloHora] = "";
                    $InfoDisponibilidad[MostrarBotonCumplida] = "S";
                    $InfoDisponibilidad[IDAuxiliar] = "";
                    $InfoDisponibilidad[MostrarBotonInscritos] = "N";
                    $InfoDisponibilidad[LabelBotonInscritos] = "";
                    $InfoDisponibilidad[Inscritos] = [];
                    $InfoDisponibilidad[IDTurno] = (string)$datos_turno->idTurno;
                    array_push($Disponibilidad, $InfoDisponibilidad);
                }
            }
            array_push($DisponibilidadFinal, $Disponibilidad);
        }

        $orden_letra = "";
        foreach ($Disponibilidad as $id_array => $datos_array) :
            $orden_letra = SIMResources::$abecedario_orden[$datos_array["OrdenElemento"]];
            $array_ordenado_hora[$orden_letra . "_" . $datos_array["Hora"] . $datos_array["IDElemento"]] = $datos_array;
        endforeach;



        if (count($array_ordenado_hora) > 0) {
            ksort($array_ordenado_hora);
        }


        $response_array_ordenado = array();

        if (count($array_ordenado_hora) <= 0) {
            $array_ordenado_hora = array();
        }


        foreach ($array_ordenado_hora as $id_array => $datos_array) :
            array_push($response_array_ordenado, $datos_array);
        endforeach;


        array_push($response_disponibilidades, $response_array_ordenado);


        $ConfigRespuesta[IDClub] = $IDClub;
        $ConfigRespuesta[IDServicio] = $IDServicio;
        $ConfigRespuesta[Fecha] = $Fecha;

        // Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
        if (!empty($UnElemento)) :
            $ConfigRespuesta["Disponibilidad"] = $response_array_ordenado;
        else :
            $ConfigRespuesta["Disponibilidad"] = $response_disponibilidades;
        endif;

        $ConfigRespuesta[name] = "";

        array_push($response, $ConfigRespuesta);
        return $response;
    }

    public function disponibilidad_general($Turnos, $IDServicio, $Token, $Fecha)
    {

        $Disponibilidad = array();
        $DisponibilidadFinal = array();
        $response = array();
        $Cuenta = 0;
        $InfoTurnos = json_decode($Turnos, true);
        $Mostrar = "S";
        foreach ($InfoTurnos as $id => $Turno) :
            if ($Fecha == date("Y-m-d")) {
                if ($Turno[start_time] >= date("H:i:s")) {
                    $Mostrar = "S";
                } else {
                    $Mostrar = "N";
                }
            } else {
                $Mostrar = "S";
            }

            if ($Mostrar == "S") :
                $Ubicaciones = SIMWebServiceCountryMedellin::App_UbicacionesDisponibles($Fecha, $Turno[IdTurno], $IDServicio, $Token);
                $InfoUbicaciones = json_decode($Ubicaciones, true);
                foreach ($InfoUbicaciones as $id => $DisponibilidadTurno) :
                    $InfoDisponibilidad[Hora] = $Turno[start_time];
                    $InfoDisponibilidad[GMT] = "-05:00";

                    if ($DisponibilidadTurno[estado] == "disponible") :
                        $Disponible = "S";
                        $Socio = "";
                        $IDSocio = "";
                        $IDReserva = "";
                        $IDSocioBeneficiario = "";
                        $LabelDisponible = $Turno[Estado];
                    else :
                        $Disponible = "N";
                        $Socio = "";
                        $IDSocio = "";
                        $IDReserva = "";
                        $IDSocioBeneficiario = "";
                        $LabelDisponible = $DisponibilidadTurno[mensaje_estado];
                    endif;

                    $InfoDisponibilidad[Disponible] = $Disponible;
                    $InfoDisponibilidad[Socio] = $Socio;
                    $InfoDisponibilidad[IDSocio] = $IDSocio;
                    $InfoDisponibilidad[ModalidadEsquiSocio] = "";
                    $InfoDisponibilidad[IDReserva] = $IDReserva;
                    $InfoDisponibilidad[IDSocioBeneficiario] = $IDSocioBeneficiario;
                    $InfoDisponibilidad[MaximoPersonaTurno] = "0";
                    $InfoDisponibilidad[NumeroInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroInvitadoExterno] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoExterno] = "0";
                    $InfoDisponibilidad[IDDisponibilidad] = "";
                    $InfoDisponibilidad[PermiteRepeticion] = "N";
                    $InfoDisponibilidad[MedicionRepeticion] = "";
                    $InfoDisponibilidad[FechaFinRepeticion] = "";
                    $InfoDisponibilidad[Georeferenciacion] = "N";
                    $InfoDisponibilidad[Latitud] = "0";
                    $InfoDisponibilidad[Longitud] = "0";
                    $InfoDisponibilidad[Rango] = "0";
                    $InfoDisponibilidad[MensajeFueraRango] = "";
                    $InfoDisponibilidad[LabelDisponible] = $LabelDisponible;
                    $InfoDisponibilidad[IDElemento] = (string)$DisponibilidadTurno[idUbicacion];
                    $InfoDisponibilidad[NombreElemento] = $DisponibilidadTurno[Nombre];
                    $InfoDisponibilidad[IDUsuario] = "";
                    $InfoDisponibilidad[PermiteReservarUsuario] = "N";
                    $InfoDisponibilidad[ColorLetra] = "#000000";
                    $InfoDisponibilidad[ColorFondo] = "#ffffff";
                    $InfoDisponibilidad[Foto] = "";
                    $InfoDisponibilidad[ModalidadElemento] = "";
                    $InfoDisponibilidad[MaximoInvitadosSalon] = "0";
                    $InfoDisponibilidad[OrdenElemento] = "";
                    $InfoDisponibilidad[PermiteListaEspera] = "N";
                    $InfoDisponibilidad[LabelTituloHora] = "";
                    $InfoDisponibilidad[MostrarBotonCumplida] = "S";
                    $InfoDisponibilidad[IDAuxiliar] = "";
                    $InfoDisponibilidad[MostrarBotonInscritos] = "N";
                    $InfoDisponibilidad[LabelBotonInscritos] = "";
                    $InfoDisponibilidad[Inscritos] = [];

                    array_push($Disponibilidad, $InfoDisponibilidad);
                endforeach;
                $Cuenta++;
            endif;
        endforeach;
        array_push($DisponibilidadFinal, $Disponibilidad);

        $ConfigRespuesta[IDClub] = $IDClub;
        $ConfigRespuesta[IDServicio] = $IDServicio;
        $ConfigRespuesta[Fecha] = $Fecha;
        $ConfigRespuesta[Disponibilidad] = $DisponibilidadFinal;
        $ConfigRespuesta[name] = "";

        array_push($response, $ConfigRespuesta);
        return $response;
    }

    public function get_fechas_disponibles_servicio($IDClub, $IDServicio, $FechaBuscar)
    {
        $response = array();
        $Fechas = array();
        $dbo = SIMDB::get();
        $Fecha = date("Y-m-d");
        $FechaInicio = strtotime($Fecha);
        $MostrarFecha = "S";

        switch ($IDServicio) {
            case "46189":
            case "46131":
                $FechaFin = strtotime("+52 week", $FechaInicio);
                break;
            default:
                $FechaFin = strtotime("+2 day", $FechaInicio);
        }


        for ($contador_fecha = $FechaInicio; $contador_fecha <= $FechaFin; $contador_fecha += 86400) :

            $Fecha = date("Y-m-d", $contador_fecha);
            if (!empty($FechaBuscar)) {
                if ($Fecha == $FechaBuscar)
                    $MostrarFecha = "S";
                else
                    $MostrarFecha = "N";
            } else {
                $MostrarFecha = "S";
            }

            if ($MostrarFecha == "S") {
                $InfoFechas[Fecha] = $Fecha;
                $InfoFechas[Activo] = "S";
                $InfoFechas[FechaReservar] = $Fecha;
                $InfoFechas[HoraReservar] = "06:00:00";
                $InfoFechas[GMT] = "-05:00";
                $InfoFechas[TiempoRestanteDias] = 0;
                $InfoFechas[TiempoRestanteHoras] = 0;
                $InfoFechas[TiempoRestanteMinutos] = 0;
                $InfoFechas[TiempoRestanteSegundos] = 0;
                $InfoFechas[TiempoRestanteMiliSegundos] = 0;
                array_push($Fechas, $InfoFechas);
            }

        endfor;

        $ConfigRespuesta[IDServicio] = $IDServicio;
        $ConfigRespuesta[IDClub] = $IDClub;
        $ConfigRespuesta[Nombre] = "";
        $ConfigRespuesta[Fechas] = $Fechas;

        array_push($response, $ConfigRespuesta);

        $respuesta[message] = "Encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;

        return $respuesta;
    }

    public function BuscarTurno($IDServicio, $IDElemento, $Fecha, $Hora, $Token, $IDTipoReserva, $IDClub)
    {
        if ($IDServicio == 46131) { //Belleza y bienestar consume otro servicio
            $Turnos = self::App_DisponibilidadServiciosBB($IDClub, $Fecha, $IDServicio, $Token, $IDTipoReserva);
            foreach ($Turnos[0]["Disponibilidad"] as $datos_dispo) {
                foreach ($datos_dispo as $datos_turno) {
                    if ($datos_turno["Hora"] == $Hora && $datos_turno["IDElemento"] == $IDElemento) :
                        if ($datos_turno["Disponible"] == "S")
                            return $datos_turno["IDTurno"];
                        else
                            return false;
                    endif;
                }
            }
        } else {
            $Turnos = SIMWebServiceCountryMedellin::App_Turnos2($Fecha, $IDServicio, $Token);
            $InfoTurnos = json_decode($Turnos, true);
            foreach ($InfoTurnos as $id => $Turno) :
                if ($Turno[start_time] == $Hora) :
                    if ($Turno[Estado] == "disponible") :
                        return $Turno[IdTurno];
                    else :
                        return false;
                    endif;
                endif;
            endforeach;
        }

        return false;
    }

    function App_ConsultarReservasGeneral($IDClub, $datos_socio)
    {
        $Token = $datos_socio["TokenCountryMedellin"];
        $pToken = urlencode($Token);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarReservas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "pToken=$pToken",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $array_datos = json_decode($DATOS);
        return $array_datos;
    }

    function App_ConsultarReservasGeneral2($IDClub, $datos_socio)
    {
        $FechaInicial = date("Y-m-d");
        $FechaFinal = date("Y-m-d", strtotime($FechaInicial . "+ 30 days"));
        $Token = $datos_socio["TokenCountryMedellin"];
        $pToken = urlencode($Token);
        $POST = "fechainicial=" . $FechaInicial . "&fechafinal=" . $FechaFinal . "&pToken=$pToken";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ConsultarReservas2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        print_r($Datos);
        return $DATOS;
    }

    public function App_ConsultarReservas($IDClub, $datos_socio)
    {
        $response_datos = array();
        //$array_datos_general=self::App_ConsultarReservasGeneral($IDClub, $datos_socio);
        $array_datos_general = self::App_ConsultarReservasGeneral2($IDClub, $datos_socio);
        $ConReserva = "N";




        foreach ($array_datos_general as $datos_reserva) {

            (string)$datos_reserva->idTipoReserva;
            (string)$datos_reserva->idSubTipoReserva;

            if ($datos_reserva->idTipoReserva == "1") {
                $PermiteEditarAuxiliar = "S";
            } else {
                $PermiteEditarAuxiliar = "N";
            }

            $ConReserva = "S";
            $Hora = $datos_reserva->horainicial;
            $array_hora = explode(" ", $Hora);
            $ampm = str_replace(".", "", $array_hora[1]);
            if ($ampm == "m")
                $ampm = "pm";

            $Hora = date("H:i:00", strtotime($array_hora[0] . " " . $ampm));
            //$Hora=$array_hora[0].":00";
            $ConfigRespuesta[IDClub] = $IDClub;
            $ConfigRespuesta[IDSocio] = $datos_socio["IDSocio"];
            $ConfigRespuesta[Socio] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $ConfigRespuesta[IDReserva] = (string)$datos_reserva->idReserva;
            $ConfigRespuesta[IDServicio] = "131";
            $ConfigRespuesta[Icono] = "";
            $ConfigRespuesta[NombreServicio] = $datos_reserva->descripcion .  " ";
            $ConfigRespuesta[NombreServicioPersonalizado] = $datos_reserva->descripcion .  " ";
            $ConfigRespuesta[IDElemento] = "";
            $ConfigRespuesta[NombreElemento] = " ";
            $ConfigRespuesta[Fecha] = (string)$datos_reserva->fecha;
            $ConfigRespuesta[Tee] = "";
            $ConfigRespuesta[CantidadInvitadoSalon] = "";
            $ConfigRespuesta[PagadaOnline] = "";
            $ConfigRespuesta[FechaTransaccion] = "";
            $ConfigRespuesta[IDServicioTipoReserva] = "";
            $ConfigRespuesta[MensajeTransaccion] = "";
            $ConfigRespuesta[LabelElementoSocio] = "";
            $ConfigRespuesta[LabelElementoExterno] = "";
            $ConfigRespuesta[PermiteEditarAuxiliar] = $PermiteEditarAuxiliar;
            $ConfigRespuesta[PermiteEditarAdicionales] = "";
            $ConfigRespuesta[PermiteListaEsperaAuxiliar] = "";
            $ConfigRespuesta[MultipleAuxiliar] = "";
            $ConfigRespuesta[LabelReconfimarBoton] = "";
            $ConfigRespuesta[PermiteReconfirmar] = "";
            $ConfigRespuesta[LabelInvitados] = "Agregar Colaborador";
            $ConfigRespuesta[AdicionalesObligatorio] = "";
            $ConfigRespuesta[TextoLegal] = "";
            $ConfigRespuesta[OcultarBotonEditarInvitados] = "S";
            $ConfigRespuesta[LabelElemento] = "";
            $ConfigRespuesta[OcultarHora] = "";
            $ConfigRespuesta[PermiteInvitadoExternoCedula] = "";
            $ConfigRespuesta[PermiteInvitadoExternoCorreo] = "";
            $ConfigRespuesta[PermiteInvitadoExternoFechaNacimiento] = "";
            $ConfigRespuesta[InvitadoExternoPago] = "";
            $ConfigRespuesta[LabelInvitadoExternoPago] = "";
            $ConfigRespuesta[InvitadoExternoValor] = "";
            $ConfigRespuesta[EliminarParaTodosOParaMi] = "";
            $ConfigRespuesta[MensajeEliminarParaTodosOParaMi] = "";
            $ConfigRespuesta[BotonEliminarReserva] = "";
            $ConfigRespuesta[LabelEliminarParaMi] = "";
            $ConfigRespuesta[LabelEliminarParaTodos] = "";
            $ConfigRespuesta[CamposDinamicosInvitadoExternoHabilitado] = "";
            $ConfigRespuesta[BotonEditarAdicionales] = "";
            $ConfigRespuesta[LabelAdicionales] = "";
            $ConfigRespuesta[EncabezadoAdicionales] = "";
            $ConfigRespuesta[LabelSeleccioneAdicionales] = "";
            $ConfigRespuesta[MensajeAdicionalesObligatorio] = "";
            $ConfigRespuesta[PermiteEditarReserva] = "";
            $ConfigRespuesta[PermiteAdicionarCaddies] = "";
            $ConfigRespuesta[LabelAdicionarCaddies] = "";
            $ConfigRespuesta[ObligatorioSeleccionarCaddie] = "";
            $ConfigRespuesta[MensajeCaddiesObligatorio] = "";
            $ConfigRespuesta[LabelTicketsDescuento] = "";
            $ConfigRespuesta[TipoBotonInvitacion] = "";
            $ConfigRespuesta[LabelAuxiliar] = "";
            $ConfigRespuesta[ListaAuxiliar] = array();
            $ConfigRespuesta[Hora] = $Hora;
            $ConfigRespuesta[GMT] = "-05:00";
            $ConfigRespuesta[HoraFin] = "";
            $ConfigRespuesta[NumeroInvitadoClub] = "";
            $ConfigRespuesta[NumeroInvitadoExterno] = "";
            $ConfigRespuesta[Beneficiario] = "";
            $ConfigRespuesta[Invitados] = array();
            $ConfigRespuesta[ReservaAsociada] = array();
            $ConfigRespuesta[Adicionales] = array();
            $ConfigRespuesta[CamposReserva] = array();
            $ConfigRespuesta[idTipoReserva] = (string)$datos_reserva->idTipoReserva;
            $ConfigRespuesta[idSubTipoReserva] = (string)$datos_reserva->idSubTipoReserva;
            array_push($response_datos, $ConfigRespuesta);
        }


        if ($ConReserva == "S") {
            $respuesta[message] = "Encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response_datos;
        } else {
            $reserva["IDClub"] = "";
            $reserva["IDSocio"] = "";
            $reserva["IDReserva"] = "";
            $reserva["IDServicio"] = "";
            $id_servicio_maestro = "";
            $reserva["NombreServicio"] = "";
            $reserva["IDElemento"] = "";
            $reserva["NombreElemento"] = "";
            $reserva["Fecha"] = "";
            $reserva["Tee"] = "";
            array_push($response_datos, $reserva);
            $respuesta["message"] = "No tienes reservas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = $response_datos;
        }


        return $respuesta;



        //return (array)$DATOS;
    }

    public function EliminaReserva($IDClub, $IDSocio, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $FechaInicial = date("Y-m-d");
        $FechaFinal = date("Y-m-d", strtotime($FechaInicial . "+ 30 days"));
        $IDTiporeserva = 0;
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Token = $datos_socio["TokenCountryMedellin"];
        $response_datos = array();
        $pToken = urlencode($Token);
        $POST = "fechainicial=" . $FechaInicial . "&fechafinal=" . $FechaFinal . "&pToken=$pToken";


        $resp_reservas = SIMWebServiceCountryMedellin::App_ConsultarReservas($IDClub, $datos_socio);
        foreach ($resp_reservas["response"] as $datos_reserva) {
            if ($datos_reserva["IDReserva"] == $IDReserva) {
                $IDTiporeserva = $datos_reserva["idTipoReserva"];
            }
        }

        $POST = "idReserva=$IDReserva&pToken=" . $pToken . "&idTipoReserva=" . $IDTiporeserva;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_EliminarReserva',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $json = json_encode($DATOS);
        (array)$json;
        $array_datos = json_decode($json);

        if ($array_datos->estado == "false") {
            $respuesta["message"] = 'Reserva no pudo eliminarse';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = $array_datos->mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_tipo_reserva($IDSocio, $IDServicio, $Fecha)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Token = $datos_socio["TokenCountryMedellin"];
        $response_tiporeservas = array();
        $pToken = urlencode($Token);


        //Se debe averiguar el tipo de reserva enonce se consulta primero las reservas
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_ServiciosBB',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "pToken=" . $pToken . "&Fecha=" . $Fecha,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response);
        $array_datos = json_decode($DATOS);
        foreach ($array_datos as $datos_tipo) {
            $tiporeserva["IDServicio"] = $IDServicio;
            $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->idServicio;
            $tiporeserva["Nombre"] = (string)$datos_tipo->nombre;
            array_push($response_tiporeservas, $tiporeserva);
        }
        return $response_tiporeservas;
    }


    public function App_AsistentesDisponibles($IDClub, $IDReservaGeneral, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Token = $datos_socio["TokenCountryMedellin"];
        $response_asistente = array();
        $response_auxiliar = array();
        $response = array();
        $pToken = urlencode($Token);
        //Se debe averiguar el tipo de reserva enonce se consulta primero las reservas
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_AsistentesDisponibles',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "pToken=" . $pToken . "&idReserva=" . $IDReservaGeneral,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response_ws = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response_ws);
        $array_datos = json_decode($DATOS);

        //print_r($response_ws);
        foreach ($array_datos as $datos_asistente) {
            $tiporeserva["IDAsistente"] = (string)$datos_asistente->idAsistente;
            $tiporeserva["IDTipoAsistente"] = (string)$datos_asistente->idTipoAsistente;;
            $tiporeserva["Nombre"] = (string)$datos_asistente->nombre;
            $tiporeserva["Descripcion"] = (string)$datos_asistente->descripcionTipoAsistente;
            array_push($response_asistente, $tiporeserva);
        }

        if (!empty($datos_asistente->nombre)) {
            $Tipo = " (" . $datos_asistente->descripcionTipoAsistente . ")";
        }
        $auxiliar["IDAuxiliar"] = (string)$datos_asistente->idAsistente;
        $auxiliar["Nombre"] = (string)$datos_asistente->nombre . $Tipo;
        $auxiliar["Foto"] = "";
        $auxiliar["Disponible"] = "S";
        $auxiliar["TextoDisponible"] = "";
        $auxiliar["Orden"] = "";
        $auxiliar["HoraInicio"] = "";
        $auxiliar["HoraFin"] = "";
        array_push($response_auxiliar, $auxiliar);

        $auxiliar_disponible["IDClub"] = $IDClub;
        $auxiliar_disponible["Auxiliares"] = $response_auxiliar;
        array_push($response, $auxiliar_disponible);

        $respuesta["message"] = count($response_auxiliar) . 'Encontrados';
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function App_AgregarAsistenteAReserva($IDClub, $IDReservaGeneral, $IDSocio, $ListaAuxiliar)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Token = $datos_socio["TokenCountryMedellin"];
        $pToken = urlencode($Token);

        if (!empty($ListaAuxiliar)) :
            $datos_auxiliares_revisar = json_decode($ListaAuxiliar, true);
            if (count($datos_auxiliares_revisar) > 0) :
                foreach ($datos_auxiliares_revisar as $key_aux => $auxiliar_seleccionado) :
                    $IDAsistente = $auxiliar_seleccionado["IDAuxiliar"];
                endforeach;
            endif;
        endif;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://amigo.countryclub.com.co/socios/webservices/wsApp1.asmx/App_AgregarAsistenteAReserva',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "pToken=" . $pToken . "&idReserva=" . $IDReservaGeneral . "&idAsistente=" . $IDAsistente,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response_ws = curl_exec($curl);
        curl_close($curl);
        $DATOS = simplexml_load_string($response_ws);
        $resp = (array)$DATOS;



        if ($resp["estado"] == "true") {
            $respuesta["message"] = "Agregar: " . $resp["mensaje"];
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "No se agrego ningun asistente";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    // API PROSOFT
    public function Token()
    {
        $POST = '{
            "Usuario": "' . USUARIO_API_MEDELLIN . '",
            "password": "' . PASS_API_MEDELLIN . '"
        }';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);

        return $DATA['token'];
    }

    public function EstadoCuenta($Cedula)
    {
        $curl = curl_init();

        $Token = SIMWebServiceCountryMedellin::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/EstadoCuenta/' . $Cedula,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function Abono2($DATOS)
    {
        $dbo = SIMDB::get();
        $Cuota = 0;
        $Id = $DATOS['NumeroDocumento'];
        $xmlResponse = $DATOS['xmlResponse'];
        $jsonResponse = $xmlResponse;
        $jsonResponse = json_decode($jsonResponse);



        // if (isset($jsonResponse['cardAuthInfo'])) {
        //     $FormaPago = $jsonResponse['cardAuthInfo']['paymentSystem'];
        // } else {
        //     $FormaPago = $jsonResponse['paymentWay'];
        // }

        // Tomamos la forma de pago desde la base de datos - coomerceId
        $FormaPago = trim($DATOS['commerceId']);
        // $FormaPago = 9;

        $NumeroSoporte = $DATOS['NumeroTransaccion'];

        $pos = strpos($DATOS['Factura'], "/");

        if ($pos === false) :
            $POST = "[";
            $POST .= '{
                    "id":"' . $Id . '",
                    "factura":"' . $DATOS['Factura'] . '",
                    "cuota": 0,
                    "valor":"' . $DATOS['ValorPago'] . '",
                    "formaPago":"' . $FormaPago . '",
                    "numerosoporte":"' . $NumeroSoporte . '"
                }';
            $POST .= "]";
        else :
            $ArregloNumeroFactura = explode("/", $DATOS['Factura']);
            $cont = count($ArregloNumeroFactura);
            $POST = "[";
            for ($i = 0; $i < count($ArregloNumeroFactura); $i++) :
                $DatosFactura = explode("|", $ArregloNumeroFactura[$i]);
                $Factura = $DatosFactura[0];
                $Valor = $DatosFactura[1];
                $Cuota = ($DatosFactura[2] > 0) ? $DatosFactura[2] : 0;

                if (!empty($Factura)) :
                    $POST .= '{
                            "id":"' . $Id . '",
                            "factura":"' . $Factura . '",
                            "cuota":' . $Cuota . ',
                            "valor":' . $Valor . ',
                            "formaPago":"' . $FormaPago . '",
                            "numerosoporte":"' . $NumeroSoporte . '"
                        }';

                    if ($i >= 0 && $i < ($cont - 2)) :
                        $POST .= ",";
                    endif;
                endif;
            endfor;
            $POST .= "]";

        endif;

        $curl = curl_init();

        $Token = SIMWebServiceCountryMedellin::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/Abono',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json'
            ),
        ));

        $dbo->query("update PagoCredibanco set XMLREQ = '$POST' where IDPagoCredibanco = {$DATOS['IDPagoCredibanco']} and  IDClub = 15");


        $response = curl_exec($curl);
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }
    public function Factura($NumeroFactura)
    {
        $dbo = SIMDB::get();

        $FacturaConsumo = $dbo->fetchAll("FacturaConsumo", "NumeroDocumentoFactura = '" . $NumeroFactura . "' LIMIT 1", "array");
        $datos_pago = $dbo->fetchAll("PagoCredibanco", "NumeroFactura = '" . $NumeroFactura . "'", "array");
        $datos_socio = $dbo->fetchAll('Socio', "IDSocio = " . $FacturaConsumo['IDSocio'], "array");
        $Id = $datos_socio['NumeroDocumento'];
        // $FormaPago = 9;
        $Prefijo = "APP";
        $NumeroSoporte = $FacturaConsumo['NumeroTransaccion'];
        $fechaFactura = explode(
            ' ',
            $FacturaConsumo['FechaTrCr']
        );
        $fechaFactura = $fechaFactura[0];

        $xmlResponse = $datos_pago['xmlResponse'];
        $json_response = json_decode($xmlResponse);
        $jsonResponse = $xmlResponse;
        $jsonResponse = json_decode($jsonResponse, true);
        // if (isset($jsonResponse['cardAuthInfo'])) {
        //     $FormaPago = $jsonResponse['cardAuthInfo']['paymentSystem'];
        // } else {
        //     $FormaPago = $jsonResponse['paymentWay'];
        // }

        // Tomamos la forma de pago desde la base de datos - coomerceId
        $FormaPago = $datos_pago['commerceId'];


        $FacturasPagadas = explode('/', $FacturaConsumo['Detalle']);
        $detalle_consumo = explode('|', $FacturasPagadas[0]);
        $consecutivoControl = $detalle_consumo[0];
        $Valor = (!empty($datos_pago['ValorPago'])) ? (int)$datos_pago['ValorPago'] : 0;
        $Propina = (!empty($datos_pago['reserved14'])) ? (int)$datos_pago['reserved14'] : 0;

        $JsonFactura = '{
                "factura":"' . $Prefijo . $FacturaConsumo['IDFactura'] . '",
                "prefijo":"' . $Prefijo . '",
                "fechaFactura":"' . $fechaFactura . '",
                "formaPago":"' . $FormaPago . '",
                "formaPagoId":0,
                "valor":' . $Valor . ',
                "propina":' . $Propina . ',
                "consecutivoControl":' . $consecutivoControl . ',
                "detalle":[';

        foreach ($FacturasPagadas as  $FacturaPagada) {
            // 0=>consecutivoControl
            // 1=>id
            // 2=>consecutivo
            // 3=>productoId
            // 4=>nombreProducto

            $detalle_consumo = explode('|', $FacturaPagada);
            $JsonFactura .= '{
                    "id":' . $detalle_consumo[1] . ',
                    "consecutivo":' . $detalle_consumo[2] . ',
                    "productoId":"' . $detalle_consumo[3] . '"
                    },';
        }
        $JsonFactura .= '
                ]
                }';

        $POST = $JsonFactura;
        // echo '<pre>';
        // print_r($POST);
        // die();
        $curl = curl_init();

        $Token = SIMWebServiceCountryMedellin::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/Factura',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json'
            ),
        ));
        $dbo->query("update PagoCredibanco set XMLREQ = '$POST' where IDPagoCredibanco = {$datos_pago['IDPagoCredibanco']} and  IDClub = 15");

        $response = curl_exec($curl);
        // echo $response;
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }


    public function Consumos($Cedula)
    {
        $curl = curl_init();

        $Token = SIMWebServiceCountryMedellin::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/Consumos/' . $Cedula,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }
    // API PROSOFT

}
