<?

SIMReg::setFromStructure(array(
    "title" => "Domiciliario",
    "table" => "Domiciliario",
    "key" => "IDDomiciliario",
    "mod" => "SocioInvitado"
));


$script = "domiciliario";

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
            
               if(($frm["IDSocios"])){
                                $frm["IDSocio"]=$frm["IDSocios"];
                                }else{
                                $frm["IDSocio"]=$frm["IDSocio"];
                                }

            if ($frm["Estado"] == "S") {
                $frm["FechaHoraIngreso"] = "{$frm['FechaIngreso']} {$frm['HoraIngreso']}";
                $frm["IDUsuario"] = SIMUser::get("IDUsuario");
                
                
            }

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

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
   if(($frm["IDSocios"])){
                                $frm["IDSocio"]=$frm["IDSocios"];
                                }else{
                                $frm["IDSocio"]=$frm["IDSocio"];
                                }
            if ($frm["Estado"] == "R") {
                $frm["FechaHoraIngreso"] = "{$frm['FechaIngreso']} {$frm['HoraIngreso']}";
                $frm["IDUsuario"] = SIMUser::get("IDUsuario");
                $frm["ID"]=SIMNet::reqInt("id");
                $frm["Tabla"]= "DomiciliarioEliminados";
                include("../includes/async/elimina_registro.async.php");
                
            $sql_borra_domiciliario = "delete from Domiciliario Where IDDomiciliario ='" . SIMNet::reqInt("id") . "' Limit 1";
            $dbo->query($sql_borra_domiciliario);
              SIMHTML::jsAlert("El domicilio se ha quitado del sistema por que ya fue entregado");
            SIMHTML::jsRedirect($script . ".php");
            }

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
