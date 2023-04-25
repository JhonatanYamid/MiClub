<?php
class SIMWebServiceZeus
{
    public function obtener_token()
    {
        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client("http://181.48.188.77/Zeus_WS/ServiceWS.asmx?WSDL", 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
			<ns1:SessionIDToken xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
			  <ns1:Request>
				<ns1:TypeSQL></ns1:TypeSQL>
				<ns1:DynamicProperty></ns1:DynamicProperty>
				<ns1:SessionID></ns1:SessionID>
				<ns1:Action></ns1:Action>
				<ns1:Body><![CDATA[<generatortoken><security user='ClubesWS' password='Zeus1234%' />    </generatortoken>]]>
			</ns1:Body>
			  </ns1:Request>
			</ns1:SessionIDToken>";

        $result = $client->call('SessionIDToken', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        //print_r( $result);
        //echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
        //echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];

        return $result;
    }

    public function obtener_token_club($urlendpoint, $usuariuozeus, $clavezeus)
    {
        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client($urlendpoint, 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
		<ns1:SessionIDToken xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
			<ns1:Request>
			<ns1:TypeSQL></ns1:TypeSQL>
			<ns1:DynamicProperty></ns1:DynamicProperty>
			<ns1:SessionID></ns1:SessionID>
			<ns1:Action></ns1:Action>
			<ns1:Body><![CDATA[<generatortoken><security user='" . $usuariuozeus . "' password='" . $clavezeus . "' />    </generatortoken>]]>
		</ns1:Body>
			</ns1:Request>
		</ns1:SessionIDToken>";

        $result = $client->call('SessionIDToken', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        //print_r( $result);
        //echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
        //echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];
        $Token = $result["SessionIDTokenResult"]["SessionID"];

        return $Token;
    }

    public function obtener_token_club_curl($urlendpoint, $usuariuozeus, $clavezeus)
    {
        $curl = curl_init();
//<Action>SessionIDToken</Action>
       $post = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                        <Body>
                            <SessionIDToken xmlns="http://www.w3.org/XML/1998/namespace:lang">
                                <Request>
                                    <TypeSQL></TypeSQL>
                                    <DynamicProperty></DynamicProperty>
                                    <SessionID></SessionID>
                                    <Action></Action>
                                    <Body><![CDATA[<generatortoken><security user="' . $usuariuozeus . '" password="' . $clavezeus . '" />    </generatortoken>]]></Body>
                                </Request>
                            </SessionIDToken>
                        </Body>
                    </Envelope>';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlendpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //echo $response;
        //exit;

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($response), $xml_values);
        xml_parser_free($parser);

        // print_r($xml_values);

        $datos = $xml_values[4]["value"];
        $xmlf = simplexml_load_string($datos);

        return $datos;

    }

    public function estado_socio($urlendpoint, $Token, $Identificacion, $Accion, $Secuencia)
    {
        require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:Action>ConsultarEstadoSocio</ns1:Action>
	    <ns1:Body>
	      <![CDATA[
	        <Interfaz_ZeusClubes>
	  	<EstadoSocio>
			<vchidentificacion>" . $Identificacion . "</vchidentificacion> <vchaccion>" . $Accion . "</vchaccion> <vchsecuencia>" . $Secuencia . "</vchsecuencia>
		</EstadoSocio>
		</Interfaz_ZeusClubes>
	      ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }

    public function estado_socio_curl($urlendpoint, $Token, $Identificacion, $Accion, $Secuencia)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlendpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "<Envelope xmlns=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <Body>\n        <StandardCommunicationSOAP xmlns=\"http://www.w3.org/XML/1998/namespace:lang\">\n            <!-- Optional -->\n            <Request>\n                <TypeSQL></TypeSQL>\n                <DynamicProperty></DynamicProperty>\n                <SessionID>" . $Token . "</SessionID>\n                <Action>ConsultarEstadoSocio</Action>\n                <Body><![CDATA[\n\t        <Interfaz_ZeusClubes>\n\t  <EstadoSocio>\n\t<vchidentificacion>" . $Identificacion . "</vchidentificacion> <vchaccion>" . $Accion . "</vchaccion> <vchsecuencia>" . $Secuencia . "</vchsecuencia>\n\t  </EstadoSocio>\n\t</Interfaz_ZeusClubes>\n\t      ]]></Body>\n            </Request>\n        </StandardCommunicationSOAP>\n    </Body>\n</Envelope>",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/xml",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //print_r($response);exit;

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($response), $xml_values);
        xml_parser_free($parser);
        $datos = $xml_values[5]["value"];
        $xmlf = simplexml_load_string($datos);

        return $xmlf;
    }

    public function cartera_socio($urlendpoint, $Token, $Accion, $Secuencia)
    {
        require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
        <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
            <ns1:Request>
            <ns1:TypeSQL></ns1:TypeSQL>
            <ns1:DynamicProperty></ns1:DynamicProperty>
            <ns1:SessionID>" . $Token . "</ns1:SessionID>
            <ns1:Action>ConsultarCarteraSocio</ns1:Action>
            <ns1:Body>
            <![CDATA[
                <Interfaz_ZeusClubes>
        <Cartera>
                <accion>" . $Accion . "</accion> <secuencia>" . $Secuencia . "</secuencia>
        </Cartera>
        </Interfaz_ZeusClubes>
            ]]>
            </ns1:Body>
            </ns1:Request>
        </ns1:StandardCommunicationSOAP>";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;
        print_r($result);

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);
        //print_r($xml);

        return $xml;
    }

    public function envia_invitacion($Token, $IDSocio, $Documento, $Nombre, $FechaIngreso)
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client("http://181.48.188.77/Zeus_WS/ServiceWS.asmx?WSDL", 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "'", "array");

        if (empty($datos_socio["AccionPadre"])):
            $accion = $datos_socio["Accion"];
            $secuencia = "00";
        else:
            $accion = $datos_socio["AccionPadre"];
            $secuencia = substr($datos_socio["Accion"], -2);
        endif;

        //$accion = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'");
        $FechaIngreso = str_replace("-", "/", $FechaIngreso);

        $params = "
			<ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
			  <ns1:Request>
				<ns1:TypeSQL></ns1:TypeSQL>
				<ns1:DynamicProperty></ns1:DynamicProperty>
				<ns1:SessionID>" . $Token . "</ns1:SessionID>
				<ns1:Action>AutorizarInvitado</ns1:Action>
				<ns1:Body>
					<![CDATA[
           			 <zeusclubes>
						<autorizaciondeinvitados>
							<autorizacion>NUEVO</autorizacion>
							<accion>" . $accion . "</accion>
									  <secuencia>" . $secuencia . "</secuencia>
									 <identificacion>NUEVO</identificacion>
									 <nombre>" . $Nombre . "</nombre>
									 <genero>M</genero>
									 <area></area>
									 <observacion></observacion>
									 <fechainicial>" . $FechaIngreso . "</fechainicial>
									 <fechafinal>" . $FechaIngreso . "</fechafinal>
									 <usuario>sa</usuario>
						</autorizaciondeinvitados>
					</zeusclubes>
					]]>
				</ns1:Body>
			  </ns1:Request>
			</ns1:StandardCommunicationSOAP>";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        //print_r( $result);
        //echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
        //echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];
        return $result;
    }

    public function consulta_extracto($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $conexion_ws = true;

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        $dia = date('j');
        if ($dia > 13) {
            $mes = date('m', strtotime("-1 month"));
            $month_ini = new DateTime("first day of this month");
            $year_consulta2 = date('Y', strtotime("-1 month"));
        } else {
            $mes = date('m', strtotime("-2 month"));
            $month_ini = new DateTime("first day of last month");
            $year_consulta2 = date('Y', strtotime("-2 month"));
        }

        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        //$client = new nusoap_client("http://190.157.5.99/wsZeusContabilidad/ServiceWS.asmx?WSDL", 'wsdl');
        $client = new nusoap_client("http://190.157.5.99/wsZeusContabilidad/ServiceWS.asmx?WSDL", 'wsdl');
        $err = $client->getError();
        if ($err) {
            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            $cuerpo_extracto = $respuesta["message"];
            $conexion_ws = false;
            //return $respuesta;
        }

        $parametros['Request']['Body'] = '<generatortoken><security user="gunclub" password="123456" /></generatortoken>';

        $result = $client->call('SessionIDToken', $parametros, '', '');
        if ($client->fault) {

            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            $cuerpo_extracto = $respuesta["message"];
            $conexion_ws = false;
            //return $respuesta;

        } else {
            $err = $client->getError();
            if ($err) {
                $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                $cuerpo_extracto = $respuesta["message"];
                $conexion_ws = false;
                //return $respuesta;
            }
        }

        if ($conexion_ws) {
            $token = $result['SessionIDTokenResult']['SessionID'];

            $parametros2['Request']['SessionID'] = $token;
            $parametros2['Request']['DynamicProperty'] = 2000;
            $parametros2['Request']['Action'] = 'Contabilidad';
            $parametros2['Request']['Body'] = '<Parametros><Periodo>' . $year_consulta2 . '' . $mes . '</Periodo><AccionInicial>' . $datos_socio["Accion"] . '</AccionInicial><AccionFinal>' . $datos_socio["Accion"] . '</AccionFinal></Parametros>';

            $total = $client->call("StandardCommunicationSOAP", $parametros2);
            $xml = simplexml_load_string($total['StandardCommunicationSOAPResult']['Body']);

            $encabezado_extracto .= '<table class="tabla">
								  <tbody>
									<tr>
									  <td colspan="3"><img src="' . CLUB_ROOT . $datos_club["FotoLogoApp"] . '" width="136" height="132"/></td>
									</tr>
									<tr>
									  <td rowspan="2" class="modo1">' . $xml->Respuesta_accion->item->resultado->extracto->accion->titular->nombre . '<br />
																	' . $xml->Respuesta_accion->item->resultado->extracto->accion->titular->direccion . '<br />
																	' . $xml->Respuesta_accion->item->resultado->extracto->accion->titular->ciudad . '
									  </td>
									  <td colspan="2" class="modo1">N de socio: ' . $xml->Respuesta_accion->item->resultado->extracto->accion->numeroaccion . '</td>
									</tr>
									<tr>
									  <td class="modo1">Periodo que se inicia en:<br>' . $month_ini->format('Y/m/d') . '</td>
									  <td class="modo1">Fecha l&iacute;mite de pago:<br>' . $xml->Respuesta_accion->item->resultado->extracto->accion->facturacionmensual->vencimiento . '</td>
									</tr>
								  </tbody>
								</table>';

            $detalle_extracto .= '<table border="0" cellpadding="0" cellspacing="0" class="tabla"><tbody>';
            $detalle_extracto .= '<tr>';
            $detalle_extracto .= '<th>Fecha</th>';
            $detalle_extracto .= '<th>Documento</th>';
            $detalle_extracto .= '<th>Detalle</th>';
            $detalle_extracto .= '<th>Valor</th>';
            $detalle_extracto .= '</tr></tbody>';

            foreach ($xml->Respuesta_accion->item->resultado->extracto->accion->detalle as $detalle):

                $detalle_extracto .= '
	                        <tr >
	                        	<td class="modo2">' . $detalle->fecha . '</td>
	                            <td class="modo2">' . $detalle->numerofactura . '</td>
	                            <td class="modo2">' . $detalle->descripcion . '</td>
	                            <td class="modo2">$' . number_format((int) $detalle->valor) . '</td>
	                        </tr>';

            endforeach;

            $detalle_extracto .= ' </table><br>';

            $detalle_extracto .= '
							  <table class="tabla">
										<tr>
										  <td colspan="3" class="modo2">Nota: Nos permitimos informar que a partir del mes de Julio/11, el interés por mora aplicable es del 1.5% mes vencido.<br>
										  IVA ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->iva) .
            ' &nbsp; &nbsp; &nbsp;  Base IVA ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->baseiva) .
            ' &nbsp; &nbsp; &nbsp;  ICO ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->ico) .
            ' &nbsp; &nbsp; &nbsp;  Base ICO ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->baseico) .
            ' &nbsp; &nbsp; &nbsp;  Exento ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->exento) . '
										  </td>
										</tr>
									</table><br>';

            $detalle_extracto .= '<table class="tabla">';

            $detalle_extracto .= '<tr>';
            $detalle_extracto .= '<th>Saldo anterior</th>';
            $detalle_extracto .= '<th>Total compras y cargos</th>';
            $detalle_extracto .= '<th>Total pagos y creditos</th>';
            $detalle_extracto .= '<th>Total a pagar</th>';
            $detalle_extracto .= '</tr>';

            $detalle_extracto .= '<tr>
                                    <td class="modo2">$ ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->estadocta->saldoanterior) . '</td>
                                    <td class="modo2">$ ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->estadocta->totalcompras) . '</td>
                                    <td class="modo2">$ ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->estadocta->totalpagos) . '</td>
                                    <td class="modo2" align="right">$ ' . number_format((int) $xml->Respuesta_accion->item->resultado->extracto->accion->estadocta->totalpagar) . '</td>
                       			</tr>';
            $detalle_extracto .= '</table><br>';
            $detalle_extracto .= ' <br /><br />';
            $detalle_extracto .= '
							<table class="tabla">
										<tr>
										  <td class="modo2" colspan="3">Recuerde, los pagos realizados después de las 5:00 pm quedarán con la fecha del siguiente día h&aacute;bil
										  </td>
										</tr>
							</table>';

            $cuerpo_extracto = '<!doctype html>
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
											background-color: #01203C;
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
											color: #34484E;
											font-family: "Trebuchet MS", Arial;
											}
											.tabla .modo1 td {
											padding: 5px;
											border-right-width: 1px;
											border-bottom-width: 1px;
											border-right-style: solid;
											border-bottom-style: solid;
											border-bottom-color: #A4C4D0;
											text-align:right;
											color:#01203C;
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
											background-color: #fdfdf1;
											background-repeat: repeat-x;
											color: #232952;
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
											color: #232952;
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

            $cuerpo_extracto .= $encabezado_extracto . "<br>" . $detalle_extracto;
            $cuerpo_extracto .= '</body>
										</html>';

            $BotonPago = "S";

        } else {
            $BotonPago = "S";
        }

        //Verifico si existe el extracto en pdf
        //$datos_socio["Accion"]="0570";
        $ruta_pdf = $mes . "/Extracto_" . $datos_socio["Accion"] . "__.pdf";
        if (file_exists(EXTRACTOSGUN_DIR . $ruta_pdf)) {
            $cuerpo_factura_pdf = '
												<table align="center">
													<tr>
														<td>
														<a href="' . EXTRACTOSGUN_ROOT . $ruta_pdf . '">
														Descargar extracto
														<img src="' . URLROOT . 'plataform/assets/img/icpdf.jpg">
														</a>
														</td>
													</tr>
													<tr>
														<td>
															<iframe src="http://docs.google.com/gview?url=' . EXTRACTOSGUN_ROOT . $ruta_pdf . '&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
														</td>
													</tr>
												</table>';
        }

        $response = array();
        $message = "Encontrado";

        $extracto["IDClub"] = $IDClub;
        $extracto["IDSocio"] = $IDSocio;
        $extracto["IDFactura"] = "Extracto" . $IDSocio;

        $extracto["NumeroFactura"] = "Extracto" . $IDSocio;
        $extracto["Fecha"] = "Extracto" . $IDSocio;
        $extracto["ValorFactura"] = "0";
        $extracto["Almacen"] = "";

        $extracto["Nombre"] = utf8_encode($datos_socio["Nombre"]);
        $extracto["Apellido"] = utf8_encode($datos_socio["Apellido"]);
        $extracto["Correo"] = $datos_socio["Correo"];
        $extracto["Accion"] = $datos_socio["Accion"];
        $extracto["CuerpoFactura"] = $cuerpo_extracto . $cuerpo_factura_pdf;
        $extracto["ValorIva"] = (int) $xml->Respuesta_accion->item->resultado->extracto->accion->resumen->iva;
        $extracto["IDPago"] = "";
        $extracto["Txtemail"] = "";
        $extracto["Action"] = "https://www.gunclub.com.co/pago-app/";
        $extracto["TotalPagar"] = (int) $xml->Respuesta_accion->item->resultado->extracto->accion->estadocta->totalpagar;
        $extracto["BotonPago"] = $BotonPago;

        array_push($response, $extracto);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;

    }

    public function consulta_movimiento($IDClub, $IDSocio, $Mes)
    {

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client("http://190.157.5.99/wsZeusContabilidad/ServiceWS.asmx?WSDL", 'wsdl');
        $err = $client->getError();
        if ($err) {
            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $parametros['Request']['Body'] = '<generatortoken><security user="gunclub" password="123456" /></generatortoken>';

        $result = $client->call('SessionIDToken', $parametros, '', '');
        if ($client->fault) {

            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;

        } else {
            $err = $client->getError();
            if ($err) {
                $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $token = $result['SessionIDTokenResult']['SessionID'];
        $dia = date('j');
        if ($dia >= 14) {
            //$mes = date('m',strtotime("-1 month"));
            $month_ini = new DateTime("first day of this month");
        } else {
            //$mes = date('m',strtotime("-2 month"));
            $month_ini = new DateTime("first day of last month");
        }

        $parametros2['Request']['SessionID'] = $token;
        $parametros2['Request']['DynamicProperty'] = 2000;
        $parametros2['Request']['Action'] = 'Contabilidad';
        $parametros2['Request']['Body'] = '<Parametros><Periodo>' . date("Y") . $Mes . '</Periodo><AccionInicial>' . $datos_socio["Accion"] . '</AccionInicial><AccionFinal>' . $datos_socio["Accion"] . '</AccionFinal></Parametros>';

        $total = $client->call("StandardCommunicationSOAP", $parametros2);
        $xml = simplexml_load_string($total['StandardCommunicationSOAPResult']['Body']);

        /*
        $encabezado_movimiento = '<table class="tabla">
        <tbody>
        <tr>
        <td colspan="3"><img src="'.CLUB_ROOT.$datos_club["FotoLogoApp"].'" width="136" height="132"/></td>
        </tr>
        <tr>
        <td rowspan="2" class="modo1">'.$xml->Respuesta_accion->item->resultado->extracto->accion->titular->nombre.'<br />
        '.$xml->Respuesta_accion->item->resultado->extracto->accion->titular->direccion.'<br />
        '.$xml->Respuesta_accion->item->resultado->extracto->accion->titular->ciudad.'
        </td>
        <td colspan="2" class="modo1">N de socio: '.$xml->Respuesta_accion->item->resultado->extracto->accion->numeroaccion.'</td>
        </tr>
        <tr>
        <td class="modo1">Periodo que se inicia en:<br>'.$month_ini->format('Y/m/d').'</td>
        <td class="modo1">Fecha l&iacute;mite de pago:<br>'.$xml->Respuesta_accion->item->resultado->extracto->accion->facturacionmensual->vencimiento.'</td>
        </tr>
        </tbody>
        </table>';
         */

        $detalle_movimiento = '<table class="tabla"><tbody>';
        $detalle_movimiento .= '<tr>';
        $detalle_movimiento .= '<th>Fecha</th>';
        $detalle_movimiento .= '<th>Documento</th>';
        $detalle_movimiento .= '<th>Detalle</th>';
        $detalle_movimiento .= '<th>Valor</th>';
        $detalle_movimiento .= '</tr></tbody>';

        foreach ($xml->Respuesta_accion->item->resultado->extracto->accion->detalle as $detalle):

            $detalle_movimiento .= '
	                        <tr>
	                        	<td class="modo2">' . $detalle->fecha . '</td>
	                            <td class="modo2">' . $detalle->numerofactura . '</td>
	                            <td class="modo2">' . $detalle->descripcion . '</td>
	                            <td class="modo2">$' . number_format((int) $detalle->valor) . '</td>
	                        </tr>';

        endforeach;

        $detalle_movimiento .= ' </table><br>';

        $cuerpo_movimiento = '<!doctype html>
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
											background-color: #01203C;
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
											color: #34484E;
											font-family: "Trebuchet MS", Arial;
											}
											.tabla .modo1 td {
											padding: 5px;
											border-right-width: 1px;
											border-bottom-width: 1px;
											border-right-style: solid;
											border-bottom-style: solid;
											border-bottom-color: #A4C4D0;
											text-align:right;
											color:#01203C;
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
											background-color: #fdfdf1;
											background-repeat: repeat-x;
											color: #232952;
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
											color: #232952;
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

        $cuerpo_movimiento .= $encabezado_movimiento . "<br>" . $detalle_movimiento;
        $cuerpo_movimiento .= '</body>
										</html>';

        $response = array();
        $message = "Encontrado";

        $extracto["IDClub"] = $IDClub;
        $extracto["IDSocio"] = $IDSocio;
        $extracto["CuerpoFactura"] = $cuerpo_movimiento;
        array_push($response, $extracto);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;

    }

    public function consulta_movimientov2($IDClub, $IDSocio, $FechaInicio, $FechaFin)
    {

        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        require_once LIBDIR . 'nusoap/lib/nusoap.php';
        $client = new nusoap_client("http://190.157.5.99/wsZeusContabilidad/ServiceWS.asmx?WSDL", 'wsdl');
        $err = $client->getError();
        if ($err) {
            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $parametros['Request']['Body'] = '<generatortoken><security user="gunclub" password="123456" /></generatortoken>';

        $result = $client->call('SessionIDToken', $parametros, '', '');
        if ($client->fault) {

            $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;

        } else {
            $err = $client->getError();
            if ($err) {
                $respuesta["message"] = "Lo sentimos no hay conexion al servidor de extractos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $FechaIncial = str_replace("-", "/", $FechaInicio);
        $FechaFinal = str_replace("-", "/", $FechaFin);
        $token = $result['SessionIDTokenResult']['SessionID'];
        $parametros = "
			<ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
				<ns1:Request>
				<ns1:TypeSQL></ns1:TypeSQL>
				<ns1:DynamicProperty>57</ns1:DynamicProperty>
				<ns1:SessionID>" . $token . "</ns1:SessionID>
				<ns1:Action>Contabilidad</ns1:Action>
				<ns1:Body>
					<![CDATA[
						<ZeusMCASQL>
							<Consultas>
								<FechaIni>" . $FechaIncial . "</FechaIni>
								<FechaFin>" . $FechaFinal . "</FechaFin>
								<ClienteIni>" . $datos_socio["Accion"] . "</ClienteIni>
								<ClienteFin>" . $datos_socio["Accion"] . "</ClienteFin>
							</Consultas>
						</ZeusMCASQL>
					]]>
				</ns1:Body>
				</ns1:Request>
			</ns1:StandardCommunicationSOAP>";

        $total = $client->call("StandardCommunicationSOAP", $parametros);
        $xml = simplexml_load_string($total['StandardCommunicationSOAPResult']['Body']);
        $contador = 0;
        /*
        foreach($xml->Respuesta_accion->item->xml_resultado->clientes_extracto->clientes->cuentas->cuentas->facturas->factura as $detalle):
        $array_movimiento[$contador]["Fecha"]=$detalle->fecha;
        $array_movimiento[$contador]["Descripcion"]=$detalle->detalle_factura;
        $array_movimiento[$contador]["Vencimiento"]=$detalle->vencimiento;
        $array_movimiento[$contador]["Valor"]=number_format((int)$detalle->valor,0,'','.');
        $contador++;
        endforeach;
         */

        foreach ($xml->Respuesta_accion->item->xml_resultado->clientes_extracto->clientes->cuentas->cuentas->facturas->factura as $detalle):
            $Descripcion = $detalle->detalle_factura;
            $Orden = str_replace("/", "", $detalle->fecha);
            if (array_key_exists($Orden, $array_fecha)) {
                $array_fecha[$Orden]++;
            } else {
                $array_fecha[$Orden] = 1;
            }
            if (strlen($array_fecha[$Orden]) == 1) {
                $NuevoOrden = "00" . $array_fecha[$Orden];
            } elseif (strlen($array_fecha[$Orden]) == 2) {
            $NuevoOrden = "0" . $array_fecha[$Orden];
        } else {
            $NuevoOrden = $array_fecha[$Orden];
        }

        $Orden .= $NuevoOrden;

        $array_movimiento_s[$Orden]["fecha"] = $detalle->fecha;
        $array_movimiento_s[$Orden]["descripcion"] = $Descripcion;
        $array_movimiento_s[$Orden]["vencimiento"] = $detalle->vencimiento;
        $array_movimiento_s[$Orden]["valor"] = number_format((int) $detalle->valor, 0, '', '.');
        $contador++;
        endforeach;

        ksort($array_movimiento_s);

        $contador = 0;
        foreach ($array_movimiento_s as $id_detalle => $detalle):
            $array_movimiento[$contador]["Fecha"] = $detalle["fecha"];
            $array_movimiento[$contador]["Descripcion"] = $detalle["descripcion"];
            $array_movimiento[$contador]["Vencimiento"] = $detalle["vencimiento"];
            $array_movimiento[$contador]["Valor"] = $detalle["valor"];
            $contador++;
        endforeach;

        return $array_movimiento;

    }

    public function movimientos($urlendpoint, $Token, $Accion, $FechaIncio, $FechaFin)
    {
        $POST = 
        '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <StandardCommunicationSOAP xmlns="http://www.w3.org/XML/1998/namespace:lang">
                    <Request>
                        <TypeSQL></TypeSQL>
                        <DynamicProperty></DynamicProperty>
                        <SessionID>'.$Token.'</SessionID>
                        <Action>ConsultaConsumosYFacturas_Socios</Action>
                        <Body>
                            <![CDATA[
                                <Interfaz_ZeusPOS>
                                    <fechai>'.$FechaIncio.'</fechai>
                                    <fechaf>'.$FechaFin.'</fechaf>
                                    <accionsoc>'.$Accion.'</accionsoc>
                                </Interfaz_ZeusPOS>
                           ]]>
                        </Body>
                    </Request>
                </StandardCommunicationSOAP>
            </Body>
        </Envelope>';
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urlendpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$POST,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/xml'
        ),
        ));

        $response = curl_exec($curl);
        /* echo $response;
        exit; */

        curl_close($curl);

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($response), $xml_values);
        xml_parser_free($parser);
        
        $datos = $xml_values[5]["value"];
        $xmlf = simplexml_load_string($datos);

        return $xmlf;
    }
    
    
    public function consultaconsumoshuesped($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio)
    {
      $POST = 
        '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <StandardCommunicationSOAP xmlns="http://www.w3.org/XML/1998/namespace:lang">
                    <Request>
                        <TypeSQL></TypeSQL>
                        <DynamicProperty></DynamicProperty>
                        <SessionID>'.$Token.'</SessionID>
                        <Action>GetCategories</Action>
                        <Body>                            
                        <![CDATA[
                            <Interfaz_ZeusPOS>
                            <Accion>021</Accion>
                            <Secuencia>00</Secuencia>
                            </Interfaz_ZeusPOS>
                            ]]>
                        </Body>
                    </Request>
                </StandardCommunicationSOAP>
            </Body>
        </Envelope>';

          $POST;

        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $urlendpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$POST,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/xml'
        ),
        ));

        $response = curl_exec($curl);
        /* echo $response;
        exit; */

        curl_close($curl);

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($response), $xml_values);
        xml_parser_free($parser);
        
     //   $datos = $xml_values[5]["value"];
        $xmlf = $response;

        return $xmlf;
         
    }
 public function POS_ConsultarProductosAgrupacion($urlendpoint, $Token, $datos_ambiente)
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }
 $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>2_ConsultarAgrupaciones</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	     <Interfaz_ZeusPOS> 
