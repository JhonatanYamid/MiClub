<?

SIMReg::setFromStructure(array(
    "title" => "ReporteTalonera",
    "table" => "SocioTalonera",
    "key" => "IDSocioTalonera",
    "mod" => "Socio"
));


$script = "reportetalonera";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            /* print_r($_POST);
            exit; */
            $IDTalonera = $_POST['IDTalonera'];
            $IDClub = $_POST['IDClub'];

            //consultamos la cantidad de entradas en la talonera
            // $cantidadEntradas = $dbo->getFields("Talonera", "CantidadEntradas", "IDTalonera = '" . $IDTalonera . "' and IDClub = '" . $IDClub . "' ");

            $datos_talonera = $dbo->fetchAll("Talonera", "IDTalonera = $IDTalonera", "array");

            $frm['CantidadTotal'] = $datos_talonera[CantidadEntradas];
            $frm['CantidadPendiente'] = $datos_talonera[CantidadEntradas];
            $frm['TipoMonedero'] = $datos_talonera[TaloneraMonedero];
            $frm['SaldoMonedero'] = $datos_talonera[SaldoTaloneraMonedero];
            $frm['TodosLosServicios'] = $datos_talonera[TodosLosServicios];
            $frm['Activo'] = 1;

            $NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = $frm[IDSocio]") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = $frm[IDSocio]");

            $SociosPosibles = $frm[IDSocio] . "-" . $NombreSocio . "|" . $frm[SociosPosibles];

            $frm[SociosPosibles] = $SociosPosibles;
            //fecha actual la insertamos en fecha compra
            $fechaActual = date("Y-m-d");
            $frm["FechaCompra"] = $fechaActual;

            // CALCULAMOS FECHA DE VENCIMIENTO
            $Medicion = $datos_talonera[MedicionDuracion];
            $CantidadDuracion = $datos_talonera[Duracion];
            $CantidadDuracion = (int) $CantidadDuracion;

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
            $FechaVencimiento = date("Y-m-d", strtotime("+" . $CantidadDuracion . " " . $Tiempo, strtotime($FechaCompra)));
             if($IDClub==196):
               $FechaVencimiento="2099-01-01";
            endif;
            $frm["FechaVencimiento"] = $FechaVencimiento;

           
             if($IDClub==185):
 //ACA HACEMOS LA CTUALIZACION DE LA TALONERA SI YA EL SOCIO TENIA UNA Y EL TIPO ES MONEDERO
            
            $SQLConsumo = "SELECT * FROM SocioTalonera WHERE IDSocio = $frm[IDSocio] and TipoMonedero=1 and IDTalonera=$IDTalonera  ORDER BY IDSocioTalonera DESC LIMIT 1";
            $QRYCoonsumo = $dbo->query($SQLConsumo);
            $Datos = $dbo->fetchArray($QRYCoonsumo);
            if($Datos["IDSocioTalonera"]>0):
            $IDSocioTalonera=$Datos["IDSocioTalonera"];
            $Datos["IDTalonera"];
            $Datos["TipoMonedero"];
            $Datos["FechaRecarga"];
            $Datos["FechaVencimiento"];
            $Datos["SaldoMonedero"];
            
            
            $frm['CantidadTotal'] = $datos_talonera[CantidadEntradas] + $Datos["CantidadTotal"];
            $frm['CantidadPendiente'] = $datos_talonera[CantidadEntradas] + $Datos["CantidadPendiente"];
            $frm['TipoMonedero'] = $datos_talonera[TaloneraMonedero];
            $frm['SaldoMonedero'] = $datos_talonera[SaldoTaloneraMonedero] + $Datos["SaldoMonedero"];
            $frm['TodosLosServicios'] = $datos_talonera[TodosLosServicios];
            $frm['Activo'] = 1;
            
            $Hoy = date("Y-m-d"); 
            $cantidad= $datos_talonera["Duracion"];
            $datos_talonera["MedicionDuracion"];
            
            if($Talonera_datos["MedicionDuracion"]=="Meses"):
           $mes = date("Y-m-d", strtotime("+ $cantidad month", strtotime($Hoy)));
            elseif($Talonera_datos["MedicionDuracion"]=="Dias"):
           $mes = date("Y-m-d", strtotime("+ $cantidad days", strtotime($Hoy)));
            else:
           $mes = date("Y-m-d", strtotime('+1 month', strtotime($Hoy)));
            endif;
            
           
 
            $sql = "UPDATE SocioTalonera SET FechaVencimiento='$mes', FechaRecarga='$Hoy', CantidadTotal=$frm[CantidadTotal] , CantidadPendiente= $frm[CantidadPendiente] , SaldoMonedero=$frm[SaldoMonedero]  WHERE IDSocioTalonera = '$IDSocioTalonera' ";
            $dbo->query($sql);
 
            else:  

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            
            endif;
            
           else:

            $id = $dbo->insert($frm, $table, $key);

           endif;

 
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        $mensaje = "Se ha generado una nueva talonera.";
        $IDModulo = 159;

        //enviar notificacion al socio de que se le asigno una nueva talonera
        SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $mensaje, $IDModulo, "");

        break;


    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            $sociosP = $dbo->getFields("SocioTalonera", "SociosPosibles", "IDSocioTalonera = $id");
            $find = strpos($sociosP,$frm['IDSocio']);

            if($find === false):
                $NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = $frm[IDSocio]") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = $frm[IDSocio]");
                $SociosPosibles = $frm[IDSocio] . "-" . $NombreSocio . "|" . $frm[SociosPosibles];

                $frm[SociosPosibles] = $SociosPosibles;
            endif;           

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
