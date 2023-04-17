<?

SIMReg::setFromStructure(array(
    "title" => "Registrar Alimentos Casino Socio",
    "table" => "RegistrarAlimentosCasino",
    "key" => "IDRegistrarAlimentosCasino",
    "mod" => "SocioInvitado"
));


$script = "registraralimentoscasinosocio";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
/* SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$IngresoPermitidoLag = 4; */


switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "search":

        $qryString = str_replace(".", "", SIMNet::req("qryString"));
        $qryString = str_replace(",", "", $qryString);
        $Cedula = $_GET["qryString"];
        $FechaRegistro = $_POST["FechaRegistro"];
        $FechaRegistro = date('Y-m-d');
        $HoraRegistro = date('H:i:s');
        $frm2['IDClub'] = SIMUser::get("club");
        $frm2['Cedula'] = $Cedula;
        $frm2['FechaRegistro'] = $FechaRegistro;
        $frm2['HoraRegistro'] = $HoraRegistro;
        if ($_GET["IDTipoBusqueda"] == 1) {

            //Consulto si tiene permiso para las comidas
            $sql = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
            $query = $dbo->query($sql);
            $datos = $dbo->fetchArray($query);
            if ($datos == NULL) {
                $mensaje = "No se encuentran registros para la cedula " . $Cedula;
                $frm2['Mensaje'] = $mensaje;
                $log = $dbo->insert($frm2, "LogRegistroAlimentoSocio", $key);
                SIMHTML::jsRedirect($script . ".php");
                die;
            }
            $Desayuno = $datos["Desayuno"];
            $Almuerzo = $datos["Almuerzo"];
            $Cena = $datos["Cena"];
            $Comida = $datos["Comida"];
            $Nombre = $datos["Nombre"];
            $IDClub = $datos["IDClub"];

            //Consulto la configuración de los horarios de las comidas
            $config = "SELECT * FROM ConfiguracionConsumosTaloneraSocio WHERE Publicar = 'S' AND IDClub = '$IDClub' ORDER BY IDConfiguracionConsumosTaloneraSocio DESC LIMIT 1";
            $queryconfig = $dbo->query($config);
            $datosconfig = $dbo->fetchArray($queryconfig);
            date_default_timezone_set('America/Bogota');

            $tipoComida = "Otro";
            if (date('H:i:s') <= $datosconfig["HoraFinDesayuno"] && date('H:i:s') >= $datosconfig["HoraInicioDesayuno"]) {
                $tipoComida = 'Desayuno';
                $valorComida = $Desayuno;
            } elseif (date('H:i:s') >= $datosconfig["HoraInicioAlmuerzo"] && date('H:i:s') <= $datosconfig["HoraFinAlmuerzo"]) {
                $tipoComida = 'Almuerzo';
                $valorComida = $Almuerzo;
            } elseif (date('H:i:s') >= $datosconfig["HoraInicioComida"] && date('H:i:s') <= $datosconfig["HoraFinComida"]) {
                $tipoComida = 'Comida';
                $valorComida = $Comida;
            } elseif (date('H:i:s') >= $datosconfig["HoraInicioCena"] && date('H:i:s') < $datosconfig["HoraFinCena"]) {
                $tipoComida = 'Cena';
                $valorComida = $Cena;
            } else {
                $tipoComida = 'No permitido';
            }

            if ($tipoComida <> 'No permitido') {
                if ($datos[$tipoComida] <> "S") {
                    $label = "LabelError" . $tipoComida;
                    $mensaje = $label . ", para la cedula: " . $Cedula;
                    $frm2['Mensaje'] = $mensaje;
                    $log = $dbo->insert($frm2, "LogRegistroAlimentoSocio", $key);
                    SIMHTML::jsRedirect($script . ".php");
                    die;
                }




                $sql = "SELECT * FROM RegistrarAlimentosCasino WHERE Cedula ='$Cedula' AND FechaRegistro='$FechaRegistro' AND $tipoComida ='$valorComida' ";
                $query = $dbo->query($sql);
                $datoscomida = $dbo->fetchArray($query);

                if ($datoscomida["FechaRegistro"] <> "" && $datoscomida[$tipoComida] == "S") {
                    $mensaje = "La cedula " . $Cedula . " ya reclamo el " . $tipoComida . " el dia de hoy";
                    $frm2['Mensaje'] = $mensaje;
                    $log = $dbo->insert($frm2, "LogRegistroAlimentoSocio", $key);
                    SIMHTML::jsRedirect($script . ".php");
                    die;
                } else {
                    //hago la consulta para saber las fechas en que el empleado tiene derecho a desayuno
                    $sql1 = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
                    $query = $dbo->query($sql1);
                    $datos = $dbo->fetchArray($query);

                    if (($FechaRegistro >= $datos["FechaInicio"])  && ($FechaRegistro <= $datos["FechaFin"])) {
                        //los campos al final de las tablas
                        $frm = SIMUtil::varsLOG($_POST);
                        $frm['IDClub'] = $datos["IDClub"];
                        $frm['Cedula'] = $Cedula;
                        $frm[$tipoComida] = $valorComida;
                        $frm['FechaRegistro'] = $FechaRegistro;
                        $frm['HoraRegistro'] = $HoraRegistro;


                        //insertamos los datos
                        $id = $dbo->insert($frm, $table, $key);
                        $mensaje = "Alimento registrado correctamente para la cedula " . $Cedula;
                        $frm2['Mensaje'] = $mensaje;
                        $log = $dbo->insert($frm2,"LogRegistroAlimentoSocio", $key);
                        SIMHTML::jsRedirect($script . ".php");
                        die;
                    } else {
                        $mensaje = "La cedula " . $Cedula . ", no tiene permitido reclamar en estas fechas " . $tipoComida;
                        $frm2['Mensaje'] = $mensaje;
                        $log = $dbo->insert($frm2, "LogRegistroAlimentoSocio", $key);
                        SIMHTML::jsRedirect($script . ".php");
                        die;
                    }
                }
            } else {
                $mensaje = "Horario no disponible, cedula: " . $Cedula;
                $frm2['Mensaje'] = $mensaje;
                $log = $dbo->insert($frm2, "LogRegistroAlimentoSocio", $key);
                SIMHTML::jsRedirect($script . ".php");
                die;
            }
        } else {
            SIMHTML::jsRedirect($script . ".php");
            die;
        }
        break;
    case "imprimir-carnet":
        $view = "views/" . $script . "/carnetselector.php";
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