<CodigoAmbiente>" . $datos_ambiente . "</CodigoAmbiente>
 <Fecha>20220718</Fecha>
  <Clasificacion>01</Clasificacion>
	     </Interfaz_ZeusPOS> ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";
        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }
     
     
 public function POS_ConsultarClasificaciones($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio)
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>1_ConsultarClasificaciones</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	    <Interfaz_ZeusPOS>
 <CodigoAmbiente>MR</CodigoAmbiente>
 
	    </Interfaz_ZeusPOS>
	    
	    ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }
    
    
 public function DetalleMovimientoDia($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio)
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty>true</ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>DetalleMovimientoDia</ns1:Action>
	    <ns1:Body>
	    <![CDATA[
	    <DetalleMovimientoDia>
	    <Fecha>20170802</Fecha>
	    </DetalleMovimientoDia>
	    ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }
    
    
    
    
 public function POS_ConsultarDisponibilidadProductos($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio)
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>4_ConsultarDisponibilidadProductos</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	    <Interfaz_ZeusPOS>
	    <CodigoProductos>000465</CodigoProductos> 
	    <Fecha>20220718</Fecha>
	    </Interfaz_ZeusPOS>
	    
	    ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }
     public function POS_CrearPedido($urlendpoint, $Token, $datos_pedido, $Celular, $Direccion, $cedula, $nombre_completo , $mesa, $mesero, $FormaPago1, $datos_ambiente, $Propina, $accion, $secuencia, $particular )
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }
            
          
 
        //datos desde el swsdomicilios  
        $array_respuesta1="";
        $preferencias="";
        $socios="";
        $GranTotal1=0;
        
        
        if(empty($Direccion)):
        $Direccion="Sin direccion";
        endif;
        if(empty($Celular)):
        $Celular="0";
        endif;
        //verificamos que sea particular o socio
        if($particular != "0"):
        
	      $socios="";
	      $nombre_completo=$particular;
        else: 
	      $socios.="<Accion>$accion</Accion> 
	      <Secuencia>$secuencia</Secuencia>"; 
	      
	endif;
	      
	      
	      //recorremos el pedido y sacamos los datos
    foreach ($datos_pedido as $detalle_datos) :

           
 
