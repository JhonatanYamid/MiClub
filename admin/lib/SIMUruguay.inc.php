<?php
class SIMUruguay
{

    public function valida_socio_uruguay($id_club, $email, $clave)
    {

        $dbo = &SIMDB::get();

        $curl = curl_init();
        $autenticar = base64_encode(USUARIO_URUGUAY . ":" . CLAVE_URUGUAY);

        $email = str_replace(".", "", $email);
        $email = str_replace("-", "", $email);
        $email = str_replace(" ", "", $email);

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_URUGUAY . 'WSSincronizacion?vista=resp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
		    <Body>
		        <procesarAlta xmlns="http://nodum.com.uy/soap/schemas/forms/v1.2/WSSincronizacion/resp">
		            <WSSincronizacion>
		                <General>
		                    <G1>
		                        <!--Usuario-->
		                        <Usuario>' . $email . '</Usuario>
		                        <!--Fecha Nacimiento-->
		                        <AnoNacimiento>' . $clave . '</AnoNacimiento>
		                    </G1>
		                </General>
		            </WSSincronizacion>
		        </procesarAlta>
		    </Body>
		</Envelope>',
            CURLOPT_HTTPHEADER => array(
                'SOAPAction: procesarAlta',
                'Authorization: Basic ' . $autenticar,
                'Content-Type: application/xml',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //echo $response;

        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $vals, $index);
        xml_parser_free($p);

        /*
        echo "<br><br>";
        echo "Estado:";
        print_r($vals[4]["value"]);
        echo " Mensaje:";
        print_r($vals[5]["value"]);
        echo " Identificador:";
        print_r($vals[6]["value"]);
        echo " Nombre:";
        print_r($vals[10]["value"]);
        echo " Apellido:";
        print_r($vals[12]["value"]);
        echo " Direccion:";
        print_r($vals[11]["value"]);
        echo " Num telefono:";
        print_r($vals[13]["value"]);
        echo " Correo:";
        print_r($vals[14]["value"]);
        echo " Administrativo:";
        print_r($vals[15]["value"]);
         */


        $EstadoPeticion = $vals[4]["value"];
        $MensajePeticion = $vals[5]["value"];
        $IdentificadorPeticion = $vals[6]["value"];
        $NombreSocio = $vals[10]["value"];
        $DireccionSocio = $vals[11]["value"];
        //$ApellidoSocio=$vals[12]["value"];
        $AccionSocio = $vals[12]["value"];
        $TelefonoSocio = $vals[13]["value"];
        $CorreoSocio = $vals[14]["value"];
        $Administrativo = $vals[15]["value"];
        $Celular = $vals[16]["value"];

        $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $AccionSocio . "' and IDClub = '" . $id_club . "' ", "array");

        if ($EstadoPeticion == "0") {
            if ((int) $datos_socio["IDSocio"] <= 0) {
                $sql_crea_socio = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil)
													 VALUES ('" . $id_club . "',1,'" . $AccionSocio . "','" . $AccionSocio . "' ,'" . $AccionSocio . "','" . trim($NombreSocio) . "','" . trim($ApellidoSocio) . "','" . trim($email) . "','" . $email . "',sha1('" . $clave . "'), '" . trim($CorreoSocio) . "',
													 NOW(),'WebServiceUruguay','Socio','S','S','S')";
                $dbo->query($sql_crea_socio);
            } else {
                $sql_actualiza_socio = "UPDATE Socio
																SET IDEstadoSocio=1,
																Nombre='" . $NombreSocio . "',
																NumeroDocumento='" . trim($email) . "',
																Email='" . trim($email) . "',
																Clave=sha1('" . $clave . "'),
																CorreoElectronico='" . trim($CorreoSocio) . "',
																FechaTrEd=NOW(),
																UsuarioTrEd='WebServiceEdita',
																TipoSocio='Socio',
																PermiteReservar='S',
																CambioClave='S',
																SolicitaEditarPerfil='S'
																WHERE IDSocio = '" . $datos_socio["IDSocio"] . "' ";
                $dbo->query($sql_actualiza_socio);
            }

