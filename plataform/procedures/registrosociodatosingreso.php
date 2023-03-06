<?

SIMReg::setFromStructure(array(
    "title" => "RegistroSocios",
    "table" => "Socio",
    "key" => "IDSocio",
    "mod" => "Socio"
));


$script = "registrosocioingreso";

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

            $IDClub = $frm['IDClub'];
            $frm['Email'] = trim($frm['NumeroDocumento']);
            $frm['Clave'] = sha1(trim($frm['NumeroDocumento']));
            $frm['UsuarioTrCr'] = " Formulario Web";
            $frm["FechaTrCr"] = date("Y-m-d H:i:s");
            $frm['NumeroDocumento'] = $frm['NumeroDocumento'];
            $frm['Accion'] = $frm['NumeroDocumento'];
            $frm['AccionPadre'] = "";
            $frm['TipoSocio'] = "Aspirante";


            /* if($IDClub == 154):
                $frm[TipoSocio] = "Invitado";
            endif; */

            $comprobar_correo = $dbo->fetchAll("Socio", "(CorreoElectronico = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
            if (!empty($comprobar_correo[IDSocio])) :
                $mensaje = "Error: Ya existe  el email o el documento en este club, por favor verifique";
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteelemailoeldocumentoenesteclub,porfavorverifique', LANGSESSION));
                SIMHTML::jsRedirect("registrosocioingreso.php?mensaje=' $mensaje '&IDClub=' $IDClub '");
                exit;
            endif;
            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $mensaje = "Registro Guardado Correctamente";

            $correo = $frm['CorreoElectronico'];
            $Asunto = "Confirmacion informacion ingreso app";
            $Mensaje = "Queda confirmado su usuario y contrasena para el ingreso";

            SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect("registrosocioingreso.php?mensaje=' $mensaje '&IDClub=' $IDClub '");
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


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
