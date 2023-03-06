<?
SIMReg::setFromStructure(array(
    "title" => "Tarjeta Rotativa",
    "table" => "TarjetaRotativa",
    "key" => "IDTarjetaRotativa",
    "mod" => "TarjetaRotativa"
));

$script = "tarjetarotativa";

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
            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $dbo->query($sqlInsert);
            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;


    case "edit":
        // $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $sql = "SELECT TarjetaRotativa.*,Socio.Nombre,Socio.Apellido FROM TarjetaRotativa, Socio  WHERE TarjetaRotativa.IDSocio=Socio.IDSocio AND Socio.IDClub = " . SIMUser::get('club') . " AND TarjetaRotativa.IDTarjetaRotativa =  " . SIMNet::reqInt("id");
        $q_sql = $dbo->query($sql);

        $frm = $dbo->assoc($q_sql);

        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $IDSocio = $frm['IDSocio'];
            $IDTipoTarjetaRotativa = $frm['IDTipoTarjetaRotativa'];
            $Cupos = $frm['Cupos'];
            $NumeroTarjeta = $frm['NumeroTarjeta'];
            $FechaInicio = $frm['FechaInicio'];
            $FechaCaducidad = $frm['FechaCaducidad'];

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
            $frm = $dbo->fetchById($table, $key, $id, "array");
            SIMHTML::jsAlert("Registro Guardado Correctamente");
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