            $respuesta["estado"] = "ok";
            $respuesta["mensaje"] = SIMUtil::get_traduccion('', '', 'creadocorrectamente', LANG);
        } else {
            $respuesta["mensaje"] = SIMUtil::get_traduccion('', '', 'Estimadosocio,afindepodercompletarlainstalacióndelaApp,lesolicitamosponerseencontactoconAtenciónalSocioatravésdelnúmero27101721,interno132oenviaruncorreoconsusdatosalcorreooperaciones@cgu.uy.Nuestrohorariodeatenciónparalasrespuestasdesusolicitudserádelunesaviernesde9:00a17:00horasysábadode9:00a13:00horas.', LANG);
        }

        return $respuesta;
    }

    public function actualiza_socio_uruguay($IDClub, $IDSocio, $datos_campos)
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Mensaje = "";
        $contador_hijo = 0;
        if (count($datos_campos) > 0) {
            $contador_array = 0;
            foreach ($datos_campos as $detalle_campo) {
                $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                $valor = trim($detalle_campo["Valor"]);
                switch ($IDCampo) {
                    case '436':
                        $NombreSocio = $valor;
                        break;
                    case '437':
                        $SegNombreSocio = $valor;
                        break;
                    case '438':
                        $ApellidoSocio = $valor;
                        break;
                    case '439':
                        $SegApellidoSocio = $valor;
                        break;
                    case '440':
                        $FechaNacimiento = $valor;
                        break;
                    case '441':
                        $CorreoSocio = $valor;
                        break;
                    case '442':
                        $TelefonoSocio = $valor;
                        break;
                    case '443':
                        $DireccionSocio = $valor;
                        break;
                    case '479':
                        $VinculadoSocio = $valor;
                        break;
                    case '1271':
                        $NombreCentroDeSalud = $valor;
                        break;
                    case '1272':
                        $FechaDeVencimientoCarnet = $valor;
                        break;
                }

                $NombreCompleto = $NombreSocio . " " . $SegNombreSocio;
                $ApellidoCompleto = $ApellidoSocio . " " . $SegApellidoSocio;
                $datos_actualizar = " Nombre='" . $NombreCompleto . "', Apellido='" . $ApellidoCompleto . "', Direccion = '" . $DireccionSocio . "', Celular='" . $TelefonoSocio . "', CorreoElectronico = '" . $CorreoSocio . "', FechaNacimiento = '" . $FechaNacimiento . "', ClaveSistemaExterno = '" . $VinculadoSocio . "' ";

                //Actualizo los datos del socio
                $sql_soc = "UPDATE Socio SET $datos_actualizar WHERE IDSocio = '" . $IDSocio . "' ";
                $dbo->query($sql_soc);

                //Hijos
                if ($IDCampo == 457 && !empty($valor)) {
                    $array_hijos[$contador_hijo]["Cedula"] = $valor;
                    $array_hijos[$contador_hijo]["Nombre"] = $datos_campos[((int) $contador_array + 1)]["Valor"];
                    $array_hijos[$contador_hijo]["FechaNacimiento"] =  $datos_campos[((int) $contador_array + 2)]["Valor"];
                    $array_hijos[$contador_hijo]["NombreCentroDeSalud"] = !empty($datos_campos[((int) $contador_array + 3)]["Valor"]) ? $datos_campos[((int) $contador_array + 3)]["Valor"] : "-";
                    $array_hijos[$contador_hijo]["FechaVencimientoCarnet"] = !empty($datos_campos[((int) $contador_array + 4)]["Valor"]) ? $datos_campos[((int) $contador_array + 4)]["Valor"] : "1900-01-01";
                } elseif ($IDCampo == 458 && !empty($valor)) {
                    $array_hijos[$contador_hijo]["Cedula"] = $valor;
                    $array_hijos[$contador_hijo]["Nombre"] = $datos_campos[((int) $contador_array + 1)]["Valor"];
                    $array_hijos[$contador_hijo]["FechaNacimiento"] =   $datos_campos[((int) $contador_array + 2)]["Valor"];
                    $array_hijos[$contador_hijo]["NombreCentroDeSalud"] = !empty($datos_campos[((int) $contador_array + 3)]["Valor"]) ? $datos_campos[((int) $contador_array + 3)]["Valor"] : "-";
                    $array_hijos[$contador_hijo]["FechaVencimientoCarnet"] = !empty($datos_campos[((int) $contador_array + 4)]["Valor"]) ? $datos_campos[((int) $contador_array + 4)]["Valor"] : "1900-01-01";
                } elseif ($IDCampo == 459 && !empty($valor)) {
                    $array_hijos[$contador_hijo]["Cedula"] = $valor;
                    $array_hijos[$contador_hijo]["Nombre"] = $datos_campos[((int) $contador_array + 1)]["Valor"];
                    $array_hijos[$contador_hijo]["FechaNacimiento"] = $datos_campos[((int) $contador_array + 2)]["Valor"];
                    $array_hijos[$contador_hijo]["NombreCentroDeSalud"] = !empty($datos_campos[((int) $contador_array + 3)]["Valor"]) ? $datos_campos[((int) $contador_array + 3)]["Valor"] : "-";
                    $array_hijos[$contador_hijo]["FechaVencimientoCarnet"] = !empty($datos_campos[((int) $contador_array + 4)]["Valor"]) ? $datos_campos[((int) $contador_array + 4)]["Valor"] : "1900-01-01";
                } elseif ($IDCampo == 460 && !empty($valor)) {
                    $array_hijos[$contador_hijo]["Cedula"] = $valor;
                    $array_hijos[$contador_hijo]["Nombre"] = $datos_campos[((int) $contador_array + 1)]["Valor"];
                    $array_hijos[$contador_hijo]["FechaNacimiento"] = $datos_campos[((int) $contador_array + 2)]["Valor"];
                    $array_hijos[$contador_hijo]["NombreCentroDeSalud"] = !empty($datos_campos[((int) $contador_array + 3)]["Valor"]) ? $datos_campos[((int) $contador_array + 3)]["Valor"] : "-";
                    $array_hijos[$contador_hijo]["FechaVencimientoCarnet"] = !empty($datos_campos[((int) $contador_array + 4)]["Valor"]) ? $datos_campos[((int) $contador_array + 4)]["Valor"] : "1900-01-01";
                } elseif ($IDCampo == 461 && !empty($valor)) {
                    $array_hijos[$contador_hijo]["Cedula"] = $valor;
                    $array_hijos[$contador_hijo]["Nombre"] = $datos_campos[((int) $contador_array + 1)]["Valor"];
                    $array_hijos[$contador_hijo]["FechaNacimiento"] = $datos_campos[((int) $contador_array + 2)]["Valor"];
                    $array_hijos[$contador_hijo]["NombreCentroDeSalud"] = !empty($datos_campos[((int) $contador_array + 3)]["Valor"]) ? $datos_campos[((int) $contador_array + 3)]["Valor"] : "-";
                    $array_hijos[$contador_hijo]["FechaVencimientoCarnet"] = !empty($datos_campos[((int) $contador_array + 4)]["Valor"]) ? $datos_campos[((int) $contador_array + 4)]["Valor"] : " 1900-01-01";
                }
                $contador_hijo++;
                $contador_array++;
            }
        }

        foreach ($array_hijos as $id_hijo => $val_hijo) {
            $fecha_nacimiento = new DateTime($val_hijo["FechaNacimiento"]);
            $FechaHoy = date("Y-m-d");
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento);
            $EdadHijo = $edad->y;
            if (!empty($val_hijo["Nombre"]) && !empty($val_hijo["Cedula"])) {
                if ($EdadHijo <= 12) {
                    if (strtotime($val_hijo["FechaNacimiento"]) <= strtotime($FechaHoy)) {
                        $xml_hijos .= '<Hijos>
					                        <Nombre_1>' . $val_hijo["Nombre"] . '</Nombre_1>
					                        <FechaNacimiento_1>' . $val_hijo["FechaNacimiento"] . '</FechaNacimiento_1>
																	<Cedula>' . $val_hijo["Cedula"] . '</Cedula>
                                                                    <Nombre_Centro_Salud>' . $val_hijo["NombreCentroDeSalud"] . '</Nombre_Centro_Salud>
                                                                    <Fecha_Vencimiento_Centro_Salud>' . $val_hijo["FechaVencimientoCarnet"] . '</Fecha_Vencimiento_Centro_Salud>
					                    </Hijos>';

                        $DocHijo = $val_hijo["Cedula"];
                        //Verifico que el hijo no exista
                        $sql_hijo = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DocHijo . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                        $r_hijo = $dbo->query($sql_hijo);
                        $row_hijo = $dbo->fetchArray($r_hijo);
                        if ((int) $row_hijo["IDSocio"] <= 0) {
                            $sql_crea_socio = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil,FechaNacimiento)
																		 VALUES ('" . $IDClub . "',1,'" . $datos_socio["Accion"] . "','" . $datos_socio["Accion"] . "' ,'" . $datos_socio["Accion"] . "','" . trim($val_hijo["Nombre"]) . "','','" . $DocHijo . "','',sha1('" . $val_hijo["Nombre"] . "'), '',
																		 NOW(),'WebServiceUruguay','Beneficiario','S','S','N','" . $val_hijo["FechaNacimiento"] . "')";
                            $dbo->query($sql_crea_socio);
                        } else {
                            $actualiza_soc = "UPDATE Socio SET Nombre='" . trim($val_hijo["Nombre"]) . "', FechaNacimiento = '" . $val_hijo["FechaNacimiento"] . "' WHERE IDSocio = '" . $row_hijo["IDSocio"] . "'";
                            $dbo->query($actualiza_soc);
                        }
                    } else {
                        $Mensaje .= SIMUtil::get_traduccion('', '', 'ElHijo', LANG) . $val_hijo["Nombre"] . SIMUtil::get_traduccion('', '', 'tieneunafechainvalida', LANG);
                    }
                } else {
                    $Mensaje .= SIMUtil::get_traduccion('', '', 'ElHijo', LANG) . $val_hijo["Nombre"] . SIMUtil::get_traduccion('', '', 'nocumpleconlaedadrequerida', LANG);
                }
            }
        }

        $curl = curl_init();
        $autenticar = base64_encode(USUARIO_URUGUAY . ":" . CLAVE_URUGUAY);

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_URUGUAY . 'WSActualizar?wsdl&vista=resp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
				<Body>
					<procesarAlta xmlns="http://nodum.com.uy/soap/schemas/forms/v1.2/WSActualizar/resp">
						<WSActualizar>
							<General>
								<G1>
									<!--Usuario-->
									<Usuario>' . $datos_socio["NumeroDocumento"] . '</Usuario>
									<!--Nombre-->
									<Nombre>' . $NombreSocio . '</Nombre>
									<!--Segundo Nombre-->
									<SegundoNombre>' . $SegNombreSocio . '</SegundoNombre>
									<!--Apellido-->
									<Apellido>' . $ApellidoSocio . '</Apellido>
									<!--Segundo Apellido-->
									<SegundoApellido>' . $SegApellidoSocio . '</SegundoApellido>
									<!--Dirección-->
									<Dirección>' . $DireccionSocio . '</Dirección>
									<!--Nro de Teléfono-->
									<NrodeTeléfono>' . $TelefonoSocio . '</NrodeTeléfono>
									<!--Dirección de E-Mail-->
									<DireccióndeE-Mail>' . $CorreoSocio . '</DireccióndeE-Mail>
															<!--Socio Vinculado-->
									<Sociovinculado>' . $VinculadoSocio . '</Sociovinculado>
                                    <!--Nombre Centro De Salud-->
									<NombreCentroDeSalud>' . $NombreCentroDeSalud . '</NombreCentroDeSalud>
                                    <!--Fecha de vencimiento Carnet De Salud-->
									<FechaVencimientoCentroDeSalud>' . $FechaDeVencimientoCarnet . '</FechaVencimientoCentroDeSalud>
								</G1>
													' . $xml_hijos . '
							</General>
						</WSActualizar>
					</procesarAlta>
				</Body>
			</Envelope>',
            CURLOPT_HTTPHEADER => array(
                'SOAPAction: procesarAlta',
                'Authorization: Basic ' . $autenticar,
                'Content-Type: application/xml',
            ),
        ));
        /* $XML = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
        <Body>
            <procesarAlta xmlns="http://nodum.com.uy/soap/schemas/forms/v1.2/WSActualizar/resp">
                <WSActualizar>
                    <General>
                        <G1>
                            <!--Usuario-->
                            <Usuario>' . $datos_socio["NumeroDocumento"] . '</Usuario>
                            <!--Nombre-->
                            <Nombre>' . $NombreSocio . '</Nombre>
                            <!--Segundo Nombre-->
                            <SegundoNombre>' . $SegNombreSocio . '</SegundoNombre>
                            <!--Apellido-->
                            <Apellido>' . $ApellidoSocio . '</Apellido>
                            <!--Segundo Apellido-->
                            <SegundoApellido>' . $SegApellidoSocio . '</SegundoApellido>
                            <!--Dirección-->
                            <Dirección>' . $DireccionSocio . '</Dirección>
                            <!--Nro de Teléfono-->
                            <NrodeTeléfono>' . $TelefonoSocio . '</NrodeTeléfono>
                            <!--Dirección de E-Mail-->
                            <DireccióndeE-Mail>' . $CorreoSocio . '</DireccióndeE-Mail>
                                                    <!--Socio Vinculado-->
                            <Sociovinculado>' . $VinculadoSocio . '</Sociovinculado>
                            <!--Nombre Centro De Salud-->
                            <NombreCentroDeSalud>' . $NombreCentroDeSalud . '</NombreCentroDeSalud>
                            <!--Fecha de vencimiento Carnet De Salud-->
                            <FechaVencimientoCentroDeSalud>' . $FechaDeVencimientoCarnet . '</FechaVencimientoCentroDeSalud>
                        </G1>
                                            ' . $xml_hijos . '
                    </General>
                </WSActualizar>
            </procesarAlta>
        </Body>
    </Envelope>';
        echo "Hola curl:";
        echo $XML;*/

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        //$p = xml_parser_create();
        //xml_parse_into_struct($p, $response, $vals, $index);
        //xml_parser_free($p);

        return $Mensaje;
    }

    public function sincroniza_socio($NumeroDocumento, $IDSocio, $IDClub)
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();
        $autenticar = base64_encode(USUARIO_URUGUAY . ":" . CLAVE_URUGUAY);

        $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
        $NumeroDocumento = str_replace("-", "", $NumeroDocumento);
        $NumeroDocumento = str_replace(" ", "", $NumeroDocumento);

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_URUGUAY . 'WSConsultar?wsdl&vista=resp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',

            CURLOPT_POSTFIELDS => '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
			    <Body>
			        <procesarAlta xmlns="http://nodum.com.uy/soap/schemas/forms/v1.2/WSConsultar/resp">
			            <WSConsultar>
			                <General>
			                    <G1>
			                        <!--Usuario-->
			                        <Usuario>' . $NumeroDocumento . '</Usuario>
			                    </G1>
			                </General>
			            </WSConsultar>
			        </procesarAlta>
			    </Body>
			</Envelope>',
            CURLOPT_HTTPHEADER => array(
                'SOAPAction: procesarAlta',
                'Authorization: Basic ' . $autenticar,
                'Content-Type: application/xml',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $vals, $index);
        xml_parser_free($p);

        echo "estado " . $EstadoPeticion = $vals[4]["value"];
        $MensajePeticion = $vals[5]["value"];
        $IdentificadorPeticion = $vals[6]["value"];
        $NombreSocio = $vals[10]["value"];
        $AccionSocio = $vals[11]["value"];
        $DireccionSocio = $vals[12]["value"];
        $TelefonoSocio = $vals[13]["value"];
        $CorreoSocio = $vals[14]["value"];
        $Celular = $vals[15]["value"];
        $Administrativo = $vals[16]["value"];
        //Hijos
        for ($i = 19; $i <= 74; $i += 11) {
            $IDSocioHijo = $vals[$i]["value"];
            $NombreHijo = $vals[$i + 1]["value"];
            $ApellidoHijo = $vals[$i + 2]["value"];
            $Nombre2Hijo = $vals[$i + 3]["value"];
            $Apellido2Hijo = $vals[$i + 4]["value"];
            $DireccionHijo = $vals[$i + 5]["value"];
            $FehaNacimientoHijo = $vals[$i + 6]["value"];
            $CelularHijo = $vals[$i + 7]["value"];
            $TelefonoHijo = $vals[$i + 8]["value"];
            $NombreCompletoHijo = $NombreHijo . " " . $Nombre2Hijo;
            $ApellidoCompletoHijo = $ApellidoHijo . " " . $Apellido2Hijo;
            if (!empty($NombreHijo)) {
                $sql_crea_hijo = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil)
														 VALUES ('" . $IDClub . "',1,'" . $AccionSocio . "','" . $AccionSocio . "' ,'" . $IDSocioHijo . "','" . trim($NombreCompletoHijo) . "','" . trim($ApellidoCompletoHijo) . "','" . trim($IDSocioHijo) . "','" . $IDSocioHijo . "',sha1('" . $IDSocioHijo . "'), '" . trim($CorreoSocio) . "',
														 NOW(),'WebServiceUruguay','Socio','S','S','S')";
                //$dbo->query( $sql_crea_hijo );
                echo "<br>Crear: " . $sql_crea_hijo;
            }
        }

        if ($Administrativo == "S") {
            $IDEstadoSocio = 1;
        } else {
            $IDEstadoSocio = 2;
        }

        if ($EstadoPeticion == "0") {

            $actualiza_socio = "Update Socio
									set IDEstadoSocio = '" . $IDEstadoSocio . "',
									Accion = '" . $AccionSocio . "',
									AccionPadre='" . $AccionSocio . "',
									Celular = '" . $Celular . "',
									Nombre = '" . trim($NombreSocio) . "',
									CorreoElectronico = '" . $CorreoSocio . "',
									UsuarioTrEd = 'Cron',
									FechaTrEd = NOW()
									Where IDSocio = '" . $IDSocio . "'";
            //$dbo->query($actualiza_socio);
            echo "<br>" . $actualiza_socio;
        }

        return $Mensaje;
    }

    public function consulta_base_completa()
    {
        $dbo = &SIMDB::get();
        $curl = curl_init();
        $IDClub = 125;
        $sql_socios = "SELECT IDSocio,NumeroDocumento,Accion FROM Socio WHERE IDClub = '" . $IDClub . "'";
        $r_socios = $dbo->query($sql_socios);
        while ($row_socios = $dbo->fetchArray($r_socios)) {
            $array_socios[$row_socios["Accion"]] = $row_socios["IDSocio"];
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_URUGUAY . 'WSListadoCliente?wsdl&vista=resp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
			    <Body>
			        <procesarAlta xmlns="http://nodum.com.uy/soap/schemas/forms/v1.2/WSListadoCliente/resp">
			            <WSListadoCliente/>
			        </procesarAlta>
			    </Body>
			</Envelope>',
            CURLOPT_HTTPHEADER => array(
                'SOAPAction: procesarAlta',
                'Authorization: Basic MTA5YXBwczoxMDlhcHBz',
                'Content-Type: application/xml',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response) {
            //$sql_estado = "UPDATE Socio SET IDEstadoSocio = 2 WHERE IDClub = '" . $IDClub . "' and IDSocio <> 393819 ";
            // $dbo->query($sql_estado);
        }

        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $vals, $index);
        xml_parser_free($p);

        //  print_r($vals);
        //exit;

        for ($i = 15; $i <= 70000; $i += 16) {

            $IDEstadoSocio = "";
            $IDSocio = $vals[$i]["value"];
            echo "<br><br>";
            $Nombre = $vals[$i + 1]["value"];
            $Apellido = $vals[$i + 2]["value"];
            $Nombre2 = $vals[$i + 3]["value"];
            $Apellido2 = $vals[$i + 4]["value"];
            $NombreCompleto = $Nombre . " " . $Nombre2;
            $ApellidoCompleto = $Apellido . " " . $Apellido2;
            $Direccion = $vals[$i + 5]["value"];
            $FechaNacimiento = $vals[$i + 6]["value"];
            $Celular = $vals[$i + 7]["value"];
            $Telefono = $vals[$i + 8]["value"];
            $Cedula = $vals[$i + 9]["value"];
            $Administrativo = $vals[$i + 10]["value"];
            $Categoria = $vals[$i + 11]["value"];
            $SubCategoria = $vals[$i + 12]["value"];
            $Matricula = $vals[$i + 13]["value"];



            $IDSocioMiClub = $array_socios[$IDSocio];


            $sqlCategoria = "SELECT IDCategoria FROM Categoria WHERE Nombre = '" . $SubCategoria . "'";
            $qryCategoria = $dbo->query($sqlCategoria);
            $datoC = $dbo->fetchArray($qryCategoria);

            if (empty($datoC["IDCategoria"])) {

                $sqlInsertCat = "INSERT INTO Categoria (IDClub, Nombre, Descripcion, Publicar, UsuarioTrCr, FechaTrCr)
                                VALUES ('" . $IDClub . "', '" . $SubCategoria . "','" . $SubCategoria . "', 'S', 'CRON', NOW())";
                $qryInsert = $dbo->query($sqlInsertCat);
                $IDCategoria = $dbo->lastID();

                $SqlInsertCC = "INSERT INTO ClubCategoria (IDClub, IDCategoria) VALUES ('" . $IDClub . "','" . $IDCategoria . "')";
                $qryInsertCC = $dbo->query($SqlInsertCC);
            } else {
                $IDCategoria = $datoC["IDCategoria"];
            }
            /* 
            echo "IDSocio1=" . $IDSocio;
            echo "Nombre1=" . $Nombre;
            echo "FechaNacimiento=" . $FechaNacimiento;
            echo "Apellido1=" . $Apellido;
            echo "Apellido2=" . $Apellido2;
            echo "Cedula=" . $Cedula;
            echo "IDSocioMiClub=" . $IDSocioMiClub; */
            if ((int) $IDSocioMiClub <= 0 && (int) $IDSocio > 0 && !empty($Nombre) && !empty($FechaNacimiento) && !empty($Apellido) && !empty($Cedula)) {

                $sql_crea_socio = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio,PermiteReservar, CambioClave, SolicitaEditarPerfil,FechaNacimiento,Celular, IDCategoria,Predio)
                                        VALUES ('$IDClub',1,'$IDSocio','$IDSocio' ,'$IDSocio','" . trim($NombreCompleto) . "','" . trim($ApellidoCompleto) . "','$IDSocio','$IDSocio',sha1('" . $IDSocio . "'), '',
                                        NOW(),'WebServiceUruguay','Socio','S','S','S','$FechaNacimiento','$Celular', '$IDCategoria','$Matricula')";


                $dbo->query($sql_crea_socio);
                /* echo "<br>" . "Sql crea socio:" . $sql_crea_socio; */
            } elseif ((int) $IDSocioMiClub > 0) {
                $actualizados++;

                if ($Categoria == 3 || ($Categoria == 1 && $Administrativo != "A")) { //Ya no son socios
                    $IDEstadoSocio = 2;
                    $actualiza_estado = "UPDATE Socio SET IDEstadoSocio = '" . $IDEstadoSocio . "' WHERE Accion = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($actualiza_estado);
                    /*    echo "<br>" . " Ya no son socios:" . $actualiza_estado .  "ADMIN " . $Administrativo; */
                }



                if ($Administrativo == "A" && $Categoria == 1) {
                    $IDEstadoSocio = 1;
                    $actualiza_estado_activo = "UPDATE Socio SET IDEstadoSocio = '" . $IDEstadoSocio . "' WHERE Accion = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($actualiza_estado_activo);
                    /*   echo "<br>" . "Socios Activos:" . $actualiza_estado_activo; */
                }

                // echo "<br>";
                $sql_actualiza = "UPDATE Socio SET IDCategoria = '$IDCategoria', Predio = '$Matricula' WHERE IDSocio = '$IDSocioMiClub'";
                /*   echo "<br>" . "Actualiza categoria:" . $sql_actualiza; */
                $dbo->query($sql_actualiza);
            }
        }

        echo "<br>" . SIMUtil::get_traduccion('', '', 'Actualizados', LANG) . ":" . $actualizados;
    }

    public function reservas_torneos($Matricula)
    {
        $curl = curl_init();



        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://200.73.132.146:2020/api/Ext/ListarParticipaciones/cgu,' . $Matricula,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $Datos = json_decode($response, true);
        return $Datos;
    }

    public function get_reservas_socio($IDClub, $IDSocio, $Limite = 0, $IDReserva = "", $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        $response = array();


        $array_id_consulta[] = $IDSocio;
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $IDSocio");

        $socio_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
        // Si esta en blanco quiere decir que es socio cabeza y debe consultar las reservas de sus beneficiarios
        if ($socio_padre == "") :
            $accion_padre = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            $sql_beneficiarios = "SELECT * FROM Socio WHERE ( AccionPadre = '" . $accion_padre . "' and AccionPadre <> '') and IDClub = '" . $IDClub . "' ORDER BY Nombre Desc ";
            $qry_beneficiarios = $dbo->query($sql_beneficiarios);
            while ($r_beneficiario = $dbo->fetchArray($qry_beneficiarios)) :
                $array_id_consulta[] = $r_beneficiario[IDSocio];
            endwhile;
        endif;

        if (count($array_id_consulta) > 0 && empty($IDReserva)) :
            $where_beneficiario = "and (IDSocio in (" . implode(",", $array_id_consulta) . ") or IDSocioBeneficiario in (" . implode(",", $array_id_consulta) . ") or IDSocioReserva = '" . $IDSocio . "')";
        endif;

        if (!empty($IDReserva)) {
            $condicion_reserva = " and IDReservaGeneral = '" . $IDReserva . "' ";
        }

        if ($Limite != 0) {
            $condicion_limite = " Limit " . $Limite;
        }

        if (empty($IDUsuario)) {
            $condicion_fecha = " and Fecha >= CURDATE() ";
        } else {
            $condicion_fecha = "  ";
        }

        $sql = "SELECT * FROM ReservaGeneral WHERE (IDClub = '$IDClub' OR IDClubOrigen = $IDClub) and IDEstadoReserva = 1  " . $condicion_fecha . $where_beneficiario . " " . $condicion_reserva . "ORDER BY Fecha ASC, Hora ASC  " . $condicion_limite;
        $qry = $dbo->query($sql);

        $Matricula = $datos_socio[AccionPadre];
        $Torneos = SIMUruguay::reservas_torneos($Matricula);

        if ($dbo->rows($qry) > 0 || count($Torneos[Objeto]) > 0) :

            if (count($Torneos[Objeto]) > 0) :
                foreach ($Torneos[Objeto] as $id => $Torneo) :

                    $reserva[IDSocio] = $IDSocio;
                    $reserva[IDClub] = $IDClub;
                    $reserva[NombreServicio] = $Torneo[Nombre];
                    $reserva[Fecha] = $Torneo[Fecha];
                    $reserva[Hora] = $Torneo[Hora] . ":00";
                    $reserva["IDReserva"] = (string)$Torneo[Id];
                    $reserva["IDServicio"] =  (string)$Torneo[Id];
                    $reserva["IDElemento"] =  (string)$Torneo[Id];
                    $reserva["NombreElemento"] = $Torneo[Nombre] . " " . $Torneo[Descripcion];
                    $reserva["Tee"] = "";
                    $reserva["PermiteEditarReserva"] = "N";
                    $reserva["OcultarBotonEditarInvitados"] = "S";




                    array_push($response, $reserva);
                endforeach;
            endif;

            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                while ($row_reserva = $dbo->fetchArray($qry)) :

                    //si es el dia actual solo muestro las que estan pendientes

                    $mostra_reserva = 1;
                    $fecha_hoy = date("Y-m-d");

                    $FechaHReserva = date("Y-m-d") . " " . $row_reserva["Hora"];
                    $NuevaFechaHoraReserva = strtotime('+30 minute', strtotime($FechaHReserva));
                    $NuevaFechaHoraReserva = date("H:i:s", $NuevaFechaHoraReserva);

                    if (($row_reserva["Fecha"] == $fecha_hoy && $NuevaFechaHoraReserva <= date("H:i:s") && empty($IDUsuario))) {
                        $mostra_reserva = 0;
                        if ($dbo->rows($qry) == 1) {
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


                            if (count($Torneos) <= 0) :
                                array_push($response, $reserva);

                                $respuesta["message"] = "No tienes reservas programadas.";
                                $respuesta["success"] = true;
                                $respuesta["response"] = $response;
                                return $respuesta;
                            endif;
                        }
                    }

                    //$mostra_reserva=1;

                    if ($mostra_reserva == 1) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $row_reserva["IDServicio"] . "' ", "array");

                        // Verifico si es una reserva asociada para no mostrarla en el resultado
                        $sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '" . $row_reserva["IDReservaGeneral"] . "' and IDEstadoReserva = 1";
                        $qry_auto = $dbo->query($sql_auto);
                        if ($dbo->rows($qry_auto) <= 0) {

                            $reserva["IDClub"] = $IDClub;
                            $reserva["IDSocio"] = $row_reserva["IDSocio"];
                            if ((int) ($row_reserva["IDSocioBeneficiario"]) <= 0) {
                                $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocio"] . "'");
                            } else {
                                $reserva["Socio"] = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'");
                            }

                            $reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
                            $reserva["IDServicio"] = $row_reserva["IDServicio"];
                            $id_servicio_maestro = $datos_servicio["IDServicioMaestro"];

                            $iconoservicio = $datos_servicio["Icono"];
                            $foto = "";
                            if (!empty($iconoservicio)) {
                                $foto = SERVICIO_ROOT . $iconoservicio;
                            } else {
                                $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                                if (!empty($icono_maestro)) {
                                    $foto = SERVICIO_ROOT . $icono_maestro;
                                }
                            }

                            $reserva["Icono"] = $foto;

                            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $IDClub . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                            if (empty($nombre_servicio_personalizado)) {
                                $nombre_servicio_personalizado = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                            }

                            if ((int) $row_reserva["IDServicioTipoReserva"] > 0 && $IDClub != "9") :
                                $nombre_servicio_personalizado .= " (" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $row_reserva["IDServicioTipoReserva"] . "'") . ")";
                            endif;

                            $reserva["NombreServicio"] = $nombre_servicio_personalizado;
                            $reserva["NombreServicioPersonalizado"] = $nombre_servicio_personalizado;

                            if ((int) $row_reserva["IDSocioBeneficiario"] > 0) {
                                $socio_benef = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'"));
                                $otros_datos_reserva = "(" . $socio_benef . ")";
                            } else {
                                $otros_datos_reserva = "(" . $reserva["Socio"] . ")";
                            }

                            if (!empty($datos_servicio["IdentificadorServicio"])) {
                                $otros_datos_reserva = " " . $row_reserva["IdentificadorServicio"] . "-" . $row_reserva["ConsecutivoServicio"];
                            }

                            $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                            $reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
                            $reserva["NombreElemento"] = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'") . " " . $otros_datos_reserva;
                            $reserva["Fecha"] = $row_reserva["Fecha"];
                            $reserva["Tee"] = $row_reserva["Tee"];
                            $reserva["CantidadInvitadoSalon"] = $row_reserva["CantidadInvitadoSalon"];
                            $reserva["PagadaOnline"] = $row_reserva["Pagado"];
                            $reserva["FechaTransaccion"] = $row_reserva["FechaTransaccion"];
                            $reserva["MensajeTransaccion"] = "Mensaje transacción: " . $row_reserva["MensajeTransaccion"];

                            $reserva["LabelElementoSocio"] = utf8_encode($datos_servicio["LabelElementoSocio"]);
                            $reserva["LabelElementoExterno"] = utf8_encode($datos_servicio["LabelElementoExterno"]);
                            $reserva["PermiteEditarAuxiliar"] = $datos_servicio["PermiteEditarAuxiliar"];
                            $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                            $reserva["PermiteListaEsperaAuxiliar"] = $datos_servicio["PermiteListaEsperaAuxiliar"];
                            $reserva["PermiteEditarAdicionales"] = $datos_servicio["PermiteEditarAdicionales"];
                            $reserva["MultipleAuxiliar"] = $datos_servicio["MultipleAuxiliar"];
                            $reserva["LabelReconfimarBoton"] = $datos_servicio["LabelReconfimarBoton"];
                            $reserva["PermiteReconfirmar"] = $datos_servicio["PermiteReconfirmar"];
                            $reserva["LabelInvitados"] = $datos_servicio["LabelInvitados"];
                            $reserva["AdicionalesObligatorio"] = $datos_servicio["AdicionalesObligatorio"];

                            //Externos
                            $reserva["PermiteInvitadoExternoCedula"] = $datos_servicio["PermiteInvitadoExternoCedula"];
                            $reserva["PermiteInvitadoExternoCorreo"] = $datos_servicio["PermiteInvitadoExternoCorreo"];
                            $reserva["PermiteInvitadoExternoFechaNacimiento"] = $datos_servicio["PermiteInvitadoExternoFechaNacimiento"];
                            $reserva["InvitadoExternoPago"] = $datos_servicio["InvitadoExternoPago"];
                            $reserva["LabelInvitadoExternoPago"] = $datos_servicio["LabelInvitadoExternoPago"];
                            $reserva["InvitadoExternoValor"] = $datos_servicio["InvitadoExternoValor"];

                            // Config Eliminar
                            $reserva["EliminarParaTodosOParaMi"] = $datos_servicio["EliminarParaTodosOParaMi"];
                            $reserva["MensajeEliminarParaTodosOParaMi"] = $datos_servicio["MensajeEliminarParaTodosOParaMi"];
                            $reserva["BotonEliminarReserva"] = $datos_servicio["BotonEliminarReserva"];
                            $reserva["LabelEliminarParaMi"] = $datos_servicio["LabelEliminarParaMi"];
                            $reserva["LabelEliminarParaTodos"] = $datos_servicio["LabelEliminarParaTodos"];

                            $reserva["BotonEditarAdicionales"] = $datos_servicio["BotonEditarAdicionales"];
                            $reserva["LabelAdicionales"] = $datos_servicio["LabelAdicionales"];
                            $reserva["EncabezadoAdicionales"] = $datos_servicio["EncabezadoAdicionales"];
                            $reserva["LabelSeleccioneAdicionales"] = $datos_servicio["LabelSeleccioneAdicionales"];
                            $reserva["MensajeAdicionalesObligatorio"] = $datos_servicio["MensajeAdicionalesObligatorio"];

                            $reserva["PermiteEditarReserva"] = "N";

                            // CONFIGURACIONES CADDIES
                            $reserva[PermiteAdicionarCaddies] = $datos_servicio[PermiteEditarCaddies];
                            $reserva[LabelAdicionarCaddies] = $datos_servicio[LabelAdicionarCaddies];
                            $reserva[ObligatorioSeleccionarCaddie] = $datos_servicio[ObligatorioSeleccionarCaddie];
                            $reserva[MensajeCaddiesObligatorio] = $datos_servicio[MensajeCaddiesObligatorio];

                            $labelauxiliar = $dbo->getFields("Club", "LabelAuxiliar", "IDClub = '" . $IDClub . "'");
                            if (empty($labelauxiliar)) {
                                $labelauxiliar = $dbo->getFields("ServicioMaestro", "LabelAuxiliar", "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'");
                            }

                            $reserva["LabelAuxiliar"] = utf8_encode($labelauxiliar);

                            $response_auxiliar_reserva = array();
                            if (!empty($row_reserva["IDAuxiliar"])) :
                                $Array_Auxiliar = explode(",", $row_reserva["IDAuxiliar"]);
                                if (count($Array_Auxiliar) > 0) :
                                    foreach ($Array_Auxiliar as $id_auxiliar) :
                                        if (!empty($id_auxiliar)) :
                                            $array_datos_auxiliar["IDAuxiliar"] = $id_auxiliar;
                                            $array_datos_auxiliar["Nombre"] = $dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $id_auxiliar . "'");
                                            $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $id_auxiliar . "'");
                                            $array_datos_auxiliar["Tipo"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                                            array_push($response_auxiliar_reserva, $array_datos_auxiliar);
                                        endif;
                                    endforeach;
                                endif;

                                $reserva["ListaAuxiliar"] = $response_auxiliar_reserva;

                                $reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
                                $reserva["Auxiliar"] = utf8_encode($dbo->getFields("Auxiliar", "Nombre", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'"));
                                $id_tipo_auxiliar = $dbo->getFields("Auxiliar", "IDAuxiliarTipo", "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'");
                                $reserva["TipoAuxiliar"] = $dbo->getFields("AuxiliarTipo", "Nombre", "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'");
                            else :
                                unset($reserva['IDAuxiliar']);
                                unset($reserva['Auxiliar']);
                                unset($reserva['TipoAuxiliar']);
                                $reserva["ListaAuxiliar"] = array();
                            endif;

                            if (!empty($row_reserva["IDTipoModalidadEsqui"])) :
                                $reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
                                $reserva["Modalidad"] = $dbo->getFields("TipoModalidadEsqui", "Nombre", "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'");
                            else :
                                unset($reserva['IDTipoModalidad']);
                                unset($reserva['Modalidad']);
                            endif;

                            if (strlen($row_reserva["Hora"]) != 8) :
                                $row_reserva["Hora"] .= ":00";
                            endif;

                            $reserva["Hora"] = $row_reserva["Hora"];

                            $zonahoraria = date_default_timezone_get();
                            $offset = timezone_offset_get(new DateTimeZone($zonahoraria), new DateTime());
                            $reserva["GMT"] = SIMWebservice::timezone_offset_string($offset);

                            if ($row_reserva["IDDisponibilidad"] <= 0) :
                                $id_disponibilidad = $dbo->getFields("ServicioDisponibilidad", "IDDisponibilidad", "IDServicio = '" . $r["IDServicio"] . "' and Activo <>'N'");
                            else :
                                $id_disponibilidad = $row_reserva["IDDisponibilidad"];
                            endif;

                            $invitadoclub = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            $invitadoexterno = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");

                            if (!empty($invitadoclub)) :
                                $reserva["NumeroInvitadoClub"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoClub", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            else :
                                $reserva["NumeroInvitadoClub"] = "";
                            endif;
                            if (!empty($invitadoexterno)) :
                                $reserva["NumeroInvitadoExterno"] = $dbo->getFields("Disponibilidad", "NumeroInvitadoExterno", "IDDisponibilidad = '" . $id_disponibilidad . "'");
                            else :
                                $reserva["NumeroInvitadoExterno"] = "";
                            endif;

                            if ($row_reserva["IDInvitadoBeneficiario"] > 0) :

                                $Invitado = $dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");

                                if (empty($Invitado)) :
                                    $Invitado = $dbo->getFields("Invitado", "Nombre", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'") . " " . $dbo->getFields("Invitado", "Apellido", "IDInvitado = '" . $row_reserva["IDInvitadoBeneficiario"] . "'");
                                endif;

                                $reserva["Beneficiario"] = " Inv. " . $Invitado;

                            else :

                                if ($row_reserva["IDSocioBeneficiario"] > 0) :
                                    $Beneficiario = strtoupper(utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_reserva["IDSocioBeneficiario"] . "'")));
                                else :
                                    $Beneficiario = "";
                                endif;

                                $reserva["Beneficiario"] = " Benef. " . $Beneficiario;

                            endif;

                            if ($row_reserva[IDCaddie] > 0) :
                                // CADDIE DE LA RESERVA
                                $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $row_reserva[IDCaddie]";
                                $QRYCaddie = $dbo->query($SQLCaddie);

                                if ($dbo->rows($QRYCaddie) > 0) :

                                    while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :
                                        $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                        $Caddie[IDCaddie] = $DatoCaddie[IDCaddie];
                                        $Caddie[Nombre] = $DatoCaddie[Nombre];
                                        $Caddie[Categoria] = $datos_categoria[Categoria];
                                        $Caddie[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                        $Caddie[Precio] = $DatoCaddie[Precio];
                                        $Caddie[Disponible] = $DatoCaddie[Disponible];
                                        $Caddie[Texto] = $DatoCaddie[Descripcion];

                                    endwhile;

                                    $reserva[CaddieSocio] = $Caddie;
                                endif;
                            endif;

                            //Invitados Reserva
                            $response_invitados_reserva = array();
                            $sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'");
                            $total_invitado = $dbo->rows($sql_invitados_reserva);
                            while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)) :
                                $id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
                                $invitado_reserva[IDReservaGeneralInvitado] = $r_invitados_reserva["IDReservaGeneralInvitado"];
                                $invitado_reserva[IDSocio] = $r_invitados_reserva["IDSocio"];
                                $invitado_reserva[NombreSocio] = strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_invitados_reserva["IDSocio"] . "'"));
                                $invitado_reserva[NombreExterno] = $r_invitados_reserva["Nombre"];
                                $invitado_reserva[Correo] = $r_invitados_reserva["Correo"];
                                $invitado_reserva[Cedula] = $r_invitados_reserva["Cedula"];
                                $invitado_reserva["SeleccionadoGrupo"] = $r_invitados_reserva["Confirmado"];
                                if ($r_invitados_reserva["IDSocio"] == 0) :
                                    $tipo_invitado = "Externo";
                                else :
                                    $tipo_invitado = "Socio";
                                endif;

                                $invitado_reserva[TipoInvitado] = $tipo_invitado;

                                //Adicionales
                                $response_adicionales_inv = array();
                                $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                            FROM ReservaGeneralAdicionalInvitado RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                            WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                            IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "' and IDReservaGeneralInvitado = '" . $r_invitados_reserva["IDReservaGeneralInvitado"] . "'
                                                                                            GROUP BY IDServicioPropiedad
                                                                                            ORDER BY SP.Nombre";
                                $r_carac = $dbo->query($sql_carac);
                                while ($row_carac = $dbo->FetchArray($r_carac)) {

                                    $adicionales_inv["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                                    $adicionales_inv["EtiquetaCampo"] = $row_carac["Caracteristica"];
                                    $adicionales_inv["TipoCampo"] = $row_carac["TipoCampo"];
                                    $adicionales_inv["Valores"] = $row_carac["Valores"];
                                    $adicionales_inv["ValoresID"] = $row_carac["Valor"];
                                    $adicionales_inv["Total"] = $row_carac["Total"];
                                    array_push($response_adicionales_inv, $adicionales_inv);
                                }

                                $invitado_reserva["Adicionales"] = $response_adicionales_inv;
                                //Fin Adicionales

                                // CADDIE INVITADO

                                $SQLCaddie = "SELECT * FROM Caddie2 WHERE IDCaddie = $r_invitados_reserva[IDCaddie]";
                                $QRYCaddie = $dbo->query($SQLCaddies);

                                while ($DatoCaddie = $dbo->fetchArray($QRYCaddie)) :

                                    $datos_categoria = $dbo->fetchAll("CategoriaCaddie2", "IDCategoriaCaddie = $DatoCaddie[IDCategoriaCaddie]");

                                    $CaddieInvitado[IDCaddie] = $DatoCaddie[IDCaddie];
                                    $CaddieInvitado[Nombre] = $DatoCaddie[Nombre];
                                    $CaddieInvitado[Categoria] = $datos_categoria[Categoria];
                                    $CaddieInvitado[IDCategoria] = $DatoCaddie[IDCategoriaCaddie];
                                    $CaddieInvitado[Precio] = $DatoCaddie[Precio];
                                    $CaddieInvitado[Disponible] = $DatoCaddie[Disponible];
                                    $CaddieInvitado[Texto] = $DatoCaddie[Descripcion];

                                endwhile;

                                $invitado_reserva["Caddie"] = $CaddieInvitado;

                                array_push($response_invitados_reserva, $invitado_reserva);
                            endwhile;

                            $reserva["Invitados"] = $response_invitados_reserva;

                            //Reservas asociadas
                            $response_reserva_asociada = array();
                            $array_asociada = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $row_reserva["IDReservaGeneral"]);
                            foreach ($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva) :
                                array_push($response_reserva_asociada, $datos_reserva);
                            endforeach;
                            $reserva["ReservaAsociada"] = $response_reserva_asociada;

                            //Adicionales
                            $response_adicionales = array();
                            $sql_carac = "SELECT RGA.*,SP.Nombre as Categoria, SP.Nombre as Caracteristica,SP.Tipo as TipoCampo
                                                                                FROM ReservaGeneralAdicional RGA, ServicioPropiedad SP, ServicioAdicional SA
                                                                                WHERE  RGA.IDServicioPropiedad = SP.IDServicioPropiedad and SA.IDServicioAdicional = RGA.IDServicioAdicional and
                                                                                                IDReservaGeneral = '" . $row_reserva["IDReservaGeneral"] . "'
                                                                                GROUP BY IDServicioPropiedad
                                                                                ORDER BY SP.Nombre";
                            $r_carac = $dbo->query($sql_carac);
                            while ($row_carac = $dbo->FetchArray($r_carac)) {

                                $adicionales["IDCaracteristica"] = $row_carac["IDServicioPropiedad"];
                                $adicionales["EtiquetaCampo"] = $row_carac["Caracteristica"];
                                $adicionales["TipoCampo"] = $row_carac["TipoCampo"];
                                $adicionales["Valores"] = $row_carac["Valores"];
                                $adicionales["ValoresID"] = $row_carac["Valor"];
                                $adicionales["Total"] = $row_carac["Total"];
                                array_push($response_adicionales, $adicionales);
                            }

                            $reserva["Adicionales"] = $response_adicionales;
                            //Fin Adicionales

                            array_push($response, $reserva);
                            unset($row_reserva);
                        } // fin verificar si fue un areserva automatica
                    }

                endwhile;
            }


            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

        else :

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

            array_push($response, $reserva);
            $respuesta["message"] = "No tienes reservas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

        endif;

        return $respuesta;
    }

    public function eliminar_torneo($Matricula, $IDTorneo)
    {
        $curl = curl_init();

        $url = 'http://200.73.132.146:2020/api/Ext/CancelarParticipacion/cgu,' . $Matricula . ',' . $IDTorneo;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        // exit;
        $Datos = json_decode($response, true);
        return $Datos;
    }
} //end class
