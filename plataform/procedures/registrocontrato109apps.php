<?php

switch ($_POST["action"]) {

    case "insert":

        require_once "recaptchalib.php";
        // tu clave secreta
        $secret = "6LdtvCYTAAAAAJKmrTc2Pak5PR2D_Uf-2c82yFsW";

        // respuesta vacía
        $response = null;

        // comprueba la clave secreta
        $reCaptcha = new ReCaptcha($secret);

        // si se detecta la respuesta como enviada
        if ($_POST["g-recaptcha-response"]) {
            $response = $reCaptcha->verifyResponse(
                $_SERVER["REMOTE_ADDR"],
                $_POST["g-recaptcha-response"]
            );
        }

        if ($response != null && $response->success) {
            $_SESSION["validcaptcha"] = "okcaptcha" . date("Y-m-d H:i:s");
            //echo $_SESSION["validcaptcha"]="okcaptcha".date("Y-m-d H:i:s");
            //echo "Hi " .  ", thanks for submitting the form!";
            $robot_verificacion = "S";
        } else {
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Debesverificarquenoeresunrobot', LANGSESSION));
            SIMHTML::jsRedirect("registrocontrato109apps.php");
            exit;
        }

        if (!empty($_POST["Nombre"]) && !empty($_POST["NumeroDocumento"]) && $robot_verificacion == "S") :

            $_POST["Fecha"] = date("Y-m-d H:m:s");

            $id = $dbo->insert($_POST, "ContratosClub", "IDContratosClub");


            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosregistradoscorrectamente', LANGSESSION));
            SIMHTML::jsRedirect("registrocontrato109apps.php?IDClub=" . $_POST[IDClub]);

        else :
            SIMHTML::jsAlert("Todos los datos son obligatorios, por favor verifica");
            SIMHTML::jsRedirect("registromocawa.php");
            exit;
        endif;
        break;
} //end switch
