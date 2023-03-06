<?
SIMReg::setFromStructure(array(
    "title" => "ConfiguraciónCuotasSociales",
    "table" => "ConfiguracionCuotasSociales",
    "key" => "IDConfiguracionCuotasSociales",
    "mod" => "HistorialSocios"
));

$script = "configuracioncuotassociales";

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
    case "InsertarReglasNegocio":
        $frm = SIMUtil::varsLOG($_POST);

        for ($i = 1; $i <= $frm['CantidadOpciones']; $i++) {
            $sql_insert = "INSERT INTO DetalleConfiguracionCuotasSociales (IDConfiguracionCuotasSociales,CampoCriterio,Validacion,ValorCriterio,Descuento,Publicar, UsuarioTrCr,FechaTrCr) 
            values (" . $frm['IDConfiguracionCuotasSociales'] . ",'" . $frm['CampoCriterio' . $i] . "','" . $frm['Validacion' . $i] . "','" . $frm['ValorCriterio' . $i] . "','" . $frm['Descuento' . $i] . "','" . $frm['Publicar' . $i] . "','" . SIMUser::get('Nombre') . "', NOW())";
            $q_insert = $dbo->query($sql_insert);
        }
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[$key]);
        exit;
        break;
    case "ModificarReglasNegocio":
        $frm = SIMUtil::varsLOG($_POST);
        $frmGet = SIMUtil::varsLOG($_GET);

        for ($i = 1; $i <= $frm['CantidadOpciones']; $i++) {
            $sql_update = "UPDATE DetalleConfiguracionCuotasSociales SET CampoCriterio = '" . $frm['CampoCriterio' . $i] . "',Validacion= '" . $frm['Validacion' . $i] . "',ValorCriterio= '" . $frm['ValorCriterio' . $i] . "', Descuento='" . $frm['Descuento' . $i] . "', Publicar='" . $frm['Publicar' . $i] . "', UsuarioTrEd='" . SIMUser::get('Nombre') . "',FechaTrEd= NOW()
            WHERE IDDetalleConfiguracionCuotasSociales = '" . $frmGet['IDDetalleConfiguracionCuotasSociales'] . "'";
            $q_insert = $dbo->query($sql_update);
        }
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[$key]);
        exit;
        break;
    case "EliminarReglasNegocio":
        $frmGet = SIMUtil::varsLOG($_GET);
        $id = $dbo->query("DELETE FROM DetalleConfiguracionCuotasSociales WHERE IDDetalleConfiguracionCuotasSociales   = '" . $frmGet["IDDetalleConfiguracionCuotasSociales"] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frmGet['id']);
        exit;
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
