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
            $frm['Clave'] = sha1($_POST['Clave']);
            $frm['UsuarioTrCr'] = " Formulario Web";
            $frm["FechaTrCr"] = date("Y-m-d H:i:s");
            $frm['NumeroDocumento'] = $_POST['Email'];

            $TotalCaracteresDocumento = strlen($frm["NumeroDocumento"]);


            if ($TotalCaracteresDocumento == 9) {
                $Documento1 = 'V' . $frm["NumeroDocumento"];
                $Documento2 = 'E' . $frm["NumeroDocumento"];
                $Documento3 = 'P' . $frm["NumeroDocumento"];
            } else if ($TotalCaracteresDocumento == 8) {
                $Documento1 = 'V' . '0' . $frm["NumeroDocumento"];
                $Documento2 = 'E'  . '0' . $frm["NumeroDocumento"];
                $Documento3 = 'P' . '0' . $frm["NumeroDocumento"];
            } else if ($TotalCaracteresDocumento == 7) {
                $Documento1 = 'V' . '00' . $frm["NumeroDocumento"];
                $Documento2 = 'E'  . '00' . $frm["NumeroDocumento"];
                $Documento3 = 'P' . '00' . $frm["NumeroDocumento"];
            }




            $SqlBuscarSocio = "SELECT * FROM Socio WHERE (NumeroDocumento='" .  $Documento1 . "' OR  NumeroDocumento='" . $Documento2 . "' OR NumeroDocumento='" . $Documento3 . "') AND IDClub='225'";
            $QueryBuscarSocio = $dbo->query($SqlBuscarSocio);
            $Socio = $dbo->fetchArray($QueryBuscarSocio);

            if (!empty($Socio["NumeroDocumento"])) {

                $AccionPadre = explode("-", $Socio["AccionPadre"]);


                if ($AccionPadre[0] == 00 || $AccionPadre[0] == 11 || $AccionPadre[0] == 22 || $AccionPadre[0] == 33) {


                    if ($Socio["CorreoElectronico"] != "cliente@hebraica.com.ve" && !empty($Socio["CorreoElectronico"])) {

                        $_POST[Clave] = SIMUtil::generarPassword(10);
                        //actualizamos contraseña del socio
                        $update = "UPDATE Socio SET Clave = '" . sha1($_POST[Clave]) . "',CambioClave='S' WHERE IDSocio ='" . $Socio["IDSocio"] . "'";


                        $dbo->query($update);

                        //encriptamos el correo
                        $CorreoMostrar = explode("@", $Socio['CorreoElectronico']);
                        $UltimaLetra = substr($CorreoMostrar[0], -1);
                        $PrimeraLetra = substr($CorreoMostrar[0], 0, 1);
                        $complemento = "...........";
                        $CorreoCompleto = $PrimeraLetra . $complemento . $UltimaLetra . "@" . $CorreoMostrar[1];

                        //enviamos correo al socio
                        $correo = $Socio['CorreoElectronico'];
                        $Asunto = "Confirmacion informacion ingreso app";
                        $Mensaje = "$_POST[Nombre]" . " "  . "$_POST[Apellido] <br><br>" . "Queda confirmado su usuario y contraseña para el ingreso al app<br><br> Usuario:  $Socio[Email] <br> Contraseña: $_POST[Clave]";
                        SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);

                        $mensaje = "Registro validado correctamente por favor revise el correo electrónico  " . $CorreoCompleto  . " donde se enviarán todos los datos para el ingreso";
                        SIMHTML::jsRedirect("registrosociohebraica.php?mensaje='$mensaje'&IDClub=$IDClub");
                    } else {

                        //enviamos correo al administrador
                        $correoAdmin = "cliente@hebraica.com.ve";
                        $AsuntoAdmin = "Registro en el app";
                        $MensajeAdmin = "Uno de los clientes hizo el registro en el app pero no puede ingresar porque no tiene el correo electronico actualizado. <br><br> Cliente:  $_POST[Nombre]  <br> Apellido: $_POST[Apellido] Numero Documento: $_POST[Email]  ";
                        SIMUtil::envia_correo_general($IDClub, $correoAdmin, $MensajeAdmin, $AsuntoAdmin);
                        $mensaje = "No existe un correo registrado en su perfil para enviarle sus claves de acceso al app, por favor póngase en contacto con la administración.";
                        SIMHTML::jsRedirect("registrosociohebraica.php?mensaje='$mensaje'&IDClub=$IDClub");
                    }
                } else {
                    $mensaje = "No tienes acceso en el app";
                    SIMHTML::jsRedirect("registrosociohebraica.php?mensaje='$mensaje'&IDClub=$IDClub");
                }
            } else {

                $mensaje = "Numero de documento no existe en la base de datos por favor diríjase a la oficina de atencion al publico en hebraica.";
                SIMHTML::jsRedirect("registrosociohebraica.php?msg=1&mensaje='$mensaje'&IDClub=$IDClub");
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


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
