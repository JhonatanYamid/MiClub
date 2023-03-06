<?

SIMReg::setFromStructure(array(
    "title" => "CheckinFuncionarios",
    "table" => "CheckinFuncionarios",
    "key" => "IDCheckinFuncionarios",
    "mod" => "CheckinFuncionarios"
));

$script = "checkinfuncionarios";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

$IDClub = SIMUser::get("club");

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

            $existe = $dbo->getFields("CheckinFuncionarios", "IDCheckinFuncionarios", "DATE(FechaEntrada) = DATE('".$frm['Fecha']."') AND IDUsuario = ".$frm['IDUsuario']." AND IDClub = $IDClub");
            
            if(!$existe){
                $idPlanificacion = $dbo->getFields("PlanificacionDiaria", "IDPlanificacionDiaria", "DATE(Fecha) = DATE('".$frm['Fecha']."') AND IDUsuario = ".$frm['IDUsuario']." AND IDClub = $IDClub");
                $frm['IDPlanificacionDiaria'] = $idPlanificacion;

                //insertamos los datos
                $id = $dbo->insert($frm, $table, $key);

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteunmovimientoenlafecha,porfavorverifique', LANGSESSION));
            }else{
                
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            }

        } else
            exit;

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
    case "detalle":
        $view = "views/" . $script . "/listDetalle.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