$porciones = explode("|", $detalle_datos["IDProducto"]);
$id_codigo= $porciones[0]; // cod
$nombre= $porciones[1]; // nombre prod
 
 
 
	        foreach ($detalle_datos["Caracteristicas"] as $detalle_preferencias) :
$porciones_pref = explode("|", $detalle_preferencias["ValoresID"]);
$id_codigo_pref= $porciones_pref[0]; // cod pref
$nombre_pref= $porciones_pref[1]; // descripcion pref
$tipo_pref= $porciones_pref[2]; // tipo pref
$valor_pref= $porciones_pref[3]; // valor pref
if($valor_pref == ""):
$valor_pref=0;
endif; 
	$preferencias.="<Preferencia>
	        <Codigo>$id_codigo_pref</Codigo>
	        <Tipo>$tipo_pref</Tipo>
	        <ValorUnitario>$valor_pref</ValorUnitario>
	        <Cantidad>1</Cantidad>
	        </Preferencia>";
	        endforeach;
	        
	        
        $array_respuesta1.="<Producto>
	        <Codigo>$id_codigo</Codigo>
	        <ValorUnitario>$detalle_datos[ValorUnitario]</ValorUnitario>
	        <Cantidad>$detalle_datos[Cantidad]</Cantidad>
	        <Preferencias> 
                ". (string)$preferencias . "
	        </Preferencias>
	        </Producto>"; 
	        
	         
	        
	              $GranTotal1 += (int) $detalle_datos["Cantidad"] * $detalle_datos["ValorUnitario"]; 
	              $GranTotal1 += $valor_pref * $detalle_datos["Cantidad"]; 
                  endforeach;
      
 
 
  	     
	         
          //VALIDAMOS QUE TENGA O NO LA PROPINA
                if($Propina=="S"):
                  $propina= (float) ($GranTotal1 * 0.1); // valor propina del 10% 
                  
                  //le sumo la propina al valor del pedido
                 $GranTotal1= $GranTotal1 + $propina; 
                else:
                  $propina=0;
                  $GranTotal1= $GranTotal1 + $propina;
                endif;          
    
              
        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>5_CreacionPedidos</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	 <Interfaz_ZeusPOS>
	    <Codcaja>" . $datos_ambiente . "</Codcaja> 
	    <Codmesa>" . $mesa . "</Codmesa> 
	    <Codmesero>" . $mesero . "</Codmesero>
	    <Titular>" . $nombre_completo . "</Titular> 
	    <Cedula>" . $cedula . "</Cedula> 
	    <Factura>N</Factura> 
	     <Telefono>" . $Celular . "</Telefono>
	      <Direccion>" . $Direccion . "</Direccion> 
	          ". (string)$socios . "
	      <Propina>" . $propina . "</Propina> 

	   <Productos>
               ". (string)$array_respuesta1 . "
	     </Productos> 
	             <Pagos> 
	               <Pago> 
	                <ValorPago>" . $GranTotal1 . "</ValorPago>
	                 <FormaPago>" . $FormaPago1 . "</FormaPago>
	                 </Pago> 
	             </Pagos> 
 
	                 <Descuento>0</Descuento>
	</Interfaz_ZeusPOS>
	                 
	    ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";      
	   
	   /*
	     $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>5_CreacionPedidos</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	 <Interfaz_ZeusPOS>
	    <Codcaja>MR</Codcaja> 
	    <Codmesa>002</Codmesa> 
	    <Codmesero>ACS659</Codmesero>
	    <Titular>EIMAR GARCIA ZUÑIGA</Titular> 
	    <Cedula>76270387</Cedula>  
	     <Telefono>3128420188</Telefono>
	      <Direccion>CALLE 12 CRA 32</Direccion> 
	       <Accion>76270387</Accion> 
	      <Secuencia>00</Secuencia>
	      <Propina>2000</Propina> 
	   <Productos> 
               <Producto> 
	        <Codigo>000010</Codigo> 
	        <ValorUnitario>22000</ValorUnitario>
	        <Cantidad>1</Cantidad>
	        <Preferencias></Preferencias>
	        </Producto> 
	     </Productos> 
	             <Pagos> 
	               <Pago> 
	                <ValorPago>22000</ValorPago>
	                 <FormaPago>01</FormaPago>
	                 </Pago> 
	             </Pagos> 
 
	                 <Descuento>0</Descuento>
	</Interfaz_ZeusPOS>
	                 
	    ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   "; */
	   

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);
 
        return $xml;
    }
    
 public function POS_ConsultarProductos($urlendpoint, $Token, $datos_ambiente, $codigo_agrupacion)
    {
     
 require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }
        
        
    

        $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>3_ConsultarProductos</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	     <Interfaz_ZeusPOS> 
	     <CodigoAmbiente>" . $datos_ambiente . "</CodigoAmbiente>
	      <Fecha>20220718</Fecha>
	      <Agrupacion>" . $codigo_agrupacion . "</Agrupacion> 
	     </Interfaz_ZeusPOS> ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        //echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);

        return $xml;
    }
    
    function GetPartnerAccounts($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio)
    {
     
		
        require_once LIBDIR . 'nusoap/lib/nusoap.php';

        $client = new nusoap_client($urlendpoint, 'wsdl', false, false, false, false, 2000000, 2000000);
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

		 $params = "
	  <ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
	    <ns1:Request>
	    <ns1:TypeSQL></ns1:TypeSQL>
	    <ns1:DynamicProperty></ns1:DynamicProperty>
	    <ns1:SessionID>" . $Token . "</ns1:SessionID>
	    <ns1:User>userpos2</ns1:User>
            <ns1:Password>userpos2022</ns1:Password>
	    <ns1:Action>GetPartnerAccounts</ns1:Action>
	    <ns1:Body>
	     <![CDATA[ 
	     <Interfaz_ZeusPOS>
	     <Accion>021</Accion>
	     <Secuencia>00</Secuencia>
	     </Interfaz_ZeusPOS>
	     
	     ]]>
	    </ns1:Body>
	    </ns1:Request>
	  </ns1:StandardCommunicationSOAP>
	   ";

        $result = $client->call('StandardCommunicationSOAP', $params, '', '');
        echo $client->response;

        if ($client->fault) {
            echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();
            if ($err) {
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            }
        }

        $xml = simplexml_load_string($result['StandardCommunicationSOAPResult']['Body']);
 

        return $xml;
    }
    // Create Contact class 

} //end class
