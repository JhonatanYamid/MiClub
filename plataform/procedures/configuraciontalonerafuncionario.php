<?

SIMReg::setFromStructure(array(
    "title" => "Configuracion Talonera Funcionario",
    "table" => "ConfiguracionTaloneraFuncionario",
    "key" => "IDConfiguracionTaloneraFuncionario",
    "mod" => "Socio"
));


$script = "configuraciontalonerafuncionario";

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

            $files = SIMFile::upload($_FILES['ArchivoPlanes'], TALONERA_DIR, "");
            
          $frm["ArchivoPlanes"] = "https://www.miclubapp.com/file/talonera/".$files[0]["innername"];

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
                $sql_servicio_forma_pago = $dbo->query("Insert into ConfiguracionTaloneraTipoPago (IDConfiguracionTalonera, IDTipoPago) Values ('" . $id . "', '" . $Pago_seleccionado . "')");
            endforeach;

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
        $sql_doc = "SELECT * FROM ConfiguracionTaloneraFuncionario WHERE IDConfiguracionTaloneraFuncionario = '" . SIMNet::reqInt("id") . "'";
        $archivo = $dbo->query($sql_doc);
        $doc=$dbo->fetchArray($archivo);   
        $file_doc=$doc["ArchivoPlanes"];
    $doc_final = str_replace("https://www.miclubapp.com/file/talonera/", "", $file_doc); 
   $filedelete= TALONERA_DIR . $doc_final;
                                                                                                                  
                                                                                                                                                                unlink($filedelete);
            
            
            $files = SIMFile::upload($_FILES['ArchivoPlanes'], TALONERA_DIR, "");
            
          $frm["ArchivoPlanes"] = "https://www.miclubapp.com/file/talonera/".$files[0]["innername"];
         //   $frm["ArchivoPlanes"]="https://appdev.miclubapp.com/file/talonera/".$Archivo;
            
            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $delete_tipo_pago = $dbo->query("Delete From ConfiguracionTaloneraTipoPago Where IDConfiguracionTalonera = '" . SIMNet::reqInt("id") . "'");
            foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
                $sql_servicio_forma_pago = $dbo->query("Insert into ConfiguracionTaloneraTipoPago (IDConfiguracionTalonera, IDTipoPago) Values ('" . SIMNet::reqInt("id") . "', '" . $Pago_seleccionado . "')");
            endforeach;

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
