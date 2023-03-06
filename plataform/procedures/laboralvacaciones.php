<?

SIMReg::setFromStructure(array(
    "title" => "Vacaciones",
    "table" => "LaboralVacaciones",
    "key" => "IDLaboralVacaciones",
    "mod" => "Laboral"
));


$script = "laboralvacaciones";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


// Cambiar club para luker

$arrIDClubLuker = [95, 96, 97, 98, 122, 169];

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

            //Se obtiene el IDUsario o IDSocio
            $tipoSolicitante = $frm["TipoSolicitante"];
            if ($tipoSolicitante == "Usuario") {
                unset($frm["IDSocio"]);
            } else if ($tipoSolicitante == "Socio") {
                unset($frm["IDUsuario"]);
            }

            $frm["IDEstado"] = 1;

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            SIMUtil::noticar_nueva_LaboralVacaciones($id);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
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
            include('../admin/lib/SIMWebServiceLaboral.inc.php');
            $frm = SIMUtil::varsLOG($_POST);

            if ($frm['IDEstado'] == 1) {
                SIMHTML::jsAlert("Es necesario actualizar el estado de la solicitud");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
                exit;
            } elseif ($frm['IDEstado'] == 3) {
                $Aprueba = 'N';
            } else {
                $Aprueba = 'S';
            }

            $datos_usuario = $dbo->fetchAll('Usuario', 'IDUsuario = "' . SIMUser::get("IDUsuario") . '"', 'array');

            $sqlJefe = "SELECT IDSocio FROM Socio WHERE IDClub = " . SIMUser::get('club') . " AND DocumentoJefe = " . $datos_usuario['NumeroDocumento'];
            $q_Jefe = $dbo->query($sqlJefe);
            $r_Jefe = $dbo->rows($q_Jefe);
            $sqlAprobador = "SELECT IDSocio FROM Socio WHERE IDClub = " . SIMUser::get('club') . " AND DocumentoEspecialista = " . $datos_usuario['NumeroDocumento'];
            $q_Aprobador = $dbo->query($sqlAprobador);
            $r_Aprobador = $dbo->rows($q_Aprobador);
            if ($r_Jefe > 0) {
                $Comentario = $frm['ComentarioAprobacion'];
            } else if ($r_Aprobador > 0) {
                $Comentario = $frm['ComentarioAprobador'];
            }

            $Response = SIMWebServiceLaboral::set_solicitud_laboral_vacaciones(SIMUser::get('club'), '', SIMUser::get('IDUsuario'), SIMNet::reqInt("id"), $Aprueba, $Comentario, "Vacaciones");


            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else {
            exit;
        }

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
        break;
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
