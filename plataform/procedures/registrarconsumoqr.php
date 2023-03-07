<?
SIMReg::setFromStructure(array(
    "title" => "RegistrarConsumoQr",
    "table" => "RegistrarConsumoQr",
    "key" => "IDRegistrarConsumoQr",
    "mod" => "Socio"
));


$script = "registrarconsumoqr";

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
        if ($_GET["IDTipoBusqueda"] == 1) {
            /*           $sql_fechamayor = "SELECT MAX(FechaIngreso) as maximafecha FROM SocioInvitado WHERE NumeroDocumento = '$documentoInvitadoNoResidente' and IDClub = '" . SIMUser::get("club") . "'  Limit 1";
            $query = $dbo->query($sql_fechamayor);
            $invitadonoresidente = $dbo->fetchArray($query);
            $datos_noresidentes = $invitadonoresidente;
            $datos_invitacion["FechaFin"] = $datos_noresidentes["maximafecha"]; */
            $Cedula = $_GET["qryString"];
            $sql = "SELECT * FROM TiqueteraFuncionarios WHERE NumeroDocumento = '$Cedula' ";
            $query = $dbo->query($sql);
            $datos = $dbo->fetchArray($query);
            $Nombre = $datos["Nombre"];
            $CantidadEntradas = $datos["CantidadEntradas"];

                /*    echo $sql;
            exit */;
        }


        $view = "views/" . $script . "/list.php";
        break;
    case "usarTiquete":


        //hago la consulta para saber si ya se registro en el dia un desayuno
        $config = "SELECT * FROM ConfiguracionConsumosTalonera WHERE Publicar = 'S' ORDER BY IDConfiguracionConsumosTalonera DESC LIMIT 1";
        $queryconfig = $dbo->query($config);
        $datosconfig = $dbo->fetchArray($queryconfig);
        date_default_timezone_set('America/Bogota');

        $tipoComida = "Otro";
        if (date('H:i:s') <= $datosconfig["HoraFinDesayuno"] && date('H:i:s') >= $datosconfig["HoraInicioDesayuno"]) {
            $tipoComida = 'Desayuno';
        } elseif (date('H:i:s') >= $datosconfig["HoraInicioAlmuerzo"] && date('H:i:s') <= $datosconfig["HoraFinAlmuerzo"]) {
            $tipoComida = 'Almuerzo';
        } elseif (date('H:i:s') >= $datosconfig["HoraInicioCena"] && date('H:i:s') < $datosconfig["HoraFinCena"]) {
            $tipoComida = 'Cena';
        } else {
            $tipoComida = 'No permitido';
        }

        if ($tipoComida <> 'No permitido') {


            $Cedula = $_POST["Cedula"];
            $sql = "SELECT * FROM TiqueteraFuncionarios WHERE NumeroDocumento = '$Cedula' ";
            $query = $dbo->query($sql);
            $datos = $dbo->fetchArray($query);
            if ($datos[$tipoComida] == "1") {


                $datos['TipoConsumo'] = $tipoComida;
                $datos['FechaConsumo'] = date('Y-m-d');
                $datos['HoraConsumo'] = date('h:i:s');
                $id = $dbo->insert($datos, 'LogTiqueteraFuncionarios', $key);

                $Nombre = $datos["Nombre"];
                $CantidadEntradas = $datos["CantidadEntradas"];
                $dbo->query("UPDATE TiqueteraFuncionarios SET CantidadEntradas = " . (intval($CantidadEntradas) - 1) . " WHERE NumeroDocumento = " . $Cedula . " LIMIT 1 ;");





                // $FechaRegistro = $_POST["FechaRegistro"];
                // $Desayuno = $_POST["Desayuno"];
                // $sql = "SELECT * FROM RegistrarAlimentosCasino WHERE Cedula ='$Cedula' AND FechaRegistro='$FechaRegistro' AND Desayuno='$Desayuno' ";
                // $query = $dbo->query($sql);
                // $datosdesayuno = $dbo->fetchArray($query);

                // if ($datosdesayuno["FechaRegistro"] <> "" && $datosdesayuno["Desayuno"] == "S") {
                //     SIMHTML::jsAlert("Ya reclamo el desayuno el dia de hoy");
                //     SIMHTML::jsRedirect($script . ".php");
                // } else
                //     //hago la consulta para saber las fechas en que el empleado tiene derecho a desayuno
                //     $sql1 = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
                // $query = $dbo->query($sql1);
                // $datos = $dbo->fetchArray($query);

                // if (($FechaRegistro >= $datos["FechaInicio"])  && ($FechaRegistro <= $datos["FechaFin"])) {
                //     //los campos al final de las tablas
                //     $frm = SIMUtil::varsLOG($_POST);


                //     //insertamos los datos
                //     $id = $dbo->insert($frm, $table, $key);
                SIMHTML::jsAlert("Registro valido por " . $tipoComida . ", quedan " . $datos["CantidadEntradas"] . " tickets disponibles");
                SIMHTML::jsRedirect($script . ".php");
            } else {
                $label = "LabelError" . $tipoComida;
                SIMHTML::jsAlert($datosconfig[$label]);
                SIMHTML::jsRedirect($script . ".php");
            }
        } else {
            $label = "LabelError" . $tipoComida;
            SIMHTML::jsAlert("En este momento no se pueden realizar consumos");
            SIMHTML::jsRedirect($script . ".php");
        }




        $view = "views/" . $script . "/list.php";
        break;

    case "almuerzo":

        //hago la consulta para saber si ya se registro en el dia un dalmuerzo
        $Cedula = $_POST["Cedula"];
        $FechaRegistro = $_POST["FechaRegistro"];
        $Almuerzo = $_POST["Almuerzo"];
        $sql = "SELECT * FROM RegistrarAlimentosCasino WHERE Cedula ='$Cedula' AND FechaRegistro='$FechaRegistro' AND Almuerzo='$Almuerzo' ";
        $query = $dbo->query($sql);
        $datosalmuerzo = $dbo->fetchArray($query);

        if ($datosalmuerzo["FechaRegistro"] <> "" && $datosalmuerzo["Almuerzo"] == "S") {
            SIMHTML::jsAlert("Ya reclamo el almuerzo el dia de hoy");
            SIMHTML::jsRedirect($script . ".php");
        } else
            //hago la consulta para saber las fechas en que el empleado tiene derecho a almuerzo
            $sql1 = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
        $query = $dbo->query($sql1);
        $datos = $dbo->fetchArray($query);

        if (($FechaRegistro >= $datos["FechaInicio"])  && ($FechaRegistro <= $datos["FechaFin"])) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);


            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else {
            SIMHTML::jsAlert("Se acabo el beneficio del almuerzo entre las fechas registradas");
        }

        $view = "views/" . $script . "/list.php";
        break;
    case "cena":
        //hago la consulta para saber si ya se registro en el dia un dalmuerzo
        $Cedula = $_POST["Cedula"];
        $FechaRegistro = $_POST["FechaRegistro"];
        $Cena = $_POST["Cena"];
        $sql = "SELECT * FROM RegistrarAlimentosCasino WHERE Cedula ='$Cedula' AND FechaRegistro='$FechaRegistro' AND Cena='$Cena' ";
        $query = $dbo->query($sql);
        $datosCena = $dbo->fetchArray($query);

        if ($datosCena["FechaRegistro"] <> "" && $datosCena["Cena"] == "S") {
            SIMHTML::jsAlert("Ya reclamo la cena el dia de hoy");
            SIMHTML::jsRedirect($script . ".php");
        } else
            //hago la consulta para saber las fechas en que el empleado tiene derecho a almuerzo
            $sql1 = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
        $query = $dbo->query($sql1);
        $datos = $dbo->fetchArray($query);

        if (($FechaRegistro >= $datos["FechaInicio"])  && ($FechaRegistro <= $datos["FechaFin"])) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);


            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else {
            SIMHTML::jsAlert("Se acabo el beneficio de la cena entre las fechas registradas");
        }

        $view = "views/" . $script . "/list.php";
        break;

    case "imprimir-carnet":
        $view = "views/" . $script . "/carnetselector.php";
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
