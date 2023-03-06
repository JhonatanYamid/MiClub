<?

SIMReg::setFromStructure(array(
    "title" => "RecepcionPagos",
    "table" => "HistorialCuotasSociales",
    "key" => "IDHistorialCuotasSociales",
    "mod" => "HistorialSocios"
));


$script = "recepcionpagos";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

// Funcion para obteber los datos de HistorialCuotasSociales
function get_HistorialCuotasSociales($IDHistorialCuotasSociales, $dbo)
{
    $SqlHistorialCuotasSociales = "SELECT * FROM HistorialCuotasSociales WHERE HistorialCuotasSociales.IDHistorialCuotasSociales = $IDHistorialCuotasSociales";
    $resultHistorialCuotasSociales = $dbo->query($SqlHistorialCuotasSociales);
    $RowHistorialCuotasSociales = $dbo->assoc($resultHistorialCuotasSociales);
    return $RowHistorialCuotasSociales;
}

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

    case "updateCuota":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
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
