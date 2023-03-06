<?

SIMReg::setFromStructure(array(
    "title" => "configuracioncaddies",
    "table" => "ConfiguracionCaddies",
    "key" => "IDConfiguracionCaddies",
    "mod" => "Socio"
));


$script = "configuracioncaddies";

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

        $frm = SIMUtil::varsLOG($_POST);


        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //dias de la semana
            foreach ($frm["IDDia"] as $Dia_seleccion) :
                $array_dia[] = $Dia_seleccion;
            endforeach;

            if (count($array_dia) > 0) :
                $id_dia = implode(",", $array_dia) . ",";
            endif;
            $frm["DiasDisponiblesAgendarEmpleado"] = $id_dia;

            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files =  SIMFile::upload($_FILES["ImagenEspera"], CADDIE_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["ImagenEspera"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["ImagenEspera"] = $files[0]["innername"];
            } //end if	


            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);


            // lista clubes
            $array_idListaClubes = explode("|||", $frm["IDListaClub"]);
            foreach ($array_idListaClubes as $id_listaclubes => $datos_ListaClubes) :
                $IDListaClubes = $datos_ListaClubes;
                if ($IDListaClubes > 0) {
                    $sql_servicio_forma_pago = $dbo->query("Insert into ClubesCaddies (IDConfiguracionCaddies, IDListaClubes) Values ('" . $id . "', '" . $IDListaClubes . "')");
                }

            endforeach;


            //tipo pago ecaddie
            foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
                $sql_servicio_forma_pago = $dbo->query("Insert into ConfiguracionCaddiesTipoPago (IDConfiguracionCaddies, IDTipoPago) Values ('" . $id . "', '" . $Pago_seleccionado . "')");
            endforeach;


            SIMHTML::jsAlert("Registro Guardado Correctamente");
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
            //dias de la semana
            foreach ($frm["IDDia"] as $Dia_seleccion) :
                $array_dia[] = $Dia_seleccion;
            endforeach;

            if (count($array_dia) > 0) :
                $id_dia = implode(",", $array_dia) . ",";
            endif;
            $frm["DiasDisponiblesAgendarEmpleado"] = $id_dia;

            //UPLOAD de imagenes
            if (isset($_FILES)) {


                $files =  SIMFile::upload($_FILES["ImagenEspera"], CADDIE_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["ImagenEspera"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                $frm["ImagenEspera"] = $files[0]["innername"];
            } //end if	

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            //actualizar lista clubes
            $array_idListaClubes = explode("|||", $frm["IDListaClub"]);


            $delete_lista_clubes = $dbo->query("Delete From ClubesCaddies Where IDConfiguracionCaddies = '" . SIMNet::reqInt("id") . "'");

            foreach ($array_idListaClubes as $id_listaclubes => $datos_ListaClubes) :

                $IDListaClubes = $datos_ListaClubes;
                if ($IDListaClubes > 0) {
                    $sql_servicio_forma_pago = $dbo->query("Insert into ClubesCaddies (IDConfiguracionCaddies, IDListaClubes) Values ('" . SIMNet::reqInt("id") . "', '" . $IDListaClubes . "')");
                }

            endforeach;

            //tipo pago ecaddie
            $delete_tipo_pago = $dbo->query("Delete From ConfiguracionCaddiesTipoPago Where IDConfiguracionCaddies = '" . SIMNet::reqInt("id") . "'");
            foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
                $sql_servicio_forma_pago = $dbo->query("Insert into ConfiguracionCaddiesTipoPago (IDConfiguracionCaddies, IDTipoPago) Values ('" . SIMNet::reqInt("id") . "', '" . $Pago_seleccionado . "')");
            endforeach;

            $frm = $dbo->fetchById($table, $key, $id, "array");



            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    case "delfoto":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['id'];
        $filedelete = CADDIE_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
