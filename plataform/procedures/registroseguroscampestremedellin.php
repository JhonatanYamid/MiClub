<?php

switch ($_POST["action"]) {

    case "insert":


        // print_r($_POST);
        //exit;

        if (!empty($_POST["seguros"])) :

            $IDSocio = $_POST["IDSocio"];

            $sql = "SELECT IDSocio FROM SolicitudSeguros WHERE IDSocio= '" . $IDSocio . "'";

            $query = $dbo->query($sql);
            $IDSocioSeguros = $dbo->fetchArray($query);
            $frm = $IDSocioSeguros;
            if ($frm["IDSocio"] > 0) {
                $mensaje1 = "Señor Socio, usted ya envio su solicitud debe esperar que un agente de seguros se contacte para entregarle mas información.";
                SIMHTML::jsAlert("Señor Socio, usted ya envio su solicitud debe esperar que un agente de seguros se contacte para entregarle mas información.");
            } else {
                $_POST["FechaTrCr"] = date("Y-m-d H:i:s");

                //insertar los no residentes
                $id = $dbo->insert($_POST, "SolicitudSeguros", "IDSolicitudSeguros");
                $mensaje1 = "Señor Socio, su solicitud fue procesada exitosamente, un agente de Seguros lo contactará para entregarle mas información.";





                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

                //info del club
                $datos_club = $dbo->fetchAll("Club", " IDClub = '20' ", "array");
                // Ahora creamos el cuerpo del mensaje con la imagen del logo del club
                $msg  .= "<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>" . "<br><br>" . "Cordial Saludo.  \n\n" . "<br><br>" . "" . "<br><br>" .
                    " " . "<br><br>";
                $NombreSocio = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $NumeroDocumento = $datos_socio["NumeroDocumento"];
                $CorreoElectronico = $datos_socio["CorreoElectronico"];

                $msg .= "El Socio:" . $NombreSocio . "<br>" .
                    "Numero Documento:" .   $NumeroDocumento . "<br>" .
                    "Correo Socio:" .   $CorreoElectronico . "<br>" .
                    " desea saber mas informacion sobre los seguros.";





                SIMHTML::jsAlert("Señor Socio, su solicitud fue procesada exitosamente, un agente de Seguros lo contactará para entregarle mas información");


                $correo = "segurosclubcampestre@d6seguros.com";

                //$Asunto = "Seguros";
                // SIMUtil::envia_correo_general(20, $correo, $Msg, $Asunto);

                $url_baja = URLROOT . "contactenos.php";
                $mail = new phpmailer();
                $array_correo = explode(",", $correo);
                if (count($array_correo) > 0) {
                    foreach ($array_correo as $correo_value) {
                        if (!empty($correo_value))
                            $mail->AddAddress($correo_value);
                    }
                }


                $mail->Subject = "Seguros";
                $mail->Body = $msg;
                $mail->IsHTML(true);
                $mail->Sender = "info@miclubapp.com";
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = "info@miclubapp.com";
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:info@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }

            // Finalmente enviamos el mensaje
            if ($confirm) {

                //SIMUtil::notifica_actualiza_datos("11",$_POST["Email"],$msg);
                SIMHTML::jsRedirect("seguroscampestremedellin.php?msg=1&mensaje1=' $mensaje1 '&IDSocio='$IDSocio'");

                exit;
            } else
                SIMHTML::jsRedirect("seguroscampestremedellin.php?msg=1&mensaje1=' $mensaje1 '&IDSocio='$IDSocio'");

        else :
            $mensaje3 = "Todos los datos son obligatorios, por favor verifica";
            SIMHTML::jsAlert("Todos los datos son obligatorios, por favor verifica");
            SIMHTML::jsRedirect("bquillanoresidentes.php?msg=2&mensaje3=' $mensaje3 '&IDSocio=' $IDSocio '");
            exit;
        endif;
        break;
} //end switch
