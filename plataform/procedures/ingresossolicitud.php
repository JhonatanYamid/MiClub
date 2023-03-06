<?
SIMReg::setFromStructure(array(
    "title" => "Solicitud Ingresos",
    "table" => "IngresosSolicitud",
    "key" => "IDIngresosSolicitud",
    "IDModulo" => "183",
    "mod" => "Ingresos"
));


$script = "ingresossolicitud";
include(LIBDIR . "SIMVistasLuker.inc.php");

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");
$modulo = SIMReg::get("IDModulo");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


$frm = SIMUtil::varsLOG($_POST);

switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case 'datosPersonales':
        //insertamos los datos en IngresosSolicitud
        $frm['UsuarioTrEd'] = SIMUser::get('IDUsuario');
        $frm['FechaTrEd'] = date('Y-m-d H:i:s');

        // $frm['IDIngresosSolicitud'] = SIMNet::reqInt("id");

        $TableSave = "IngresosDatosPersonales";
        $KeyTableSave = "IDIngresosDatosPersonales";


        // Creamos el directorio del socio para guardar los enexos
        $NumeroDocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = " . $frm['IDSocio']);
        if (!file_exists(INGRESOS_FILES . $NumeroDocumentoSocio)) {
            mkdir(INGRESOS_FILES . $NumeroDocumentoSocio, 0777, TRUE);
        }
        $dir_socio = INGRESOS_FILES . $NumeroDocumentoSocio;
        // Fin Creamos el directorio del socio para guardar los enexos
        //Actualizamos los datos
        // $IDIngresosDatosPersonales = $dbo->insert($frm, $TableSave, $KeyTableSave);

        $IDIngresosDatosPersonales = $dbo->update($frm, $TableSave, $KeyTableSave, SIMNet::reqInt("ID"));


        //subir las imagenes
        if (isset($_FILES)) {
            foreach ($_FILES as $File => $archivo) {
                if ($archivo["name"] != '') :
                    $tamano_archivo = $archivo["size"];
                    if ($tamano_archivo >= 6000000) {
                        $respuesta = "El archivo " . $archivo['name'] . " supera el limite de peso permitido de 6 megas, por favor verifique";
                        SIMHTML::jsAlert($respuesta);
                        SIMHTML::jsRedirect($script . ".php");
                        exit;
                    } else {
                        $files = SIMFile::upload($_FILES[$File], $dir_socio, "DOC");

                        if (empty($files) && !empty($_FILES[$File]["name"])) :
                            $respuesta = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            SIMHTML::jsAlert($respuesta);
                            SIMHTML::jsRedirect($script . ".php");
                            exit;
                        endif;
                        // Borrar archivos
                        $NombreArchivo = $dbo->getFields($TableSave, $File, "$KeyTableSave = $IDIngresosDatosPersonales");
                        $DirFile = INGRESOS_ROOT . $NumeroDocumentoSocio . "/" . $NombreArchivo;
                        unlink($DirFile);
                        //Fin Borrar archivos

                        $Archivo = $files[0]["innername"];
                        $sql_IngresosDatosPersonales = "UPDATE IngresosDatosPersonales SET $File = '$Archivo' WHERE IDIngresosDatosPersonales = $IDIngresosDatosPersonales";
                        $dbo->query($sql_IngresosDatosPersonales);
                    }
                endif;
            }
        }
        SIMHTML::jsAlert("Registro Guardado Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));

        break;
    case 'Experiencias':
        //insertamos los datos en IngresosSolicitud
        $frm['UsuarioTrEd'] = SIMUser::get('user');
        $frm['FechaTrEd'] = date('Y-m-d H:i:s');

        // $frm['IDIngresosSolicitud'] = SIMNet::reqInt("id");

        $TableSave = "IngresosExperiencias";
        $KeyTableSave = "IDIngresosExperiencias";

        // Creamos el directorio del socio para guardar los enexos
        $NumeroDocumentoSocio = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = " . $frm['IDSocio']);
        if (!file_exists(INGRESOS_DIR . $NumeroDocumentoSocio)) {
            mkdir(INGRESOS_DIR . $NumeroDocumentoSocio, 0777, TRUE);
        }
        $dir_socio = INGRESOS_DIR . $NumeroDocumentoSocio;
        // Fin Creamos el directorio del socio para guardar los enexos

        //Actualizamos los datos
        $IDIngresosExperiencias = $dbo->update($frm, $TableSave, $KeyTableSave, $frm['ID']);

        //subir las imagenes
        // if (isset($_FILES)) {
        //     foreach ($_FILES as $File => $archivo) {
        //         if ($archivo["name"] != '') :
        //             $tamano_archivo = $archivo["size"];
        //             if ($tamano_archivo >= 6000000) {
        //                 $respuesta = "El archivo " . $archivo['name'] . " supera el limite de peso permitido de 6 megas, por favor verifique";
        //                 SIMHTML::jsAlert($respuesta);
        //                 SIMHTML::jsRedirect($script . ".php");
        //                 exit;
        //             } else {
        //                 $files = SIMFile::upload($_FILES[$File], $dir_socio, "IMAGE");
        //                 if (empty($files) && !empty($_FILES[$File]["name"])) :
        //                     $respuesta = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
        //                     SIMHTML::jsAlert($respuesta);
        //                     SIMHTML::jsRedirect($script . ".php");
        //                     exit;
        //                 endif;
        //                 // Borrar archivos
        //                 $NombreArchivo = $dbo->getFields($TableSave, $File, "$KeyTableSave = $IDIngresosDatosPersonales");
        //                 $DirFile = INGRESOS_ROOT . $NumeroDocumentoSocio . "/" . $NombreArchivo;
        //                 unlink($DirFile);
        //                 //Fin Borrar archivos

        //                 $Archivo = $files[0]["innername"];
        //                 $sql_IngresosDatosPersonales = "UPDATE IngresosDatosPersonales SET $File = '$Archivo' WHERE IDIngresosDatosPersonales = $IDIngresosDatosPersonales";
        //                 $dbo->query($sql_IngresosDatosPersonales);
        //             }
        //         endif;
        //     }
        // }
        SIMHTML::jsAlert("Registro Guardado Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));

        break;

    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");


        $sql_DatosPersonales = "SELECT * FROM IngresosDatosPersonales WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " ORDER BY IDIngresosDatosPersonales DESC LIMIT 1";
        $q_DatosPersonales = $dbo->query($sql_DatosPersonales);
        $frm_DatosPersonales = $dbo->assoc($q_DatosPersonales);

        $sql_IngresosExperiencias = "SELECT * FROM IngresosExperiencias WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " AND Empresa != '' ORDER BY IDIngresosExperiencias ASC";
        $q_IngresosExperiencias = $dbo->query($sql_IngresosExperiencias);


        $sql_IngresosBeneficiarios = "SELECT * FROM IngresosBeneficiarios WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " AND NombreCompleto != ''  ORDER BY IDIngresosBeneficiarios ASC";
        $q_IngresosBeneficiarios = $dbo->query($sql_IngresosBeneficiarios);

        $sql_IngresosEstudios = "SELECT * FROM IngresosEstudios WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " ORDER BY IDIngresosEstudios ASC";
        $q_IngresosEstudios = $dbo->query($sql_IngresosEstudios);

        $sql_IngresosDeportes = "SELECT * FROM IngresosDeportes WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " ORDER BY IDIngresosDeportes ASC";
        $q_IngresosDeportes = $dbo->query($sql_IngresosDeportes);

        $sql_IngresosIdiomas = "SELECT * FROM IngresosIdiomas WHERE IDIngresosSolicitud = " . $frm['IDIngresosSolicitud'] . " AND Idioma != 0 ORDER BY IDIngresosIdiomas ASC";
        $q_IngresosIdiomas = $dbo->query($sql_IngresosIdiomas);

        $frm_datosSocio  = $dbo->fetchAll("Socio", "IDSocio = " . $frm['IDSocio'], "array");

        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            if ($Response['success']) {
                SIMHTML::jsAlert("Registro Guardado Correctamente");
            } else {
                SIMHTML::jsAlert("Se ha producido un error");
            }
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
