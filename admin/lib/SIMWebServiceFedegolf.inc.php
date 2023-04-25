<?php
class SIMWebServiceFedegolf
{

    public function get_configuracion_fedegolf($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $responsePublicidad = array();

        $sql = "SELECT LabelNombreHandicapTexto,LabelCodigoHandicapTexto,LabelCodigoHandicap,LabelNombreHandicap,AyudaHandicap,Publicidad,TiempoPublicidad,TiempoPublicidadHeader
              FROM Club
              WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["LabelNombreHandicapTexto"] = $r["LabelNombreHandicapTexto"];
                $configuracion["LabelCodigoHandicapTexto"] = $r["LabelCodigoHandicapTexto"];
                $configuracion["LabelCodigoHandicap"] = $r["LabelCodigoHandicap"];
                $configuracion["LabelNombreHandicap"] = $r["LabelNombreHandicap"];
                $configuracion["AyudaHandicap"] = $r["AyudaHandicap"];

                $sql_otros = "SELECT BotonVerScores,BotonBuscarOtrosHandicap,LabelSeleccioneClub,LabelSeleccioneCampo,LabelSeleccioneMarca,BotonCalcularHandicap
                    FROM ConfiguracionClub
                    WHERE IDClub = '" . $IDClub . "' ";
                $qry_otros = $dbo->query($sql_otros);
                $r_otros = $dbo->fetchArray($qry_otros);

                $configuracion["BotonVerScores"] = $r_otros["BotonVerScores"];
                $configuracion["BotonBuscarOtrosHandicap"] = $r_otros["BotonBuscarOtrosHandicap"];
                $configuracion["LabelSeleccioneClub"] = $r_otros["LabelSeleccioneClub"];
                $configuracion["LabelSeleccioneCampo"] = $r_otros["LabelSeleccioneCampo"];
                $configuracion["LabelSeleccioneMarca"] = $r_otros["LabelSeleccioneMarca"];
                $configuracion["BotonCalcularHandicap"] = $r_otros["BotonCalcularHandicap"];

                //publicidad handicap
                $publicidad["Publicidad"] = $r["Publicidad"];
                $publicidad["PublicidadTiempo"] = $r["TiempoPublicidad"];
                $publicidad["TiempoPublicidadHeader"] = $r["TiempoPublicidadHeader"];

                //$sql_publicidad = "SELECT P.IDPublicidad FROM Publicidad P,PublicidadModulo PM WHERE  Publicar = 'S'  AND ( FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' AND  P.IDPublicidad= PM.IDPublicidad AND PM.IDModulo='30' AND PM.Activo='S' ORDER BY Orden";
                //$sql_publicidad = "SELECT * FROM Publicidad P,PublicidadModulo PM WHERE  Publicar = 'S'  AND ( FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' AND  P.IDPublicidad= PM.IDPublicidad AND PM.IDModulo='30' AND PM.Activo='S' ORDER BY Orden";


                $sql_publicidadDatos = "SELECT * FROM Publicidad P,PublicidadModulo PM WHERE  Publicar = 'S'  AND ( FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ) and IDClub = '" . $IDClub . "' AND  P.IDPublicidad= PM.IDPublicidad AND PM.IDModulo='30' AND PM.Activo='S' ";

                $qry_publicidadDatos = $dbo->query($sql_publicidadDatos);
                while ($r_publicidad = $dbo->fetchArray($qry_publicidadDatos)) {


                    $publicidades["IDPublicidad"] = $r_publicidad["IDPublicidad"];
                    $publicidades["Nombre"] = $r_publicidad["Nombre"];
                    $publicidades["Url"] = $r_publicidad["Url"];
                    $publicidades["VentanaExterna"] = $r_publicidad["VentanaExterna"];

                    if (!empty($r_publicidad["Foto1"])) :
                        $foto = PUBLICIDAD_ROOT . $r_publicidad["Foto1"];
                    else :
                        $foto = "";
                    endif;
                    $publicidades["Foto1"] = $foto;


                    array_push($responsePublicidad, $publicidades);
                    //cambiar el array aleatorio para mostrar las imagenes
                    shuffle($responsePublicidad);
                }
                $publicidad["Publicidades"] = $responsePublicidad;


                $configuracion["PublicidadHandicap"] = $publicidad;

                //fin publicidad
                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "Configuracion Fedegolf no está activo";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_clubes()
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'RetornaClubes',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));
            $response_ws = curl_exec($curl);
            curl_close($curl);
            //echo $response;
            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->estado == "Activo") {
                            $datos["IDClubFedegolf"] = $datos_resp->codClub;
                            $datos["Nombre"] = $datos_resp->NombreClub;
                            array_push($response, $datos);
                        }
                    }
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_clubes_ant()
    {
        $response = array();
        $url = "http://208.83.253.107:9098/api/Usuarios/GetClubes";
        $contenido_resultado = file_get_contents($url);
        $resultado = json_decode($contenido_resultado);
        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                $datos["IDClubFedegolf"] = $datos_service->Id;
                $datos["Nombre"] = $datos_service->Nombre;
                array_push($response, $datos);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_canchas($IDClubFedegolf)
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'RetornaCanchas',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			                "req": {
			                    "codClub" : "' . $IDClubFedegolf . '"
			                }
			            }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        $datos["IDCancha"] = $datos_resp->codCancha;
                        $datos["Nombre"] = $datos_resp->NombreCancha;
                        array_push($response, $datos);
                    }
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C2. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C2. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_canchas_ant($IDClubFedegolf)
    {
        $response = array();
        $url = "http://208.83.253.107:9098/api/Usuarios/GetCanchas?idClub=" . $IDClubFedegolf;
        $contenido_resultado = file_get_contents($url);
        $resultado = json_decode($contenido_resultado);
        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                $datos["IDCancha"] = $datos_service->Id;
                $datos["Nombre"] = $datos_service->Nombre;
                array_push($response, $datos);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_marcas($IDClubFedegolf, $IDCancha, $Codigo)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Codigo . "' and IDClub = 17 ", "array");
        if ($datos_socio["Genero"] == "M") {
            $Genero = "Masculino";
        } elseif ($datos_socio["Genero"] == "F") {
            $Genero = "Femenino";
        } else {
            $Genero = "T";
        }

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'RetornaMarcas',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
				"req": {
						"codClub" : "' . $IDClubFedegolf . '",
						"codCancha" : "' . $IDCancha . '"
				}
			}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($Genero == "T" || $Genero == $datos_resp->Genero) {
                            $datos["IDMarca"] = $datos_resp->codMarca;
                            $datos["Nombre"] = $datos_resp->nombreMarca;
                            $datos["PatronCurva"] = "";
                            array_push($response, $datos);
                        }
                    }
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C2. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C2. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_marcas_ant($IDClubFedegolf, $IDCancha)
    {
        $response = array();
        $url = "http://208.83.253.107:9098/api/Usuarios/GetMarcas?idClub=" . $IDClubFedegolf . "&idCancha=" . $IDCancha;
        $contenido_resultado = file_get_contents($url);
        $resultado = json_decode($contenido_resultado);
        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                $datos["IDMarca"] = $datos_service->Id;
                $datos["Nombre"] = $datos_service->Nombre;
                $datos["PatronCurva"] = $datos_service->PatronCurva;
                array_push($response, $datos);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_handicap($IDClubFedegolf, $IDCancha, $IDMarca, $CodigoJugador, $IDSocio)
    {
        //$datos_socio =$dbo->fetchAll("Socio"," IDSocio = '".$IDSocio."' ","array");
        $response = array();
        $Token = self::Token();
        $Tipo = "CD";

        $Handicap = $IDClubFedegolf . " - " . $IDCancha . " - " . $IDMarca . " - " . $CodigoJugador . " - " . $Tipo;

        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'CalcularHandicap',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "cod_Club" : "' . $IDClubFedegolf . '",
			        "cod_Cancha" : "' . $IDCancha . '",
			        "cod_marca":"' . $IDMarca . '",
			        "codJugador" : "' . $CodigoJugador . '",
			        "tipo" : "' . $Tipo . '"
			    }
			}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);
            //echo $response_ws;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    if ($resultado->Respuesta->Mensaje == "OK") {
                        $Handicap = $resultado->Handicap;
                        $Nombre = $resultado->Nombre;
                        $Indice = $resultado->Indice;
                        $Codigo = $resultado->Codigo;
                    } else {
                        $Handicap = 0;
                    }
                } //end while

                $Handicap = round($Handicap);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $Handicap;
            } else {
                $respuesta["message"] = "C6. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C6. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_handicap_ant($Curva, $Indice)
    {
        $response = array();
        $url = "http://208.83.253.107:9098/api/Usuarios/GetHandicap?curva=" . $Curva . "&indice=" . $Indice;
        $contenido_resultado = file_get_contents($url);
        $resultado = (string) json_decode($contenido_resultado);

        //if( !empty( $resultado ) || $resultado != "")    {
        if ($resultado != "") {
            $respuesta["message"] = "Respuesta handicap.";
            $respuesta["success"] = true;
            $respuesta["response"] = $resultado;
        } else {
            $respuesta["message"] = "No se encontraron registros ";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_hoyos($IDClubFedegolf, $IDCancha, $IDMarca)
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'RetornaHoyos',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			    "codClub" : "' . $IDClubFedegolf . '",
			    "codCancha" : "' . $IDCancha . '",
			    "codmarca":"' . $IDMarca . '"
			    }
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        $datos["nombreMarca"] = $datos_resp->nombreMarca;
                        $datos["Id"] = $datos_resp->currentMark->Id;
                        $datos["Id_Interno_Marca__c"] = $datos_resp->currentMark->Id_Interno_Marca__c;
                        $datos["Name"] = $datos_resp->Name;
                        $datos["Par_de_Cancha_Hoyo_1__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_1__c;
                        $datos["Par_de_Cancha_Hoyo_2__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_2__c;
                        $datos["Par_de_Cancha_Hoyo_3__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_3__c;
                        $datos["Par_de_Cancha_Hoyo_4__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_4__c;
                        $datos["Par_de_Cancha_Hoyo_5__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_5__c;
                        $datos["Par_de_Cancha_Hoyo_6__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_6__c;
                        $datos["Par_de_Cancha_Hoyo_7__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_7__c;
                        $datos["Par_de_Cancha_Hoyo_8__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_8__c;
                        $datos["Par_de_Cancha_Hoyo_9__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_9__c;
                        $datos["Par_de_Cancha_Hoyo_10__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_10__c;
                        $datos["Par_de_Cancha_Hoyo_11__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_11__c;
                        $datos["Par_de_Cancha_Hoyo_12__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_12__c;
                        $datos["Par_de_Cancha_Hoyo_13__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_13__c;
                        $datos["Par_de_Cancha_Hoyo_14__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_14__c;
                        $datos["Par_de_Cancha_Hoyo_15__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_15__c;
                        $datos["Par_de_Cancha_Hoyo_16__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_16__c;
                        $datos["Par_de_Cancha_Hoyo_17__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_17__c;
                        $datos["Par_de_Cancha_Hoyo_18__c"] = $datos_resp->currentMark->Par_de_Cancha_Hoyo_18__c;
                        $datos["Distancia_Hoyo_1__c"] = $datos_resp->currentMark->Distancia_Hoyo_1__c;
                        $datos["Distancia_Hoyo_2__c"] = $datos_resp->currentMark->Distancia_Hoyo_2__c;
                        $datos["Distancia_Hoyo_3__c"] = $datos_resp->currentMark->Distancia_Hoyo_3__c;
                        $datos["Distancia_Hoyo_4__c"] = $datos_resp->currentMark->Distancia_Hoyo_4__c;
                        $datos["Distancia_Hoyo_5__c"] = $datos_resp->currentMark->Distancia_Hoyo_5__c;
                        $datos["Distancia_Hoyo_6__c"] = $datos_resp->currentMark->Distancia_Hoyo_6__c;
                        $datos["Distancia_Hoyo_7__c"] = $datos_resp->currentMark->Distancia_Hoyo_7__c;
                        $datos["Distancia_Hoyo_8__c"] = $datos_resp->currentMark->Distancia_Hoyo_8__c;
                        $datos["Distancia_Hoyo_9__c"] = $datos_resp->currentMark->Distancia_Hoyo_9__c;
                        $datos["Distancia_Hoyo_10__c"] = $datos_resp->currentMark->Distancia_Hoyo_10__c;
                        $datos["Distancia_Hoyo_11__c"] = $datos_resp->currentMark->Distancia_Hoyo_11__c;
                        $datos["Distancia_Hoyo_12__c"] = $datos_resp->currentMark->Distancia_Hoyo_12__c;
                        $datos["Distancia_Hoyo_13__c"] = $datos_resp->currentMark->Distancia_Hoyo_13__c;
                        $datos["Distancia_Hoyo_14__c"] = $datos_resp->currentMark->Distancia_Hoyo_14__c;
                        $datos["Distancia_Hoyo_15__c"] = $datos_resp->currentMark->Distancia_Hoyo_15__c;
                        $datos["Distancia_Hoyo_16__c"] = $datos_resp->currentMark->Distancia_Hoyo_16__c;
                        $datos["Distancia_Hoyo_17__c"] = $datos_resp->currentMark->Distancia_Hoyo_17__c;
                        $datos["Distancia_Hoyo_18__c"] = $datos_resp->currentMark->Distancia_Hoyo_18__c;
                        $datos["Cancha__c"] = $datos_resp->currentMark->Cancha__c;

                        array_push($response, $datos);
                    }
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C6. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C6. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_autenticacion($Email, $Pwd)
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'ServicioLogueo',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "username" : "' . $Email . '",
			        "Password":"' . $Pwd . '"
			    }
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);
            //echo $response;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {
                            $datos["Id"] = $datos_resp->federado->Id;
                            $datos["Estado__c"] = $datos_resp->federado->Id;
                            $datos["CodigoJugador__c"] = $datos_resp->federado->CodigoJugador__c;
                            $datos["Numero_de_documento__c"] = $datos_resp->federado->Numero_de_documento__c;
                            $datos["FirstName"] = $datos_resp->federado->FirstName;
                            $datos["LastName"] = $datos_resp->federado->LastName;
                            $datos["Tipo_de_documento__c"] = $datos_resp->federado->Tipo_de_documento__c;
                            $datos["Genero__c"] = $datos_resp->federado->Genero__c;
                            $datos["AccountId"] = $datos_resp->federado->AccountId;
                            $datos["Codigo_de_club__c"] = $datos_resp->federado->Account->Codigo_de_club__c;
                            $datos["Id2"] = $datos_resp->federado->Account->Id;
                            array_push($response, $datos);
                            $respuesta["message"] = $message;
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {
                            $respuesta["message"] = $datos_resp->Respuesta->Mensaje;
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        }
                    }
                } //end while
            } else {
                $respuesta["message"] = "L1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "L1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_autenticacion_ant($Email, $Pwd)
    {
        $response = array();
        $url = "http://208.83.253.107:9098/swagger/ui/indexUsuarios/Authentication?email=" . $Email . "&pwd=" . $Pwd;
        $contenido_resultado = file_get_contents($url);
        $resultado = json_decode($contenido_resultado);
        if (!empty($resultado)) {
            $respuesta["message"] = "Respuesta handicap";
            $respuesta["success"] = true;
            $respuesta["response"] = $resultado;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_games_jugador($IDClub, $IDSocio = "", $Codigo = "", $DetalleScore = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio)) :
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  ", "array");
            $codigo_fedegolf = $datos_socio["NumeroDocumento"];
        elseif (!empty($Codigo)) :
            $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Codigo . "'  and IDClub = '" . $IDClub . "' ", "array");
            $codigo_fedegolf = $datos_socio["NumeroDocumento"];
        endif;

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            //Si no encuentra el codigo es por que es de alguien que no a ingresado al app
            if (empty($codigo_fedegolf)) {
                $codigo_fedegolf = self::busca_codigo($Codigo, $Token);
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'HistorialJuegoApp',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "Documento" : "' . $codigo_fedegolf . '"
			    }
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);
            //echo $response_ws;

            $resultado = json_decode($response_ws);

            //Primero Consulto los diferenciales mas bajos para marcarlos como menores en otro color
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        $array_diferencial[] = (float) $datos_resp->Scores->Differencial_Final_Ajustado__c;
                    }
                }
            }
            asort($array_diferencial);
            //Escojo los 10 mas bajos
            $array_diferencial = array_slice($array_diferencial, 0, 8);

            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        $datos_juegos[substr($datos_resp->Scores->Tiempo_de_Juego__c, 0, 10) . rand(0, 100)] = $datos_resp;
                    }
                }
            }
            krsort($datos_juegos);

            if (count($resultado) > 0) {
                foreach ($datos_juegos as $datos_resp) {
                    $juegosjugador["IDClub"] = $IDClub;
                    $juegosjugador["Id"] = $datos_resp->Scores->Id;

                    $datos_club_marca = explode("/", $datos_resp->Scores->Club_Campo_Marca__c);

                    $juegosjugador["Club"] = $datos_club_marca[0];
                    $juegosjugador["Cancha"] = $datos_club_marca[1];
                    $juegosjugador["Fecha"] = substr($datos_resp->Scores->Tiempo_de_Juego__c, 0, 10);
                    $juegosjugador["Marca"] = $datos_club_marca[2];

                    $array_score_ajustado = explode("/", $datos_resp->Scores->Scores_Gross_Ajust__c);
                    $juegosjugador["ScoreAjustado"] = $array_score_ajustado[0] . "/ \r\n" . $array_score_ajustado[1];
                    $juegosjugador["Diferencial"] = number_format($datos_resp->Scores->Differencial_Final_Ajustado__c, 1);
                    //Verifico si es de los diferenciales bajos
                    if (in_array($datos_resp->Scores->Differencial_Final_Ajustado__c, $array_diferencial) && $contador_juegos <= 8) :
                        $diferencial_bajo = "S";
                        $color_diferencial = "#64D547";
                        $contador_juegos++;
                        //quito el elemento del array del orden
                        if (($key = array_search($datos_resp->Scores->Differencial_Final_Ajustado__c, $array_diferencial)) !== false) :
                            unset($array_diferencial[$key]);
                        endif;
                    else :
                        $diferencial_bajo = "N";
                        $color_diferencial = "#000000";
                    endif;
                    $juegosjugador["PatronCurva"] = $datos_resp->Scores->Patrones__c;
                    $juegosjugador["PatronCampo"] = $datos_resp->Scores->Patrones__c;
                    $juegosjugador["Score"] = "Ver detalle";
                    $juegosjugador["ScoreColor"] = $color_diferencial;
                    $juegosjugador["ScoreValido"] = $diferencial_bajo;
                    $juegosjugador["DetalleScore"] = $datos_resp->Scores->Id;

                    array_push($response, $juegosjugador);
                }
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C6. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C6. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_games_jugador_ant($IDClub, $IDSocio = "", $Codigo = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if ((!empty($IDSocio) || !empty($Codigo)) && !empty($IDClub)) {

            if (!empty($IDSocio)) :
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                $codigo_fedegolf = $datos_socio["Email"];
            elseif (!empty($Codigo)) :
                $codigo_fedegolf = $Codigo;
            endif;

            $url = "http://208.83.253.107:9098/api/Usuarios/GetGames?code=" . $codigo_fedegolf;
            $contenido_resultado = file_get_contents($url);
            $resultado = json_decode($contenido_resultado);
            if (count($resultado) > 0) {
                $message = count($resultado) . " Encontrados";

                //Primero Consulto los diferenciales mas bajos para marcarlos como menores en otro color
                foreach ($resultado as $datos_service) {
                    $array_diferencial[] = (float) $datos_service->Diferencial;
                }
                asort($array_diferencial);
                //Escojo los 10 mas bajos
                $array_diferencial = array_slice($array_diferencial, 0, 10);

                $contador_juegos = 1;
                foreach ($resultado as $datos_service) {
                    $juegosjugador["IDClub"] = $IDClub;
                    $juegosjugador["Id"] = $datos_service->Id;
                    $juegosjugador["Club"] = $datos_service->Club;
                    $juegosjugador["Cancha"] = $datos_service->Cancha;
                    $juegosjugador["Fecha"] = substr($datos_service->FechaJuego, 0, 10);
                    $juegosjugador["Marca"] = $datos_service->Marca;
                    $juegosjugador["ScoreAjustado"] = $datos_service->ScoreAjustado;
                    $juegosjugador["Diferencial"] = $datos_service->Diferencial;
                    //Verifico si es de los diferenciales bajos
                    if (in_array($datos_service->Diferencial, $array_diferencial) && $contador_juegos <= 10) :
                        $diferencial_bajo = "S";
                        $color_diferencial = "#64D547";
                        $contador_juegos++;
                        //quito el elemento del array del orden
                        if (($key = array_search($datos_service->Diferencial, $array_diferencial)) !== false) :
                            unset($array_diferencial[$key]);
                        endif;
                    else :
                        $diferencial_bajo = "N";
                        $color_diferencial = "#000000";
                    endif;
                    $juegosjugador["PatronCurva"] = $datos_service->PatronCurva;
                    $juegosjugador["PatronCampo"] = $datos_service->PatronCampo;
                    $juegosjugador["Score"] = $DetalleScore;
                    $juegosjugador["ScoreColor"] = $color_diferencial;
                    $juegosjugador["ScoreValido"] = $diferencial_bajo;

                    array_push($response, $juegosjugador);
                }

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "F2. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    } //end function

    public function get_games($IDClub, $IDSocio, $Score, $Codigo = "", $DetalleScore = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if ((!empty($IDSocio) || !empty($Codigo)) && !empty($IDClub) && !empty($DetalleScore)) {

            if (!empty($IDSocio)) :
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ", "array");
                $codigo_fedegolf = $datos_socio["NumeroDocumento"];
            elseif (!empty($Codigo)) :
                $codigo_fedegolf = $Codigo;
                $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Codigo . "' and IDClub = '" . $IDClub . "' ", "array");
                if (empty($datos_socio["Nombre"])) :
                    //Consulto el nombre ya que el codigo puede ser que no haya ingresado al sisitema y no tengo los datos
                    if (!empty($datos_socio["NumeroDocumento"])) {
                        $DatoBuscar = $datos_socio["NumeroDocumento"];
                    } else {
                        $DatoBuscar = $Codigo;
                    }

                    $resultado_consulta = self::get_usuario_codigo($DatoBuscar);
                    $datos_socio["Nombre"] = $resultado_consulta["response"][0]["nombre"];
                    $datos_socio["Apellido"] = $resultado_consulta["response"][0]["apellido"];
                endif;
            endif;

            $response = array();
            $Token = self::Token();
            if ($Token != "errortoken") {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => URL_WS_FEDEGOLF . 'TarjetaJuegoRest',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
				    "req": {
				        "IdTarjeta" : "' . $DetalleScore . '",
				        "EsRonda18" : "1"
				    }
				}',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $Token,
                        'Content-Type: application/json',
                        'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                    ),
                ));

                $response_ws = curl_exec($curl);

                curl_close($curl);
                //echo $response_ws;
                //exit;

                $resultado = json_decode($response_ws);
                if (count($resultado) > 0) {
                    foreach ($resultado as $datos_service) {
                        foreach ($datos_service as $datos_resp) {

                            //Encabezado Consulta
                            $cabeza_game = '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
								<tbody>
								<tr class="modo2">
									<td>' . utf8_encode($datos_socio["Nombre"] . ' ' . $datos_socio["Apellido"]) . '
									</td>
								</tr>
								</tbody>
							</table>';

                            $cabeza_game .= '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
								<tr>
									<td>Club - Campo - marca: </td><td>' . $datos_resp->unaTarjeta->Club_Campo_Marca__c . '<td>
								<tr>
								<tr>
									<td>Scores Gross Ajustado: </td><td>' . $datos_resp->unaTarjeta->Scores_Gross_Ajust__c . '<td>
								<tr>
								<tr>
									<td>Diferencial Final: </td><td>' . $datos_resp->unaTarjeta->Diferencial_Final__c . '<td>
								<tr>
								<tr>
									<td>Diferencial: </td><td>' . $datos_resp->unaTarjeta->Differencial__c . '<td>
								<tr>
								<tr>
									<td>Patrones: </td><td>' . $datos_resp->unaTarjeta->Patrones__c . '<td>
								<tr>
								<tr>
									<td>Tipo de Juego: </td><td>' . $datos_resp->unaTarjeta->Tipo_de_Juego__c . '<td>
								<tr>
								<tr>
									<td>Tiempo de Juego: </td><td>' . $datos_resp->unaTarjeta->Tiempo_de_Juego__c . '<td>
								<tr>
								<tr>
									<td>Total de ajuste: </td><td>' . $datos_resp->unaTarjeta->Total_de_Ajustes__c . '<td>
								<tr>
								<tr>
									<td>Total Par: </td><td>' . $datos_resp->unaTarjeta->Total_Par__c . '<td>
								<tr>
								</tr>

							</table>';

                            //Consulto el detalle de la factura
                            $detalle_game = '
							<table border="0" cellpadding="0" cellspacing="0" class="tabla">
								<tbody>
							';

                            $message = count($resultado_tarjeta) . " Encontrados";
                            $detalle_tarjeta .= '<table border="0" cellpadding="0" cellspacing="0" class="tabla"><tr bgcolor="#F3F3F3">';
                            $detalle_tarjeta .= '<td>Hoyo</td>';
                            $detalle_tarjeta .= '<td>Score</td>';
                            $detalle_tarjeta .= '<td>Ajus.</td>';
                            $detalle_tarjeta .= '<td>Par</td>';
                            $detalle_tarjeta .= '<td>Ventaja</td>';
                            $detalle_tarjeta .= '<td>Dis.</td>';
                            $detalle_tarjeta .= '</tr>';

                            //Hoyo 1- 9
                            for ($contador = 1; $contador <= 9; $contador++) :

                                $NombreCampoScore = "Score_" . $contador . "__c";
                                $NombreCampoAjuste = "Ajuste_" . $contador . "__c";
                                $NombreCampoPar = "Par_" . $contador . "__c";
                                $NombreCampoVentaja = "Ventaja_Hoyo_" . $contador . "__c";
                                $detalle_tarjeta .= '<tr>';
                                $detalle_tarjeta .= '<td>' . $contador . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoScore . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoAjuste . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoPar . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoVentaja . '</td>';
                                $detalle_tarjeta .= '<td></td>';
                                $detalle_tarjeta .= '</tr>';
                            endfor;

                            //Ida
                            $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                            $detalle_tarjeta .= '<td>Ida</td>';
                            $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->Total_Vuelta_1__c . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"]["Ida"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Par"]["Ida"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"]["Ida"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Distancia"]["Ida"] . '</td>';
                            $detalle_tarjeta .= '</tr>';
                            //Hoyo 10 -18
                            for ($contador = 10; $contador <= 18; $contador++) :
                                $NombreCampoScore = "Score_" . $contador . "__c";
                                $NombreCampoAjuste = "Ajuste_" . $contador . "__c";
                                $NombreCampoPar = "Par_" . $contador . "__c";
                                $NombreCampoVentaja = "Ventaja_Hoyo_" . $contador . "__c";
                                $detalle_tarjeta .= '<tr>';
                                $detalle_tarjeta .= '<td>' . $contador . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoScore . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoAjuste . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoPar . '</td>';
                                $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->$NombreCampoVentaja . '</td>';
                                $detalle_tarjeta .= '<td></td>';
                                $detalle_tarjeta .= '</tr>';
                            endfor;
                            //Vuelta
                            $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                            $detalle_tarjeta .= '<td>Vuelta</td>';
                            $detalle_tarjeta .= '<td>' . $datos_resp->unaTarjeta->Total_Vuelta_2__c . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"]["Vuelta"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Par"]["Vuelta"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"]["Vuelta"] . '</td>';
                            $detalle_tarjeta .= '<td>' . $array_datos["Distancia"]["Vuelta"] . '</td>';
                            $detalle_tarjeta .= '</tr>';

                            $TotalIdaVuelta = (int) $datos_resp->unaTarjeta->Total_Vuelta_1__c + (int) $datos_resp->unaTarjeta->Total_Vuelta_2__c;
                            $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                            $detalle_tarjeta .= '<td><b>TOTAL</b></td>';
                            $detalle_tarjeta .= '<td>' . $TotalIdaVuelta . '</td>';
                            $detalle_tarjeta .= '<td>&nbsp;</td>';
                            $detalle_tarjeta .= '<td>&nbsp;</td>';
                            $detalle_tarjeta .= '<td>&nbsp;</td>';
                            $detalle_tarjeta .= '<td>&nbsp;</td>';
                            $detalle_tarjeta .= '</tr>';

                            //Totales
                            /*
                            $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                            $detalle_tarjeta .= '<td>Total</td>';
                            $detalle_tarjeta .= '<td>'.$array_datos["Score"]["Total"].'</td>';
                            $detalle_tarjeta .= '<td>'.$array_datos["Ajustado"]["Total"].'</td>';
                            $detalle_tarjeta .= '<td>'.$array_datos["Par"]["Total"].'</td>';
                            $detalle_tarjeta .= '<td>'.$array_datos["Ventaja"]["Total"].'</td>';
                            $detalle_tarjeta .= '<td>'.$array_datos["Distancia"]["Total"].'</td>';
                            $detalle_tarjeta .= '</tr>';
                             */

                            $detalle_tarjeta .= '</table>';

                            $detalle_game .= '
										 <tr class="modo1">
											<td>
											' . $detalle_tarjeta . '
											</td>
										</tr>
										';
                            /*
                            $datos["Id"] = $datos_service->Id;
                            array_push($response, $datos);
                             */

                            $detalle_game .= '
											</tbody>
										</table>
										';

                            $cuerpo_game = '<!doctype html>
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
											font-size: 12px;
											font-weight:bold;
											background-color: #FFF;
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
											font-size: 18px;
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

                            $cuerpo_game .= $cabeza_game . "<br>" . $detalle_game;
                            $cuerpo_game .= '</body>
										</html>';

                            $tablagame["CuerpoGame"] = $cuerpo_game;
                            array_push($response, $tablagame);
                        }
                    } //end while
                    $respuesta["message"] = $message;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = "C6. No se encontraron registros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else
            } else {
                $respuesta["message"] = "C6. No hay comunicacion con sistema externo";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = "F22. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    } //end function

    public function get_games_ant($IDClub, $IDSocio, $Score, $Codigo = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if ((!empty($IDSocio) || !empty($Codigo)) && !empty($IDClub) && !empty($Score)) {

            if (!empty($IDSocio)) :
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                $codigo_fedegolf = $datos_socio["Email"];
            elseif (!empty($Codigo)) :
                $codigo_fedegolf = $Codigo;
                $datos_socio = $dbo->fetchAll("Socio", " Email = '" . $Codigo . "' ", "array");
                if (empty($datos_socio["Nombre"])) :
                    //Consulto el nombre ya que el codigo puede ser que no haya ingresado al sisitema y no tengo los datos
                    $resultado_consulta = self::get_usuario_codigo($Codigo);
                    $datos_socio["Nombre"] = $resultado_consulta["response"][0]["nombre"];
                    $datos_socio["Apellido"] = $resultado_consulta["response"][0]["apellido"];
                endif;
            endif;

            //Consulto el detalle de la tarjeta
            $detalle_tarjeta = "";
            $url_tarjeta = "http://208.83.253.107:9098/api/Usuarios/GetGameCard?code=" . $codigo_fedegolf . "&score=" . $Score;
            $contenido_resultado_tarjeta = file_get_contents($url_tarjeta);
            $resultado_tarjeta = json_decode($contenido_resultado_tarjeta);
            if (count($resultado_tarjeta) > 0) {
                //Encabezado Consulta
                $cabeza_game = '<table border="0" cellpadding="0" cellspacing="0" class="tabla">
									  <tbody>
										<tr class="modo2">
										  <td>' . utf8_encode($datos_socio["Nombre"] . ' ' . $datos_socio["Apellido"]) . '</td>
										</tr>
									  </tbody>
									</table>';

                //Consulto el detalle de la factura
                $detalle_game = '
									<table border="0" cellpadding="0" cellspacing="0" class="tabla">
									  <tbody>
									';

                $message = count($resultado_tarjeta) . " Encontrados";
                $detalle_tarjeta .= '<table border="0" cellpadding="0" cellspacing="0" class="tabla"><tr bgcolor="#F3F3F3">';
                $detalle_tarjeta .= '<td>Hoyo</td>';
                $detalle_tarjeta .= '<td>Score</td>';
                $detalle_tarjeta .= '<td>Ajus.</td>';
                $detalle_tarjeta .= '<td>Par</td>';
                $detalle_tarjeta .= '<td>Ventaja</td>';
                $detalle_tarjeta .= '<td>Dis.</td>';
                $detalle_tarjeta .= '</tr>';

                foreach ($resultado_tarjeta as $datos_service_tarjeta => $datos_tarjeta) {
                    $array_datos[$datos_service_tarjeta]["1"] = $datos_tarjeta->v1;
                    $array_datos[$datos_service_tarjeta]["2"] = $datos_tarjeta->v2;
                    $array_datos[$datos_service_tarjeta]["3"] = $datos_tarjeta->v3;
                    $array_datos[$datos_service_tarjeta]["4"] = $datos_tarjeta->v4;
                    $array_datos[$datos_service_tarjeta]["5"] = $datos_tarjeta->v5;
                    $array_datos[$datos_service_tarjeta]["6"] = $datos_tarjeta->v6;
                    $array_datos[$datos_service_tarjeta]["7"] = $datos_tarjeta->v7;
                    $array_datos[$datos_service_tarjeta]["8"] = $datos_tarjeta->v8;
                    $array_datos[$datos_service_tarjeta]["9"] = $datos_tarjeta->v9;
                    $array_datos[$datos_service_tarjeta]["10"] = $datos_tarjeta->v10;
                    $array_datos[$datos_service_tarjeta]["11"] = $datos_tarjeta->v11;
                    $array_datos[$datos_service_tarjeta]["12"] = $datos_tarjeta->v12;
                    $array_datos[$datos_service_tarjeta]["13"] = $datos_tarjeta->v13;
                    $array_datos[$datos_service_tarjeta]["14"] = $datos_tarjeta->v14;
                    $array_datos[$datos_service_tarjeta]["15"] = $datos_tarjeta->v15;
                    $array_datos[$datos_service_tarjeta]["16"] = $datos_tarjeta->v16;
                    $array_datos[$datos_service_tarjeta]["17"] = $datos_tarjeta->v17;
                    $array_datos[$datos_service_tarjeta]["18"] = $datos_tarjeta->v18;
                    $array_datos[$datos_service_tarjeta]["Ida"] = $datos_tarjeta->Ida;
                    $array_datos[$datos_service_tarjeta]["Vuelta"] = $datos_tarjeta->Vuelta;
                    $array_datos[$datos_service_tarjeta]["Total"] = $datos_tarjeta->Total;
                }
                //Hoyo 1- 9
                for ($contador = 1; $contador <= 9; $contador++) :
                    $detalle_tarjeta .= '<tr>';
                    $detalle_tarjeta .= '<td>' . $contador . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Score"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Par"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Distancia"][$contador] . '</td>';
                    $detalle_tarjeta .= '</tr>';
                endfor;

                //Ida
                $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                $detalle_tarjeta .= '<td>Ida</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Score"]["Ida"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"]["Ida"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Par"]["Ida"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"]["Ida"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Distancia"]["Ida"] . '</td>';
                $detalle_tarjeta .= '</tr>';
                //Hoyo 10 -18
                for ($contador = 10; $contador <= 18; $contador++) :
                    $detalle_tarjeta .= '<tr>';
                    $detalle_tarjeta .= '<td>' . $contador . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Score"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Par"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"][$contador] . '</td>';
                    $detalle_tarjeta .= '<td>' . $array_datos["Distancia"][$contador] . '</td>';
                    $detalle_tarjeta .= '</tr>';
                endfor;
                //Vuelta
                $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                $detalle_tarjeta .= '<td>Vuelta</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Score"]["Vuelta"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"]["Vuelta"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Par"]["Vuelta"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"]["Vuelta"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Distancia"]["Vuelta"] . '</td>';
                $detalle_tarjeta .= '</tr>';
                //Totales
                $detalle_tarjeta .= '<tr bgcolor="#EDF8FF">';
                $detalle_tarjeta .= '<td>Total</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Score"]["Total"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ajustado"]["Total"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Par"]["Total"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Ventaja"]["Total"] . '</td>';
                $detalle_tarjeta .= '<td>' . $array_datos["Distancia"]["Total"] . '</td>';
                $detalle_tarjeta .= '</tr>';

                $detalle_tarjeta .= '</table>';

                $detalle_game .= '
												 <tr class="modo1">
												  <td>
													' . $detalle_tarjeta . '
												  </td>
												</tr>
												';
                /*
                $datos["Id"] = $datos_service->Id;
                array_push($response, $datos);
                 */

                $detalle_game .= '
												  </tbody>
												</table>
												';

                $cuerpo_game = '<!doctype html>
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
													font-size: 12px;
													font-weight:bold;
													background-color: #FFF;
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
													font-size: 18px;
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

                $cuerpo_game .= $cabeza_game . "<br>" . $detalle_game;
                $cuerpo_game .= '</body>
												</html>';

                $tablagame["CuerpoGame"] = $cuerpo_game;
                array_push($response, $tablagame);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "F3. No se encontraron resultados";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "F2. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    } //end function

    public function busca_documento($Documento, $Token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_WS_FEDEGOLF . 'InfoAfiliado',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
				"req": {
						"Documento" : "' . $Documento . '"
				}
		}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
                'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
            ),
        ));
        $response_ws = curl_exec($curl);
        curl_close($curl);
        $resultado = json_decode($response_ws);

        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                foreach ($datos_service as $datos_resp) {
                    if ($datos_resp->Respuesta->Mensaje == "OK") {
                        $encontrados = "S";
                    } else {
                        $datos_service = array();
                    }
                }
            }
        }
        return $datos_service;
    }

    public function busca_codigo($Codigo, $Token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_WS_FEDEGOLF . 'BuscarFedxNombre',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
			"req": {
					"Codigo" : "' . $Codigo . '",
					"Cedula" : "",
					"Nombres" : "",
					"Apellidos" : ""
			}
		}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
                'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
            ),
        ));
        $response_ws = curl_exec($curl);
        curl_close($curl);
        $resultado = json_decode($response_ws);
        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                foreach ($datos_service as $datos_resp) {
                    if ($datos_resp->Respuesta->Mensaje == "OK") {
                        $NumeroDocumento = $datos_resp->Persona->Numero_de_documento__c;
                    } else {
                        $NumeroDocumento = "";
                    }
                }
            }
        }
        return $NumeroDocumento;
    }

    public function get_usuario_codigo($Documento)
    {

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {
            //Buscar por documento
            $datos_service = self::busca_documento($Documento, $Token);
            if (count($datos_resp) <= 0) {
                $Documento = self::busca_codigo($Documento, $Token);
                if (!empty($Documento)) {
                    $datos_service = self::busca_documento($Documento, $Token);
                }
            }

            if (count($datos_service) > 0) {
                foreach ($datos_service as $datos_resp) {
                    if ($datos_resp->Respuesta->Mensaje == "OK") {
                        $datos["Id"] = $datos_resp->Persona->Id;
                        $datos["RecordTypeId"] = $datos_resp->Persona->RecordTypeId;
                        $datos["Categoria__c"] = $datos_resp->Persona->Categoria__c;
                        $datos["Tipo_de_documento__c"] = $datos_resp->Persona->Tipo_de_documento__c;
                        $datos["Tipificacion_de_Federados__c"] = $datos_resp->Persona->Tipificacion_de_Federados__c;
                        $datos["Fecha_de_vigencia__c"] = $datos_resp->Persona->Fecha_de_vigencia__c;
                        $datos["RecordTypeName"] = $datos_resp->Persona->RecordType->Name;

                        $datos["codJugador"] = $datos_resp->Persona->CodigoJugador__c;
                        $datos["email"] = $datos_resp->Persona->Email;
                        $nombre = str_replace("'", " ", $datos_resp->Persona->FirstName);
                        $apellido = str_replace("'", " ", $datos_resp->Persona->LastName);
                        $datos["nombre"] = $nombre;
                        $datos["apellido"] = $apellido;
                        $datos["genero"] = $datos_resp->Persona->Genero__c;
                        $datos["fecha_nacimiento"] = $datos_resp->Persona->Birthdate;
                        $datos["codtipodocumento"] = $datos_resp->Persona->Tipo_de_documento__c;
                        $datos["documento"] = $datos_resp->Persona->Numero_de_documento__c;
                        $datos["cod_club"] = $datos_resp->Persona->CodigoJugador__c;
                        $datos["club"] = "";
                        $datos["nivel"] = "";
                        $datos["indice"] = $datos_resp->Persona->Indice__c;
                        $datos["handicap"] = "";
                        $datos["Cancha"] = "";
                        $datos["Marca"] = "";
                        $datos["Curva"] = "";

                        switch ($datos["Categoria__c"]) {
                            case "Senior Caballeros":
                                $IDCategoriaSocio = 84;
                                break;
                            case "Pre Senior Caballeros":
                                $IDCategoriaSocio = 85;
                                break;
                            case "Aficionados Caballeros":
                                $IDCategoriaSocio = 86;
                                break;
                            case "Senior Damas":
                                $IDCategoriaSocio = 87;
                                break;
                            case "Prejuvenil Caballeros":
                                $IDCategoriaSocio = 88;
                                break;
                            case "Pre Senior Damas":
                                $IDCategoriaSocio = 89;
                                break;
                            case "Juvenil Caballeros":
                                $IDCategoriaSocio = 90;
                                break;
                            case "Aficionados Damas":
                                $IDCategoriaSocio = 91;
                                break;
                            case "Prejuvenil Damas":
                                $IDCategoriaSocio = 92;
                                break;
                            case "Juvenil Damas":
                                $IDCategoriaSocio = 93;
                                break;
                            case "Universitarios Damas":
                                $IDCategoriaSocio = 4717;
                                break;
                            case "Universitarios Caballeros":
                                $IDCategoriaSocio = 4718;
                                break;
                            case "Mid Amateur Damas":
                                $IDCategoriaSocio = 4719;
                                break;
                            case "Mid Amateur Caballeros":
                                $IDCategoriaSocio = 4720;
                                break;

                            default:
                                $IDCategoriaSocio = 0;
                        }
                        $datos["CategoriaSocio"] = $IDCategoriaSocio;

                        array_push($response, $datos);
                        $respuesta["message"] = $message;
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    } else {
                        $respuesta["message"] = $datos_resp->Respuesta->Mensaje;
                        $respuesta["success"] = false;
                        $respuesta["response"] = $response;
                    }
                } //end while
            } else {
                $respuesta["message"] = "L1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "L1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_usuario_codigo_ant($Codigo)
    {
        $response = array();
        $url = "http://208.83.253.107:9098/api/Usuarios/GetByCode?code=" . $Codigo;
        //$contenido_resultado = file_get_contents($url);
        $contenido_resultado;
        $resultado = json_decode($contenido_resultado);
        if (count($resultado) > 0) {
            $datos["codJugador"] = $resultado->codJugador;
            $datos["email"] = $resultado->email;
            $nombre = str_replace("'", " ", $resultado->nombre);
            $apellido = str_replace("'", " ", $resultado->apellido);
            $datos["nombre"] = $nombre;
            $datos["apellido"] = $apellido;
            $datos["genero"] = $resultado->genero;
            $datos["fecha_nacimiento"] = $resultado->fecha_nacimiento;
            $datos["codtipodocumento"] = $resultado->codtipodocumento;
            $datos["documento"] = $resultado->documento;
            $datos["cod_club"] = $resultado->cod_club;
            $datos["club"] = $resultado->club;
            $datos["nivel"] = $resultado->nivel;
            $datos["indice"] = $resultado->indice;
            $datos["handicap"] = $resultado->handicap;
            $datos["Cancha"] = $resultado->Cancha;
            $datos["Marca"] = $resultado->Marca;
            $datos["Curva"] = $resultado->curva;
            array_push($response, $datos);

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function get_usuario_nombre($Tag)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'BuscarFedxNombre',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
		    "req": {
		        "Codigo" : "",
		        "Cedula" : "",
		        "Nombres" : "' . $Tag . '",
		        "Apellidos" : ""
		    }
		}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response_ws;
            //exit;

            $resultado = json_decode($response_ws);

            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {
                            $datos["codJugador"] = $datos_resp->Persona->CodigoJugador__c;
                            $datos["email"] = "";
                            $datos["nombre"] = $datos_resp->Persona->FirstName;
                            $datos["apellido"] = $datos_resp->Persona->LastName;
                            $datos["genero"] = "";
                            $datos["fecha_nacimiento"] = "";
                            $datos["codtipodocumento"] = "";
                            $datos["documento"] = $datos_resp->Persona->Numero_de_documento__c;
                            $datos["cod_club"] = "";
                            $datos["club"] = "";
                            $datos["nivel"] = "";
                            $datos["indice"] = $datos_resp->Persona->Indice__c;
                            $datos["handicap"] = "";
                            array_push($response, $datos);
                        }
                    }
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "C7. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C7. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_usuario_nombre_ant($Tag)
    {
        $response = array();
        $Tag = str_replace(" ", "%20", $Tag);
        $url = "http://208.83.253.107:9098/api/Usuarios/GetByNameLike?text=" . $Tag;
        //$contenido_resultado = file_get_contents($url);
        $resultado = json_decode($contenido_resultado);
        if (count($resultado) > 0) {
            foreach ($resultado as $datos_service) {
                $datos["codJugador"] = $datos_service->codJugador;
                $datos["email"] = $datos_service->email;
                $datos["nombre"] = $datos_service->nombre;
                $datos["apellido"] = $datos_service->apellido;
                $datos["genero"] = $datos_service->genero;
                $datos["fecha_nacimiento"] = $datos_service->fecha_nacimiento;
                $datos["codtipodocumento"] = $datos_service->codtipodocumento;
                $datos["documento"] = $datos_service->documento;
                $datos["cod_club"] = $datos_service->cod_club;
                $datos["club"] = $datos_service->club;
                $datos["nivel"] = $datos_service->nivel;
                $datos["indice"] = $datos_service->indice;
                $datos["handicap"] = $datos_service->handicap;
                array_push($response, $datos);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } //end function

    public function carne_fedegolf($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio) && !empty($IDClub)) {

            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $codigo_fedegolf = $datos_socio["Email"];

            //Especial para Federacion de golf
            $datos_fedegolf = self::get_usuario_codigo($datos_socio["NumeroDocumento"]);
            $datos_carne = $datos_fedegolf["response"]["0"];

            $elemento["IDClub"] = $IDClub;
            $elemento["CodigoJugador"] = $datos_carne["codJugador"];
            $elemento["Email"] = $datos_carne["email"];
            $elemento["Nombre"] = $datos_carne["nombre"];
            $elemento["Apellido"] = $datos_carne["apellido"];
            $elemento["Genero"] = $datos_carne["genero"];
            $elemento["Documento"] = $datos_carne["documento"];
            $elemento["CodigoClub"] = $datos_carne["cod_club"];
            $elemento["Club"] = $datos_carne["club"];
            $elemento["Nivel"] = $datos_carne["nivel"];
            /*
            switch ($elemento["Nivel"]) {
            case 'Pre Senior Caballeros':
            $LogoNivel="https://miclubapp.com/plataform/assets/img/logofedeg.png";
            break;
            default:
            $LogoNivel="https://miclubapp.com/plataform/assets/img/logofedeg.png";
            break;
            }
             */

            switch ($datos_socio["IDCategoria"]) {
                case "84";
                    $LogoNivel = "https://www.miclubapp.com/file/club/SeniorCaballeros.png";
                    break;
                case "87";
                    $LogoNivel = "https://www.miclubapp.com/file/club/SeniorDamas.png";
                    break;
                case "88";
                    $LogoNivel = "https://www.miclubapp.com/file/club/PrejuvenilCaballeros.png";
                    break;
                case "90";
                    $LogoNivel = "https://www.miclubapp.com/file/club/JuvenilCaballeros.png";
                    break;
                case "92";
                    $LogoNivel = "https://www.miclubapp.com/file/club/PrejuvenilDamas.png";
                    break;
                case "93";
                    $LogoNivel = "https://www.miclubapp.com/file/club/JuvenilDamas.png";
                    break;
                case "4717";
                    $LogoNivel = "https://www.miclubapp.com/file/club/UniversitariosDamas.png";
                    break;
                case "4718";
                    $LogoNivel = "https://www.miclubapp.com/file/club/UniversitariosCaballeros.png";
                    break;
                case "4719";
                    $LogoNivel = "https://www.miclubapp.com/file/club/MidAmateurDamas.png";
                    break;
                case "4720";
                    $LogoNivel = "https://www.miclubapp.com/file/club/MidAmateurCaballeros.png";
                    break;

                default:
                    $LogoNivel = "https://www.miclubapp.com/file/club/LogoGolf.png";
            }

            //$LogoNivel="";

            $elemento["LogoNivel"] = $LogoNivel;
            $elemento["Nivel"] = $datos_carne["nivel"];
            $elemento["Indice"] = $datos_carne["indice"];
            $elemento["Handicap"] = $datos_carne["handicap"];
            $elemento["Cancha"] = $datos_carne["Cancha"];
            $elemento["Marca"] = $datos_carne["Marca"];

            $elemento["CodigoBarras"] = $datos_socio["CodigoBarras"];

            $tipo_codigo_carne = $dbo->getFields("Club", "TipoCodigoCarne", "IDClub = '" . $IDClub . "'");

            switch ($tipo_codigo_carne) {
                case "Barras":
                    if (!empty($datos_socio["CodigoBarras"])) {
                        $foto_cod_barras = SOCIO_ROOT . $datos_socio["CodigoBarras"];
                    }
                    break;
                case "QR":
                    if (!empty($datos_socio["CodigoQR"])) {
                        $foto_cod_barras = SOCIO_ROOT . "qr/" . $datos_socio["CodigoQR"];
                    }
                    break;
                default:
                    $foto_cod_barras = "";
            }
            if (!empty($datos_socio["Foto"])) {
                $foto = SOCIO_ROOT . $datos_socio["Foto"];
            }

            $elemento["Foto"] = $foto;
            $elemento["CodigoBarras"] = $foto_cod_barras;

            array_push($response, $elemento);
            $respuesta["message"] = "Encontrado" . $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "F1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function login($username, $Password)
    {
        $Password = (int) $Password;
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'ServicioLogueo2',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
					"req": {
							"username" : "' . $username . '",
							"Password":"' . $Password . '"
					}
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response_ws;
            //exit;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {

                            $datos["codJugador"] = $datos_resp->federado->CodigoJugador__c;
                            $datos["email"] = $datos_resp->federado->Email;
                            $nombre = str_replace("'", " ", $datos_resp->federado->FirstName);
                            $apellido = str_replace("'", " ", $datos_resp->federado->LastName);
                            $datos["nombre"] = $nombre;
                            $datos["apellido"] = $apellido;
                            $datos["genero"] = $datos_resp->federado->Genero__c;
                            $datos["fecha_nacimiento"] = $datos_resp->federado->Birthdate;
                            $datos["codtipodocumento"] = $datos_resp->federado->Tipo_de_documento__c;
                            $datos["documento"] = $datos_resp->federado->Numero_de_documento__c;
                            $datos["cod_club"] = "";
                            $datos["club"] = $datos_resp->federado->Account->Codigo_de_club__c;
                            $datos["nivel"] = "";
                            $datos["indice"] = "";
                            $datos["handicap"] = "";
                            $datos["Cancha"] = "";
                            $datos["Marca"] = "";
                            $datos["Curva"] = "";
                            array_push($response, $datos);
                            $respuesta["message"] = $message;
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {
                            $respuesta["message"] = $datos_resp->Respuesta->Mensaje;
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        }
                    }
                } //end while
            } else {
                $respuesta["message"] = "C1." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C1." . SIMUtil::get_traduccion('', '', 'Nohaycomunicacionconsistemaexterno', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    }

    public function login_email($username, $Password)
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'ServicioLogueo',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "username" : "' . $username . '",
			        "Password":"' . $Password . '"
			    }
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {

                            $datos["codJugador"] = $datos_resp->federado->CodigoJugador__c;
                            $datos["email"] = $datos_resp->federado->Email;
                            $nombre = str_replace("'", " ", $datos_resp->federado->FirstName);
                            $apellido = str_replace("'", " ", $datos_resp->federado->LastName);
                            $datos["nombre"] = $nombre;
                            $datos["apellido"] = $apellido;
                            $datos["genero"] = $datos_resp->federado->Genero__c;
                            $datos["fecha_nacimiento"] = $datos_resp->federado->Birthdate;
                            $datos["codtipodocumento"] = $datos_resp->federado->Tipo_de_documento__c;
                            $datos["documento"] = $datos_resp->federado->Numero_de_documento__c;
                            $datos["cod_club"] = "";
                            $datos["club"] = $datos_resp->federado->Account->Codigo_de_club__c;
                            $datos["nivel"] = "";
                            $datos["indice"] = "";
                            $datos["handicap"] = "";
                            $datos["Cancha"] = "";
                            $datos["Marca"] = "";
                            $datos["Curva"] = "";
                            array_push($response, $datos);
                            $respuesta["message"] = $message;
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {
                            $respuesta["message"] = $datos_resp->Respuesta->Mensaje;
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        }
                    }
                } //end while
            } else {
                $respuesta["message"] = "C1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "C1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    }

    public function Token()
    {
        $cliente = curl_init();
        curl_setopt($cliente, CURLOPT_URL, URL_TOKEN_FEDEGOLF);
        curl_setopt($cliente, CURLOPT_POST, 1);
        curl_setopt($cliente, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cliente, CURLOPT_POSTFIELDS, http_build_query(array(
            'client_id' => CLIENT_ID_FEDEGOLF,
            'client_secret' => CLIENT_SECRET_FEDEGOLF,
            'password' => PASSWORD_FEDEGOLF,
            'username' => USERNAME_FEDEGOLF,
            'grant_type' => GRANT_TYPE_FEDEGOLF,
        )));
        $resultado = curl_exec($cliente);
        curl_close($cliente);

        $DatosToken = json_decode($resultado);
        if (empty($DatosToken->access_token)) {
            $Token = "errortoken";
        } else {
            $Token = $DatosToken->access_token;
        }

        return $Token;
    }
    public function TokenPruebas()
    {
        $cliente = curl_init();
        curl_setopt($cliente, CURLOPT_URL, "https://test.salesforce.com/services/oauth2/token");
        curl_setopt($cliente, CURLOPT_POST, 1);
        curl_setopt($cliente, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cliente, CURLOPT_POSTFIELDS, http_build_query(array(
            'client_id' => "3MVG9zJJ_hX_0bb.gm04U5HJnw.TJpHG1mTjibeMiPzIlRT2677ldAWq1X5xLuyHxPLp8nElsq__J8OQEga6E",
            'client_secret' => "10423B3FB644660D40E73AC96B362B99060FC02D8CCD620FFCB3DF5410A7B0E0",
            'password' => "fedegolf2021",
            'username' => "apiuser@fedegolfsb.com",
            'grant_type' => "password",
        )));
        $resultado = curl_exec($cliente);
        curl_close($cliente);

        $DatosToken = json_decode($resultado);
        if (empty($DatosToken->access_token)) {
            $Token = "errortoken";
        } else {
            $Token = $DatosToken->access_token;
        }

        return $Token;
    }

    public function get_categoria_noticia()
    {
        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'CategoriasNoticias',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response_ws;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {

                            $IDSeccion = self::verifica_seccion("Seccion", $datos_resp->NombreCategoria);
                            $datos["NombreCategoria"] = $datos_resp->NombreCategoria;
                            array_push($response, $datos);
                            $respuesta["message"] = $message;
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {
                            $respuesta["message"] = $datos_resp->Respuesta->Mensaje;
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        }
                    }
                } //end while
            } else {
                $respuesta["message"] = "L1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "L1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function get_noticia()
    {
        $dbo = &SIMDB::get();

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $sql_seccion = "SELECT  IDSeccion, Nombre FROM Seccion WHERE IDClub = 17 ";
            $r_seccion = $dbo->query($sql_seccion);
            while ($row_seccion = $dbo->fetchArray($r_seccion)) {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => URL_WS_FEDEGOLF . 'NoticiasCategoria',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
		    "req": {
		        "Categoria" : "' . $row_seccion["Nombre"] . '"
		    }
		}',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $Token,
                        'Content-Type: application/json',
                        'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                    ),
                ));

                $response_ws = curl_exec($curl);

                curl_close($curl);
                $response_ws;

                $resultado = json_decode($response_ws);
                if (count($resultado) > 0) {
                    foreach ($resultado as $datos_service) {
                        foreach ($datos_service as $datos_resp) {
                            if ($datos_resp->Respuesta->Mensaje == "OK") {

                                $Titular = str_replace("'", " ", $datos_resp->Noticias->Title__c);
                                $datos["Titular"] = $Titular;
                                $datos["ID"] = $datos_resp->Noticias->Id;
                                $datos["Imagen"] = $datos_resp->Noticias->Banner_Image__c;
                                $datos["Categoria"] = $datos_resp->Noticias->Division__c;
                                $datos["Introduccion"] = $datos_resp->Noticias->Resumen__c;
                                self::crear_noticia($datos, $row_seccion["IDSeccion"], $Token);
                            }
                        }
                    } //end while
                } else {
                    $respuesta["message"] = "L1. No se encontraron registros";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else

            }
        } else {
            $respuesta["message"] = "L1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function crear_noticia($datos, $IDSeccion, $Token)
    {
        $dbo = &SIMDB::get();

        $id_noticia = $dbo->getFields("Noticia", "IDNoticia", "SWF = '" . $datos["ID"] . "' and IDClub = 17 and IDSeccion = '" . $IDSeccion . "'");

        $Identificador = $datos["ID"];
        $Titular = $datos["Titular"];
        $Introduccion = $datos["Introduccion"];
        $Introduccion = str_replace("'", "", $Introduccion);
        $datos_detalle = self::detalle_noticia($Identificador, $Token);
        $Cuerpo = $datos_detalle["Cuerpo"];
        $Cuerpo = str_replace("'", "", $Cuerpo);

        $FechaInicio = substr($datos_detalle["Fecha"], 0, 10);
        //$FechaInicio="2021-01-01";
        $FechaFin = "2050-01-01";
        $Imagen = $datos["Imagen"];

        $array_datos_imagen = explode("src", $Imagen);
        $Imagen = $array_datos_imagen[1];
        $Imagen = str_replace('="', "", $Imagen);
        $pos = strpos($Imagen, '"');
        if ($pos === false) {
            $Imagen = "";
        } else {
            $Imagen = substr($Imagen, 0, $pos);
        }

        $Imagen = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen);
        $Imagen = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen);
        $Imagen = str_replace("&amp;", "&", $Imagen);
        $ruta_foto_noticia = $Imagen;

        if (empty($id_noticia)) :
            //Inserto Noticia
            $sql_noticia = "INSERT INTO Noticia (IDClub, DirigidoA,IDSeccion, Titular, Introduccion, Cuerpo, Publicar, FechaInicio, FechaFin, NoticiaFile, SWF, UsuarioTrCr, FechaTrCr)
															Values(17,'S','" . $IDSeccion . "','" . $Titular . "','" . $Introduccion . "','" . $Cuerpo . "','S','" . $FechaInicio . "','" . $FechaFin . "','" . $ruta_foto_noticia . "','" . $Identificador . "','WebService','" . date("Y-m-d H:is") . "')";

            $dbo->query($sql_noticia);
        else :
            //Actualizo Noticia
            $sql_update_noticia = "UPDATE Noticia set  Titular = '" . $Titular . "',
																						 Introduccion = '" . $Introduccion . "',
																						 DirigidoA='S',
																						 Cuerpo = '" . $Cuerpo . "',
																						 FechaInicio = '" . $FechaInicio . "',
																						 NoticiaFile = '" . $ruta_foto_noticia . "',
																						 UsuarioTrEd = 'WebService',
																						 FechaTrEd = '" . date("Y-m-d H:is") . "',
																						 Publicar = 'S',
																						 SWF='" . $Identificador . "'
																						 Where IDNoticia = '" . $id_noticia . "'";

            $dbo->query($sql_update_noticia);
        endif;
    }

    public function detalle_noticia($Identificador, $Token)
    {
        $dbo = &SIMDB::get();
        $response = array();
        //$Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'DetalleNoticia',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "IdNoticia" : "' . $Identificador . '"
			    }
			}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);
            //echo $response_ws;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {
                            $datos["Cuerpo"] = $datos_resp->unaNota->Body__c;
                            $datos["Fecha"] = $datos_resp->unaNota->Fecha_de_publicacion__c;
                        }
                    }
                } //end while
            }
        }
        return $datos;
    }

    public function verifica_seccion($TablaSeccion, $nombre)
    {
        $dbo = &SIMDB::get();
        switch ($TablaSeccion):
            case "Seccion":
                //Verifico si la seccion existe para crearla
                if (!empty($nombre)) :
                    $IDSeccion = $dbo->getFields("Seccion", "IDSeccion", "Nombre = '" . $nombre . "' and IDClub = 17");
                    if (empty($IDSeccion)) :
                        //crear seccion
                        $sql_seccion = "INSERT INTO Seccion (IDClub, DirigidoA, Nombre, Descripcion, Publicar)
																											Values(17,'S','" . $nombre . "','WebService " . $nombre . "','S') ";
                        $dbo->query($sql_seccion);
                    else :
                        $sql_seccion = "UPDATE Seccion
																															SET DirigidoA='S', Publicar='S'
																															WHERE IDSeccion = '" . $IDSeccion . "'";
                        $dbo->query($sql_seccion);
                    endif;
                endif;
                break;
        endswitch;
    }

    public function get_evento()
    {
        $dbo = &SIMDB::get();

        $response = array();
        $Token = self::Token();
        if ($Token != "errortoken") {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'ConsultaCalendario',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            //echo $response_ws;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {
                            $datos["Titular"] = $datos_resp->unTorneo->Name;
                            $datos["ID"] = $datos_resp->unTorneo->Id;
                            $datos["Division"] = $datos_resp->unTorneo->Division_del_torneo__c;
                            $datos["FechaInicioTorneo"] = $datos_resp->unTorneo->Fecha_de_inicio_del_Torneo__c;
                            $datos["FechaFinTorneo"] = $datos_resp->Noticias->Fecha_de_finalizacion_del_torneo__c;
                            self::crear_evento($datos, $Token);
                        }
                    }
                } //end while
            } else {
                $respuesta["message"] = "L1. No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

        } else {
            $respuesta["message"] = "L1. No hay comunicacion con sistema externo";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        }

        return $respuesta;
    } //end function

    public function crear_evento($datos, $Token)
    {
        $dbo = &SIMDB::get();

        $id_evento = $dbo->getFields("Evento", "IDEvento", "Tag = '" . $datos["ID"] . "' and IDClub = 17");

        $Identificador = $datos["ID"];
        $Titular = $datos["Titular"];
        $Titular = str_replace("'", "", $Titular);
        $Introduccion = $datos["Introduccion"];
        $Introduccion = str_replace("'", "", $Introduccion);
        //$Cuerpo=self::detalle_evento($Identificador,$Token);
        $FechaInicio = "2021-01-01";
        $FechaFin = "2050-01-01";
        $FechaInicioEvento = $datos["FechaInicioTorneo"];
        $FechaFinEvento = $datos["FechaFinTorneo"];
        if (empty($FechaFinEvento)) {
            $FechaFinEvento = $FechaInicioEvento;
        }

        $datos_eventos = self::detalle_evento($Identificador, $Token);
        $Cuerpo = $datos_eventos["Cuerpo"];
        $Cuerpo = str_replace("&#39;", "", $Cuerpo);

        if (empty($id_evento)) :
            //Inserto Noticia
            $sql_evento = "INSERT INTO Evento (IDClub, IDSeccionEvento, DirigidoA, Titular, Introduccion, Cuerpo, Publicar, FechaInicio, FechaFin, EventoFile, Tag, FechaEvento, FechaFinEvento, Lugar, EmailContacto, UsuarioTrCr, FechaTrCr)
																			VALUES(17,'154','S','" . $Titular . "','" . $Introduccion . "','" . $Cuerpo . "','S','" . $FechaInicio . "','" . $FechaFin . "','" . $datos_eventos["Imagen"] . "',
																							'" . $Identificador . "','" . $FechaInicioEvento . "','" . $FechaFinEvento . "','" . $datos_eventos["Lugar"] . "','fedegolf@federacioncolombianadegolf.com','WebService','" . date("Y-m-d H:is") . "')";

            $dbo->query($sql_evento);
        else :
            //Actualizo Noticia
            $sql_update_evento = "UPDATE Evento set  Titular = '" . $Titular . "',
																						 Introduccion = '" . $Introduccion . "',
																						 DirigidoA='S',
																						 Cuerpo = '" . $Cuerpo . "',
																						 FechaInicio = '" . $FechaInicio . "',
																						 FechaEvento = '" . $FechaInicioEvento . "',
																						 FechaFinEvento	='" . $FechaFinEvento . "',
																						 EventoFile = '" . $datos_eventos["Imagen"] . "',
																						 UsuarioTrEd = 'WebService',
																						 FechaTrEd = '" . date("Y-m-d H:is") . "',
																						 Publicar = 'S',
																						 EmailContacto='fedegolf@federacioncolombianadegolf.com',
																						 Lugar = '" . $datos_eventos["Lugar"] . "',
																						 Tag='" . $Identificador . "'
																						 Where IDEvento = '" . $id_evento . "'";
            $sql_update_evento;
            $dbo->query($sql_update_evento);
        endif;
    }

    public function detalle_evento($Identificador, $Token, $Pruebas = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($Pruebas)) :
            $URL = 'https://fedegolf--fedegolfsb.my.salesforce.com/services/apexrest/DetalleTorneo';
        else :
            $URL = URL_WS_FEDEGOLF . 'DetalleTorneo';
        endif;

        // $Token = self::Token();
        if ($Token != "errortoken") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "req": {
			        "IdTorneo" : "' . $Identificador . '"
			    }
			}
			',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);
            // echo $response_ws;

            $resultado = json_decode($response_ws);
            if (count($resultado) > 0) {
                foreach ($resultado as $datos_service) {
                    foreach ($datos_service as $datos_resp) {
                        if ($datos_resp->Respuesta->Mensaje == "OK") {
                            $datos["Cuerpo"] = $datos_resp->elTorneo->Descripcion__c;
                            $Imagen = $datos_resp->elTorneo->Imagen_del_torneo__c;

                            if (empty($Imagen)) :
                                $Imagen = $datos_resp->elTorneo->Imagen_Patrocinador__c;
                            endif;

                            $array_datos_imagen = explode("src", $Imagen);
                            $Imagen = $array_datos_imagen[1];
                            $Imagen = str_replace('="', "", $Imagen);
                            $pos = strpos($Imagen, '"');
                            if ($pos === false) {
                                $Imagen = "";
                            } else {
                                $Imagen = substr($Imagen, 0, $pos);
                            }
                            $Imagen = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen);
                            $Imagen = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen);
                            $Imagen = str_replace("&amp;", "&", $Imagen);
                            $datos["Imagen"] = $Imagen;
                            $datos["Lugar"] = $datos_resp->elTorneo->Club__r->Name;
                            if ($datos_resp->elTorneo->habilitar_preinscripcion__c == "true") {
                                $Link = '<br><a href="https://www.federacioncolombianadegolf.com/apex/torneos?Post=' . $Identificador . '>Inscr&iacute;base pulsando aca.</a>';
                                $datos["Cuerpo"] .= $Link;
                            }

                            $datos[ActivaInscripcion] = $datos_resp->elTorneo->Activar_inscripci_n_en_linea__c;
                        }
                    }
                } //end while
            }
        }
        return $datos;
    }

    public function get_beneficio($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();

        $Token = self::Token();
        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_WS_FEDEGOLF . 'ConsultaClubFederados',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            curl_close($curl);

            $resultado = json_decode($response_ws);
            $response = array();

            if (!empty($Tag)) {
                $buscar = true;
            }

            if (count($resultado) > 0) {
                foreach ($resultado as $datos_servicio) :
                    foreach ($datos_servicio as $datos) :
                        if ($datos->Respuesta->Mensaje == "OK") :

                            $beneficio["IDBeneficio"] = $datos->unaPromo->Id;
                            $beneficio["IDCategoria"] = $IDCategoria;
                            $beneficio["IDClub"] = $IDClub;
                            $beneficio["Nombre"] = $datos->unaPromo->Oferta__c;
                            $beneficio["Introduccion"] = $datos->unaPromo->Titulo__c;
                            $beneficio["Descripcion"] = $datos->unaPromo->Categoria__c;
                            $beneficio["DescripcionHtml"] = $datos->unaPromo->Descripcion__c;
                            $beneficio["Telefono"] = "";
                            $beneficio["PaginaWeb"] = $datos->unaPromo->Sitio_web__c;
                            $beneficio["Latitud"] = "";
                            $beneficio["Longitud"] = "";
                            $beneficio["FechaInicio"] = $datos->unaPromo->CreatedDate;
                            $beneficio["FechaFin"] = $datos->unaPromo->CreatedDate;

                            $OcultarPaginaWeb = "N";
                            $OcultarTelefono = "N";
                            $OcultarMapa = "N";
                            $OcultarBotonRuta = "N";

                            if (empty($beneficio["PaginaWeb"])) :
                                $OcultarPaginaWeb = "S";
                            endif;

                            if (empty($beneficio["Telefono"])) :
                                $OcultarTelefono = "S";
                            endif;

                            if (empty($beneficio["Latitud"]) || empty($beneficio["Longitud"])) :
                                $OcultarMapa = "S";
                                $OcultarBotonRuta = "S";
                            endif;

                            $beneficio["OcultarPaginaWeb"] = $OcultarPaginaWeb;
                            $beneficio["OcultarTelefono"] = $OcultarTelefono;
                            $beneficio["OcultarMapa"] = $OcultarMapa;
                            $beneficio["OcultarBotonRuta"] = $OcultarBotonRuta;

                            $response_fotos = array();

                            $Imagen1 = $datos->unaPromo->Imagen_logo_patrocinador__c;
                            $Imagen2 = $datos->unaPromo->Imagen_patrocinador__c;
                            $Imagen3 = $datos->unaPromo->Imagen_promocion__c;

                            $array_datos_imagen1 = explode("src", $Imagen1);
                            $array_datos_imagen2 = explode("src", $Imagen2);
                            $array_datos_imagen3 = explode("src", $Imagen3);

                            $Imagen1 = $array_datos_imagen1[1];
                            $Imagen2 = $array_datos_imagen2[1];
                            $Imagen3 = $array_datos_imagen3[1];

                            $Imagen1 = str_replace('="', "", $Imagen1);
                            $Imagen2 = str_replace('="', "", $Imagen2);
                            $Imagen3 = str_replace('="', "", $Imagen3);

                            $pos1 = strpos($Imagen1, '"');
                            $pos2 = strpos($Imagen2, '"');
                            $pos3 = strpos($Imagen3, '"');

                            if ($pos1 === false) {
                                $Imagen1 = "";
                            } else {
                                $Imagen1 = substr($Imagen3, 0, $pos1);
                            }

                            if ($pos2 === false) {
                                $Imagen2 = "";
                            } else {
                                $Imagen2 = substr($Imagen2, 0, $pos2);
                            }

                            if ($pos3 === false) {
                                $Imagen3 = "";
                            } else {
                                $Imagen3 = substr($Imagen3, 0, $pos3);
                            }

                            $Imagen1 = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen1);
                            $Imagen1 = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen1);
                            $Imagen1 = str_replace("&amp;", "&", $Imagen1);
                            $ruta_foto_noticia = $Imagen1;

                            $array_dato_foto["Foto"] = $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $Imagen2 = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen2);
                            $Imagen2 = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen2);
                            $Imagen2 = str_replace("&amp;", "&", $Imagen2);
                            $ruta_foto_noticia = $Imagen2;

                            $array_dato_foto["Foto"] = $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $Imagen3 = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen1);
                            $Imagen3 = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen1);
                            $Imagen3 = str_replace("&amp;", "&", $Imagen3);
                            $ruta_foto_noticia = $Imagen3;

                            $array_dato_foto["Foto"] = $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $beneficio["Fotos"] = $response_fotos;

                            if ($buscar) {
                                $pos = strpos(strtoupper($beneficio["Nombre"]), strtoupper($Tag));

                                if ($pos === false) {
                                } else {
                                    array_push($response, $beneficio);
                                }
                            } else {
                                array_push($response, $beneficio);
                            }

                        endif;
                    endforeach;
                endforeach;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            $dbo = &SIMDB::get();

            // Seccion Especifica
            if (!empty($IDCategoria)) :
                $array_condiciones[] = " IDSeccionBeneficio  = '" . $IDCategoria . "' ";
            endif;

            // Seccion Especifica
            if (!empty($IDBeneficio)) :
                $array_condiciones[] = " IDBeneficio  = '" . $IDBeneficio . "' ";
            endif;

            // Tag
            if (!empty($Tag)) :
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $sql = "SELECT * FROM Beneficio WHERE Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {

                    $beneficio["IDBeneficio"] = $r["IDBeneficio"];
                    $beneficio["IDCategoria"] = $r["IDSeccionBeneficio"];
                    $beneficio["IDClub"] = $r["IDClub"];
                    $beneficio["Nombre"] = $r["Nombre"];
                    $beneficio["Introduccion"] = $r["Introduccion"];
                    $beneficio["Descripcion"] = $r["Descripcion"];

                    $cuerpo_beneficio = str_replace("file/clasificados/editor/", CLASIFICADOS_ROOT . 'editor/', $r["DescripcionHtml"]);
                    //Documentos adjuntos
                    if (!empty($r["Adjunto1File"])) :
                        $cuerpo_beneficio .= "<br><a href='" . CLASIFICADOS_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                    endif;

                    $beneficio["DescripcionHtml"] = $cuerpo_beneficio;
                    $beneficio["Telefono"] = $r["Telefono"];
                    $beneficio["PaginaWeb"] = $r["PaginaWeb"];
                    $beneficio["Latitud"] = $r["Latitud"];
                    $beneficio["Longitud"] = $r["Longitud"];
                    $beneficio["FechaInicio"] = $r["FechaInicio"];
                    $beneficio["FechaFin"] = $r["FechaFin"];

                    $beneficio["OcultarPaginaWeb"] = $r["OcultarPaginaWeb"];
                    $beneficio["OcultarTelefono"] = $r["OcultarTelefono"];
                    $beneficio["OcultarMapa"] = $r["OcultarMapa"];
                    $beneficio["OcultarBotonRuta"] = $r["OcultarBotonRuta"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 1; $i_foto++) :
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])) :
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $beneficio["Fotos"] = $response_fotos;
                    array_push($response, $beneficio);
                } //ednw hile
            }

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;
            // exit;
        }
    }

    // FUNCIONES NUEVAS DE BENEFICIOS HAY QUE PASAR A PRODUCCIÓN EN CONJUNTO CON FEDEGOLF
    public function get_categoria_beneficio($IDClub)
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();
        $response = array();

        $Token = self::Token();

        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL =>  URL_FEDEGOLF . '/services/apexrest/CategoriasClubFederados',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);

            $resultado = json_decode($response_ws);
            $Orden = 1;
            if (count($resultado) > 0) :
                foreach ($resultado->CategoriasResult as $id => $Categoria) :
                    $Nombre = trim($Categoria->NombreCategoria);
                    $Descripcion = trim($Categoria->NombreCategoria);

                    // BUSCAMOS SI LA CATEGORIA ESTA CREADA
                    $SQLCategoria = "SELECT IDSeccionBeneficio FROM SeccionBeneficio  WHERE IDClub = $IDClub AND Nombre = '$Nombre'";
                    $QRYCategoria = $dbo->query($SQLCategoria);
                    // SI LA CATEGORIA NO EXISTE LA CREAMOS
                    if ($dbo->rows($QRYCategoria) <= 0) :
                        $InsertCategoria = "INSERT INTO SeccionBeneficio (IDClub, Nombre, Descripcion, SoloIcono, Publicar, Orden) 
                                            VALUES ($IDClub, '$Nombre', '$Descripcion', 'N', 'S', $Orden)";

                        $dbo->query($InsertCategoria);
                        $Orden++;
                    endif;
                endforeach;
            endif;

            $sql = "SELECT * FROM SeccionBeneficio WHERE Publicar = 'S' and IDClub = '$IDClub' ORDER BY Nombre";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) :
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDCategoria"] = $r["Nombre"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];

                    if (!empty($r["Foto"])) :
                        $foto = CLASIFICADOS_ROOT . $r["Foto"];
                    else :
                        $foto = "";
                    endif;

                    $seccion["Icono"] = $foto;

                    array_push($response, $seccion);
                } //ednw hile

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
    } // fin function

    public function get_beneficio_categoria($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();
        $response = array();

        $Token = self::Token();

        if (empty($IDCategoria))
            $IDCategoria = "TODOS";

        if ($Token != "errortoken") {

            $POST =
                '{
                "req": {
                    "Categoria" : "' . $IDCategoria . '"
                }
            }';
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FEDEGOLF . '/services/apexrest/ConsultaClubFederados',
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
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            // echo $response_ws;
            curl_close($curl);

            $resultado = json_decode($response_ws);

            if (!empty($Tag)) {
                $buscar = true;
            }

            if (count($resultado) > 0) {
                foreach ($resultado as $datos_servicio) :
                    foreach ($datos_servicio as $datos) :
                        if ($datos->Respuesta->Mensaje == "OK") :

                            $beneficio["IDBeneficio"] = $datos->unaPromo->Id;
                            $beneficio["IDCategoria"] = $IDCategoria;
                            $beneficio["IDClub"] = $IDClub;
                            $beneficio["Nombre"] = $datos->unaPromo->Oferta__c;
                            $beneficio["Introduccion"] = $datos->unaPromo->Titulo__c;
                            $beneficio["Descripcion"] = $datos->unaPromo->Categoria__c;
                            $beneficio["DescripcionHtml"] = $datos->unaPromo->Descripcion__c;
                            $beneficio["Telefono"] = "";
                            $beneficio["PaginaWeb"] = $datos->unaPromo->Sitio_web__c;
                            $beneficio["Latitud"] = "";
                            $beneficio["Longitud"] = "";
                            $beneficio["FechaInicio"] = $datos->unaPromo->CreatedDate;
                            $beneficio["FechaFin"] = $datos->unaPromo->CreatedDate;

                            $OcultarPaginaWeb = "N";
                            $OcultarTelefono = "N";
                            $OcultarMapa = "N";
                            $OcultarBotonRuta = "N";

                            if (empty($beneficio["PaginaWeb"])) :
                                $OcultarPaginaWeb = "S";
                            endif;

                            if (empty($beneficio["Telefono"])) :
                                $OcultarTelefono = "S";
                            endif;

                            if (empty($beneficio["Latitud"]) || empty($beneficio["Longitud"])) :
                                $OcultarMapa = "S";
                                $OcultarBotonRuta = "S";
                            endif;

                            $beneficio["OcultarPaginaWeb"] = $OcultarPaginaWeb;
                            $beneficio["OcultarTelefono"] = $OcultarTelefono;
                            $beneficio["OcultarMapa"] = $OcultarMapa;
                            $beneficio["OcultarBotonRuta"] = $OcultarBotonRuta;

                            $response_fotos = array();

                            $Imagen1 = $datos->unaPromo->Imagen_logo_patrocinador__c;
                            $Imagen2 = $datos->unaPromo->Imagen_patrocinador__c;
                            $Imagen3 = $datos->unaPromo->Imagen_promocion__c;

                            $array_datos_imagen1 = explode("src", $Imagen1);
                            $array_datos_imagen2 = explode("src", $Imagen2);
                            $array_datos_imagen3 = explode("src", $Imagen3);

                            $Imagen1 = $array_datos_imagen1[1];
                            $Imagen2 = $array_datos_imagen2[1];
                            $Imagen3 = $array_datos_imagen3[1];

                            $Imagen1 = str_replace('="', "", $Imagen1);
                            $Imagen2 = str_replace('="', "", $Imagen2);
                            $Imagen3 = str_replace('="', "", $Imagen3);

                            $pos1 = strpos($Imagen1, '"');
                            $pos2 = strpos($Imagen2, '"');
                            $pos3 = strpos($Imagen3, '"');

                            if ($pos1 === false) {
                                $Imagen1 = "";
                            } else {
                                $Imagen1 = substr($Imagen3, 0, $pos1);
                            }

                            if ($pos2 === false) {
                                $Imagen2 = "";
                            } else {
                                $Imagen2 = substr($Imagen2, 0, $pos2);
                            }

                            if ($pos3 === false) {
                                $Imagen3 = "";
                            } else {
                                $Imagen3 = substr($Imagen3, 0, $pos3);
                            }

                            $Imagen1 = str_replace("&amp;", "&", $Imagen1);
                            $ruta_foto_noticia = $Imagen1;

                            $array_dato_foto["Foto"] = URL_FEDEGOLF . $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $Imagen2 = str_replace("&amp;", "&", $Imagen2);
                            $ruta_foto_noticia = $Imagen2;

                            $array_dato_foto["Foto"] = URL_FEDEGOLF . $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $Imagen3 = str_replace("&amp;", "&", $Imagen3);
                            $ruta_foto_noticia = $Imagen3;

                            $array_dato_foto["Foto"] = URL_FEDEGOLF . $ruta_foto_noticia;
                            array_push($response_fotos, $array_dato_foto);

                            $beneficio["Fotos"] = $response_fotos;

                            if ($buscar) {
                                $pos = strpos(strtoupper($beneficio["Nombre"]), strtoupper($Tag));

                                if ($pos === false) {
                                } else {
                                    array_push($response, $beneficio);
                                }
                            } else {
                                array_push($response, $beneficio);
                            }

                        endif;
                    endforeach;
                endforeach;
            }

            /* // Seccion Especifica
            if (!empty($IDCategoria)):
                $array_condiciones[] = " IDSeccionBeneficio  = '" . $IDCategoria . "' ";
            endif;

            // Seccion Especifica
            if (!empty($IDBeneficio)):
                $array_condiciones[] = " IDBeneficio  = '" . $IDBeneficio . "' ";
            endif;

            // Tag
            if (!empty($Tag)):
                $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0):
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_clasificado = " and " . $condiciones;
            endif;

            $sql = "SELECT * FROM Beneficio WHERE Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {

                    $beneficio["IDBeneficio"] = $r["IDBeneficio"];
                    $beneficio["IDCategoria"] = $r["IDSeccionBeneficio"];
                    $beneficio["IDClub"] = $r["IDClub"];
                    $beneficio["Nombre"] = $r["Nombre"];
                    $beneficio["Introduccion"] = $r["Introduccion"];
                    $beneficio["Descripcion"] = $r["Descripcion"];

                    $cuerpo_beneficio = str_replace("file/clasificados/editor/", CLASIFICADOS_ROOT . 'editor/', $r["DescripcionHtml"]);
                    //Documentos adjuntos
                    if (!empty($r["Adjunto1File"])):
                        $cuerpo_beneficio .= "<br><a href='" . CLASIFICADOS_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                    endif;

                    $beneficio["DescripcionHtml"] = $cuerpo_beneficio;
                    $beneficio["Telefono"] = $r["Telefono"];
                    $beneficio["PaginaWeb"] = $r["PaginaWeb"];
                    $beneficio["Latitud"] = $r["Latitud"];
                    $beneficio["Longitud"] = $r["Longitud"];
                    $beneficio["FechaInicio"] = $r["FechaInicio"];
                    $beneficio["FechaFin"] = $r["FechaFin"];

                    $beneficio["OcultarPaginaWeb"] = $r["OcultarPaginaWeb"];
                    $beneficio["OcultarTelefono"] = $r["OcultarTelefono"];
                    $beneficio["OcultarMapa"] = $r["OcultarMapa"];
                    $beneficio["OcultarBotonRuta"] = $r["OcultarBotonRuta"];

                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 1; $i_foto++):
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])):
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $beneficio["Fotos"] = $response_fotos;
                    array_push($response, $beneficio);

                } //ednw hile
            } */

            if (count($resultado) <= 0 /* && $dbo->rows($qry) <= 0 */) :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            endif;

            return $respuesta;
            // exit;
        }
    }

    public function get_seccionevento($IDClub, $IDSocio = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        $curl = curl_init();
        $response = array();

        $Token = self::Token();
        // $Token = self::TokenPruebas();

        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FEDEGOLF . '/services/apexrest/ConsultaDivisionesTorneos',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            curl_close($curl);
            $resultado = json_decode($response_ws);

            if (count($resultado) > 0) :
                foreach ($resultado->DivisionesResult as $id => $Seccion) :

                    $seccion["IDClub"] = $IDClub;
                    $seccion["IDSeccion"] = $Seccion->NombreDivision;
                    $seccion["Nombre"] = $Seccion->NombreDivision;
                    $seccion["Descripcion"] = "Seccion de servicio fedegolf: " . $Seccion->NombreDivision;

                    array_push($response, $seccion);

                endforeach;
            endif;

            /* 
                $sql = "SELECT S.* FROM SeccionEvento" . $Version . " S " . $tabla_join . " WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY S.Orden";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " Encontrados";
                    while ($r = $dbo->fetchArray($qry)) {

                        $id_noticia = $dbo->getFields("Evento" . $Version, "IDEvento" . $Version, "(DirigidoA = 'S' or DirigidoA = 'T') and IDSeccionEvento" . $Version . " = '" . $r["IDSeccionEvento" . $Version] . "' and Publicar = 'S' and  FechaInicio <= CURDATE() and FechaFin >= CURDATE()");

                        if (!empty($id_noticia)):

                            $seccion["IDClub"] = $r["IDClub"];
                            $seccion["IDSeccion"] = $r["IDSeccionEvento" . $Version];
                            $seccion["Nombre"] = $r["Nombre"];
                            $seccion["Descripcion"] = $r["Descripcion"];

                            array_push($response, $seccion);
                        endif;

                    } //ednw hile
                } 
            */

            if (count($resultado) <= 0 && $dbo->rows($qry) <= 0) :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            endif;

            return $respuesta;
        }
    } // fin function

    public function get_eventos($IDClub, $IDSeccion = "", $IDSocio = "", $tag = "", $Fecha = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        $curl = curl_init();
        $response = array();

        if (empty($IDSeccion))
            $IDSeccion = "TODOS";

        $POST = '{
            "req": {
                "Division" : "' . $IDSeccion . '"
            }
        }
        ';

        $Token = self::Token();

        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FEDEGOLF . '/services/apexrest/ConsultaCalendario',
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
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            // echo $response_ws;
            curl_close($curl);
            $resultado = json_decode($response_ws);

            if (!empty($IDSeccion)) :
                if (count($resultado) > 0) :
                    foreach ($resultado->TorneosResult as $id => $Evento) :

                        $evento["IDClub"] = $IDClub;
                        $evento["IDSeccionEvento"] = $IDSeccion;
                        $evento["IDEvento"] = $Evento->unTorneo->Id;
                        $evento["Titular"] = $Evento->unTorneo->Name;
                        $evento["Introduccion"] = $Evento->unTorneo->Name;

                        $datos_eventos = self::detalle_evento($Evento->unTorneo->Id, $Token, "");

                        $Cuerpo = $datos_eventos[Cuerpo];
                        $Cuerpo = str_replace("&#39;", "", $Cuerpo);

                        $evento["Cuerpo"] = $Cuerpo;

                        if ($datos_eventos[ActivaInscripcion] == "true") {
                            $Link = '<br><a href="' . $Evento->unTorneo->URL_Boton_Pago__c . '">Inscr&iacute;base pulsando aqu&iacute;.</a>';
                            $datos["Cuerpo"] .= $Link;
                        }

                        $evento["Foto"] = $datos_eventos[Imagen];
                        $evento["Lugar"] = $datos_eventos[Lugar];

                        $evento["CuerpoEmail"] = "";
                        $evento["Fecha"] = $Evento->unTorneo->Fecha_de_inicio_del_Torneo__c;
                        $evento["FechaFinEvento"] = $Evento->unTorneo->Fecha_de_finalizacion_del_torneo__c;
                        $evento["Hora"] = "";
                        $evento["Valor"] = "";
                        $evento["EmailContacto"] = "fedegolf@federacioncolombianadegolf.com";

                        $evento["InscripcionApp"] = "N";
                        $evento["MensajePagoInscripcion"] = "";
                        $evento["PagoReserva"] = "N";

                        $evento["TipoPago"] = [];
                        $evento["CampoFormulario"] = [];
                        $evento["Foto2"] = "";

                        array_push($response, $evento);

                    endforeach;
                endif;
            endif;

            /* 
                // Secciones Socio
                if (!empty($IDSocio) && $IDSeccion == ""):
                    $sql_seccion_socio = $dbo->query("Select * From SocioSeccionEvento" . $Version . " Where IDSocio = '" . $IDSocio . "'");
                    while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
                        $array_secciones_socio[] = $row_seccion["IDSeccionEvento"];
                    endwhile;

                    if (count($array_secciones_socio) > 0):
                        $IDSecciones = implode(",", $array_secciones_socio);
                        //$array_condiciones[] = " IDSeccionEvento".$Version." in(".$IDSecciones.") ";
                    endif;

                    $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
                    if ($IDClub == 36 && $TipoSocio == "Estudiante") {
                        $array_condiciones[] = " TipoSocio = 'Estudiante'";
                    } else {
                        $array_condiciones[] = " (TipoSocio = '' or TipoSocio = 'Socio' ) ";
                    }
                endif;

                // Seccion Especifica
                if (!empty($IDSeccion)):
                    $array_condiciones[] = " IDSeccionEvento" . $Version . "  = '" . $IDSeccion . "'";
                endif;

                // Tag
                if (!empty($tag)):
                    $array_condiciones[] = " (Titular  like '%" . $tag . "%' or Introduccion like '%" . $tag . "%' or Cuerpo like '%" . $tag . "%' or Lugar like '%" . $tag . "%' )";
                endif;

                if (!empty($Fecha)):
                    $array_condiciones[] = " FechaEvento  = '" . $Fecha . "'";
                endif;

                if (count($array_condiciones) > 0):
                    $condiciones = implode(" and ", $array_condiciones);
                    $condiciones_noticia = " and " . $condiciones;
                endif;

                $orden = " FechaEvento ASC";
                $CondicionFechaEvento = " ";

                $sql = "SELECT * FROM Evento" . $Version . " WHERE (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() " . $CondicionFechaEvento . " and IDClub = '" . $IDClub . "' " . $condiciones_noticia . " ORDER BY " . $orden . " ";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " Encontrados";
                    while ($r = $dbo->fetchArray($qry)) {
                        $evento["IDClub"] = $r["IDClub"];
                        $evento["IDSeccionEvento"] = $r["IDSeccionEvento" . $Version];
                        $evento["IDEvento"] = $r["IDEvento" . $Version];
                        $evento["Titular"] = $r["FechaEvento"] . ": " . $r["Titular"];
                        $evento["Introduccion"] = $r["Introduccion"];

                        $cuerpo_evento = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                        //Documentos adjuntos
                        if (!empty($r["Adjunto1File"])):
                            $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                        endif;
                        if (!empty($r["Adjunto2File"])):
                            $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                        endif;
                        if (!empty($r["Adjunto3File"])):
                            $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto3File"] . "' >" . $r["Adjunto3File"] . '</a>';
                        endif;
                        if (!empty($r["Adjunto4File"])):
                            $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto4File"] . "' >" . $r["Adjunto4File"] . '</a>';
                        endif;

                        $evento["Cuerpo"] = $cuerpo_evento;

                        $evento["CuerpoEmail"] = $r["CuerpoEmail"];
                        $evento["Fecha"] = $r["FechaEvento"];
                        $evento["FechaFinEvento"] = $r["FechaFinEvento"];
                        $evento["Lugar"] = $r["Lugar"];

                        $HoraEvento = (string) $r["Hora"];
                        if ($HoraEvento == "00:00:00") {
                            $evento["Hora"] = "";
                        } else {
                            $evento["Hora"] = (string) $r["Hora"];
                        }

                        $evento["Valor"] = $r["Valor"];
                        $evento["EmailContacto"] = $r["EmailContacto"];
                        $evento["InscripcionApp"] = $r["InscripcionApp"];

                        if ($r["InscripcionApp"] == "S") {
                            //Verifico si quedan cupos
                            $sql_registrados = "SELECT count(*) as Total FROM  EventoRegistro" . $Version . " Where IDEvento" . $Version . " = '" . $r["IDEvento"] . "'";
                            $r_registrados = $dbo->query($sql_registrados);
                            $row_registrados = $dbo->FetchArray($r_registrados);
                            if ((int) $row_registrados["Total"] >= (int) $r["MaximoParticipantes"]) {
                                $evento["InscripcionApp"] = "N";
                                $evento["Cuerpo"] .= "<br><strong>Se lleg&oacute; al maximo de cupos</strong>";
                            }

                            //verifico  la fecha/hora límite de inscripcion
                            $fechahora_actual = date("Y-m-d H:i:s");
                            $FechaHoraLimite = $r["FechaLimiteInscripcion"] . " " . $r["HoraLimiteInscripcion"];
                            if ($FechaHoraLimite != "0000-00-00" && strtotime($fechahora_actual) > strtotime($FechaHoraLimite)) {
                                $evento["InscripcionApp"] = "N";
                                $evento["Cuerpo"] .= "<br><strong>Inscripciones cerradas</strong>";
                            }

                        }

                        $evento["MensajePagoInscripcion"] = $r["MensajePagoInscripcion"];
                        //Tipos de pagos recibidos
                        $response_tipo_pago = array();
                        $sql_tipo_pago = "SELECT * FROM EventoTipoPago" . $Version . " ETP, TipoPago TP  WHERE ETP.IDTipoPago = TP.IDTipoPago and IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' ";
                        $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                        if ($dbo->rows($qry_tipo_pago) > 0) {
                            $evento["PagoReserva"] = "S";
                            while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                                $tipopago["IDClub"] = $IDClub;
                                $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                                $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                                $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                                $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);

                                if ($IDClub == 93 && $r_tipo_pago["IDTipoPago"] == 3) {
                                    $tipopago["Nombre"] = "Pago en recepción";
                                } else {
                                    $tipopago["Nombre"] = $r_tipo_pago["Nombre"];
                                }

                                $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                                $imagen = "";
                                //Para el condado y es pagos online muestro la imagen de placetopay
                                switch ($r_tipo_pago["IDTipoPago"]) {
                                    case "1":
                                        $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                        break;
                                    case "2":
                                        $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                        break;
                                    case "3":
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;
                                    case "11":
                                        $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                        break;
                                    default:
                                        $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                        break;
                                }

                                $tipopago["Imagen"] = $imagen;
                                array_push($response_tipo_pago, $tipopago);

                            } //end while
                            $evento["TipoPago"] = $response_tipo_pago;
                        } else {
                            $evento["PagoReserva"] = "N";
                        }

                        //Campos Formulario
                        $response_campo_formulario = array();
                        $sql_campo_form = "SELECT * FROM CampoFormularioEvento" . $Version . " WHERE IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' and Publicar = 'S' order by Orden ";
                        $qry_campo_form = $dbo->query($sql_campo_form);
                        if ($dbo->rows($qry_campo_form) > 0) {
                            while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                                $campoformulario["IDCampoFormularioEvento"] = $r_campo["IDCampoFormularioEvento" . $Version];
                                $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                                $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                                $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                                $campoformulario["Valores"] = $r_campo["Valores"];
                                array_push($response_campo_formulario, $campoformulario);

                            } //end while
                            $evento["CampoFormulario"] = $response_campo_formulario;
                        } else {
                            $evento["CampoFormulario"] = $response_campo_formulario;
                        }

                        if (!empty($r["EventoFile"])):
                            if (strstr(strtolower($r["EventoFile"]), "http://")) {
                                $foto1 = $r["EventoFile"];
                            } elseif (strstr(strtolower($r["EventoFile"]), "https://")) {
                            $foto1 = $r["EventoFile"];
                        } else {
                            $foto1 = IMGEVENTO_ROOT . $r["EventoFile"];
                        }

                        //$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
                        else :
                            $foto1 = "";
                        endif;

                        if (!empty($r["FotoDestacada"])):
                            if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                                $foto2 = $r["FotoDestacada"];
                            } elseif (strstr(strtolower($r["FotoDestacada"]), "https://")) {
                            $foto2 = $r["FotoDestacada"];
                        } else {
                            $foto2 = IMGEVENTO_ROOT . $r["FotoDestacada"];
                        }

                        //$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];
                        else :
                            $foto2 = "";
                        endif;

                        $evento["Foto"] = $foto1;
                        $evento["Foto2"] = $foto2;

                        array_push($response, $evento);

                    } //ednw hile

                } //End if 
            */

            if (count($resultado) <= 0 && $dbo->rows($qry) <= 0) :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            endif;

            return $respuesta;
        }
    } // fin function

    public function categoria_comunidad_al_aire($IDClub, $IDSocio = "", $IDUsuario = "", $TipoApp = "", $Version = "")
    {

        $dbo = &SIMDB::get();
        $curl = curl_init();
        $response = array();

        $Token = self::Token();

        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FEDEGOLF . '/services/apexrest/ConsultaCategComunidadAire',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $Token,
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);
            // echo $response_ws;
            curl_close($curl);
            $resultado = json_decode($response_ws);

            if (count($resultado) > 0) :
                foreach ($resultado->CategoriasResult as $id => $Categoria) :

                    $seccion["IDClub"] = $IDClub;
                    $seccion["IDSeccion"] = $Categoria->NombreCategoria;
                    $seccion["Nombre"] = $Categoria->NombreCategoria;
                    $seccion["Descripcion"] = $Categoria->NombreCategoria;
                    array_push($response, $seccion);

                endforeach;

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
    }

    public function comunidad_al_aire($IDClub, $IDSeccion = "", $IDSocio = "", $Tag = "", $Version = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();
        $response = array();

        $datos_config_not = $dbo->fetchAll("ConfiguracionNoticias", " IDClub = '" . $IDClub . "' ", "array");

        if (empty($IDSeccion)) :
            $IDSeccion = "TODOS";
        endif;

        $IDSeccion = str_replace("&amp;", "&", $IDSeccion);

        $POST = '{
            "req": {
                "Categoria" : "' . $IDSeccion . '"
            }
        }';
        $Token = self::Token();


        if ($Token != "errortoken") {
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FEDEGOLF . '/services/apexrest/ConsultaContenidoComunidadAire',
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
                    'Content-Type: application/json',
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));

            $response_ws = curl_exec($curl);

            // echo $response_ws;
            // exit;
            curl_close($curl);
            $resultado = json_decode($response_ws);

            // print_r($resultado);
            if (!empty($IDSeccion)) :
                if (count($resultado) > 0) :
                    foreach ($resultado->PublicsResult as $id => $Noticia) :
                        if ($Noticia->Respuesta->Mensaje == "OK") :
                            $noticia["IDClub"] = $IDClub;
                            $noticia["IDSeccion"] = $Noticia->Capsula_Categoria;
                            $noticia["IDNoticia"] = "";
                            $noticia["Titular"] = $Noticia->Capsula_Nombre;
                            $noticia["Introduccion"] = $Noticia->Capsula_Desc_Corta;

                            if (!empty($Noticia->Contenido_URL_Video)) :
                                $Video = '<p><iframe width="100%" height="315" src="' . $Noticia->Contenido_URL_Video . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></p>';
                            endif;

                            $Imagen1 = $Noticia->Contenido_Imagen;

                            /*  if($pos === false):
                                $ImagenCuerpo = $Noticia->Contenido_Imagen;
                            else: */

                            if (!empty($Imagen1)) :

                                $array_datos_imagen1 = explode("src", $Imagen1);
                                $Imagen1 = $array_datos_imagen1[1];
                                $Imagen1 = str_replace('="', "", $Imagen1);
                                $pos1 = strpos($Imagen1, '"');
                                if ($pos1 === false) {
                                    $Imagen1 = "";
                                } else {
                                    $Imagen1 = substr($Imagen1, 0, $pos1);
                                }

                                /*  $Imagen1 = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen1);
                                $Imagen1 = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen1); */

                                $Imagen1 = str_replace("&amp;", "&", $Imagen1);


                                $ImagenCuerpo = '<p><img src="' . URL_FEDEGOLF . $Imagen1 . '"></img></p>';

                            endif;
                            /* endif; */

                            $Cuerpo =  $Noticia->Capsula_Desc_Corta . "<br>" . $ImagenCuerpo . "<br>" . $Video . "<br>" . $Noticia->Contenido_Desc_Larga;

                            $noticia["Cuerpo"] = $Cuerpo;

                            $noticia["Fecha"] = "";

                            $Imagen = $Noticia->Capsula_Imagen;

                            $array_datos_imagen = explode("src", $Imagen);
                            $Imagen = $array_datos_imagen[1];
                            $Imagen = str_replace('="', "", $Imagen);
                            $pos = strpos($Imagen, '"');
                            if ($pos === false) {
                                $Imagen = "";
                            } else {
                                $Imagen = substr($Imagen, 0, $pos);
                            }

                            $Imagen = str_replace("&amp;", "&", $Imagen);

                            $pos1 = strpos($Imagen, 'https://fedegolf--c.documentforce.com');

                            if ($pos1 === false) :
                            else :
                                $Imagen = str_replace("https://fedegolf--fedegolfsb--c.documentforce.com", "https://fedegolfsb-fedegolf.cs16.force.com/", $Imagen);
                                $Imagen = str_replace("https://fedegolf--c.documentforce.com", "https://www.federacioncolombianadegolf.com", $Imagen);
                            endif;

                            $noticia["Foto"] = $Imagen;
                            $noticia["Foto2"] = "";
                            $noticia["FotoPortada"] = /* URL_FEDEGOLF . */ $Imagen;

                            array_push($response, $noticia);
                        endif;
                    endforeach;
                endif;
            endif;

            if (count($resultado) <= 0) :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            endif;

            return $respuesta;
        }
    }
} //end class
