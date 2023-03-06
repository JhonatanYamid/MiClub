<?

SIMReg::setFromStructure(array(
    "title" => "Registrar Alimentos Casino",
    "table" => "RegistrarAlimentosCasino",
    "key" => "IDRegistrarAlimentosCasino",
    "mod" => "SocioInvitado"
));


$script = "registraralimentoscasino";

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
            $sql = "SELECT * FROM ConsumoAlimentosCasino WHERE Cedula = '$Cedula' ";
            $query = $dbo->query($sql);
            $datos = $dbo->fetchArray($query);
            $Desayuno = $datos["Desayuno"];
            $Nombre = $datos["Nombre"];

            $Almuerzo = $datos["Almuerzo"];
            $Cena = $datos["Cena"];

                /*    echo $sql;
            exit */;
        }






        $view = "views/" . $script . "/list.php";
        break;
    case "desayuno":
        /*    print_r($_POST);
        exit; */

        //hago la consulta para saber si ya se registro en el dia un desayuno
        $Cedula = $_POST["Cedula"];
        $FechaRegistro = $_POST["FechaRegistro"];
        $Desayuno = $_POST["Desayuno"];
        $sql = "SELECT * FROM RegistrarAlimentosCasino WHERE Cedula ='$Cedula' AND FechaRegistro='$FechaRegistro' AND Desayuno='$Desayuno' ";
        $query = $dbo->query($sql);
        $datosdesayuno = $dbo->fetchArray($query);

        if ($datosdesayuno["FechaRegistro"] <> "" && $datosdesayuno["Desayuno"] == "S") {
            SIMHTML::jsAlert("Ya reclamo el desayuno el dia de hoy");
            SIMHTML::jsRedirect($script . ".php");
        } else
            //hago la consulta para saber las fechas en que el empleado tiene derecho a desayuno
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
            SIMHTML::jsAlert("Se acabo el beneficio del desayuno entre las fechas registradas");
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
