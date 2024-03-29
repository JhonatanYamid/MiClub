<?

SIMReg::setFromStructure(array(
    "title" => "RegistroSocios",
    "table" => "Socio",
    "key" => "IDSocio",
    "mod" => "Socio"
));


$script = "registrosocio";

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

            $IDClub = trim($_POST['IDClub']);
            $frm['Clave'] = sha1($_POST['Email']);
            $frm['UsuarioTrCr'] = " Formulario Web";
            $frm["FechaTrCr"] = date("Y-m-d H:i:s");
            $frm['NumeroDocumento'] = $_POST['Email'];
            $frm[NumeroInvitados] = 100;
            $frm[IDEstadoSocio] = 2;


            $frm['Accion'] = $frm['NumeroDocumento'];


            $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");

            if (!empty($comprobar_correo[IDSocio])) :
                $mensaje = "Error: Ya existe  el email o el documento en este club, por favor verifique";

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteelemailoeldocumentoenesteclub,porfavorverifique', LANGSESSION));
                SIMHTML::jsRedirect("registrosocioarsa.php?mensaje=' $mensaje '&IDClub=' $IDClub '");
                exit;
            endif;

            /*   print_r($frm);
            exit; */
            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $mensaje = "Estamos verificando su información, Pronto le confirmaremos en el correo electrónico su usuario y contraseña para ingresar al app.";


            /*   $correo = $frm['CorreoElectronico'];
            $Asunto = "Confirmación información ingreso app";
            $Mensaje = "Queda confirmado su usuario y contraseña para el ingreso al app<br><br> Usuario:  $_POST[Email] <br> Contraseña: $_POST[Email]";

            SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto); */

            //enviar correo a arsa
            $correArsa = "comiteapp@arsabogota.org";
            // $correArsa = "sneider-rxn@hotmail.com";
            $AsuntoArsa = "Nuevo registro de usuario";
            $MensajeArsa = "
            Un nuevo usuario se ha registrado <br>
            Nombre: $_POST[Nombre] <br>
            Apellido: $_POST[Apellido] <br>
            Tipo Documeto: $_POST[TipoDocumento] <br>
            Número De Documento: $_POST[Email]<br>
            Dirección Completa: $_POST[Direccion]<br>
            Correo Electrónico: $_POS[CorreoElectronico]<br>
            Teléfono: $_POST[Telefono]<br>
            Celular:$_POST[Celular]<br> ";
            SIMUtil::envia_correo_general($IDClub, $correArsa, $MensajeArsa, $AsuntoArsa);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamentePorFavorReviseElCorreoElectronicoDondeSeEnviaranTodosLosDatosParaElIngresoAlAApp', LANGSESSION));
            SIMHTML::jsRedirect("registrosocioarsa.php?mensaje='$mensaje'&IDClub=$IDClub");
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
