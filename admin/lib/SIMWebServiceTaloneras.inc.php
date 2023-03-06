<?php
// SERVICIOS PARA EL MANEJO DE TALONERAS

class SIMWebServiceTaloneras
{
    public function get_taloneras_servicio($IDClub, $IDSocio, $IDUsuario = "")
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) :

            if ($IDSocio > 0) :
                $DirgidoA = " AND (DirigidoA = 'S' OR DirigidoA = 'T')";
            else :
                $DirgidoA = " AND (DirigidoA = 'E' OR DirigidoA = 'T')";
            endif;

            // OBTENER CONFIGURACIÓN
            $Config = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario);
            $IDConfiguracionTalonera = $Config[response][0][IDConfiguracionTalonera];
            $PermiteTalonerasMultipleServicios = $Config[response][0][PermiteTalonerasMultipleServicios];
            $SoloTalonerasMultiplesServicios = $Config[response][0][SoloTalonerasMultiplesServicios];

            // CONSULTAMOS LOS SERVICIO DEL CLUB PARA SACAR LAS TALONERAS
            $SQLServicios = "SELECT S.IDServicio, S.IDServicioMaestro, SC.TituloServicio FROM Servicio S, ServicioClub SC WHERE SC.Activo = 'S' AND S.IDServicioMaestro = SC.IDServicioMaestro AND S.IDClub = '$IDClub' AND SC.IDClub = '$IDClub' order by SC.Orden DESC";
            $QRYServicio = $dbo->query($SQLServicios);

            if ($dbo->rows($QRYServicio) > 0) :
                while ($Datos = $dbo->fetchArray($QRYServicio)) :

                    $Talonera[IDClub] = $IDClub;
                    $Talonera[IDServicio] = $Datos[IDServicio];

                    $NombreServicio = $Datos[TituloServicio];
                    // SI NO TIENE TITULO EL SERVICIO SACAMOS EL TITULO DEL SERVICIO MAESTRO
                    if (empty($NombreServicio)) :
                        $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $Datos[IDServicioMaestro]");
                    endif;

                    $Talonera[NombreServicio] = $NombreServicio;

                    // BUSCAMOS LAS TALONERAS DE ESE SERVICIO EN LA TABLA TALONERAS
                    $SQLTaloneras = "SELECT T.IDTalonera as IDTaloneraMostrar, T.*, TS.* FROM Talonera T, TaloneraServicios TS  WHERE IDClub = '$IDClub' AND ((T.IDServicio = '$Datos[IDServicio]' OR TodosLosServicios = 1) OR (TS.IDTalonera = T.IDTalonera AND TS.IDServicio = '$Datos[IDServicio]')) AND Activa = 1 GROUP BY T.IDTalonera";
                    $QRYTaloneras = $dbo->query($SQLTaloneras);

                    $Taloneras = array();
                    if ($dbo->rows($QRYTaloneras) > 0) :
                        while ($DatosTaloneras = $dbo->fetchArray($QRYTaloneras)) :

                            $InfoTalonera[IDTalonera] = $DatosTaloneras[IDTaloneraMostrar];
                            $InfoTalonera[NombreTalonera] = $DatosTaloneras[NombreTalonera];
                            $InfoTalonera[Caracteristicas] = $DatosTaloneras[DescripcionTalonera];
                            $InfoTalonera[ValorParaMi] = $DatosTaloneras[ValorSocio];
                            $InfoTalonera[ValorParaTodosMiembros] = $DatosTaloneras[ValorGrupoFamiliar];
                            $InfoTalonera[ValorMiembroIndividual] = $DatosTaloneras[ValorPorMiembro];

                            array_push($Taloneras, $InfoTalonera);
                        endwhile;
                    endif;
                    $Talonera[Taloneras] = $Taloneras; //ARREGLO DE TALONERAS DEL SERVICIO

                    // SACAMOS LOS TIPO PAGO DE LA CONFIGURACIÓN GENERAL DE LAS TALONERAS
                    $TipoPago = array();
                    $SQlTipoPago = "SELECT * FROM ConfiguracionTaloneraTipoPago CTP, TipoPago TP  WHERE CTP.IDTipoPago = TP.IDTipoPago and IDConfiguracionTalonera = '$IDConfiguracionTalonera'";
                    $QRYTipoPago = $dbo->query($SQlTipoPago);
                    if ($dbo->rows($QRYTipoPago) > 0) :
                        while ($DatosTipoPago = $dbo->fetchArray($QRYTipoPago)) :

                            $InfoTipoPago["IDClub"] = $IDClub;
                            $InfoTipoPago["IDServicio"] = $Datos[IDServicio];
                            $InfoTipoPago["IDTipoPago"] = $DatosTipoPago["IDTipoPago"];
                            $InfoTipoPago["PasarelaPago"] = $DatosTipoPago["PasarelaPago"];
                            $InfoTipoPago["Action"] = SIMUtil::obtener_accion_pasarela($DatosTipoPago["IDTipoPago"], $IDClub);
                            $InfoTipoPago["Nombre"] = $DatosTipoPago["Nombre"];
                            $InfoTipoPago["PaGoCredibanco"] = $DatosTipoPago["PaGoCredibanco"];

                            switch ($DatosTipoPago["IDTipoPago"]):

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
                            endswitch;

                            $InfoTipoPago["Imagen"] = $imagen;
                            array_push($TipoPago, $InfoTipoPago);

                        endwhile;
                    endif;

                    $Talonera[TipoPago] = $TipoPago; //ARREGLO DE TIPOS DE PAGO DE LAS TALONERAS

                    if ($dbo->rows($QRYTaloneras) > 0) :
                        array_push($response, $Talonera);
                    endif;

                    $IDServicioUltimo = $Datos[IDServicio];
                endwhile;
            endif;

            if ($PermiteTalonerasMultipleServicios == 1) :

                if ($SoloTalonerasMultiplesServicios == 1) :
                    $response = array();
                endif;

                $Talonera[IDClub] = $IDClub;
                $Talonera[IDServicio] = $IDServicioUltimo;
                $Talonera[NombreServicio] = "Todos los Servicios";

                // BUSCAMOS LAS TALONERAS DE ESE SERVICIO EN LA TABLA TALONERAS
                $SQLTaloneras = "SELECT * FROM Talonera WHERE IDClub = '$IDClub' AND TodosLosServicios = 1 AND Activa = 1";
                $QRYTaloneras = $dbo->query($SQLTaloneras);

                $Taloneras = array();

                if ($dbo->rows($QRYTaloneras) > 0) :
                    while ($DatosTaloneras = $dbo->fetchArray($QRYTaloneras)) :

                        $InfoTalonera[IDTalonera] = $DatosTaloneras[IDTalonera];
                        $InfoTalonera[NombreTalonera] = $DatosTaloneras[NombreTalonera];
                        $InfoTalonera[Caracteristicas] = $DatosTaloneras[DescripcionTalonera];
                        $InfoTalonera[ValorParaMi] = $DatosTaloneras[ValorSocio];
                        $InfoTalonera[ValorParaTodosMiembros] = $DatosTaloneras[ValorGrupoFamiliar];
                        $InfoTalonera[ValorMiembroIndividual] = $DatosTaloneras[ValorPorMiembro];

                        array_push($Taloneras, $InfoTalonera);
                    endwhile;
                endif;

                $Talonera[Taloneras] = $Taloneras; //ARREGLO DE TALONERAS DEL SERVICIO

                // SACAMOS LOS TIPO PAGO DE LA CONFIGURACIÓN GENERAL DE LAS TALONERAS
                $TipoPago = array();
                $SQlTipoPago = "SELECT * FROM ConfiguracionTaloneraTipoPago CTP, TipoPago TP  WHERE CTP.IDTipoPago = TP.IDTipoPago and IDConfiguracionTalonera = '$IDConfiguracionTalonera'";
                $QRYTipoPago = $dbo->query($SQlTipoPago);
                if ($dbo->rows($QRYTipoPago) > 0) :
                    while ($DatosTipoPago = $dbo->fetchArray($QRYTipoPago)) :

                        $InfoTipoPago["IDClub"] = $IDClub;
                        $InfoTipoPago["IDServicio"] = $IDServicioUltimo;
                        $InfoTipoPago["IDTipoPago"] = $DatosTipoPago["IDTipoPago"];
                        $InfoTipoPago["PasarelaPago"] = $DatosTipoPago["PasarelaPago"];
                        $InfoTipoPago["Action"] = SIMUtil::obtener_accion_pasarela($DatosTipoPago["IDTipoPago"], $IDClub);
                        $InfoTipoPago["Nombre"] = $DatosTipoPago["Nombre"];
                        $InfoTipoPago["PaGoCredibanco"] = $DatosTipoPago["PaGoCredibanco"];

                        switch ($DatosTipoPago["IDTipoPago"]):

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
                        endswitch;

                        $InfoTipoPago["Imagen"] = $imagen;
                        array_push($TipoPago, $InfoTipoPago);

                    endwhile;
                endif;

                $Talonera[TipoPago] = $TipoPago; //ARREGLO DE TIPOS DE PAGO DE LAS TALONERAS

                if ($dbo->rows($QRYTaloneras) > 0) :
                    array_push($response, $Talonera);
                endif;

            endif;

            if (count($response) > 0) :
                $message = count($response) . " registros encontrados";
                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;
            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay taloneras disponibles";
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[success] = false;
            $respuesta[message] = "t1. Faltan parametros";
            $respuesta[response] = "";
        endif;

        return $respuesta;
    } //FIN get_taloneras_servicio

    public function get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) :
            // INFORMACION DEL SOCIO O USUARIO
            if ($IDSocio > 0) :
                $DirgidoA = " AND (DirigidoA = 'S' OR DirigidoA = 'T')";
                $datos_persona = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");
            else :
                $DirgidoA = " AND (DirigidoA = 'E' OR DirigidoA = 'T')";
                $datos_persona = $dbo->fetchAll("Usuario", "IDUsuario = $IDUsuario", "array");
            endif;

            // CONSULTO LA CONFIGURACIÓN QUE SE TENGA ACTIVA
            $SQLConfiguracion = "SELECT * FROM ConfiguracionTalonera WHERE IDClub = '$IDClub' AND Activa = 1 $DirgidoA";
            $QRYConfiguracion = $dbo->query($SQLConfiguracion);
            if ($dbo->rows($QRYConfiguracion) > 0) :
                $message = $dbo->rows($QRYConfiguracion) . " Encontrados";
                while ($Datos = $dbo->fetchArray($QRYConfiguracion)) :

                    $Configuracion[IDClub] = $Datos[IDClub];
                    $Configuracion[IDConfiguracionTalonera] = $Datos[IDConfiguracionTalonera];
                    $Configuracion[LabelConfirmacionEnvio] = $Datos[LabelConfirmacionEnvio];
                    $Configuracion[LabelServicioComprar] = $Datos[LabelServicioComprar];
                    $Configuracion[LabelTipoPaquete] = $Datos[LabelTipoPaquete];
                    $Configuracion[LabelBotonConfirmarCompra] = $Datos[LabelBotonConfirmarCompra];
                    $Configuracion[LabelBotonComprar] = $Datos[LabelBotonComprar];
                    $Configuracion[LabelMisTaloneras] = $Datos[LabelMisTaloneras];
                    $Configuracion[LabelParaMi] = $Datos[LabelParaMi];
                    $Configuracion[LabelParaMiFamilia] = $Datos[LabelParaMiFamilia];
                    $Configuracion[DescontarInvitados] = $Datos[DescontarInvitados];
                    $Configuracion[PermiteTalonerasMultipleServicios] = $Datos[PermiteTalonerasMultipleServicios];
                    $Configuracion[SoloTalonerasMultiplesServicios] = $Datos[SoloTalonerasMultiplesServicios];
                    $Configuracion[PermitirComprarTaloneraPorApp] = $Datos[PermitirComprarTaloneraPorApp];
                    $Configuracion[PermitirRecargarMonedero] = $Datos[PermitirRecargarMonedero];
                    $Configuracion[RetornarValorEliminaAdmin] = $Datos[RetornarValorEliminaAdmin];

                    // SACAMOS TODOS LOS SOCIOS
                    $SociosMiembros = array();
                    if ($IDSocio > 0) :

                        // SACAMOS LOS BENEFICIARIOS
                        $respuesta = SIMWebService::get_beneficiarios($IDClub, $IDSocio);

                        $Beneficiarios = $respuesta[response][Beneficiarios];

                        foreach ($Beneficiarios as $id => $Dato) :

                            if ($Dato[IDBeneficiario] > 0) :

                                $DetaleMiembros[IDSocio] = $Dato[IDBeneficiario];
                                $DetaleMiembros[Socio] = $Dato[Nombre];

                                array_push($SociosMiembros, $DetaleMiembros);

                            endif;

                        endforeach;

                    else :

                        $DetaleMiembros[IDUsuario] = $IDUsuario;
                        $DetaleMiembros[Usuario] = $datos_persona[Nombre];

                        array_push($SociosMiembros, $DetaleMiembros);

                    endif;

                    $Configuracion[SociosMiembros] = $SociosMiembros;

                    array_push($response, $Configuracion);

                endwhile;
                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;
            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay configuraciones creadas";
                $respuesta[response] = "";
            endif;
        else :
            $respuesta[success] = false;
            $respuesta[message] = "t2. Faltan parametros";
            $respuesta[response] = "";
        endif;

        return $respuesta;
    } // FIN get_configuracion_taloneras

    public function get_estado_cuenta_taloneras($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) :

            if ($IDSocio > 0) {
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $SQLServicios = "SELECT S.IDServicio, S.IDServicioMaestro, SC.TituloServicio FROM Servicio S, ServicioClub SC WHERE SC.Activo = 'S' AND S.IDServicioMaestro = SC.IDServicioMaestro AND S.IDClub = '$IDClub' AND SC.IDClub = '$IDClub'";
                $QRYServicio = $dbo->query($SQLServicios);
                $Servicios = $dbo->fetch($QRYServicio);
            } else {
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $SQLServicios = "SELECT S.IDServicio, S.IDServicioMaestro, SC.TituloServicio FROM Servicio S, ServicioClub SC WHERE SC.Activo = 'S' AND S.IDServicioMaestro = SC.IDServicioMaestro AND S.IDClub = '$IDClub' AND SC.IDClub = '$IDClub' AND TituloServicio = 'Administracion'";
                $QRYServicio = $dbo->query($SQLServicios);
                $Servicios = [
                    0 => [
                        "IDServicio" => "176",
                        "IDServicioMaestro" => "33",
                        "TituloServicio" => "Mis Taloneras"
                    ]
                ];

                // $Servicio = $dbo->fetchArray($QRYServicio);
            }

            foreach ($Servicios as $Servicio1) :

                $EstadoTalonera[IDClub] = $IDClub;
                $EstadoTalonera[IDServicio] = $Servicio1[IDServicio];

                $NombreServicio = $Servicio1[TituloServicio];
                // SI NO TIENE TITULO EL SERVICIO SACAMOS EL TITULO DEL SERVICIO MAESTRO
                if (empty($NombreServicio)) :
                    $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $Servicio1[IDServicioMaestro]");
                endif;

                $EstadoTalonera[NombreServicio] = $NombreServicio;

                if ($IDSocio > 0) {
                    $SQLSocioTalonera = "SELECT * FROM SocioTalonera WHERE IDClub = '$IDClub' AND (IDServicio = $Servicio1[IDServicio] OR (TodosLosServicios = 1 AND IDServicio = 0)) AND $Campo = '$Valor' AND Activo='1'";
                } else {
                    $SQLSocioTalonera = "SELECT * FROM `TiqueteraFuncionarios` WHERE IDClub = '$IDClub' AND IDUsuarioTalonera = '$IDUsuario'";
                }
                $QRYSocioTalonera = $dbo->query($SQLSocioTalonera);

                $TalonerasCompradas = array();
                if ($dbo->rows($QRYSocioTalonera) > 0) :
                    while ($Datos = $dbo->fetchArray($QRYSocioTalonera)) :
                        if ($IDSocio > 0) {
                            $datos_talonera = $dbo->fetchAll("Talonera", "IDTalonera = $Datos[IDTalonera]", "array");
                            $InfoTaloneras[IDCompraTalonera] = $Datos[IDSocioTalonera];
                            $InfoTaloneras[IDTalonera] = $Datos[IDTalonera];
                            $InfoTaloneras[NombreTalonera] = (string)$datos_talonera[NombreTalonera];
                            if ($Datos[TipoMonedero] == 1) :
                                $InfoPorConsumir = "Saldo a favor: $" . number_format($Datos[SaldoMonedero], 2, '.', ',');
                            else :
                                $InfoPorConsumir = "Por Consumir: " . $Datos[CantidadPendiente];
                            endif;

                            $InfoTaloneras[PorConsumir] = $InfoPorConsumir;
                            $InfoTaloneras[FechaCompra] = $Datos[FechaCompra];
                            $InfoTaloneras[FechaVencimiento] = $Datos[FechaVencimiento];
                            $InfoTaloneras[Caracteristicas] = $datos_talonera[DescripcionTalonera];
                            $InfoTaloneras[Total] = $Datos[ValorPagado];
                            // SACAMOS TODOS LOS ASIGNADOS O PERMITIDOS PARA USAR ESA TALONERA
                            $AsignadosA = array();
                            $arragloAsignados = explode("|", $Datos[SociosPosibles]);

                            foreach ($arragloAsignados as $SocioAsignado) :

                                $Socio = explode("-", $SocioAsignado);

                                $InfoAsignado[IDSocio] = $Socio[0];
                                $InfoAsignado[Socio] = $Socio[1];

                                if ($Socio[0] > 0) :
                                    array_push($AsignadosA, $InfoAsignado);
                                endif;



                            endforeach;



                            $InfoTaloneras[AsignadasA] = $AsignadosA;

                            // BUSCAMOS TODOS LOS CONSUMOS DE LA TABLA ConsumoSocioTalorena QUE SE BEDE LLENAR CUANDO SE PAGUE CON TALONERAS
                            $SQLConsumos = "SELECT * FROM ConsumoSocioTalonera WHERE IDSocioTalonera = $Datos[IDSocioTalonera]";
                            $QRYConsumos = $dbo->query($SQLConsumos);
                            $Consumos = array();

                            while ($InfoConsumos = $dbo->fetchArray($QRYConsumos)) :

                                $Reserva = $dbo->getFields("ReservaGeneral", "IDReservaGeneral", "IDReservaGeneral = $InfoConsumos[IDReservaGeneral]");

                                $Eliminada = "";
                                if (empty($Reserva)) :
                                    $Eliminada = "\n(Reserva Eliminada)";
                                endif;

                                $DatosConsumos[IDSocio] = $InfoConsumos[IDSocioConsume];
                                $DatosConsumos[Socio] = $InfoConsumos[SocioConsume] . $Eliminada;
                                $DatosConsumos[Fecha] = $InfoConsumos[FechaConsumo];

                                array_push($Consumos, $DatosConsumos);

                            endwhile;
                        } else {
                            // $datos_talonera = $dbo->fetchAll("TiqueteraFuncionarios", "IDTiqueteraFuncionarios = $Datos[IDTiqueteraFuncionarios]", "array");

                            $datos_talonera = $dbo->fetchAll("TaloneraFunc", "IDTaloneraFunc =  $Datos[IDTaloneraFunc]", "array");
                            $InfoTaloneras[IDCompraTalonera] = $Datos[IDTiqueteraFuncionarios];
                            $InfoTaloneras[IDTalonera] = $Datos[IDTaloneraFunc];
                            $InfoTaloneras[NombreTalonera] = (string)$datos_talonera[NombreTalonera];
                            $InfoPorConsumir = "Por Consumir: " . $Datos[CantidadEntradas];
                            $InfoTaloneras[PorConsumir] = $InfoPorConsumir;
                            $InfoTaloneras[FechaCompra] = $datos_talonera[FechaInicio];
                            $InfoTaloneras[FechaVencimiento] = $datos_talonera[FechaFin];
                            $InfoTaloneras[Caracteristicas] = $datos_talonera[DescripcionTalonera];
                            $InfoTaloneras[Total] = $Datos[CantidadEntradas];

                            // SACAMOS TODOS LOS ASIGNADOS O PERMITIDOS PARA USAR ESA TALONERA
                            $InfoAsignado[IDSocio] = $Datos[NumeroDocumento];
                            $InfoAsignado[Socio] = $Datos[Nombre];
                            $AsignadosA = array();
                            array_push($AsignadosA, $InfoAsignado);


                            $InfoTaloneras[AsignadasA] = $AsignadosA;

                            // BUSCAMOS TODOS LOS CONSUMOS DE LA TABLA ConsumoSocioTalorena QUE SE BEDE LLENAR CUANDO SE PAGUE CON TALONERAS
                            $SQLConsumos = "SELECT * FROM LogTiqueteraFuncionarios WHERE NumeroDocumento = $Datos[NumeroDocumento]";
                            $QRYConsumos = $dbo->query($SQLConsumos);
                            $Consumos = array();

                            while ($InfoConsumos = $dbo->fetchArray($QRYConsumos)) :

                                $tipoConsumo = $InfoConsumos[TipoConsumo];

                                $DatosConsumos[IDSocio] = $InfoConsumos[IDUsuario];
                                $DatosConsumos[Socio] = $InfoConsumos[Nombre] . " (" . $tipoConsumo . ")";
                                $DatosConsumos[Fecha] = $InfoConsumos[FechaConsumo] . " " . $InfoConsumos[HoraConsumo];

                                array_push($Consumos, $DatosConsumos);

                            endwhile;
                        }
                        $InfoTaloneras[Consumos] = $Consumos;

                        array_push($TalonerasCompradas, $InfoTaloneras);

                    endwhile;
                    $EstadoTalonera[TalonerasCompradas] = $TalonerasCompradas;

                    array_push($response, $EstadoTalonera);

                endif;

            endforeach;

            if (count($response) > 0) :
                $message = count($response) . " registros encontrados";
                $respuesta[success] = true;
                $respuesta[message] = $message;
                $respuesta[response] = $response;
            else :
                $respuesta[success] = false;
                $respuesta[message] = "No hay taloneras";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta[success] = false;
            $respuesta[message] = "t3. Faltan parametros";
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function set_taloneras($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDTalonera, $Asignados)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDTalonera)) :

            $Asignados = json_decode($Asignados, true);
            // DEBEN EXISTIR DATOS EN LOS ASIGNADOS PARA SABER A QUIEN VA
            if (count($Asignados) > 0) :

                $datos_talonera = $dbo->fetchAll("Talonera", "IDTalonera = $IDTalonera", "array");
                $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub", "array");
                $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");

                // OBTENER CONFIGURACIÓN
                $Config = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario);
                $PermitirComprarTaloneraPorApp = $Config[response][0][PermitirComprarTaloneraPorApp];
                $PermitirRecargarMonedero = $Config[response][0][PermitirRecargarMonedero];

                $CantidadTotal = $datos_talonera[CantidadEntradas];
                $CantidadPendiente = $CantidadTotal; //CUANDO SE COMPRA DEBE SER EL TOTAL IGUAL AL PENDIENTE, DESPUES SE DESCONTARAN

                if ($IDSocio > 0) :

                    $Campo = "IDSocio";
                    $Valor = $IDSocio;
                    $datos_persona = $dbo->fetchAll("Socio", "IDSocio = $IDSocio", "array");

                    if (count($Asignados) > 1) :
                        // SACAMOS EL ARREGLO DE SOCIOS QUE TOMARON LA TALONERA
                        foreach ($Asignados as $detalle_asignados) :
                            $IDSocioAsignado = $detalle_asignados[IDSocio];
                            $Socio = $detalle_asignados[Socio];

                            $array_socio[] = $IDSocioAsignado . "-" . $Socio;

                        endforeach;

                        $SociosPosibles = implode("|", $array_socio);

                        // SI LA CANTIDAD DE ASIGNADO ES MAYOR A 1 ES PORQUE SELECCIONO PARA TODA LA FAMILIA
                        $ValorPagado = $datos_talonera[ValorGrupoFamiliar];

                        $Dirigida = 'F';
                    else :
                        // SI ENTRA EN ESTE ELSE ES PORQUE EL ARREGLO DE ASIGNADO SOLO TRAE UN ELEMENTO Y ES O EL SOCIO O ALGUN MIEMBRO
                        foreach ($Asignados as $detalle_asignados) :

                            $IDSocioAsignado = $detalle_asignados[IDSocio];
                            $Socio = $detalle_asignados[Socio];

                            $array_socio[] = $IDSocioAsignado . "-" . $Socio;

                        endforeach;

                        $SociosPosibles = implode("|", $array_socio);

                        if (trim($IDSocioAsignado) == trim($IDSocio)) :
                            // SI EL SOCIO DEL ARREGLO ES EL MISMO SOCIO DE LA SOLICITUD EL VALOR ES PARA MI
                            $Dirigida = 'S';
                            $ValorPagado = $datos_talonera[ValorSocio];
                        else :
                            //SI ES POR ESTE CASO ES PARA UN MIEMBRO DEL GRUPO FAMILIAR, ES OTRO VALOR
                            $Dirigida = 'M';
                            $ValorPagado = $datos_talonera[ValorPorMiembro];
                        endif;

                    endif;
                else :

                    $Campo = "IDUsuario";
                    $Valor = $IDUsuario;
                    $datos_persona = $dbo->fetchAll("Usuario", "IDUsuario = $IDUsuario", "array");
                    //SI ES PARA UN USUARIO LA TALONERA SE PONE QUE ES DIRIGIDO A UN USUARIO Y EL VALOR SERA EL MISMO PARA EL SOCIO
                    $Dirigida = 'U';
                    $ValorPagado = $datos_talonera[ValorUsuario];
                endif;

                // Para NUBA CHILDCARE siempre lo inserto para mi familia
                if ($IDClub == 196) {

                    if ($IDTalonera != 838) {
                        $sql_soc_fam = "SELECT IDSocio,Nombre, Apellido FROM Socio WHERE AccionPadre = '" . $datos_persona["Accion"] . "' and iDClub = '" . $IDClub . "' ";
                        $r_soc_fam = $dbo->query($sql_soc_fam);
                        while ($row_soc_fam = $dbo->fetchArray($r_soc_fam)) {
                            $array_socio_nuba[] = $row_soc_fam["IDSocio"] . "-" . $row_soc_fam["Nombre"] . " " . $row_soc_fam["Apellido"];
                        }
                    }

                    $SociosPosibles = implode("|", $array_socio_nuba);

                    // SI LA CANTIDAD DE ASIGNADO ES MAYOR A 1 ES PORQUE SELECCIONO PARA TODA LA FAMILIA
                    $ValorPagado = $datos_talonera[ValorGrupoFamiliar];
                    $Dirigida = 'F';

                    //la tipo ilimitada  
                    if ($IDTalonera == 838 || $IDTalonera == 837 || $IDTalonera == 836 || $IDTalonera == 418) {
                        $Dirigida = 'S';
                        $ValorPagado = $datos_talonera[ValorUsuario];
                        foreach ($Asignados as $detalle_asignados) :
                            $IDSocioAsignado = $detalle_asignados[IDSocio];
                            if ($IDSocioAsignado >= 0 && $IDSocioAsignado <> $IDSocio) {
                                $array_socio_selecc[] = $IDSocioAsignado . "-" . $Socio;
                                $asigna .= $IDSocioAsignado . "-" . $Socio;
                                $array_socio[] = $IDSocioAsignado . "-" . $Socio;
                            }
                        endforeach;

                        if (count($array_socio_selecc) > 1 || count($array_socio_selecc) <= 0) {
                            $respuesta[success] = false;
                            $respuesta[message] = "Esta talonera debe ir asociada a un hijo";
                            $respuesta[response] = null;
                            return $respuesta;
                        } else {
                            $SociosPosibles = implode("|", $array_socio);
                        }
                    }
                }


                // CALCULAMOS FECHA DE VENCIMIENTO
                $Medicion = $datos_talonera[MedicionDuracion];
                $CantidadDuracion = (int) $datos_talonera[Duracion];
                switch ($Medicion):
                    case "Dias":
                        $Tiempo = "days";
                        break;
                    case "Horas":
                        $Tiempo = "hour";
                        break;
                    case "Minutos":
                        $Tiempo = "minutes";
                        break;
                    case "Meses":
                        $Tiempo = "month";
                        break;
                endswitch;

                $FechaCompra = date("Y-m-d");
                $FechaRecarga = date("Y-m-d");
                $FechaVencimiento = date("Y-m-d", strtotime("+" . $CantidadDuracion . " " . $Tiempo, strtotime($FechaCompra)));

                /* ... SECCION VALIDACIONES ESPECIALES ... */

                if ($PermitirComprarTaloneraPorApp == 0) :

                    $respuesta[success] = false;
                    $respuesta[message] = "No esta permitido comprar taloneras por la aplicación";
                    $respuesta[response] = "";

                    return $respuesta;

                endif;
                /* ... FIN SECCION VALIDACIONES ESPECAILES .. */

                $Insertar = 1; //SIEMPRE DEBE INSERTAR LA TALONERA

                if ($PermitirRecargarMonedero == 1) :
                    // BUSCAMOS UNA TALONERA COMO LA QUE VA A COMPARA QUE SEA TIPO MONEDERO PARA ACTULIZARLA

                    $SQLBuscarTalonera = "SELECT IDSocioTalonera WHERE IDClub = $IDClub AND $Campo = $Valor AND TipoMonedero = 1 AND (IDServicio = $IDServicio OR TodosLosServicios = 1) AND Activo = 1 ORDER BY FechaTrCr DESC LIMIT 1";
                    $QRYBuscarTalonera = $dbo->query($SQLBuscarTalonera);
                    $DatosSocioTalonera = $dbo->fetchArray($QRYBuscarTalonera);

                    $IDSocioTalonera = $DatosSocioTalonera[IDSocioTalonera];

                    if (!empty($DatosSocioTalonera)) :

                        $Insertar = 0; //SI SE DEBE ACTULIZAR ENTONCES NO LA INSERTAMOS COMO NUEVA

                        $SQLUpdate = "UPDATE SocioTalonera SET IDTalonera = '$IDTalonera', ValorPagado = '$ValorPagado', FechaRecarga = '$FechaRecarga', FechaVencimiento = '$FechaVencimiento', SaldoMonedero = '$datos_talonera[SaldoTaloneraMonedero]',FechaTrEd = NOW(), UsuarioTrEd = '$Campo-$Valor' WHERE IDSocioTalonera = $IDSocioTalonera";
                        $dbo->query($SQLUpdate);
                    endif;


                endif;


                // INSERTAMOS LA COMPRA DE LA TALONERA

                $SQLInsert = "INSERT INTO SocioTalonera (IDClub, IDTalonera, IDServicio,$Campo, CantidadTotal, CantidadPendiente, SociosPosibles,Dirigida,ValorPagado ,FechaCompra,FechaVencimiento,TipoMonedero,SaldoMonedero,TodosLosServicios,UsuarioTrCr, FechaTrCr,Activo)
				                                                    VALUES  ('$IDClub','$IDTalonera','$IDServicio','$Valor','$CantidadTotal',$CantidadPendiente,'$SociosPosibles','$Dirigida','$ValorPagado','$FechaCompra','$FechaVencimiento','$datos_talonera[TaloneraMonedero]','$datos_talonera[SaldoTaloneraMonedero]',$datos_talonera[TodosLosServicios],'$Campo-$Valor',NOW(),'0')";
                if ($Insertar == 1) :
                    $dbo->query($SQLInsert);
                    $IDSocioTalonera = $dbo->lastID();
                endif;

                //enviar notificacion al socio de que se le asigno una nueva talonera
                $mensaje = "Se ha generado una nueva talonera.";
                $IDModulo = 159;
                SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, $mensaje, $IDModulo, "");

                $ValorPagoTexto = $datos_club_otros["SignoPago"] . " " . (float) $ValorPagado . " " . $datos_club_otros["TextoPago"];

                /* SACAMOS TODOS LOS PARAMETROS PARA EL ARREGLO POST QUE SE ENVIA A LAS PASARELA DE PAGOS */

                $moneda = "COP";
                $refVenta = time();
                $llave_encripcion = $datos_club["ApiKey"];
                $usuarioId = $datos_club["MerchantId"];
                $accountId = (string) $datos_club["AccountId"];
                $descripcion = "Pago Talorena App Mi Club " . $datos_club[Nombre];
                $extra1 = $IDSocioTalonera;
                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda";
                $firma = md5($firma_cadena);

                $emailSocio = $datos_persona["CorreoElectronico"];
                if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                    $emailComprador = $emailSocio;
                } else {
                    $emailComprador = "";
                }

                // ESTA URLS SE VUELVEN DINAMICAS DEPENDIENDO DE LA MODALIDAD DE PAGO; SE PUEDEN VER EN LOS PAGOS SIMPasarelaPagos.inc.php
                $url_respuesta = URLROOT . "respuesta_transaccion.php";
                $url_confirmacion = URLROOT . "confirmacion_pagos.php";

                $Respuesta_Talonera[IDCompraTalonera] = $IDSocioTalonera;
                $Respuesta_Talonera[ValorPago] = $ValorPagado;
                $Respuesta_Talonera[ValorPagoTexto] = $ValorPagoTexto;
                $Respuesta_Talonera[Action] = "";

                $response_parametros = array();
                $datos_post["llave"] = "moneda";
                $datos_post["valor"] = (string) $moneda;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "ref";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "llave";
                $datos_post["valor"] = $llave_encripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "userid";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "usuarioId";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "accountId";
                $datos_post["valor"] = $accountId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "descripcion";
                $datos_post["valor"] = $descripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra1";
                $datos_post["valor"] = (string) $extra1;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = (string) $ValorPagado;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "iva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "baseDevolucionIva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "firma";
                $datos_post["valor"] = $firma;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "emailComprador";
                $datos_post["valor"] = $emailComprador;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "prueba";
                $datos_post["valor"] = (string) $datos_club["IsTest"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "Modulo";
                $datos_post["valor"] = (string) "Taloneras";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "IDSocio";
                $datos_post["valor"] = $IDSocio;
                array_push($response_parametros, $datos_post);

                $Respuesta_Talonera["ParametrosPost"] = $response_parametros;

                /* FIN ARREGLO POST */

                //PAGO CON CREDIBANCO VERSION VIEJA, NO SE QUITA POR PREVENCIÓN
                $datos_post_pago = array();
                $datos_post_pago["iva"] = 0;
                $datos_post_pago["purchaseCode"] = $refVenta;
                $datos_post_pago["totalAmount"] = $ValorPagado * 100;
                $datos_post_pago["ipAddress"] = SIMUtil::get_IP();

                $Respuesta_Talonera["ParametrosPaGo"] = $datos_post_pago;
                //FIN PAGO

                $respuesta["message"] = "Talega Guardada";
                $respuesta["success"] = true;
                $respuesta["response"] = $Respuesta_Talonera;

            else :
                $respuesta[success] = false;
                $respuesta[message] = "Debe seleccionar para quien va dirigida la talonera";
                $respuesta[response] = "";
            endif;

        else :
            $respuesta[success] = false;
            $respuesta[message] = "t4. Faltan parametros";
            $respuesta[response] = "";
        endif;

        return $respuesta;
    } //FIN set_taloneras

    public function set_tipo_pago_talorena($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera, $IDTipoPago, $CodigoPago = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSocioTalonera) && !empty($IDTipoPago)) {

            //verifico que la Talonera exista y pertenezca al club
            $IDCompraTalonera = $dbo->getFields("SocioTalonera", "IDSocioTalonera", "IDSocioTalonera = '$IDSocioTalonera' and IDClub = '$IDClub'");

            if ($IDSocio > 0) :
                $datos_persona = $dbo->fetchAll("Socio", "IDSocio = '$IDSocio'");
            else :
                $datos_persona = $dbo->fetchAll("Usuario", "IDUsuario = '$IDUsuario'");
            endif;

            if (!empty($IDCompraTalonera)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '$CodigoPago' and IDClub = '$IDClub'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '$CodigoPago' and IDClub = '$IDClub'");
                    $valorCodigo = $dbo->getFields("ClubCodigoPago", "Valor", "Codigo = '$CodigoPago' and IDClub = '$IDClub'");

                    $datos_compra = $dbo->fetchAll("SocioTalonera", "IDSocioTalonera = '$IDSocioTalonera'");

                    if (empty($id_codigo)) :

                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    elseif ($codigo_disponible != "S") :

                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;

                    elseif ($valorCodigo != $valorTalonera && ($IDClub == 8 || $IDClub == 28)) :

                        $respuesta["message"] = "El codigo que intenta redimir esta registrado por otro valor";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_actualiza_codigo);
                    endif;

                endif;

                if ($IDSocio > 0 && $datos_persona["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($IDTipoPago == 3) {
                    // se activa la talonera
                    $cond_activacion = ", Activo=1 ";
                }

                $sql_tipo_pago = "UPDATE SocioTalonera SET IDTipoPago =  '$IDTipoPago', CodigoPago = '$CodigoPago' " . $cond_activacion . " WHERE IDSocioTalonera = '$IDSocioTalonera' AND IDClub = '$IDClub'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Atencion la talonera no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function valida_pago_talonera($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM SocioTalonera WHERE IDSocioTalonera = '$IDSocioTalonera'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) :
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4) :

                    if ($r["EstadoTransaccion"] == "") :
                        $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4") :
                        $respuesta["message"] = "Talonera pagada correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = "El pago no fue realizado";
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    endif;

                elseif ($r["IDTipoPago"] == 12) :


                    if ($r["EstadoTransaccion"] == "A") :
                        $respuesta["message"] = "Talonera pagada correctamente!";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        //Compruebo de nuevo la transaccion para confirmar que no este pagada
                        $orden = $dbo->getFields("PagoCredibanco", "NumeroFactura", "reserved12 = '" . $r["IDTaloneraGeneral"] . "'");
                        if (empty($orden)) {
                            $respuesta["message"] = "Talonera en espera de confirmacion de pago";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        } else {
                            $repuesta = SIMPasarelaPagos::CredibancoRespuestaV2($orden);
                            if ($repuesta["success"]) {
                                $estado = $repuesta["response"]["orderStatus"];
                                switch ($estado):
                                    case "0":
                                        // $estadoTx = "NO PAGADO";
                                        $respuesta["message"] = "C10. No pagado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    case "1":
                                    case "7":
                                        // $estadoTx = "PENDIENTE";
                                        $respuesta["message"] = "C11. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;

                                    case "2":
                                        // $estadoTx = "APROBADO";
                                        $respuesta["message"] = "C12.Talonera pagada correctamente.";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                    case "3":
                                    case "6":
                                        // $estadoTx = "RECHAZADO";
                                        $respuesta["message"] = "C13.Transaccion rechazada";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        break;
                                    default:
                                        // $estadoTx = "OTRO";
                                        $respuesta["message"] = "C14. Esperando respuesta de la transaccion";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                        break;
                                endswitch;
                            } else {
                                $respuesta["message"] = "El pago no fue realizado";
                                $respuesta["success"] = false;
                                $respuesta["response"] = $response;
                            }
                        }
                    endif;
                elseif ($r[IDTipoPago] == 6 && $r[Pagado] != 'S') :
                    $respuesta["message"] = "El pago no fue realizado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                elseif (($r["IDTipoPago"] == 18 || $r["IDTipoPago"] == 19 || $r["IDTipoPago"] == 13) && $r[MedioPago] == 'WOMPI-APPROVED') :

                    $respuesta["message"] = "Reserva pagada con wompi correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                else :
                    $respuesta["message"] = "La Talonera no fue pagada por pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            }
        else :
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function eliminia_talorena($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera, $Admin = "", $Razon = "")
    {
        $dbo = SIMDB::get();

        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDSocioTalonera)) :

            if ($IDSocio > 0) :
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_persona = $dbo->fetchAll("Socio", "IDSocio = '$IDSocio'");
            else :
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_persona = $dbo->fetchAll("Usuario", "IDUsuario = '$IDUsuario'");
            endif;

            $datos_talonera = $dbo->fetchAll("SocioTalonera", "IDSocioTalonera = $IDSocioTalonera");
            // OBTENER CONFIGURACIÓN
            $Config = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario);
            $IDConfiguracionTalonera = $Config[response][0][IDConfiguracionTalonera];

            if (!empty($datos_persona)) :
                if (!empty($datos_talonera)) :

                    $TaloneraPago = "N";
                    $SQLTipoPago = "SELECT * FROM ConfiguracionTaloneraTipoPago WHERE IDConfiguracionTalonera = $IDConfiguracionTalonera";
                    $QRYTipoPago = $dbo->query($SQLTipoPago);
                    while ($DatosTipoPago = $dbo->fetchArray($QRYTipoPago)) :

                        $TaloneraPago = "S";
                        //VERIFICAR SI LA TALONERA FUE PAGADA O NO PARA SABER SI SE ELIMINA
                        if ((int)$datos_talonera["IDTipoPago"] <= 0) :
                            $PermiteEliminar = "S";
                        endif;

                    endwhile;

                    if (($datos_talonera["IDTipoPago"] == 1 && $datos_talonera["EstadoTransaccion"] != "A" && empty($Admin)) || ($datos_talonera["IDTipoPago"] == 12 && $datos_talonera["EstadoTransaccion"] != "A" && empty($Admin))) :

                        $update = "UPDATE SocioTalonera SET Activo = 0 WHERE IDSocioTalonera = $IDSocioTalonera";
                        $dbo->query($update);

                        $respuesta["message"] = "Esperando respuesta de la transaccion";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    //para credibanco las trabsacciones pagadas no se pueden eliminar
                    if ($datos_talonera["IDTipoPago"] == 12 && empty($Admin)) :
                        $EstadoTransaccion = $dbo->getFields("PagoCredibanco", "orderStatus", "reserved12 = '" . $IDReserva . "'");
                        if ($EstadoTransaccion == 2) :
                            $respuesta["message"] = "Transaccion pagada correctamente, para eliminar comuniquese con administrador";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;

                    if ((int)$datos_talonera["IDTipoPago"] > 0 && empty($datos_talonera["Pagado"])) :
                        $PermiteEliminar = "S";
                    endif;

                    //VALIDAMOS WOMPI
                    if ((int)$datos_talonera["IDTipoPago"] == 19) :
                        $PermiteEliminar = "N";
                    endif;

                    if ($PermiteEliminar == 'S') :

                        //INSERTAMOS UNA COPIA EN SOCIOTALONERAELIMINADA
                        $RazonEliminación = "ELIMINADA POR SOCIO";
                        if (!empty($Admin)) :
                            $RazonEliminación = "ELIMINADA DESDE ADMINISTRADOR, MOTIVO: $Razon";
                        endif;

                        $SQLInsertCopia = "INSERT IGNORE INTO SocioTaloneraEliminada (IDSocioTalonera,IDClub,IDTalonera,IDServicio,$Campo,IDTipoPago,CodigoPago,Dirigida,ValorPagado,CantidadTotal,CantidadPendiente,FechaCompra,FechaVencimiento,SociosPosibles,EstadoTransaccion,RazonElimacion,UsuarioTrCr,FechaTrCr)
                                                SELECT IDSocioTalonera,IDClub,IDTalonera,IDServicio,$Valor,IDTipoPago,CodigoPago,Dirigida,ValorPagado,CantidadTotal,CantidadPendiente,FechaCompra,FechaVencimiento,SociosPosibles,EstadoTransaccion,'$RazonEliminación','$Campo-$Valor',NOW() 
                                                FROM SocioTalonera WHERE IDSocioTalonera = $IDSocioTalonera";
                        $dbo->query($SQLInsertCopia);

                        //ELIMINAMOS LA TALONERA
                        $SQLDelete = "DELETE FROM SocioTalonera WHERE IDSocioTalonera = $IDSocioTalonera";
                        $dbo->query($SQLDelete);


                        SIMWebServiceTaloneras::notificar_elimina_talonera($IDClub, $IDSocioTalonera, $IDSocio, $IDUsuario, $IDConfiguracionTalonera);

                        if ($IDSocio > 0) :

                            $codigo_canje = SIMWebServiceTaloneras::push_notifica_codigo_pago($IDClub, $IDSocioTalonera, $IDSocio, $IDConfiguracionTalonera);
                            if (!empty($codigo_canje)) :
                                $msg_respuesta = " Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_canje . " Lo puede consultar tambien en el modulo de Notificaciones";
                            endif;

                        endif;

                        $respuesta["message"] = "Talonera eliminada correctamente. " . $msg_respuesta . $mensaje_eliminacion;
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :

                        $respuesta["message"] = "Esperando respuesta ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    endif;

                else :
                    $respuesta["message"] = "La Talonera no existe.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            else :
                $respuesta["message"] = "La persona no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;


        else :
            $respuesta["message"] = "t5. Faltan parametros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function notificar_elimina_talonera($IDClub, $IDSocioTalonera, $IDSocio, $IDUsuario, $IDConfiguracionTalonera)
    {
        $dbo = &SIMDB::get();

        if (!empty($id_reserva)) :

            if ($IDSocio > 0) :
                $Campo = "IDSocio";
                $Valor = $IDSocio;
                $datos_persona = $dbo->fetchAll("Socio", "IDSocio = '$IDSocio'");
            else :
                $Campo = "IDUsuario";
                $Valor = $IDUsuario;
                $datos_persona = $dbo->fetchAll("Usuario", "IDUsuario = '$IDUsuario'");
            endif;

            $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub");
            $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ");
            $datos_talonera = $dbo->fetchAll("SocioTaloneraEliminada", "IDSocioTalonera = $IDSocioTalonera");
            $talonera = $dbo->fetchAll("Talonera", "IDTalonera = $datos_talonera[IDTalonera]");
            $datos_configuracion = $dbo->fetchAll("ConfiguracionTalonera", " IDConfiguracionTalonera = '$IDConfiguracionTalonera'");

            $correo = $datos_configuracion["CorreoNotificaciones"];

            $NombreServicio = $dbo->getFields("Servicio", "Nombre", "IDServicio = '$datos_talonera[IDServicio]'");
            if (empty($NombreServicio)) :
                $ServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '$datos_talonera[IDServicio]'");
                $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $ServicioMaestro");
            endif;

            if (!empty($correo)) :
                $msg = "<br>Cordial Saludo,<br><br>
                        Se ha eliminado una nueva talonera.<br><br>
                        <b>Servicio: </b>$NombreServicio<br>
                        <b>Talonera: </b>$talonera[Nombre]<br>
                        <b>Socio: </b>" . utf8_encode($datos_persona["Nombre"] . " " . $datos_persona["Apellido"]) . "<br>
                        
                        Por favor no responda este correo<br><br>
                        <b>Mi Club App</b>";

                $mensaje = "
                        <body>
                            <table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
                                <tr>
                                    <td>
                                        <img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
                                    </td>
                                </tr>
                                <tr>
                                    <td>" .
                    $msg
                    . "</td>
                                </tr>
                            </table>
                        </body>";

                $url_baja = URLROOT . "contactenos.php";
                $mail = new phpmailer();
                $array_correo = explode(",", $correo);
                if (count($array_correo) > 0) {
                    foreach ($array_correo as $correo_value) {
                        if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                            $mail->AddAddress($correo_value);
                        }
                    }
                }

                $mail->Subject = "Eliminacion Talonera " . $NombreServicio . " " . $datos_persona["Nombre"] . " " . $datos_persona["Apellido"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            endif;
        endif;
    }

    public function push_notifica_codigo_pago($IDClub, $IDSocioTalonera, $IDSocio, $IDConfiguracionTalonera)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocioTalonera)) :

            $datos_talonera = $dbo->fetchAll("SocioTaloneraEliminada", "IDSocioTalonera = $IDSocioTalonera");
            $datos_persona = $dbo->fetchAll("Socio", "IDSocio = '$IDSocio'");
            $valor = $datos_talonera[ValorPagado];

            if ($datos_talonera["Pagado"] == "S" && (($datos_talonera["EstadoTransaccion"] == 4 || $datos_talonera["EstadoTransaccion"] == "A" || $datos_talonera["EstadoTransaccion"] == "Aprobada") || $datos_talonera["IDTipoPago"] == 2)) :

                //generar un codigo valido para redimir
                $codigo_redimir = SIMUtil::generarPassword("6");
                //Inserto el codigo
                $sql_codigo = "INSERT Into ClubCodigoPago (IDClub, IDSocio, Codigo, Disponible, IDServicio, Valor, UsuarioTrCr, FechaTrCr ) VALUES ('$IDClub','$IDSocio', '','S','$datos_talonera[IDServicio]','$valor','Automatico de eliminacion',NOW())";
                $dbo->query($sql_codigo);

                //Envio push con Codigo
                $users = array(
                    array(
                        "id" => $datos_persona["IDSocio"],
                        "idclub" => $datos_talonera["IDClub"],
                        "registration_key" => $datos_persona["Token"],
                        "deviceType" => $datos_persona["Dispositivo"]
                    ),
                );
                $message = "Se genero el siguiente codigo para que lo pueda utilizar en su proxima compra de talonera " . $codigo_redimir;

                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "2";
                $custom["titulo"] = "Codigo Talonera";
                $custom["idseccion"] = "0";
                $custom["iddetalle"] = "0";

                if ($datos_persona["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_persona["Token"];
                } elseif ($datos_persona["Dispositivo"] == "Android") {
                    $array_android[] = $datos_persona["Token"];
                }

                //Guardo el log
                $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales,IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle) Values ('" . $id . "', '$IDSocio','$IDClub','" . $datos_persona["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "')");

                SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            endif;
        endif;

        return $codigo_redimir;
    }

    public function talonera_disponible($IDClub, $IDSocio, $IDServicio, $FechaReserva, $IDSocioBeneficiario)
    {
        $dbo = SIMDB::get();

        $Hoy = date("Y-m-d");

        if (empty($IDSocioBeneficiario)) :
            $Persona = $IDSocio;
        else :
            $Persona = $IDSocioBeneficiario;
        endif;

        $SQLTalonera = "SELECT * FROM SocioTalonera WHERE IDClub = $IDClub AND (CantidadPendiente > 0 OR (TipoMonedero = 1 AND SaldoMonedero > 0)) AND FechaVencimiento >= '$FechaReserva' AND (IDServicio = $IDServicio OR TodosLosServicios = 1) AND (SociosPosibles LIKE '%$Persona%') AND Activo = '1' ORDER BY  SaldoMonedero DESC, CantidadPendiente DESC, IDSocioTalonera DESC LIMIT 1";
        $QRYTalonera = $dbo->query($SQLTalonera);
        $Datos = $dbo->fetchArray($QRYTalonera);

        $IDSocioTalonera = $Datos[IDSocioTalonera];

        return $IDSocioTalonera;
    }

    public function pagar_reserva($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $IDTaloneraDisponible)
    {


        $dbo = SIMDB::get();

        $datos_configuracion = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, "");
        $datos_reserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = $IDReserva");

        $DescontarInvitados = $datos_configuracion[response][0][DescontarInvitados];

        $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '$IDSocio'");
        $SocioConsume = $datos_socio[Nombre] . " " . $datos_socio[Apellido];

        // BUSCAMOS CANTIDAD DE INVITADOS RESERVA Y SABER SI ALCANZA LA TALONERA
        $SQLInvitados = "SELECT * FROM ReservaGeneralInvitado WHERE IDReservaGeneral = $IDReserva";
        $QRYInvitados = $dbo->query($SQLInvitados);

        $CantidadInvitados = $dbo->rows($QRYInvitados);

        $Talonera = $dbo->fetchAll("SocioTalonera", "IDSocioTalonera = $IDTaloneraDisponible");

        $CantidadPendiente = $Talonera[CantidadPendiente];
        $SaldoMonedero = $Talonera[SaldoMonedero];
        $ValorReserva = $datos_reserva[ValorPagado];

        // VALIDAR SI ES TIPO MONEDERO PARA CALCULAR CON RESPECTO AL VALOR DE LA RESERVA PARA SABER SI TIENE EL SALDO SUFICIENTE
        // SI NO SE VALIDA ES CANTIDAD COMO SIEMPRE


        if ($Talonera["Dirigida"] == "S") {
            $Talonera["SociosPosibles"] = str_replace("|", "-", $Talonera["SociosPosibles"]);
            $array_socio_posible = explode("-", $Talonera["SociosPosibles"]);
            if (!in_array($IDSocio, $array_socio_posible)) {
                $respuesta["message"] = "Lo sentimos la talonera esta configurada solo para una persona";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        if ($Talonera[TipoMonedero] == 1) :

            if ($SaldoMonedero >= $ValorReserva) :
                $Valido = true;
            else :
                $Valido = false;
                $mensaje = "No tiene saldo suficiente para pagar la reserva \nValor Reserva: $ValorReserva\nSaldo Disponible: $SaldoMonedero";
            endif;

        else :
            // SE MIRA SI ESTA CONFIGURADO EN SI QUE DESCUNTE A LOS INVITADOS DE LO CONTRARIO SOLO ES UNA TALONERA
            if ($DescontarInvitados == 1) :
                $CantidadNecesaria = $CantidadInvitados + 1;
                $MensajeAdicional = " para usted y los invitados ";
            else :
                $CantidadNecesaria =  1;
            endif;

            if ($CantidadNecesaria <= $CantidadPendiente) :
                $Valido = true;
            else :
                $Valido = false;
                $mensaje = "No tiene suficientes cupos en la talonera $MensajeAdicional \nCantidad Talonera: $CantidadPendiente\nCantidad Necesaria: $CantidadNecesaria";
            endif;
        endif;


        if ($Valido) :
            // ACTULIZAMOS LA RESERVA PARA EL TIPO DE PAGO
            $sql_tipo_pago = "UPDATE ReservaGeneral SET IDTipoPago = '$IDTipoPago', EstadoTransaccion = 'A', MedioPago = 'Talonera', TipoMedioPago = 'Talonera' WHERE IDReservaGeneral = '$IDReserva' AND IDClub = '$IDClub'";
            $dbo->query($sql_tipo_pago);

            //Consulto las automaticas para dejarlas tambien como pagadas
            $sql_aut = "SELECT IDReservaGeneralAsociada FROM ReservaGeneralAutomatica WHERE IDReservaGeneral = '" . $IDReserva . "' ";
            $r_aut = $dbo->query($sql_aut);
            while ($row_aut = $dbo->fetchArray($r_aut)) {
                $sql_tipo_pago = "UPDATE ReservaGeneral SET IDTipoPago = '$IDTipoPago', EstadoTransaccion = 'A', MedioPago = 'Talonera', TipoMedioPago = 'Talonera' WHERE IDReservaGeneral = '" . $row_aut["IDReservaGeneralAsociada"] . "' AND IDClub = '$IDClub'";
                $dbo->query($sql_tipo_pago);
            }

            $FechaUltimoUso = date("Y-m-d");

            // MISMA VALIDACION SI ES UN TIPO MONEDERO
            if ($Talonera[TipoMonedero] == 1) :
                // DESCONTAMOS EN SALDO EN LA TALONERA
                $NuevoSaldo = $SaldoMonedero - $ValorReserva;
                $ModoUso = "Monedero";

                $SQLActualiza = "UPDATE SocioTalonera SET SaldoMonedero = $NuevoSaldo, FechaUltimoUso = '$FechaUltimoUso' WHERE IDSocioTalonera = $IDTaloneraDisponible";
            else :
                // DESCONTAMOS LA TALONERA
                $NuevaCantidad = $CantidadPendiente - $CantidadNecesaria;
                $ModoUso = "Talonera";

                $SQLActualiza = "UPDATE SocioTalonera SET CantidadPendiente = $NuevaCantidad, FechaUltimoUso = '$FechaUltimoUso' WHERE IDSocioTalonera = $IDTaloneraDisponible";
            endif;
            $dbo->query($SQLActualiza);

            // AGREGAMOS EL CONSUMO DE LA TALONERA
            $InserConsumo = "INSERT INTO ConsumoSocioTalonera (IDClub,IDSocioTalonera,IDSocioConsume,SocioConsume,FechaConsumo,IDReservaGeneral,UsuarioTrCr,FechaTrCr, ModoUso) VALUES
                                                            ('$IDClub','$IDTaloneraDisponible','$IDSocio','$SocioConsume',NOW(),'$IDReserva','SOCIO-$IDSocio-$SocioConsume',NOW(),'$ModoUso')";
            $dbo->query($InserConsumo);


            //AGREGAMOS LA FECHA DE VENCIMIENTO EN 1 MES, DESDE EL PRIMER USO DE TALONERA
            //EXCLUSIVO PARA NUBA 

            if ($IDClub == 196) :
                $Talonera = $dbo->fetchAll("SocioTalonera", "IDSocioTalonera = $IDTaloneraDisponible");
                $IDTalonera = $Talonera["IDTalonera"];
                $Talonera_datos = $dbo->fetchAll("Talonera", "IDTalonera = $IDTalonera");
                if ($Talonera["FechaVencimiento"] == "2099-01-01") :
                    $Hoy = date("Y-m-d");
                    $cantidad = $Talonera_datos["Duracion"];
                    $Talonera_datos["MedicionDuracion"];

                    if ($Talonera_datos["MedicionDuracion"] == "Meses") :
                        $mes = date("Y-m-d", strtotime("+ $cantidad month", strtotime($Hoy)));
                    elseif ($Talonera_datos["MedicionDuracion"] == "Dias") :
                        $mes = date("Y-m-d", strtotime("+ $cantidad days", strtotime($Hoy)));
                    else :
                        $mes = date("Y-m-d", strtotime('+1 month', strtotime($Hoy)));
                    endif;



                    $sql = "UPDATE SocioTalonera SET FechaVencimiento = '$mes' WHERE IDSocioTalonera = '$IDTaloneraDisponible' ";
                    $dbo->query($sql);
                endif;

            endif;


            $respuesta["message"] = "Pago con talonera exitoso";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

        else :

            $respuesta["message"] = $mensaje;
            $respuesta["success"] = false;
            $respuesta["validacion"] = "false";
            $respuesta["response"] = null;

        endif;

        return $respuesta;
    }

    public function revertir_cantidad_talonera($IDClub, $IDReserva, $IDSocio, $ValorPagado, $TurnosUtilizados, $Admin)
    {
        $dbo = SIMDB::get();
        $RetornarValor = "S";
        $IDServicio = 0;
        $Config = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario);
        $PermiteRetornar = $Config[response][0][RetornarValorEliminaAdmin];
        if ($PermiteRetornar == '0' && !empty($Admin)) {
            $RetornarValor = "N";
        }


        if ($RetornarValor == 'S') {
            // BUSCAMOS LA TALONERA QUE SE CONSUMIO
            $SQLConsumo = "SELECT IDSocioTalonera FROM ConsumoSocioTalonera WHERE IDSocioConsume = $IDSocio AND IDReservaGeneral = $IDReserva ORDER BY IDConsumoSocioTalonera DESC LIMIT 1";
            $QRYCoonsumo = $dbo->query($SQLConsumo);
            $Datos = $dbo->fetchArray($QRYCoonsumo);

            $Talonera = $dbo->fetchAll("SocioTalonera", "IDSocioTalonera = $Datos[IDSocioTalonera]");
            $DatosReserva = $dbo->fetchAll("ReservaGeneralEliminada", "IDReservaGeneral = $IDReserva");
            if ((int)$DatosReserva["IDServicio"] <= 0) {
                $DatosReserva = $dbo->fetchAll("ReservaGeneral", "IDReservaGeneral = $IDReserva");
                $IDServicio = $DatosReserva["IDServicio"];
            } else {
                $IDServicio = $DatosReserva["IDServicio"];
            }

            $CantidadPendiente = $Talonera[CantidadPendiente];
            $SaldoMonedero = $Talonera[SaldoMonedero];

            if ($Talonera[TipoMonedero] == 1) :
                $SaldoMonedero += $ValorPagado;
                $SQLUpdateTalonera = "UPDATE SocioTalonera SET SaldoMonedero = $SaldoMonedero WHERE IDSocioTalonera = $Datos[IDSocioTalonera]";
            else :
                $CantidadPendiente++;
                $CantidadPendiente += $TurnosUtilizados;

                //Especial office in si es reserva de sala se retorna el doble
                if ($IDClub == 185 && $DatosReserva["IDServicio"] != 31489 && $DatosReserva["IDServicio"] != 31487 && (int)$IDServicio > 0) {
                    $CantidadPendiente *= 2;
                }
                //$CantidadPendiente += $Talonera[CantidadPendiente];
                // ACTUALIZAMOS LA TALONERA
                $SQLUpdateTalonera = "UPDATE SocioTalonera SET CantidadPendiente = $CantidadPendiente WHERE IDSocioTalonera = $Datos[IDSocioTalonera]";
            endif;

            $dbo->query($SQLUpdateTalonera);
        }
    }
}
