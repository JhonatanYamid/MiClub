<?php
class SIMWebServiceVacunacion
{

    public function get_configuracion_vacunacion($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio)) {
            $Campo = "IDSocio";
            $Valor = $IDSocio;
        } else {
            $Campo = "IDUsuario";
            $Valor = $IDUsuario;
        }
        $datos_vacuna = $dbo->fetchAll("Vacuna", $Campo . " = '" . $Valor . "' ", "array");

        if (!empty($Version)) :
            $Version = " AND Version = $Version ";
        endif;

        $sql = "SELECT * FROM ConfiguracionVacunacion
							WHERE IDClub = '$IDClub' $Version";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["Version"] = $r["Version"];
                $configuracion["LabelEstaVacunado"] = $r["LabelEstaVacunado"];
                $configuracion["LabelRegistrarCitaVacuna"] = $r["LabelRegistrarCitaVacuna"];
                $configuracion["LabelRegistrarVacuna"] = $r["LabelRegistrarVacuna"];
                $configuracion["TerminosHtmlEstaVacunado"] = $r["TerminosHtmlEstaVacunado"];
                $configuracion["MostrarEstaVacunado"] = $r["MostrarEstaVacunado"];
                $configuracion["MostrarRegistrarVacuna"] = $r["MostrarRegistrarVacuna"];
                $configuracion["MostrarRegistrarCitaVacuna"] = $r["MostrarRegistrarCitaVacuna"];
                $configuracion["EstoyVacunado"] = $datos_vacuna["Vacunado"];
                $configuracion["LabelSi"] = $r["LabelSi"];
                $configuracion["LabelNo"] = $r["LabelNo"];
                $configuracion["LabelEntidadVacuna"] = $r["LabelEntidadVacuna"];
                $configuracion["LabelFechaPrimeraCita"] = $r["LabelFechaPrimeraCita"];
                $configuracion["LabelFechaSegundaCita"] = $r["LabelFechaSegundaCita"];
                $configuracion["LabelEntidadVacuno"] = $r["LabelEntidadVacuno"];
                $configuracion["LabelMarcaVacuna"] = $r["LabelMarcaVacuna"];
                $configuracion["LabelLugarVacunacion"] = $r["LabelLugarVacunacion"];

                $configuracion["LabelFechaPrimeraDosis"] = $r["LabelFechaPrimeraDosis"];
                $configuracion["LabelFechaSegundaDosis"] = $r["LabelFechaSegundaDosis"];
                $configuracion["LabelCertificadoPrimeraVacuna"] = $r["LabelCertificadoPrimeraVacuna"];
                $configuracion["LabelCertificadoSegundaVacuna"] = $r["LabelCertificadoSegundaVacuna"];
                //Tercera
                $configuracion["LabelFechaTerceraDosis"] = $r["LabelFechaTerceraDosis"];
                $configuracion["ObligatorioLugarTerceraVacuna"] = $r["ObligatorioLugarTerceraVacuna"];
                $configuracion["ObligatorioFechaTerceraVacuna"] = $r["ObligatorioFechaTerceraVacuna"];
                $configuracion["ObligatorioCertificadoTerceraVacuna"] = $r["ObligatorioCertificadoTerceraVacuna"];
                $configuracion["LabelCertificadoTerceraVacuna"] = $r["LabelCertificadoTerceraVacuna"];

                $configuracion["ObligatorioEntidadCita"] = $r["ObligatorioEntidadCita"];
                $configuracion["ObligatorioFechaPrimeraCita"] = $r["ObligatorioFechaPrimeraCita"];
                $configuracion["ObligatorioFechaSegundaCita"] = $r["ObligatorioFechaSegundaCita"];
                $configuracion["ObligatorioEntidadVacuna"] = $r["ObligatorioEntidadVacuna"];
                $configuracion["ObligatorioMarcaVacuna"] = $r["ObligatorioMarcaVacuna"];
                $configuracion["ObligatorioLugarPrimeraVacuna"] = $r["ObligatorioLugarPrimeraVacuna"];
                $configuracion["ObligatorioFechaPrimeraVacuna"] = $r["ObligatorioFechaPrimeraVacuna"];
                $configuracion["ObligatorioCertificadoPrimeraVacuna"] = $r["ObligatorioCertificadoPrimeraVacuna"];
                $configuracion["ObligatorioLugarSegundaVacuna"] = $r["ObligatorioLugarSegundaVacuna"];
                $configuracion["ObligatorioFechaSegundaVacuna"] = $r["ObligatorioFechaSegundaVacuna"];
                $configuracion["ObligatorioCertificadoSegundaVacuna"] = $r["ObligatorioCertificadoSegundaVacuna"];
                $configuracion["TipoCampoEntidad"] = $r["TipoCampoEntidad"];



                //Entidades
                $response_entidad = array();
                $sql_entidad = "SELECT IDVacunaEntidad, Nombre
													 FROM VacunaEntidad
													 WHERE IDClub  = '" . $IDClub . "' and Publicar = 'S' ";
                $r_entidad = $dbo->query($sql_entidad);
                while ($row_entidad = $dbo->fetchArray($r_entidad)) {
                    $datos_entidad["IDEntidad"] = $row_entidad["IDVacunaEntidad"];
                    $datos_entidad["Nombre"] = $row_entidad["Nombre"];
                    array_push($response_entidad, $datos_entidad);
                }
                $configuracion["Entidades"] = $response_entidad;

                $response_campo_editar = array();
                $sql_campo_editar = "SELECT IDCampoVacunacion, Nombre,Tipo,Valores,Obligatorio,Orden FROM CampoVacunacion CV
																WHERE CV.IDClub = '" . $IDClub . "' ORDER BY CV.Orden";

                $qry_campo_editar = $dbo->query($sql_campo_editar);
                if ($dbo->rows($qry_campo_editar) > 0) {
                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                        $campo_editar["IDCampo"] = $r_campo_editar["IDCampoVacunacion"];
                        $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                        $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                        $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                        $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                        array_push($response_campo_editar, $campo_editar);
                    } //ednw while
                }
                $configuracion["CampoEditar"] = $response_campo_editar;

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "V1." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_configuracion_vacunacionv2($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio)) {
            $Campo = "IDSocio";
            $Valor = $IDSocio;
        } else {
            $Campo = "IDUsuario";
            $Valor = $IDUsuario;
        }
        $datos_vacuna = $dbo->fetchAll("Vacunado", $Campo . " = '" . $Valor . "'", "array");

        if (empty($datos_vacuna)) :
            $Vacunado = "";
        else :
            $Vacunado = $datos_vacuna["DeseoVacuna"];
        endif;

        $sql = "SELECT * FROM ConfiguracionVacunacion2
									WHERE IDClub = '$IDClub'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["LabelEstaVacunado"] = $r["LabelEstaVacunado"];
                $configuracion["LabelRegistrarCitaVacuna"] = $r["LabelRegistrarCitaVacuna"];
                $configuracion["LabelRegistrarVacuna"] = $r["LabelRegistrarVacuna"];
                $configuracion["TerminosHtmlEstaVacunado"] = $r["TerminosHtmlEstaVacunado"];
                $configuracion["MostrarEstaVacunado"] = $r["MostrarEstaVacunado"];
                $configuracion["MostrarRegistrarVacuna"] = $r["MostrarRegistrarVacuna"];
                $configuracion["MostrarRegistrarCitaVacuna"] = $r["MostrarRegistrarCitaVacuna"];
                $configuracion["EstoyVacunado"] = $Vacunado;
                $configuracion["LabelSi"] = $r["LabelSi"];
                $configuracion["LabelNo"] = $r["LabelNo"];
                $configuracion["LabelEntidadVacuna"] = $r["LabelEntidadVacuna"];
                $configuracion["LabelEntidadVacuno"] = $r["LabelEntidadVacuno"];
                $configuracion["LabelMarcaVacuna"] = $r["LabelMarcaVacuna"];
                $configuracion["LabelLugarVacunacion"] = $r["LabelLugarVacunacion"];
                $configuracion["PermiteNoQuieroVacunar"] = $r["PermiteNoQuieroVacunar"];
                $configuracion["LabelNoQuieroVacunar"] = $r["LabelNoQuieroVacunar"];
                $configuracion["TipoCampoEntidad"] = $r["TipoCampoEntidad"];
                $configuracion["PreguntarTerminos"] = $r["PreguntarTerminos"];
                $configuracion["MostrarRegistrarCarneGobierno"] = $r["MostrarRegistrarCarneGobierno"];
                $configuracion["IntroRegistrarCarneGobierno"] = $r["IntroRegistrarCarneGobierno"];
                $configuracion["LabelRegistrarCarneGobierno"] = $r["LabelRegistrarCarneGobierno"];
                $configuracion["PreguntarTerminos"] = $r["PreguntarTerminos"];

                $configuracion["TextoVerCertificadoDigital"] = $r["TextoVerCertificadoDigital"];
                $configuracion["ImagenEjemploPdfCertificado"] = VACUNA_ROOT . $r["ImagenEjemploPdfCertificado"];
                $configuracion["TextoEjemploPdfCertificado"] = $r["TextoEjemploPdfCertificado"];
                $configuracion["LabelTextoCarnetFamilia"] = $r["LabelTextoCarnetFamilia"];

                $configuracion["LabelBotonEditarDosis"] = $r["LabelBotonEditarDosis"];
                $configuracion["LabelBotonEliminarTodas"] = $r["LabelBotonEliminarTodas"];
                $configuracion["LabelConfirmarEliminarTodas"] = $r["LabelConfirmarEliminarTodas"];


                //DOSIS
                $response_dosis = array();
                $sqlDosis = "SELECT * FROM Dosis WHERE IDClub = '$IDClub' AND Activa = 'S'";
                $qryDosis = $dbo->query($sqlDosis);
                while ($Dosis = $dbo->fetchArray($qryDosis)) :

                    $datos_dosis[IDDosis] = $Dosis[IDDosis];
                    $datos_dosis[NombreDosis] = $Dosis[NombreDosis];
                    $datos_dosis[Orden] = $Dosis[NumeroDosis];
                    $datos_dosis[LabelFechaCita] = $Dosis[LabelFechaCita];
                    $datos_dosis[LabelFechaDosis] = $Dosis[LabelFechaDosis];
                    $datos_dosis[LabelCertificadoVacuna] = $Dosis[LabelCertificadoVacuna];
                    $datos_dosis[ObligatorioEntidadCita] = $Dosis[ObligatorioEntidadCita];
                    $datos_dosis[ObligatorioEntidadVacuna] = $Dosis[ObligatorioEntidadVacuna];
                    $datos_dosis[ObligatorioMarcaVacuna] = $Dosis[ObligatorioMarcaVacuna];
                    $datos_dosis[ObligatorioLugarVacuna] = $Dosis[ObligatorioLugarVacuna];
                    $datos_dosis[ObligatorioFechaVacuna] = $Dosis[ObligatorioFechaVacuna];
                    $datos_dosis[ObligatorioFechaCita] = $Dosis[ObligatorioFechaCita];
                    $datos_dosis[ObligatorioCertificadoVacuna] = $Dosis[ObligatorioCertificadoVacuna];

                    $datos_dosis["OcultarEntidadDosis"] = $Dosis["OcultarEntidadDosis"];
                    $datos_dosis["OcultarLugarDosis"] = $Dosis["OcultarLugarDosis"];
                    $datos_dosis["OcultarFechaDosis"] = $Dosis["OcultarFechaDosis"];
                    $datos_dosis["OcultarMarcaDosis"] = $Dosis["OcultarMarcaDosis"];
                    $datos_dosis["OcultarCertificadoDosis"] = $Dosis["OcultarCertificadoDosis"];


                    array_push($response_dosis, $datos_dosis);

                endwhile;
                $configuracion["Dosis"] = $response_dosis;

                //Entidades
                $response_entidad = array();
                $sql_entidad = "SELECT IDVacunaEntidad, Nombre
															 FROM VacunaEntidad
															 WHERE IDClub  = '" . $IDClub . "' and Publicar = 'S' ";
                $r_entidad = $dbo->query($sql_entidad);
                while ($row_entidad = $dbo->fetchArray($r_entidad)) {
                    $datos_entidad["IDEntidad"] = $row_entidad["IDVacunaEntidad"];
                    $datos_entidad["Nombre"] = $row_entidad["Nombre"];
                    array_push($response_entidad, $datos_entidad);
                }
                $configuracion["Entidades"] = $response_entidad;

                $response_campo_editar = array();
                $sql_campo_editar = "SELECT IDCampoVacunacion, Nombre,Tipo,Valores,Obligatorio,Orden FROM CampoVacunacion CV
                                                WHERE CV.IDClub = '" . $IDClub . "' ORDER BY CV.Orden DESC";

                $qry_campo_editar = $dbo->query($sql_campo_editar);
                if ($dbo->rows($qry_campo_editar) > 0) {
                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                        $campo_editar["IDCampo"] = $r_campo_editar["IDCampoVacunacion"];
                        $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                        $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                        $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                        $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                        if ($campo_editar["Tipo"] == "imagen" || $campo_editar["Tipo"] == "imagenarchivo") {
                            $campo_editar["ParametroEnvioPost"] = "CampoImagen|" . $campo_editar["IDCampo"]; //ESTE CAMPO SE ENVIA SI ES UNA IMAGEN PARA PODER ENVIAR EL POST Y SUBIR LA IMAGEN EN LA BASE DE DATOS
                        }
                        array_push($response_campo_editar, $campo_editar);
                    } //ednw while
                }
                $configuracion["CampoEditar"] = $response_campo_editar;

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "V1." .  SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    // SERVICIO PARA LLEVAR LAS MARCAS DE LA VACUNA
    public function get_vacunas($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT *
                            FROM VacunaMarca
                            WHERE Publicar= 'S' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["IDVacuna"] = $r["IDVacunaMarca"];
                $configuracion["Nombre"] = $r["Nombre"];
                $configuracion["CantidadDosis"] = (int) $r["NumeroDosis"];
                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "V2." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } // fin function

    public function get_informacion_vacunacion($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) {
            $Campo = "IDSocio";
            $Valor = $IDSocio;
        } else {
            $Campo = "IDUsuario";
            $Valor = $IDUsuario;
        }

        $response = array();
        $sql = "SELECT *
										FROM Vacuna
										WHERE " . $Campo . "= '" . $Valor . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $marca_vacuna = $dbo->getFields("VacunaMarca", "Nombre", " IDVacunaMarca = '" . $r["IDVacunaMarca"] . "' ");

                $configuracion["IDVacuna"] = $r["IDVacunaMarca"];
                $configuracion["IDEntidadVacuna"] = $r["IDVacunaEntidad"];

                if ($r["Entidad"] == "") {
                    $configuracion["EntidadVacuna"] = null;
                } else {
                    $configuracion["EntidadVacuna"] = $r["Entidad"];
                }

                $configuracion["MarcaVacuna"] = $marca_vacuna;
                if ($r["FechaPrimeraDosis"] != "" && $r["FechaPrimeraDosis"] != "0000-00-00") {
                    $VacunadoPrimera = "S";
                } else {
                    $VacunadoPrimera = "N";
                }
                if ($r["FechaSegundaDosis"] != "" && $r["FechaSegundaDosis"] != "0000-00-00") {
                    $VacunadoSegunda = "S";
                } else {
                    $VacunadoSegunda = "N";
                }

                if ($r["FechaTerceraDosis"] != "" && $r["FechaTerceraDosis"] != "0000-00-00") {
                    $VacunadoTercera = "S";
                } else {
                    $VacunadoTercera = "N";
                }

                $configuracion["VacunadoPrimeraDosis"] = $VacunadoPrimera;

                if ($r["FechaPrimeraDosis"] == "0000-00-00" || $r["FechaPrimeraDosis"] == "") {
                    $configuracion["FechaPrimeraDosis"] = null;
                } else {
                    $configuracion["FechaPrimeraDosis"] = $r["FechaPrimeraDosis"];
                }

                if ($r["FechaSegundaDosis"] == "0000-00-00" || $r["FechaSegundaDosis"] == "") {
                    $configuracion["FechaSegundaDosis"] = null;
                } else {
                    $configuracion["FechaSegundaDosis"] = $r["FechaSegundaDosis"];
                }

                if ($r["FechaTerceraDosis"] == "0000-00-00" || $r["FechaTerceraDosis"] == "") {
                    $configuracion["FechaTerceraDosis"] = null;
                } else {
                    $configuracion["FechaTerceraDosis"] = $r["FechaTerceraDosis"];
                }

                $configuracion["LugarPrimeraDosis"] = $r["Lugar"];
                $configuracion["FotoPrimeraDosis"] = VACUNA_ROOT . $r["ImagenPrimeraDosis"];
                $configuracion["VacunadoSegundaDosis"] = $VacunadoSegunda;
                $configuracion["LugarSegundaDosis"] = $r["LugarSegundaDosis"];
                $configuracion["FotoSegundaDosis"] = VACUNA_ROOT . $r["ImagenSegundaDosis"];

                //Tercera
                $configuracion["VacunadoTerceraDosis"] = $VacunadoTercera;
                $configuracion["LugarTerceraDosis"] = $r["LugarTerceraDosis"];
                $configuracion["FotoTerceraDosis"] = VACUNA_ROOT . $r["ImagenTerceraDosis"];

                if ($r["FechaCitaPrimeraDosis"] == "0000-00-00" || $r["FechaCitaPrimeraDosis"] == "") {
                    $configuracion["FechaCitaPrimeraDosis"] = null;
                } else {
                    $configuracion["FechaCitaPrimeraDosis"] = $r["FechaCitaPrimeraDosis"];
                }

                if ($r["FechaCitaSegundaDosis"] == "0000-00-00" || $r["FechaCitaSegundaDosis"] == "") {
                    $configuracion["FechaCitaSegundaDosis"] = null;
                } else {
                    $configuracion["FechaCitaSegundaDosis"] = $r["FechaCitaSegundaDosis"];
                }

                if ($r["FechaCitaTerceraDosis"] == "0000-00-00" || $r["FechaCitaTerceraDosis"] == "") {
                    $configuracion["FechaCitaTerceraDosis"] = null;
                } else {
                    $configuracion["FechaCitaTerceraDosis"] = $r["FechaCitaTerceraDosis"];
                }

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "V2." .  SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    } // fin function

    public function datos_persona_vacunacion($IDClub, $IDSocio, $IDUsuario, $ConsultaNucleo = "")
    {
        $dbo = &SIMDB::get();


        if (!empty($IDSocio)) {
            $Campo = "IDSocio";
            $Valor = $IDSocio;
            $Tabla = "Socio";
        } else {
            $Campo = "IDUsuario";
            $Valor = $IDUsuario;
            $Tabla = "Usuario";
        }

        $response = array();

        // SACAMOS LA INFORMACION DE LA VACUNA QUE TIENE LA PERSONA
        $sql = "SELECT * FROM Vacuna2 WHERE $Campo = '$Valor' ORDER BY IDDosis ASC";
        $qry = $dbo->query($sql);

        // INFORMACIÓN DE LA PERSONA
        $datos_persona = $dbo->fetchAll($Tabla, " $Campo = '$Valor' ", "array");
        $datos_vacunado = $dbo->fetchAll("Vacunado", " $Campo = '$Valor' ", "array");
        $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion2", "IDClub = '" . $IDClub . "' ORDER BY IDConfiguracionVacunacion DESC Limit 1");

        $datos_vacuna = $dbo->fetchAll("Vacunado", $Campo . " = '" . $Valor . "'", "array");
        if (empty($datos_vacuna)) :
            $Vacunado = "";
        else :
            $Vacunado = $datos_vacuna["DeseoVacuna"];
        endif;

        $configuracion[IDSocio] = $IDSocio;
        $configuracion[MostrarEstaVacunado] = $datos_configuracion["MostrarEstaVacunado"];
        $configuracion[EstoyVacunado] = $Vacunado;
        $configuracion[MostrarRegistrarVacuna] = $datos_configuracion["MostrarRegistrarVacuna"];
        $configuracion[MostrarRegistrarCitaVacuna] = $datos_configuracion["MostrarRegistrarCitaVacuna"];

        $configuracion[PermiteEditar] = $datos_configuracion["PermiteEditar"];
        $configuracion[PermiteEliminarTodas] = $datos_configuracion["PermiteEliminarTodas"];

        if (!empty($datos_vacunado["CodigoQrGobierno"])) {
            $DatosInfo[QRCode] = VACUNA_ROOT . "qr/" . $datos_vacunado[CodigoQrGobierno];
        } else {
            $DatosInfo[QRCode] = VACUNA_ROOT . "qr/" . $datos_vacunado[CodigoQr];
        }

        if (!empty($datos_vacunado["CodigoQrGobierno"])) {
            $DatosInfo[PdfCertificadoDigital] = VACUNA_ROOT . $datos_vacunado[ArchivoVacuna];
        }

        $DatosInfo[Nombre] = $datos_persona[Nombre] . " " . $datos_persona[Apellido];
        $DatosInfo[Documento] = $datos_persona[NumeroDocumento];
        $DatosInfo[FechaNacimiento] = $datos_persona[FechaNacimiento];

        // PARA AGREGAR ADICIONALES AL CARNET
        $adicionales = array();
        $datos_adicionales["llave"] = "";
        $datos_adicionales["valor"] = "";
        array_push($adicionales, $datos_adicionales);

        $DatosInfo[Adicionales] = $adicionales;

        $configuracion[DatosInfo] = $DatosInfo;
        $Dosis = array();

        if ($dbo->rows($qry) > 0) {

            // ARMAMOS TODAS LA DOSIS QUE SE HAN APLICADO
            while ($r = $dbo->fetchArray($qry)) :

                $datos_dosis = $dbo->fetchAll("Dosis", "IDDosis = ' $r[IDDosis]' ", "array");

                $InfoDosis[EntidadVacuna] = $r[EntidadDosis];
                $InfoDosis[IDEntidadVacuna] = $r[IDVacunaEntidadDosis];
                $InfoDosis[IDDosis] = $r[IDDosis];
                $InfoDosis[Orden] = $datos_dosis[NumeroDosis];
                $InfoDosis[NombreDosis] = $datos_dosis[NombreDosis];

                if (empty($r[Marca])) :
                    $Marca = "";
                else :
                    $Marca = $r[Marca];
                endif;

                $InfoDosis[NombreVacuna] = $Marca;
                $InfoDosis[IDVacuna] = $r[IDVacuna];
                $InfoDosis[EstaVacunado] = $r[EstoyVacunado];
                $InfoDosis[FechaCita] = $r[FechaCitaVacuna];

                $otros = array();

                if (!empty($r[Lugar])) {
                    $datos_otros[Nombre] = "Lugar";
                    $datos_otros[Tipo] = "text";
                    $datos_otros[Valores] = $r[Lugar];
                    array_push($otros, $datos_otros);
                }

                if (!empty($r[Certificado])) {
                    $datos_otros[Nombre] = "Certificado";
                    $datos_otros[Tipo] = "image";
                    $datos_otros[Valores] = VACUNA_ROOT . $r[Certificado];
                    array_push($otros, $datos_otros);
                }

                if (!empty($datos_vacunado[ArchivoVacuna])) {
                    $datos_otros[Nombre] = "Certificado PDF Gobierno";
                    $datos_otros[Tipo] = "file";
                    $datos_otros[Valores] = VACUNA_ROOT . $datos_vacunado[ArchivoVacuna];
                    array_push($otros, $datos_otros);
                }


                // AGREGAMOS TODOS LOS CAMPOS DINAMICOS QUE SE TIENE CONFIGURADOS

                $sqlCampos = "SELECT Nombre,Tipo,Valor FROM VacunaCampoVacunacion2 VCV2, CampoVacunacion CV WHERE VCV2.IDVacuna = $r[IDVacuna] AND VCV2.IDCampoVacunacion = CV.IDCampoVacunacion AND CV.IDClub = '$IDClub' AND CV.Publicar = 'S'";
                $qryCampos = $dbo->query($sqlCampos);
                while ($datos = $dbo->fetchArray($qryCampos)) :
                    $mostrar = "SI";
                    switch ($datos[Tipo]):
                        case "text":
                        case "textarea":
                        case "radio":
                        case "checkbox":
                        case "select":
                        case "number":
                        case "date":
                        case "time":
                        case "email":
                        case "titulo":
                            $tipo = "text";
                            $Valor = $datos[Valor];
                            if(empty($datos[Valor])):
                                $mostrar = "NO";
                            endif;
                            break;
                        case "imagen":
                            $tipo = "image";
                            $Valor = VACUNA_ROOT . $datos[Valor];
                            if(empty($datos[Valor])):
                                $mostrar = "NO";
                            endif;
                            break;
                        case "imagenarchivo":
                            $tipo = "imagenarchivo";
                            $Valor = VACUNA_ROOT . $datos[Valor];
                            if(empty($datos[Valor])):
                                $mostrar = "NO";
                            endif;
                            break;

                    endswitch;

                    if($mostrar == "SI"):
                        $datos_otros[Nombre] = $datos[Nombre];
                        $datos_otros[Tipo] = $tipo;
                        $datos_otros[Valores] = $Valor;
                        array_push($otros, $datos_otros);
                    endif;

                endwhile;

                $InfoDosis[CamposAdicionales] = $otros;

                array_push($Dosis, $InfoDosis);

                if ($r[EstoyVacunado] == "S") :
                    $UltimaDosis = $r[IDDosis]; //SACAR LA ULTIMA DOSIS PARA SABER SI HAY SIGUIENTE DOSIS
                endif;

            endwhile;

            // SACAR ID DE LA PROXIMA DOSIS
            // INFORMACIÓN DE LA DOSIS QUE TIENE PARA HALLAR LA PROXIMA DOSIS
            $datos_dosis = $dbo->fetchAll("Dosis", "IDDosis = '$UltimaDosis' ", "array");

            // BUSCAMOS LA PROXIMA DOSIS
            $ProximaDosis = $datos_dosis[NumeroDosis] + 1; // PROXIMA DOSIS           

        } else {
            $ProximaDosis = 1; //SI NO HAY DATOS DE DOSIS LA PRXIMA DOSIS DEBE SER LA 1 CONFIGURADA POR EL CLUB
            // $Dosis = [];          
        }
        // CALCULAR PRIMERA DOSIS

        $sqlProximaDosis = "SELECT * FROM Dosis WHERE IDClub = '$IDClub' AND NumeroDosis = '$ProximaDosis'";
        $qryProximaDosis = $dbo->query($sqlProximaDosis);
        $datos_proxima_dosis = $dbo->fetchArray($qryProximaDosis);

        $configuracion[IDDosisProximaAplicar] = $datos_proxima_dosis[IDDosis];

        $configuracion[Dosis] = $Dosis;



        return $configuracion;
    } // fin function


    public function get_informacion_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $ConsultaNucleo = "")
    {
        $dbo = &SIMDB::get();
        $response = array();
        $configuracion = array();

        $configuracion = self::datos_persona_vacunacion($IDClub, $IDSocio, $IDUsuario, $ConsultaNucleo = "");

        $array_nucleo = array();
        $response_nucleo = array();
        //Consulto el nucleo familiar

        if (!empty($IDSocio)) {
            $Campo = "IDSocio";
            $Valor = $IDSocio;
            $Tabla = "Socio";
        } else {
            $Campo = "IDUsuario";
            $Valor = $IDUsuario;
            $Tabla = "Usuario";
        }

        $response = array();

        // INFORMACIÓN DE LA PERSONA
        $datos_persona = $dbo->fetchAll($Tabla, " $Campo = '$Valor' ", "array");

        if ($IDSocio > 0) :

            if (!empty($datos_persona["AccionPadre"])) : // Es beneficiario
                $condicion_nucleo = " and (AccionPadre = '" . $datos_persona["AccionPadre"] . "' or Accion = '" . $datos_persona["AccionPadre"] . "')";
                $tipo_socio = $datos_persona["TipoSocio"];
            else : // es Cabeza familia
                $condicion_nucleo = " and AccionPadre = '" . $datos_persona["Accion"] . "'";
                $tipo_socio = $datos_persona["TipoSocio"];
            endif;       

            $response_nucleo = array();
            $sql_nucleo = "SELECT * FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_persona["IDSocio"] . "' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 ) " . $condicion_nucleo;
            $qry_nucleo = $dbo->query($sql_nucleo);
            while ($datos_nucleo = $dbo->fetchArray($qry_nucleo)) {
                //echo "a";
                $datos_persona_nucleo = self::datos_persona_vacunacion($IDClub, $datos_nucleo["IDSocio"], "", "N");
                array_push($response_nucleo, $datos_persona_nucleo);
            }
            //Fin Nucleo familiar          

        endif;

        $configuracion[Nucleofamiliar] = $response_nucleo;
        array_push($response, $configuracion);


        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function


    public function set_vacunacion($IDClub, $IDSocio, $IDUsuario, $IDVacuna, $Lugar, $Dosis, $Entidad, $Fecha, $Foto, $File = "", $IDEntidad, $Campos = "")
    {


        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavoractualicelaversiondelapp', LANG);
        $respuesta["success"] = false;
        $respuesta["response"] = null;
        return $respuesta;


        //Valido el pseo del archivo
        $tamano_archivo = $File["Foto"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDVacuna) && !empty($Lugar) && !empty($Dosis)) {

            //UPLOAD de imagenes

            if (isset($File)) {

                $files = SIMFile::upload($File["Foto"], VACUNA_DIR, "IMAGE");
                if (empty($files) && !empty($File["Foto"]["name"])) :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
                $Archivo = $files[0]["innername"];
            } //end if

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $id_vac_soc = (int) $dbo->getFields("Vacuna", "IDVacuna", " $Campo = '" . $Valor . "' ");

            if ($id_vac_soc > 0) {
                if ($Dosis == 1) {
                    $sql_vac_socio = "UPDATE Vacuna
																		SET IDVacunaMarca = '" . $IDVacuna . "',
																		Entidad = '" . $Entidad . "',
																		IDVacunaEntidad='" . $IDEntidad . "',
																		Lugar = '" . $Lugar . "',
																		FechaPrimeraDosis='" . $Fecha . "',
																		ImagenPrimeraDosis= '" . $Archivo . "',
																		UsuarioTrEd='WS',
																		FechaTrEd=NOW()
																		WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                } elseif ($Dosis == 2) {
                    $sql_vac_socio = "UPDATE Vacuna
																SET IDVacunaMarca = '" . $IDVacuna . "',
																Entidad = '" . $Entidad . "',
																IDVacunaEntidad='" . $IDEntidad . "',
																LugarSegundaDosis = '" . $Lugar . "',
																FechaSegundaDosis='" . $Fecha . "',
																ImagenSegundaDosis= '" . $Archivo . "',
																UsuarioTrEd='WS',
																FechaTrEd=NOW()
																WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                } else {
                    $sql_vac_socio = "UPDATE Vacuna
																SET IDVacunaMarca = '" . $IDVacuna . "',
																Entidad = '" . $Entidad . "',
																IDVacunaEntidad='" . $IDEntidad . "',
																LugarTerceraDosis = '" . $Lugar . "',
																FechaTerceraDosis='" . $Fecha . "',
																ImagenTerceraDosis= '" . $Archivo . "',
																UsuarioTrEd='WS',
																FechaTrEd=NOW()
																WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                }
            } else {
                if ($Dosis == 1) {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaMarca, IDVacunaEntidad, Entidad, Lugar,  FechaPrimeraDosis, ImagenPrimeraDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDVacuna . "','" . $IDEntidad . "', '" . $Entidad . "','" . $Lugar . "','" . $Fecha . "','" . $Archivo . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                } elseif ($Dosis == 2) {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaMarca, IDVacunaEntidad, Entidad, LugarSegundaDosis,  FechaSegundaDosis, ImagenSegundaDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDVacuna . "','" . $IDEntidad . "','" . $Entidad . "','" . $Lugar . "','" . $Fecha . "','" . $Archivo . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                } else {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaMarca, IDVacunaEntidad, Entidad, LugarTerceraDosis,  FechaTerceraDosis, ImagenTerceraDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDVacuna . "','" . $IDEntidad . "','" . $Entidad . "','" . $Lugar . "','" . $Fecha . "','" . $Archivo . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                }
            }

            $sql_vac_socio = "UPDATE Vacuna
													SET Vacunado = 'S',
													FechaVacunado = NOW(),
													UsuarioTrEd='WS',
													FechaTrEd=NOW()
													WHERE " . $Campo . " = '" . $Valor . "'";
            $dbo->query($sql_vac_socio);

            $Comentario = "Se registro una vacuna por parte de " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " <br>Entidad: " . $Entidad . " <br>Lugar: " . $Lugar . " <br>Fecha: " . $Fecha;
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
            $datos_campos = json_decode($Campos, true);
            if (count($datos_campos) > 0) {
                foreach ($datos_campos as $detalle_campo) {
                    $IDCampo = $detalle_campo["IDCampo"];
                    $valorcampo = $detalle_campo["Valor"];
                    $sql_verifica = "SELECT IDVacunaCampoVacunacion FROM VacunaCampoVacunacion WHERE " . $Campo . " = '" . $Valor . "' and IDCampoVacunacion = '" . $IDCampo . "' and Dosis = '" . $Dosis . "' ";
                    $r_verifica = $dbo->query($sql_verifica);
                    if ($dbo->rows($r_verifica) > 0) {
                        $row_verifica = $dbo->fetchArray($r_verifica);
                        $sql_socio_datos = "UPDATE VacunaCampoVacunacion
																				SET  Valor='" . $valorcampo . "',FechaTrEd = NOW()
																				WHERE IDVacunaCampoVacunacion = '" . $row_verifica["IDVacunaCampoVacunacion"] . "'";
                    } else {
                        $sql_socio_datos = "INSERT INTO VacunaCampoVacunacion (IDCampoVacunacion, " . $Campo . ",Valor,Dosis,FechaTrCr)
																					VALUES ('" . $IDCampo . "','" . $Valor . "','" . $valorcampo . "','" . $Dosis . "',NOW())";
                    }
                    $dbo->query($sql_socio_datos);
                };
            }

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' ORDER BY IDConfiguracionVacunacion DESC Limit 1");
            if (!empty($datos_configuracion["LabelConfirmaRegistraVacuna"])) {
                $mensaje = $datos_configuracion["LabelConfirmaRegistraVacuna"];
            } else {
                $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $IDVacunaMarca, $Lugar, $Dosis, $Entidad, $Fecha, $Foto, $File = "", $IDEntidad, $Campos = "", $IDDosis, $IDSocioBeneficiario = "")
    {


        if ((int)$IDSocioBeneficiario > 0) {
            $IDSocio = $IDSocioBeneficiario;
        }

        //Valido el pseo del archivo
        $tamano_archivo = $File["Foto"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $dbo = &SIMDB::get();
        if (!empty($IDClub)) {

            //UPLOAD de imagenes
            if (isset($File)) {
                if (!empty($File['Foto']['name'])) {
                    $files = SIMFile::upload($File["Foto"], VACUNA_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Foto"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];
                }
            } //end if

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }
            // BUSCAMOS LA ENTIDAD QUE VACUNA
            if (empty($Entidad)) :
                $DatosEntidad = $dbo->fetchAll("VacunaEntidad", " IDVacunaEntidad = '$IDEntidad'", "array");
                $Entidad = $DatosEntidad[Nombre];
            endif;

            $DatosMarca = $dbo->fetchAll("VacunaMarca", "IDVacunaMarca = '$IDVacunaMarca'", "array");
            $DatosVacunado = $dbo->fetchAll("Vacunado", $Campo . " = '" . $Valor . "'", "array");

            // SI NO RESPONDIO LA PREGUNTA DE DESEAR ESTAR VACUNADO DAMOS POR ECHO DE QUE SI LO DESEA
            if (empty($DatosVacunado)) :
                $sql = "INSERT INTO Vacunado ($Campo, DeseoVacuna, UsuarioTrCr, FechaTrCr) VALUES ('$Valor','si','WebService',NOW())";
                $dbo->query($sql);
                $Vacunado = $dbo->lastID();
            else :
                $Vacunado = $DatosVacunado[IDVacunado];
            endif;

            if (empty($DatosVacunado[CodigoQr])) :
                // CREAMOS EL QR Y LO GUARDAMOS EN LA TABLA DE VACUNADO
                require_once LIBDIR . "phpqrcode/qrlib.php";

                $matrixPointSize = 5; //DATO ESTATICO
                $errorCorrectionLevel = 'L'; //DATO ESTATICO

                $parametros_codigo_qr = URLROOT . "PaginaQRVacunacion.php?IDVacunado=$Vacunado&IDClub=$IDClub&$Campo=$Valor"; //ENVIAMOS LA URL QUE SE LEERA EN QR Y REDIRECCIONARA
                $PNG_TEMP_DIR = VACUNA_DIR . "qr/";

                $filename = $PNG_TEMP_DIR . 'test' . md5($Vacunado . '|' . $Campo . '|' . $Valor . '|' . $matrixPointSize) . '.png';
                QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

                $ArchivoQR = basename($filename);

                // ACTUALIZAMOS EL CODIGO QR
                $ActulizaQr = "UPDATE Vacunado SET CodigoQr = '$ArchivoQR' WHERE IDVacunado = $Vacunado";
                $dbo->query($ActulizaQr);
            endif;

            $sqlVacuna = "SELECT * FROM Vacuna2 WHERE $Campo = '$Valor' AND IDDosis = '$IDDosis'";
            $qryVacuna = $dbo->query($sqlVacuna);
            $Vacuna = $dbo->fetchArray($qryVacuna);

            if ($Vacuna[IDVacuna] > 0) :

                $IDVacuna = $Vacuna[IDVacuna];
                $Certificado = (empty($Archivo)) ? $Vacuna['Certificado'] : $Archivo;
                $sql = "UPDATE Vacuna2 SET 
                        	IDVacunaMarca = '$IDVacuna', 
                        	IDClub = '$IDClub', 
                        	IDDosis = '$IDDosis', 
                        	Marca = '$DatosMarca[Nombre]', 
                            Lugar = '$Lugar',
                            EstoyVacunado = 'S',
                            FechaCitaVacuna = '$Fecha',
                            FechaVacuna = '$Fecha',
                            IDVacunaEntidadDosis = '$IDEntidad',
                            EntidadDosis = '$Entidad',
                            Certificado = '$Certificado',
                            FechaTrEd = NOW(),
                            UsuarioTrEd = '$Campo-$Valor'
                            WHERE IDVacuna = $IDVacuna";

                $dbo->query($sql);
            else :
                $sql = "INSERT INTO Vacuna2 (IDClub, IDDosis, $Campo, IDVacunaMarca, IDVacunaEntidadCita, IDVacunaEntidadDosis, IDVacunado, EstoyVacunado, FechaCitaVacuna, FechaVacuna, EntidadCita, EntidadDosis, Marca, Lugar, Certificado, FechaTrCr, UsuarioTrCr) 
                                    VALUES ('$IDClub','$IDDosis','$Valor','$DatosMarca[IDVacunaMarca]','$IDEntidad','$IDEntidad','$Vacunado','S','$Fecha','$Fecha','$Entidad','$Entidad','$DatosMarca[Nombre]','$Lugar','$Archivo',NOW(),'$Campo-$Valor')";
                $dbo->query($sql);

                $IDVacuna = $dbo->lastID();
            endif;

            // ENVIAMOS CORREO AL RESPONSABLE QUE SE HA GENERA UNA VACUNA.

            $Comentario = "Se registro una vacuna por parte de " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " <br>Entidad: " . $Entidad . " <br>Lugar: " . $Lugar . " <br>Fecha: " . $Fecha;
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            // AGREGAMOS LOS CAMPOS PARA LA DOSIS
            $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
            $datos_campos = json_decode($Campos, true);
            if (count($datos_campos) > 0) {
                foreach ($datos_campos as $detalle_campo) {
                    $IDCampo = $detalle_campo["IDCampo"];
                    $valorcampo = $detalle_campo["Valor"];
                    $sql_verifica = "SELECT IDVacunaCampoVacunacion FROM VacunaCampoVacunacion2 WHERE IDVacuna = '$IDVacuna' AND IDCampoVacunacion = '$IDCampo'";
                    $r_verifica = $dbo->query($sql_verifica);
                    if ($dbo->rows($r_verifica) > 0) {
                        $row_verifica = $dbo->fetchArray($r_verifica);

                        $sql_socio_datos = "UPDATE VacunaCampoVacunacion2
                                            SET  Valor='$valorcampo',FechaTrEd = NOW()
                                            WHERE IDVacunaCampoVacunacion = '$row_verifica[IDVacunaCampoVacunacion]'";
                    } else {
                        $sql_socio_datos = "INSERT INTO VacunaCampoVacunacion2 (IDCampoVacunacion,Valor,IDVacuna,FechaTrCr)
                                            VALUES ('$IDCampo','$valorcampo','$IDVacuna',NOW())";
                    }
                    $dbo->query($sql_socio_datos);
                };
            }
            //subir las imagenes
            if (isset($File)) {
                foreach ($File as $nombre_archivo => $archivo) {
                    $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                    if ($ArrayPreguntaEncuesta[0] != "Foto") :
                        if ($archivo['name'] != '') {
                            $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
                            $tamano_archivo = $archivo["size"];

                            if ($tamano_archivo >= 6000000) {
                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {

                                $files = SIMFile::upload($File[$nombre_archivo], VACUNA_DIR);
                                if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                $IDVacunaCampoVacunacion = $dbo->getFields('VacunaCampoVacunacion2', 'IDVacunaCampoVacunacion', 'IDVacuna = "' . $IDVacuna . '" AND IDCampoVacunacion = "' . $IDPreguntaActualiza . '"');
                                $Archivo = $files[0]["innername"];
                                if ($IDVacunaCampoVacunacion > 0) {
                                    $actualiza_pregunta = "UPDATE VacunaCampoVacunacion2 SET Valor = '$Archivo', FechaTrEd = NOW() WHERE IDVacunaCampoVacunacion  = '$IDVacunaCampoVacunacion'";
                                    $dbo->query($actualiza_pregunta);
                                } else {
                                    $actualiza_pregunta = "INSERT INTO VacunaCampoVacunacion2 (IDCampoVacunacion,IDVacuna,Valor,UsuarioTrCr,FechaTrCr) VALUES ('$IDPreguntaActualiza','$IDVacuna','$Archivo','APP_ADMIN', NOW())";
                                    $dbo->query($actualiza_pregunta);
                                }
                            }
                        }
                    endif;
                }
            }

            // NOTIFICAMOS PARA MEDELLIN LA VACUNACIÓN
            if ($IDClub == 20) :
                require LIBDIR . "SIMWebServiceMedellin.inc.php";
                $mensajeMedellin = SIMWebServiceMedellin::NotificaVacuna($IDVacuna);
            endif;

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' ORDER BY IDConfiguracionVacunacion DESC Limit 1");
            if (!empty($datos_configuracion["LabelConfirmaRegistraVacuna"])) {
                $mensaje = $datos_configuracion["LabelConfirmaRegistraVacuna"] . "\n" . $mensajeMedellin;
            } else {
                $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG) . "\n" . $mensajeMedellin;
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cita_vacunacion($IDClub, $IDSocio, $IDUsuario, $Dosis, $Entidad, $Fecha, $IDEntidad)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Entidad) && !empty($Fecha) && !empty($Dosis)) {

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $id_vac_soc = (int) $dbo->getFields("Vacuna", "IDVacuna", " $Campo = '" . $Valor . "' ");

            if ($id_vac_soc > 0) {
                if ($Dosis == 1) {
                    $sql_vac_socio = "UPDATE Vacuna
                                            SET LugarCitaPrimera = '" . $Entidad . "',
                                            Entidad = '" . $Entidad . "',
                                            IDVacunaEntidad = '" . $IDEntidad . "',
                                            FechaCitaPrimeraDosis = '" . $Fecha . "',
                                            UsuarioTrEd='WS',
                                            FechaTrEd=NOW()
                                            WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                } elseif ($Dosis == 2) {
                    $sql_vac_socio = "UPDATE Vacuna
                                            SET LugarCitaSegunda = '" . $Entidad . "',
                                            Entidad = '" . $Entidad . "',
                                            IDVacunaEntidad = '" . $IDEntidad . "',
                                            FechaCitaSegundaDosis = '" . $Fecha . "',
                                            UsuarioTrEd='WS',
                                            FechaTrEd=NOW()
                                            WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                } else {
                    $sql_vac_socio = "UPDATE Vacuna
																SET LugarCitaTercera = '" . $Entidad . "',
																Entidad = '" . $Entidad . "',
																IDVacunaEntidad = '" . $IDEntidad . "',
																FechaCitaTerceraDosis = '" . $Fecha . "',
																UsuarioTrEd='WS',
																FechaTrEd=NOW()
																WHERE IDVacuna = '" . $id_vac_soc . "'";
                    $dbo->query($sql_vac_socio);
                }
            } else {
                if ($Dosis == 1) {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaEntidad, Entidad,LugarCitaPrimera, FechaCitaPrimeraDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDEntidad . "','" . $Entidad . "','" . $Entidad . "','" . $Fecha . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                } elseif ($Dosis == 2) {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaEntidad, Entidad,LugarSegundaDosis, FechaCitaSegundaDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDEntidad . "' ,'" . $Entidad . "','" . $Entidad . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                } else {
                    $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", IDVacunaEntidad, Entidad,LugarTerceraDosis, FechaCitaTerceraDosis, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $IDEntidad . "' ,'" . $Entidad . "','" . $Entidad . "','WebService',NOW())";
                    $dbo->query($sql_vac);
                }
            }

            $Comentario = "Se registro una vacuna por parte de " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " <br>Entidad: " . $Entidad . " <br>Lugar: " . $Lugar . " <br>Fecha: " . $Fecha;
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' AND Activo = 'S' ORDER BY IDConfiguracionVacunacion DESC Limit 1");
            if (!empty($datos_configuracion["LabelConfirmaRegistraCita"])) {
                $mensaje = $datos_configuracion["LabelConfirmaRegistraCita"];
            } else {
                $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cita_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $Dosis, $Entidad, $Fecha, $IDEntidad, $IDDosis)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDDosis)) {

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $DatosVacunado = $dbo->fetchAll("Vacunado", $Campo . " = '" . $Valor . "'", "array");

            // SI NO RESPONDIO LA PREGUNTA DE DESEAR ESTAR VACUNADO DAMOS POR ECHO DE QUE SI LO DESEA
            if (empty($DatosVacunado)) :
                $sql = "INSERT INTO Vacunado ($Campo, DeseoVacuna, UsuarioTrCr, FechaTrCr) VALUES ('$Valor','si','WebService',NOW())";
                $dbo->query($sql);
                $Vacunado = $dbo->lastID();
            else :
                $Vacunado = $DatosVacunado[IDVacunado];
            endif;

            if (empty($Entidad)) :
                $DatosEntidad = $dbo->fetchAll("VacunaEntidad", " IDVacunaEntidad = '$IDEntidad'", "array");
                $Entidad = $DatosEntidad[Nombre];
            endif;

            // BUSCAMOS SI LA DOSIS YA ESTA CREADA
            $sqlVacuna = "SELECT * FROM Vacuna2 WHERE $Campo = '$Valor' AND IDDosis = '$IDDosis'";
            $qryVacuna = $dbo->query($sqlVacuna);
            $DatosVacuna = $dbo->fetchArray($qryVacuna);

            if ($DatosVacuna[IDVacuna] > 0) :
                // ACTULIZO LA INFORMACIÓN DE LA DOSIS SI YA EXISTE
                $sql = "UPDATE Vacuna2 SET                
                FechaCitaVacuna = '$Fecha',
                IDClub = '$IDClub',
                IDDosis = '$IDDosis',
                IDVacunaEntidadCita = '$IDEntidad',
                EntidadCita = '$Entidad',
                EstoyVacunado = 'N',
                FechaTrEd = NOW(),
                UsuarioTrEd = '$Campo-$Valor'
                WHERE IDVacuna = '$DatosVacuna[IDVacuna]'";
            else :
                $sql = "INSERT INTO Vacuna2 (IDClub,$Campo, IDDosis, IDVacunaEntidadCita, FechaCitaVacuna,IDVacunado,EntidadCita,EstoyVacunado,FechaTrCr,UsuarioTrCr) 
                                VALUES ('$IDClub','$Valor','$IDDosis','$IDEntidad','$Fecha','$DatosVacunado[IDVacunado]','$Entidad','N',NOW(),'$Campo-$Valor')";
            endif;

            $dbo->query($sql);

            $Comentario = "Se registro una vacuna por parte de " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " <br>Entidad: " . $Entidad . " <br>Lugar: " . $Lugar . " <br>Fecha: " . $Fecha;
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' AND Activo = 'S' ORDER BY IDConfiguracionVacunacion DESC Limit 1");
            if (!empty($datos_configuracion["LabelConfirmaRegistraCita"])) {
                $mensaje = $datos_configuracion["LabelConfirmaRegistraCita"];
            } else {
                $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_vacunado($IDClub, $IDSocio, $IDUsuario, $Vacunado)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Vacunado)) {

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $id_vac_soc = (int) $dbo->getFields("Vacuna", "IDVacuna", " $Campo = '" . $Valor . "' ");

            if ($id_vac_soc > 0) {
                $sql_vac_socio = "UPDATE Vacuna
																SET Vacunado = '" . $Vacunado . "',
																FechaVacunado = NOW(),
																UsuarioTrEd='WS',
																FechaTrEd=NOW()
																WHERE IDVacuna = '" . $id_vac_soc . "'";
                $dbo->query($sql_vac_socio);
            } else {
                $sql_vac = "INSERT INTO Vacuna (" . $Campo . ", Vacunado, FechaVacunado, UsuarioTrCr, FechaTrCr)
													VALUES ('" . $Valor . "','" . $Vacunado . "',NOW(),'WebService',NOW())";
                $dbo->query($sql_vac);
            }

            $Comentario = "Se registró una confirmación de vacunación: " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " Fecha: " . date("Y-m-d");
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' AND Activo = 'S' ORDER BY IDConfiguracionVacunacion DESC Limit 1");

            if ($Vacunado == 'S') {
                if (!empty($datos_configuracion["LabelSiConfirmacion"])) {
                    $mensaje = $datos_configuracion["LabelSiConfirmacion"];
                } else {
                    $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }
            } else {
                if (!empty($datos_configuracion["LabelNoConfirmacion"])) {
                    $mensaje = $datos_configuracion["LabelNoConfirmacion"];
                } else {
                    $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_vacunadov2($IDClub, $IDSocio, $IDUsuario, $Vacunado)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($Vacunado)) {

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $id_vac_soc = (int) $dbo->getFields("Vacunado", "IDVacunado", " $Campo = '" . $Valor . "' ");

            if ($id_vac_soc > 0) {
                $sql = "UPDATE Vacunado SET DeseoVacuna = '$Vacunado', UsuarioTrEd = 'WS', FechaTrEd = NOW() WHERE IDVacunado = '$id_vac_soc'";
            } else {
                $sql = "INSERT INTO Vacunado ($Campo, DeseoVacuna, UsuarioTrCr, FechaTrCr) VALUES ('$Valor','$Vacunado','WebService',NOW())";
            }

            $dbo->query($sql);

            $Comentario = "Se registró una confirmación de vacunación: " . $datos_person["Nombre"] . " " . $datos_person["Apellido"] . " Fecha: " . date("Y-m-d");
            SIMUtil::notificar_vacuna($IDClub, $Comentario);

            $datos_configuracion = $dbo->fetchAll("ConfiguracionVacunacion", "IDClub = '" . $IDClub . "' AND Activo = 'S' ORDER BY IDConfiguracionVacunacion DESC Limit 1");

            if ($Vacunado == 'si') {
                if (!empty($datos_configuracion["LabelSiConfirmacion"])) {
                    $mensaje = $datos_configuracion["LabelSiConfirmacion"];
                } else {
                    $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }
            } elseif ($Vacunado == 'no') {
                if (!empty($datos_configuracion["LabelNoConfirmacion"])) {
                    $mensaje = $datos_configuracion["LabelNoConfirmacion"];
                } else {
                    $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }
            } elseif ($Vacunado == 'noquiero') {
                if (!empty($datos_configuracion["LabelNoQuieroConfirmacion"])) {
                    $mensaje = $datos_configuracion["LabelNoQuieroConfirmacion"];
                } else {
                    $mensaje = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }
            }

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V4." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_carne_gobierno($IDClub, $IDSocio, $IDUsuario, $URLQR, $Foto, $File = "", $IDSocioBeneficiario)
    {


        $dbo = &SIMDB::get();

        if ((int)$IDSocioBeneficiario > 0) {
            $IDSocio = $IDSocioBeneficiario;
        }

        //Valido el pseo del archivo
        $tamano_archivo = $File["Foto"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }


        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($URLQR)) {


            //UPLOAD de imagenes
            if (isset($File)) {

                $files = SIMFile::upload($File["Archivo"], VACUNA_DIR, "");
                if (empty($files) && !empty($File["Foto"]["name"])) :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
                $Archivo = $files[0]["innername"];
            } //end if



            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            $DatosVacunado = $dbo->fetchAll("Vacunado", $Campo . " = '" . $Valor . "'", "array");

            // SI NO RESPONDIO LA PREGUNTA DE DESEAR ESTAR VACUNADO DAMOS POR ECHO DE QUE SI LO DESEA
            if (empty($DatosVacunado)) :
                $sql = "INSERT INTO Vacunado ($Campo, DeseoVacuna, UsuarioTrCr, FechaTrCr) VALUES ('$Valor','si','WebService',NOW())";
                $dbo->query($sql);
                $Vacunado = $dbo->lastID();
            else :
                $Vacunado = $DatosVacunado[IDVacunado];
            endif;





            if (!empty($URLQR)) :
                // CREAMOS EL QR Y LO GUARDAMOS EN LA TABLA DE VACUNADO
                require_once LIBDIR . "phpqrcode/qrlib.php";

                $matrixPointSize = 5; //DATO ESTATICO
                $errorCorrectionLevel = 'L'; //DATO ESTATICO

                $parametros_codigo_qr = $URLQR;
                $PNG_TEMP_DIR = VACUNA_DIR . "qr/";

                $filename = $PNG_TEMP_DIR . 'GOB' . md5($Vacunado . '|' . $Campo . '|' . $Valor . '|' . $matrixPointSize) . '.png';
                QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

                $ArchivoQR = basename($filename);

                // ACTUALIZAMOS EL CODIGO QR
                $ActulizaQr = "UPDATE Vacunado SET CodigoQrGobierno = '$ArchivoQR',ArchivoVacuna='" . $Archivo . "' WHERE IDVacunado = $Vacunado";
                $dbo->query($ActulizaQr);
            endif;

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Archivosubidoconexito', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "V10." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function eliminar_todas_dosis_vacunacionv2($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();

        if(!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))):

            if (!empty($IDSocio)) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_person = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_person = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            }

            // BUSCAMOS LAS DOSIS
            $SQLBuscar = "SELECT * FROM Vacuna2 WHERE IDClub = $IDClub AND $Campo = $Valor";
            $QRYBuscar = $dbo->query($SQLBuscar);

            if($dbo->rows($QRYBuscar)):
                while($DatosVacunas = $dbo->fetchArray($QRYBuscar)):
                    // INSERTAMOS EN LA TABLA ELIMINADOS
                    $dbo->insert($DatosVacunas,"Vacuna2Eliminados","IDVacuna");
        
                    $SQLEliminamos = "DELETE FROM Vacuna2 WHERE IDVacuna = $DatosVacunas[IDVacuna]";
                    $QRYEliminamos = $dbo->query($SQLEliminamos);
                endwhile;

                $respuesta["message"] = "Datos Eliminados con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            else:
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

        else:
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

       return $respuesta;

    }

   
} //end class
