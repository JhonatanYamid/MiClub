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

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            $IDClub = $_POST['IDClub'];            
            // $Clave = SIMUtil::generarPassword(10);
            $Clave = "1234567";
            $frm[CambioClave] = "S";
            $frm['UsuarioTrCr'] = " Formulario Web";
            $frm["FechaTrCr"] = date("Y-m-d H:i:s");
            $frm['NumeroDocumento'] = $_POST['Email'];
            $frm['Accion'] = $_POST['Email'];
            $frm['AccionPadre'] = $_POST['Email'];

            // PARA TERRANUM VALIDAMOS EL DOMINIO DEL CORREO
            if($IDClub == 224):
                $valor = trim($_POST[CorreoElectronico]);
                
                $DatosCorreo = explode("@",$valor);
                $DominioValidar = "@" . $DatosCorreo[1];
                $DominiosValidos = ["@colliers.com","@bancolombia.com.co","@pei.co"];

                if(!in_array($DominioValidar,$DominiosValidos) && $frm['NumeroDocumento'] != 123456):
                    $mensaje = "Ingresa un dominio de correo electrónico valido.<br><br>En caso de seguir experimentando problemas, comunicate a través de los siguientes canales de atención:<br><br>WhatsApp: +57 3183678545<br><br>Correo: lrios@terranum.com"; 
                    
                    echo "<center><img src='https://miclubapp.com/Error%20en%20tu%20registro.png'></center>";
                    exit;
                    SIMHTML::jsRedirect("registrosociotorre.php?mensaje='$mensaje'&IDClub='$IDClub'"); 
                    exit;                 
                endif;
            endif;


            $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
            if (!empty($comprobar_correo[IDSocio])) :
                $mensaje = "Error: Ya existe  el email o el documento en este club, por favor verifique";

                SIMHTML::jsRedirect("registrosocio.php?mensaje=' $mensaje '&IDClub=' $IDClub '");
                exit;
            endif;

            $frm['Clave'] = sha1($Clave);

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $mensaje = "Registro Guardado Correctamente, se ha enviado un correo de confirmación con el usuario y la contraseña para ingresar en el app";

            $mensaje2 = "Registro Usuarios";           

            $correo = $frm['CorreoElectronico'];
            $Asunto = "Confirmacion informacion ingreso app";
            $Mensaje = "Queda confirmado su usuario y contrase&ntilde;a para el ingreso al app<br><br> Usuario:  $frm[NumeroDocumento] <br> Contrase&ntilde;a: $Clave";

            SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);

            if($IDClub == 181):
                echo "<center><img src='https://miclubapp.com/Registro%20Exitoso.png'></center>";
                exit;
            endif;

            header("Location: https://miclubapp.com/registrosociotorre.php?mensaje2=$mensaje2&mensaje=$mensaje&IDClub=$IDClub");
        } else
            exit;

        break;
    } // End switch
