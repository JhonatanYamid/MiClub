<?

SIMReg::setFromStructure(array(
    "title" => "Talonera Funcionario",
    "table" => "TaloneraFunc",
    "key" => "IDTaloneraFunc",
    "mod" => "Socio"
));


$script = "talonerafunc";

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

            // // ACTULIZAMOS LOS SERVICIOS DE LA TALONERA
            // $Servicios = explode("|||",$frm[SeleccionServicios]);            
            // foreach($Servicios as $id => $Servicio):
            //     $Datos = explode("-",$Servicio);
            //     if($Datos[1] > 0):
            //         $insert = "INSERT INTO TaloneraServicios (IDTalonera,IDServicio) VALUES ($id,$Datos[1])";
            //         $dbo->query($insert);
            //     endif;
            // endforeach;

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
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
           
            // ACTULIZAMOS LOS SERVICIOS DE LA TALONERA
            // $Servicios = explode("|||",$frm[SeleccionServicios]);           

            // foreach($Servicios as $id => $Servicio):
            //     $Datos = explode("-",$Servicio);
            //     if($Datos[1] > 0):

            //         $delete = "DELETE FROM TaloneraServicios WHERE IDTalonera = $frm[ID] AND IDServicio = $Datos[1]";
            //         $dbo->query($delete);

            //         $insert = "INSERT INTO TaloneraServicios (IDTalonera,IDServicio) VALUES ($frm[ID],$Datos[1])";
            //         $dbo->query($insert);
            //     endif;
            // endforeach;

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
