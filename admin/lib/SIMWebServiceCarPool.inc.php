<?php

class SIMWebServiceCarPool
{

    public function get_configuracion($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $arrConfiguracion = array();
        $arrTipos = array();
        $arrMotivos = array();

        //CARPOL_DIR CARPOL_ROOT

        $sqlConf = "SELECT LabelEliminarRuta,LabelCrearDisponibilidad, IconoCrearDisponibilidad, LabelMisViajes, IconoMisViajes, LabelDesdeClub, IconoDesdeClub, LabelHaciaClub, IconoHaciaClub,IconoEliminarRuta,LabelIntroduccionMapa,
                        IF(PermiteModelo = 1, 'S', 'N') as PermiteModelo, IF(PermiteColor = 1, 'S', 'N') as PermiteColor, TextoCalificacion, LabelLLamarConductor, LabelTelefono, LabelEscribirDireccion,
                        IF(PermiteAgregarTelefono = 1, 'S', 'N') as PermiteAgregarTelefono, IF(PermiteAgregarValor = 1, 'S', 'N') as PermiteAgregarValor, IF(PermitePlaca = 1, 'S', 'N') as PermitePlaca, 
                        IF(PermitePlaca = 1, 'S', 'N') as PermitePlaca, IF(PermiteMarca = 1, 'S', 'N') as PermiteMarca, IF(PermiteDescripcion = 1, 'S', 'N') as PermiteDescripcion, LabelDescripcion,
                        LabelMisPublicaciones, LabelMisSolicitudes, LabelReusarRuta, LabelCancelarRuta, LabelCancelarSolicitud
                    FROM ConfiguracionCarPool WHERE IDClub = $IDClub AND Activo = 1 ORDER BY IDConfiguracionCarPool DESC LIMIT 1";
        $qryConf = $dbo->query($sqlConf);
        $cantConf = $dbo->rows($qryConf);

        if ($cantConf > 0) {

            $r_conf = $dbo->fetchArray($qryConf);

            $arrConfiguracion["LabelCrearDisponibilidad"] = $r_conf["LabelCrearDisponibilidad"];
            $arrConfiguracion["LabelMisViajes"] = $r_conf["LabelMisViajes"];
            $arrConfiguracion["LabelDesdeClub"] = $r_conf["LabelDesdeClub"];
            $arrConfiguracion["LabelHaciaClub"] = $r_conf["LabelHaciaClub"];
            $arrConfiguracion["PermiteModelo"] = $r_conf["PermiteModelo"];
            $arrConfiguracion["PermiteColor"] = $r_conf["PermiteColor"];
            $arrConfiguracion["TextoCalificacion"] = $r_conf["TextoCalificacion"];
            $arrConfiguracion["LabelEscribirDireccion"] = $r_conf["LabelEscribirDireccion"];
            $arrConfiguracion["LabelIntroduccionMapa"] = $r_conf["LabelIntroduccionMapa"];
            $arrConfiguracion["LabelLLamarConductor"] = $r_conf["LabelLLamarConductor"];
            $arrConfiguracion["LabelTelefono"] = $r_conf["LabelTelefono"];
            $arrConfiguracion["PermiteAgregarTelefono"] = $r_conf["PermiteAgregarTelefono"];
            $arrConfiguracion["PermiteAgregarValor"] = $r_conf["PermiteAgregarValor"];
            $arrConfiguracion["PermitePlaca"] = $r_conf["PermitePlaca"];
            $arrConfiguracion["PermiteMarca"] = $r_conf["PermiteMarca"];
            $arrConfiguracion["PermiteDescripcion"] = $r_conf["PermiteDescripcion"];
            $arrConfiguracion["LabelDescripcion"] = $r_conf["LabelDescripcion"];
            $arrConfiguracion["LabelMisPublicaciones"] = $r_conf["LabelMisPublicaciones"];
            $arrConfiguracion["LabelMisSolicitudes"] = $r_conf["LabelMisSolicitudes"];
            $arrConfiguracion["LabelReusarRuta"] = $r_conf["LabelReusarRuta"];
            $arrConfiguracion["LabelCancelarRuta"] = $r_conf["LabelCancelarRuta"];
            $arrConfiguracion["LabelCancelarSolicitud"] = $r_conf["LabelCancelarSolicitud"];
            $arrConfiguracion["LabelEliminarRuta"] = $r_conf["LabelEliminarRuta"];

            //Configura la ruta de las imagenes
            $arrConfiguracion["IconoCrearDisponibilidad"] = $r_conf["IconoCrearDisponibilidad"] == '' ? '' : CARPOL_ROOT . $r_conf["IconoCrearDisponibilidad"];
            $arrConfiguracion["IconoMisViajes"] = $r_conf["IconoMisViajes"] == '' ? '' : CARPOL_ROOT . $r_conf["IconoMisViajes"];
            $arrConfiguracion["IconoDesdeClub"] = $r_conf["IconoDesdeClub"] == '' ? '' : CARPOL_ROOT . $r_conf["IconoDesdeClub"];
            $arrConfiguracion["IconoHaciaClub"] = $r_conf["IconoHaciaClub"] == '' ? '' : CARPOL_ROOT . $r_conf["IconoHaciaClub"];
            $arrConfiguracion["IconoEliminarRuta"] = $r_conf["IconoEliminarRuta"] == '' ? '' : CARPOL_ROOT . $r_conf["IconoEliminarRuta"];

            //Trae los tipos de vehiculos activos para el club
            $sqlTipos = "SELECT IDTipoVehiculo, Nombre
                        FROM TipoVehiculo WHERE Publicar = 'S'";
            $qryTipos = $dbo->query($sqlTipos);

            while ($r_tipo = $dbo->fetchArray($qryTipos)) {
                $arrTipo["IDTipoVehiculo"] = $r_tipo["IDTipoVehiculo"];
                $arrTipo["Nombre"] = $r_tipo["Nombre"];
                
            //Trae las preguntas compartidas dependiendo del vehiculo
            $sqlTipospreg = "SELECT * FROM CarroPreguntaDinamica WHERE IDTipoVehiculo=$r_tipo[IDTipoVehiculo] and Publicar = 'S'";
            $qryTipos_preg = $dbo->query($sqlTipospreg);
              $preguntasdinamicas=array();
            while ($r_tipo_preg = $dbo->fetchArray($qryTipos_preg)) {
           
                $preg["IDCampo"] = $r_tipo_preg["IDCarroPreguntaDinamica"];
                $preg["TipoCampo"] = $r_tipo_preg["Tipo"];
                $preg["EtiquetaCampo"] = $r_tipo_preg["Descripcion"];
                $preg["Obligatorio"] = $r_tipo_preg["Obligatorio"];
                $preg["Valores"] = $r_tipo_preg["Valor"];
                $preg["Orden"] = $r_tipo_preg["Orden"];
 

                array_push($preguntasdinamicas, $preg);
                
            }
                $arrConfiguracion["TiposVehiculo"] = $preguntasdinamicas;
                array_push($arrTipos, $arrConfiguracion["TiposVehiculo"]);
                array_push($arrTipos, $arrTipo);
            }

            //Trae los motivos de calificacion configurados para el club
            $sqlMotivos = "SELECT IDMotivosCalificacion, Nombre
                    FROM MotivosCalificacion WHERE IDClub = $IDClub AND Activo = 1";
            $qryMotivos = $dbo->query($sqlMotivos);

            $arrMotivos = array();

            while ($r_motivo = $dbo->fetchArray($qryMotivos)) {
                $arrMotivo["IDMotivosCalificacion"] = $r_motivo["IDMotivosCalificacion"];
                $arrMotivo["Motivo"] = $r_motivo["Nombre"];

                array_push($arrMotivos, $arrMotivo);
            }
 
            $arrConfiguracion["TiposVehiculo"] = $arrTipos;
            $arrConfiguracion["MotivosCalificacion"] = $arrMotivos;

            $message = $cantConf . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANGSESSION);
            $success = true;
            //array_push($response, $arrConfiguracion);
        } else {
            $success = false;
            $message = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANGSESSION);
        }

        $respuesta["message"] = $message;
        $respuesta["success"] = $success;
        $respuesta["response"] = $arrConfiguracion;

        return $respuesta;
    }

    public function get_viajes($IDSocio = 0, $IDUsuario = 0, $IDClub, $Sentido = "", $tipo, $IDTipoVehiculo = "")
    {
        $dbo = &SIMDB::get();
        $hoy = new DateTime();

        $response = array();
        $where = "";

        //Valida las variables que recibe para crear las condiciones de busqueda

        if ($tipo == 1) //1=> para retornar unicamente los viajes Disponibles, 2=> retorna todos los viajes
            $where .= "AND Estado = 1 AND CONCAT(Fecha,' ',Hora) > CURRENT_TIMESTAMP() ";

        if ($IDSocio > 0) {
            if ($tipo == 1)
                $where .= "AND IDSocio != $IDSocio ";
            else
                $where .= "AND IDSocio = $IDSocio ";
        } else {
            if ($tipo == 1)
                $where .= "AND IDUsuario != $IDUsuario ";
            else
                $where .= "AND IDUsuario = $IDUsuario ";
        }

        if ($Sentido != "") {
            $Sentido = $Sentido == "HaciaClub" ? 1 : 0;
            $where .= "AND Sentido = $Sentido ";
        }


        //Trae los campos de direccion latitud y longitud del club 
        $club = $dbo->getFields("Club", array("Direccion", "Nombre"), "IDClub = $IDClub");
        //si el club permite eliminar la ruta
        $PermiteEliminarRuta = $dbo->getFields("ConfiguracionCarPool", "PermiteEliminarRuta", "IDClub = $IDClub");
        $PermiteChat = $dbo->getFields("ConfiguracionCarPool", "PermiteChat", "IDClub = $IDClub");
        $config = $dbo->getFields("ConfiguracionCarPool", array("Latitud, Longitud,PermiteReusar"), "IDClub = $IDClub");

        //Crea el arreglo con los datos de la ubicacion del club
        $arrDirClub = [
            "Direccion" => $club['Direccion'],
            "Latitud" => $config['Latitud'],
            "Longitud" => $config['Longitud']
        ];

        //filtro por vehiculo
        if (!empty($IDTipoVehiculo)) {
            $condicionVehiculo = " AND IDTipoVehiculo='" . $IDTipoVehiculo . "' ";
        }

        $sqlViajes = "SELECT IDViaje, IDSocio, IDUsuario, IDTipoVehiculo, Hora, Fecha, Sentido, CuposTotales, CuposDisponibles, 
                            LugarEncuentro, Direccion, Latitud, Longitud, ValorCupo, Estado, Modelo, Color, Telefono, Placa, Marca, Descripcion
                     FROM Viaje WHERE IDClub = $IDClub $where $condicionVehiculo ORDER BY CONCAT(Fecha,' ',Hora) DESC";

        $qryViajes = $dbo->query($sqlViajes);

        // if ($dbo->rows($qryViajes) > 0) {

        while ($rViajes = $dbo->fetchArray($qryViajes)) {
            $fecha = $rViajes['Fecha'];
            $hora = $rViajes['Hora'];

            if ($tipo == 2 || strtotime($hoy->format("Y-m-d H:i:s")) <= strtotime($fecha . " " . $hora)) {

                $arrViaje['IDViaje'] = $rViajes['IDViaje'];

                //Da formato a la fecha y hora segun requerimientos de la app movil
                $arrViaje["Hora"] = date('H:i:s', strtotime($hora));
                $arrViaje["Fecha"] = date('d-m-Y', strtotime($fecha));
                $arrViaje["FechaVisual"] = date('d-m-Y', strtotime($fecha));
                $arrViaje["HoraVisual"] = date('h:i a', strtotime($hora));

                $arrViaje['Sentido'] = $rViajes['Sentido'] == 1 ? "HaciaClub" : "DesdeClub";
                $arrViaje['CuposTotales'] = $rViajes['CuposTotales'];
                $arrViaje['CuposDisponibles'] = $rViajes['CuposDisponibles'];
                $arrViaje['Telefono'] = $rViajes['Telefono'];
                $arrViaje['Placa'] = $rViajes['Placa'];
                $arrViaje['Marca'] = $rViajes['Marca'];
                $arrViaje['Descripcion'] = $rViajes['Descripcion'];
                $arrViaje['NombreEmpresa'] = $club['Nombre'];
                $arrViaje['PermiteEliminarRuta'] = $PermiteEliminarRuta;

                //Trae el nombre del dueño del viaje 
                if ($rViajes['IDUsuario'] > 0) {
                    $arrViaje['NombreCreador'] = $dbo->getFields("Usuario", "Nombre", "IDUsuario = " . $rViajes['IDUsuario']);
                } else {
                    $arrViaje['NombreCreador'] = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = " . $rViajes['IDSocio']);
                }

                //chat
                $arrViaje['PermiteChat'] = $PermiteChat;
                $arrViaje['LabelChat'] = "Chat con "  . $arrViaje['NombreCreador'];
                $arrViaje['CanalChat'] = "Carpool." . $arrViaje['IDViaje'];

                $arrViaje['LugarEncuentro'] = $rViajes['LugarEncuentro'];

                //Crea el arreglo con los datos de la ubicacion del origen/destino
                $arrDirViaje = [
                    "Direccion" => $rViajes['Direccion'],
                    "Latitud" => $rViajes['Latitud'],
                    "Longitud" => $rViajes['Longitud']
                ];

                //Agrega al arreglo del viaje los datos de origen y destino dependiendo del sentido
                if ($rViajes['Sentido'] == 1) {
                    $arrViaje['Destino'] = $arrDirClub;
                    $arrViaje['Origen'] = $arrDirViaje;
                } else {
                    $arrViaje['Destino'] = $arrDirViaje;
                    $arrViaje['Origen'] = $arrDirClub;
                }

                $arrViaje['ValorCupo'] = $rViajes['ValorCupo'];
                $arrViaje['IDTipoVehiculo'] = $rViajes['IDTipoVehiculo'];
                $arrViaje['NombreTipoVehiculo'] = $dbo->getFields("TipoVehiculo", "Nombre", "IDTipoVehiculo = " . $rViajes['IDTipoVehiculo']);

                //Agrega al arreglo final el nombre de estado y color
                if ($rViajes['Estado'] == 1) {
                    $arrViaje['Estado'] = 'Abierto';

                    $numSolicitudes = $dbo->getFields("SolicitudViaje", "COUNT(IDSolicitudViaje)", "IDViaje = " . $rViajes['IDViaje'] . " AND Estado = 1");

                    if ($numSolicitudes > 0) {

                        $arrViaje['NombreEstado'] = $numSolicitudes . " " . SIMUtil::get_traduccion('', '', 'Poraprobar', LANGSESSION);
                        $arrViaje['ColorEstado'] = '#ffa000';
                    } else {

                        $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'sinsolicitudes', LANGSESSION);
                        $arrViaje['ColorEstado'] = '#898c93';
                    }
                } else {
                    $arrViaje['Estado'] = 'Cerrado';

                    if ($rViajes['Estado'] == 2) {

                        $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Cupolleno', LANGSESSION);
                        $arrViaje['ColorEstado'] = '#26c213';
                    } else {
                        $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Viajecancelado', LANGSESSION);
                        $arrViaje['ColorEstado'] = '#898c93';
                    }
                }

                $arrViaje['Modelo'] = $rViajes['Modelo'];
                $arrViaje['Color'] = $rViajes['Color'];

                //Trae las solicitudes
                $solicitudes = array();

                $sqlSolicitudes = "SELECT IDSolicitudViaje, IDSocio, IDUsuario, IF(Estado = 1, 'PorAprobar', IF(Estado = 2, 'Aprobado', 'Rechazado')) as Estado, TextoSolicitante
                                    FROM SolicitudViaje WHERE Estado != 4 AND IDViaje = " . $rViajes['IDViaje'];
                $qrySolicitudes = $dbo->query($sqlSolicitudes);

                while ($rSolicitudes = $dbo->fetchArray($qrySolicitudes)) {
                    $solicitud['IDSolicitudViaje'] = $rSolicitudes['IDSolicitudViaje'];

                    if ($rSolicitudes['IDUsuario'] > 0) {
                        $solicitud['NombreSolicitante'] = $dbo->getFields("Usuario", "Nombre", "IDUsuario = " . $rSolicitudes['IDUsuario']);
                    } else {
                        $solicitud['NombreSolicitante'] = $dbo->getFields("Socio", "Nombre", "IDSocio = " . $rSolicitudes['IDSocio']);
                    }

                    $solicitud['TextoSolicitante'] = $rSolicitudes['TextoSolicitante'];
                    $solicitud['Estado'] = $rSolicitudes['Estado'];

                    array_push($solicitudes, $solicitud);
                }

                $arrViaje['Solicitudes'] = $solicitudes;

                $arrViaje['PermiteReusar'] = $config['PermiteReusar'] == 1 ? 'S' : 'N';

                array_push($response, $arrViaje);
            }
        }

        $respuesta["message"] = count($response) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANGSESSION);
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        // }
        // else {
        //     $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANGSESSION);
        //     $respuesta["success"] = false;
        //     $respuesta["response"] = null;
        // }

        return $respuesta;
    }

    public function get_viajes_solicitados($IDSocio = 0, $IDUsuario = 0)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $where = $IDSocio > 0 ? "sv.IDSocio = $IDSocio" : "sv.IDUsuario = $IDUsuario";

        $sqlSolicitudes = "SELECT sv.IDSolicitudViaje, sv.IDSocio, sv.IDUsuario, sv.IDViaje, sv.Estado, sv.TextoSolicitante, v.IDClub, v.IDSocio as IDSocioViaje, v.IDUsuario as IDUsuarioViaje, v.IDTipoVehiculo, 
                                v.Hora, v.Fecha, v.Sentido, v.CuposTotales, v.CuposDisponibles, v.LugarEncuentro, v.Direccion, v.Latitud, v.Longitud, v.ValorCupo, v.Estado as EstadoViaje, v.Modelo, v.Color, v.Telefono, v.Placa, v.Marca, v.Descripcion
                            FROM SolicitudViaje as sv, Viaje as v 
                            WHERE sv.IDViaje = v.IDViaje AND sv.Estado < 4 AND CONCAT(v.Fecha,' ',v.Hora) > CURRENT_TIMESTAMP() AND $where";

        // echo $sqlSolicitudes;
        $qrySolicitudes = $dbo->query($sqlSolicitudes);

        // if ($dbo->rows($qrySolicitudes) > 0) {

        while ($rSolicitudes = $dbo->fetchArray($qrySolicitudes)) {

            $arrSolicitud['IDSolicitudViaje'] = $rSolicitudes['IDSolicitudViaje'];

            //Busca el nombre del solicitante
            if ($rSolicitudes['IDUsuario'] > 0) {
                $arrSolicitud['NombreSolicitante'] = $dbo->getFields("Usuario", "Nombre", "IDUsuario = " . $rSolicitudes['IDUsuario']);
            } else {
                $arrSolicitud['NombreSolicitante'] = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = " . $rSolicitudes['IDSocio']);
            }

            $arrSolicitud['TextoSolicitante'] = $rSolicitudes['TextoSolicitante'];

            //Agrega al arreglo final el nombre de estado y color
            if ($rSolicitudes['Estado'] == 1) {
                $arrSolicitud['Estado'] = "PorAprobar";
                $arrSolicitud['ColorEstado'] = '#ffa000';
                $arrSolicitud['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Poraprobar', LANGSESSION);
            } else if ($rSolicitudes['Estado'] == 2) {
                $arrSolicitud['Estado'] = "Aprobado";
                $arrSolicitud['ColorEstado'] = '#26c213';
                $arrSolicitud['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Aprobado', LANGSESSION);
            } else if ($rSolicitudes['Estado'] == 3) {
                $arrSolicitud['Estado'] = "Rechazado";
                $arrSolicitud['ColorEstado'] = '#ff0000';
                $arrSolicitud['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Rechazado', LANGSESSION);
            }

            //Trae la informacion de la configuracion y ubicacion del club
            $config = $dbo->getFields("ConfiguracionCarPool", array("PermiteCalificar", "PermiteCancelar", "PermiteAgregarTelefono", "Latitud", "Longitud", "PermiteChat"), "IDClub = " . $rSolicitudes['IDClub']);

            $club = $dbo->getFields("Club", array("Direccion", "Nombre"), "IDClub = " . $rSolicitudes['IDClub']);

            $permiteCalificar = $rSolicitudes['Estado'] == 2 ? "S" : "N";
            $arrSolicitud['PermiteCalificar'] = $config['PermiteCalificar'] == 1 ? $permiteCalificar : "N";

            $permiteCancelar = $config['PermiteCancelar'] == 1 ? "S" : "N";
            $arrSolicitud['PermiteCancelar'] = $config['PermiteCalificar'] == 1 ? $permiteCancelar : "N";

            $permitellamar = $rSolicitudes['Estado'] == 2 ? "S" : "N";
            $arrSolicitud['PermiteLlamarAlConductor'] = $config['PermiteAgregarTelefono'] == 1 ? $permitellamar : "N";
            $arrSolicitud['NumeroTelefonoConductor'] = $rSolicitudes['Telefono'];

            //organiza el arreglo del viaje
            $arrViaje['IDViaje'] = $rSolicitudes['IDViaje'];


            //Da formato a la fecha y hora segun requerimientos de la app movil
            $fecha = $rSolicitudes['Fecha'];
            $hora = $rSolicitudes['Hora'];

            $arrViaje["Hora"] = date('H:i:s', strtotime($hora));
            $arrViaje["Fecha"] = date('d-m-Y', strtotime($fecha));
            $arrViaje["FechaVisual"] = date('d-m-Y', strtotime($fecha));
            $arrViaje["HoraVisual"] = date('h:i a', strtotime($hora));

            //Crea el arreglo con los datos de la ubicacion del club
            $arrDirClub = [
                "Direccion" => $club['Direccion'],
                "Latitud" => $config['Latitud'],
                "Longitud" => $config['Longitud']
            ];

            //Crea el arreglo con los datos de la ubicacion del origen/destino
            $arrDirViaje = [
                "Direccion" => $rSolicitudes['Direccion'],
                "Latitud" => $rSolicitudes['Latitud'],
                "Longitud" => $rSolicitudes['Longitud']
            ];

            //Agrega al arreglo del viaje los datos de origen y destino dependiendo del sentido
            if ($rSolicitudes['Sentido'] == 1) {
                $arrViaje['Sentido'] = "HaciaClub";
                $arrViaje['Origen'] = $arrDirViaje;
                $arrViaje['Destino'] = $arrDirClub;
            } else {
                $arrViaje['Sentido'] = "DesdeClub";
                $arrViaje['Origen'] = $arrDirClub;
                $arrViaje['Destino'] = $arrDirViaje;
            }

            $arrViaje['CuposTotales'] = $rSolicitudes['CuposTotales'];
            $arrViaje['CuposDisponibles'] = $rSolicitudes['CuposDisponibles'];
            $arrViaje['NombreEmpresa'] = $club['Nombre'];

            //Trae el nombre del dueño del viaje 
            if ($rSolicitudes['IDUsuarioViaje'] > 0) {
                $arrViaje['NombreCreador'] = $dbo->getFields("Usuario", "Nombre", "IDUsuario = " . $rSolicitudes['IDUsuarioViaje']);
            } else {
                $arrViaje['NombreCreador'] = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = " . $rSolicitudes['IDSocioViaje']);
            }

            //chat
            //$arrViaje['PermiteChat'] = $config['PermiteChat'];
            $arrViaje['PermiteChat'] = $arrSolicitud['Estado'] == "Aprobado" ? "S" : "";
            $arrViaje['LabelChat'] = "Chat con " . $arrViaje['NombreCreador'];
            $arrViaje['CanalChat'] = "Carpool." . $arrViaje['IDViaje'];

            $arrViaje['LugarEncuentro'] = $rSolicitudes['LugarEncuentro'];
            $arrViaje['ValorCupo'] = $rSolicitudes['ValorCupo'];
            $arrViaje['IDTipoVehiculo'] = $rSolicitudes['IDTipoVehiculo'];
            $arrViaje['NombreTipoVehiculo'] = $dbo->getFields("TipoVehiculo", "Nombre", "IDTipoVehiculo = " . $rSolicitudes['IDTipoVehiculo']);

            //Agrega al arreglo final el nombre de estado y color
            if ($rSolicitudes['EstadoViaje'] == 1) {

                $arrViaje['Estado'] = 'Abierto';
                $arrViaje['ColorEstado'] = '#26c213';
                $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Esperandocupo', LANGSESSION);
            } else {
                $arrViaje['Estado'] = 'Cerrado';

                if ($rSolicitudes['EstadoViaje'] == 2) {
                    $arrViaje['ColorEstado'] = '#ff0000';
                    $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Cupolleno', LANGSESSION);
                } else {
                    $arrViaje['ColorEstado'] = '#898c93';
                    $arrViaje['NombreEstado'] = SIMUtil::get_traduccion('', '', 'Viajecancelado', LANGSESSION);
                }
            }

            $arrViaje['Modelo'] = $rSolicitudes['Modelo'];
            $arrViaje['Color'] = $rSolicitudes['Color'];
            $arrViaje['Placa'] = $rSolicitudes['Placa'];
            $arrViaje['Marca'] = $rSolicitudes['Marca'];
            $arrViaje['Descripcion'] = $rSolicitudes['Descripcion'];

            $arrSolicitud['Viaje'] = $arrViaje;

            array_push($response, $arrSolicitud);
        }

        $respuesta["message"] = $dbo->rows($qrySolicitudes) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANGSESSION);
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        // }
        // else {
        //     $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANGSESSION);
        //     $respuesta["success"] = false;
        //     $respuesta["response"] = null;
        // }

        return $respuesta;
    }

    public function set_solicitar($IDSocio = 0, $IDUsuario = 0, $IDViaje)
    {
        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        $viaje = $dbo->getFields("Viaje", array("Estado", "CuposDisponibles"), "IDViaje = $IDViaje");

        if ($viaje['Estado'] == 1) {

            if ($viaje['CuposDisponibles'] > 0) {

                if ($IDUsuario > 0) {
                    $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
                } else {
                    $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
                }

                $arrSolicitud = [
                    "IDSocio" => $IDSocio,
                    "IDUsuario" => $IDUsuario,
                    "IDViaje" => $IDViaje,
                    "Estado" => 1,
                    "UsuarioTrCr" => $nombre,
                    "FechaTrCr" => $hoy->format("Y-m-d H:i:s")
                ];

                $idSolicitud = $dbo->insert($arrSolicitud, 'SolicitudViaje', 'IDSolicitudViaje');

                //Envia una notificacion y correo al usuario o socio
                SimWebServiceCarPool::enviar_notificacion(1, $IDUsuario, $IDSocio, $IDViaje);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Solicitudcreadaconexito', LANGSESSION);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,nohaycuposdisponibles', LANGSESSION);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,elviajenoestadisponible', LANGSESSION);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_viaje($IDClub, $IDSocio = 0, $IDUsuario = 0, $Fecha, $Hora, $LugarEncuentro, $IDTipoVehiculo, $Sentido, $Latitud, $Longitud, $Direccion, $Cupos, $Modelo = "", $Color = "", $Valor = "", $Telefono = "", $Placa = "", $Marca = "", $Descripcion = "", $CamposDinamicosTipoVehiculo)
    {

        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDClub) && !empty($Fecha) && !empty($Hora) && !empty($LugarEncuentro) && !empty($IDTipoVehiculo) && !empty($Sentido) && !empty($Latitud) && !empty($Longitud) && !empty($Direccion) && !empty($Cupos)) {

            if ($IDUsuario > 0) {
                $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
            } else {
                $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
            }

            $arrViaje = [
                "IDClub" => $IDClub,
                "IDSocio" => $IDSocio,
                "IDUsuario" => $IDUsuario,
                "IDTipoVehiculo" => $IDTipoVehiculo,
                "Fecha" => $Fecha,
                "Hora" => $Hora,
                "LugarEncuentro" => $LugarEncuentro,
                "Sentido" => $Sentido == "HaciaClub" ? 1 : 0,
                "Latitud" => $Latitud,
                "Longitud" => $Longitud,
                "Direccion" => $Direccion,
                "CuposTotales" => $Cupos,
                "CuposDisponibles" => $Cupos,
                "Modelo" => $Modelo,
                "Color" => $Color,
                "ValorCupo" => $Valor,
                "Telefono" => $Telefono,
                "Placa" => $Placa,
                "Marca" => $Marca,
                "Descripcion" => $Descripcion,
                "Estado" => 1,
                "UsuarioTrCr" => $nombre,
                "FechaTrCr" => $hoy->format("Y-m-d H:i:s")
            ];

            $idViaje = $dbo->insert($arrViaje, 'Viaje', 'IDViaje');

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Registradoconexito', LANGSESSION);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_calificacion($IDSolicitudViaje, $IDSocio = 0, $IDUsuario = 0, $Calificacion, $Motivos)
    {
        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDSolicitudViaje) && !empty($Calificacion) && !empty($Motivos)) {

            if ($IDUsuario > 0) {
                $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
            } else {
                $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
            }

            $arrMotivos = explode(",", $Motivos);

            $arrCalificacion = [
                "IDSocio" => $IDSocio,
                "IDUsuario" => $IDUsuario,
                "IDSolicitudViaje" => $IDSolicitudViaje,
                "Calificacion" => $Calificacion,
                "UsuarioTrCr" => $nombre,
                "FechaTrCr" => $hoy->format("Y-m-d H:i:s")
            ];

            foreach ($arrMotivos as $idMotivo) {
                $arrCalificacion['IDMotivosCalificacion'] = $idMotivo;
                $idCalificacion = $dbo->insert($arrCalificacion, 'CalificacionCarPool', 'IDCalificacionCarPool');
            }

            //Envia una notificacion y correo al usuario o socio
            $idViaje = $dbo->getFields("IDSolicitudViaje", "IDViaje", "IDSolicitudViaje = $IDSolicitudViaje");
            SimWebServiceCarPool::enviar_notificacion(6, $IDUsuario, $IDSocio, $idViaje);

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Registradoconexito', LANGSESSION);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_aprobar($IDViaje, $IDSolicitudViaje, $IDSocio = 0, $IDUsuario = 0, $Aprobado)
    {

        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDViaje) && !empty($IDSolicitudViaje) && !empty($Aprobado)) {

            $cupos = $dbo->getFields("Viaje", "CuposDisponibles", "IDViaje = $IDViaje");

            if ($cupos > 0) {

                $solicitudAnterior = $dbo->getFields("SolicitudViaje", array("Estado", "IDUsuario", "IDSocio"), "IDSolicitudViaje = $IDSolicitudViaje");

                if ($IDUsuario > 0) {
                    $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
                } else {
                    $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
                }

                $arrSolicitud = [
                    "IDSocioAprueba" => $IDSocio,
                    "IDUsuarioAprueba" => $IDUsuario,
                    "Estado" => $Aprobado == 'S' ? 2 : 3,
                    "UsuarioTrEd" => $nombre,
                    "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                ];

                $dbo->update($arrSolicitud, "SolicitudViaje", "IDSolicitudViaje", $IDSolicitudViaje);

                if ($Aprobado == 'S' && $solicitudAnterior['Estado'] != 2) {

                    $disponibles = $cupos - 1;

                    $arrViaje = [
                        "CuposDisponibles" => $disponibles,
                        "UsuarioTrEd" => $nombre,
                        "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                    ];

                    $arrViaje['Estado'] = $disponibles > 0 ? 1 : 2;

                    if ($disponibles == 0) //envia notificacion y correo si los cupos estan completos
                        SimWebServiceCarPool::enviar_notificacion(4, $IDUsuario, $IDSocio, $IDViaje);

                    $dbo->update($arrViaje, "Viaje", "IDViaje", $IDViaje);

                    //Envia una notificacion y correo al usuario o socio
                    SimWebServiceCarPool::enviar_notificacion(2, $solicitudAnterior['IDUsuario'], $solicitudAnterior['IDSocio'], $IDViaje);
                } else if ($Aprobado == 'N' && $solicitudAnterior['Estado'] != 3) {

                    $disponibles = $solicitudAnterior['Estado'] == 2 ? $cupos + 1 : $cupos;

                    $arrViaje = [
                        "CuposDisponibles" => $disponibles,
                        "UsuarioTrEd" => $nombre,
                        "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                    ];

                    $arrViaje['Estado'] = $disponibles > 0 ? 1 : 2;

                    $dbo->update($arrViaje, "Viaje", "IDViaje", $IDViaje);

                    //Envia una notificacion y correo al usuario o socio
                    SimWebServiceCarPool::enviar_notificacion(3, $solicitudAnterior['IDUsuario'], $solicitudAnterior['IDSocio'], $IDViaje);
                }

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Registradoconexito', LANGSESSION);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,nohaycuposdisponibles', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cancelar_viaje($IDViaje, $IDSocio = 0, $IDUsuario = 0)
    {

        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDViaje)) {

            //Obtiene los datos del viaje para validar si se puede cancelar y crear notificaciones
            $viaje = $dbo->getFields("Viaje", array("Fecha", "Hora", "IDClub", "Estado"), "IDViaje = $IDViaje");

            $fecha = $viaje['Fecha'] . " " . $viaje['Hora'];

            if ($viaje['Estado'] != 3) {
                if (strtotime($fecha) > strtotime($hoy->format("Y-m-d H:i:s"))) {

                    //Obtiene el nombre del usuario que se envia, usuario dueño del viaje
                    if ($IDUsuario > 0) {
                        $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
                    } else {
                        $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
                    }

                    //crea arreglo para actualizar el estado del viaje
                    $arrViaje = [
                        "Estado" => 3,
                        "UsuarioTrEd" => $nombre,
                        "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                    ];

                    $dbo->update($arrViaje, "Viaje", "IDViaje", $IDViaje);

                    //Busca todas las solicitudes realizadas al viaje
                    $sqlSolicitudes = "SELECT IDSolicitudViaje, Estado, IDSocio, IDUsuario FROM SolicitudViaje WHERE IDViaje = $IDViaje AND Estado < 3";
                    $qrySolicitudes = $dbo->query($sqlSolicitudes);

                    if ($dbo->rows($qrySolicitudes) > 0) {

                        while ($r_Solicitud = $dbo->fetchArray($qrySolicitudes)) {

                            //Crea arreglo para actualizar las solicitudes
                            $arrSolicitud = [
                                "IDSocioAprueba" => $IDSocio,
                                "IDUsuarioAprueba" => $IDUsuario,
                                "Estado" => 3,
                                "UsuarioTrEd" => $nombre,
                                "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                            ];

                            //Actualiza las solicitudes de viaje a rechazadas
                            $dbo->update($arrSolicitud, "SolicitudViaje", "IDSolicitudViaje", $r_Solicitud['IDSolicitudViaje']);

                            //Envia una notificacion y correo al usuario o socio
                            SimWebServiceCarPool::enviar_notificacion(5, $IDUsuario, $IDSocio, $IDViaje);
                        }
                    }

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Canceladoconexito', LANGSESSION);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noesposiblecancelarelviaje', LANGSESSION);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elviajeyafuecancelado', LANGSESSION);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cancelar_solicitud($IDSolicitudViaje, $IDSocio = 0, $IDUsuario = 0)
    {

        $dbo = &SIMDB::get();
        $hoy = new DateTime;

        if (($IDSocio > 0 || $IDUsuario > 0) && !empty($IDSolicitudViaje)) {

            $solicitud = $dbo->getFields("SolicitudViaje", array("Estado", "IDViaje"), "IDSolicitudViaje = $IDSolicitudViaje");

            if ($IDUsuario > 0) {
                $nombre = $dbo->getFields("Usuario", "Nombre", "IDUsuario = $IDUsuario");
            } else {
                $nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = $IDSocio");
            }

            $arrSolicitud = [
                "Estado" => 4,
                "UsuarioTrEd" => $nombre,
                "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
            ];

            $dbo->update($arrSolicitud, "SolicitudViaje", "IDSolicitudViaje", $IDSolicitudViaje);

            if ($solicitud['Estado'] == 2) {

                $cupos = $dbo->getFields("Viaje", "CuposDisponibles", "IDViaje = " . $solicitud['IDViaje']);
                $disponibles = $cupos + 1;

                $arrViaje = [
                    "CuposDisponibles" => $disponibles,
                    "UsuarioTrEd" => $nombre,
                    "FechaTrEd" => $hoy->format("Y-m-d H:i:s")
                ];

                $arrViaje['Estado'] = $disponibles > 0 ? 1 : 2;

                $dbo->update($arrViaje, "Viaje", "IDViaje", $solicitud['IDViaje']);
            }

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Canceladoconexito', LANGSESSION);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function enviar_notificacion($tipo, $idUsuario, $idSocio, $idViaje)
    {

        $dbo = &SIMDB::get();

        //Obtiene los datos del viaje para validar si se puede cancelar y crear notificaciones
        $viaje = $dbo->getFields("Viaje", array("Fecha", "Hora", "Direccion", "Sentido", "IDClub"), "IDViaje = $idViaje");

        //Obtiene el nombre del club
        $nmClub = $dbo->getFields("Club", "Nombre", "IDClub = " . $viaje['IDClub']);

        //Organiza la informacion a enviar en las notificaciones y correo
        $fecha = date('d-m-Y', strtotime($viaje['Fecha'] . " " . $viaje['hora']));

        if ($viaje['Sentido'] == 1) {
            $ruta = utf8_encode($nmClub . " a " . $viaje['Direccion']);
        } else {
            $ruta = utf8_encode($viaje['Direccion'] . " a " . $nmClub);
        }

        if ($tipo == 1) { // Solicitud de cupo

            $msgNotificacion = "Tiene una nueva solicitud de cupo";
            $msgCorreo = "Le informamos que tiene una nueva solicitud de cupo para el viaje del dia $fecha con ruta $ruta";
            $asunto = "Nueva solicitud";
        } else if ($tipo == 2) { //Aprobacion de solicitud

            $msgNotificacion = "Su solicitud para el viaje del dia $fecha fue aprobada";
            $msgCorreo = "Le informamos que su solicitud para el viaje del dia $fecha con ruta $ruta fue aprobada";
            $asunto = "Solicitud aprobada";
        } else if ($tipo == 3) { // Rechazo de solicitud

            $msgNotificacion = "Su solicitud para el viaje del dia $fecha fue rechazada";
            $msgCorreo = "Le informamos que su solicitud para el viaje del dia $fecha con ruta $ruta fue rechazada";
            $asunto = "Solicitud rechazada";
        } else if ($tipo == 4) { // Cupo lleno

            $msgNotificacion = "El cupo para el viaje del dia $fecha esta completo";
            $msgCorreo = "Le informamos que el cupo para el viaje del dia $fecha con ruta $ruta esta completo";
            $asunto = "Cupo lleno";
        } else if ($tipo == 5) { // Cancelacion de viaje 

            $msgNotificacion = "El viaje del dia $fecha fue cancelado";
            $msgCorreo = "Le informamos que el viaje del dia $fecha con ruta $ruta fue cancelado";
            $asunto = "Viaje cancelado";
        } else if ($tipo == 6) { // Calificacion

            $msgNotificacion = "El viaje del dia $fecha tiene una nueva calificación";
            $msgCorreo = "Le informamos que el viaje del dia $fecha con ruta $ruta tiene una nueva calificación";
            $asunto = "Tiene una nueva calificación";
        }

        //Valida si es usuario o socio y obtiene correo, tambien envia una notificacion push
        if ($idUsuario > 0) {
            $correo = $dbo->getFields("Usuario", "Email", "IDUsuario = $idUsuario");

            //Envia la notificacion al usuario
            SIMUtil::enviar_notificacion_push_general_funcionario($viaje['IDClub'], $idUsuario, $msgNotificacion);
        } else {
            $correo = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio = $idSocio");

            //Envia la notificacion al socio
            SIMUtil::enviar_notificacion_push_general($viaje['IDClub'], $idSocio, $msgNotificacion, 175, '');
        }

        //Envia notificacion por correo
        if ($correo != "") {
            $msg = "<br>Cordial Saludo,<br><br>
                    $msgCorreo<br><br>
                    Por favor no responda este correo,<br>
                    Cordialmente<br><br>
                    <b>Notificaciones " . utf8_encode($nmClub) . "</b>";

            SIMUtil::send_mail($viaje['IDClub'], $correo, $asunto, $msg);
        }

        return true;
    }

    public function set_eliminar_ruta_publicada_carpool($IDSocio, $IDUsuario, $IDViaje)
    {
        $dbo = &SIMDB::get();

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDViaje)) {


            if ($IDSocio > 0) {
                $ID = $IDSocio;
                $Campo = "IDSocio";
            } else if ($IDUsuario > 0) {
                $ID = $IDUsuario;
                $Campo = "IDUsuario";
            }

            $sql_elimina_viaje = $dbo->query("DELETE FROM Viaje  Where IDViaje = '" . $IDViaje . "' and $Campo=" . $ID);



            $respuesta["message"] = "eliminado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = "eliminado";
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function  get_configuracion_chat($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();


        $sql = "SELECT PubNubPublishKey,PubNubSuscribeKey FROM ConfiguracionCarPool  WHERE IDClub = '" . $IDClub . "' AND Activo='1'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosConfiguracionChat = $dbo->fetchArray($qry)) {

                $configuracionChat["PubNubPublishKey"] = $DatosConfiguracionChat["PubNubPublishKey"];
                $configuracionChat["PubNubSuscribeKey"] = $DatosConfiguracionChat["PubNubSuscribeKey"];
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracionChat;
        } //End if
        else {
            $respuesta["message"] = "Configuracion no esta activa";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }
}
