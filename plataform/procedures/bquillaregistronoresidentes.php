<?php

switch ($_POST["action"]) {

    case "insert":


        $_POST["FechaTrCr"] = date("Y-m-d H:i:s");
        $_POST["FechaRegistro"] = date("Y-m-d H:i:s");

        // print_r($_POST);
        //exit;

        if (!empty($_POST["NombreVisitante"]) && !empty($_POST["Parentesco"]) && !empty($_POST["FechaDeInicio"]) && !empty($_POST["FechaFinal"]) && !empty($_POST["CiudadDeProcedenciaDelVisitante"]) && !empty($_POST["IDSocio"] && !empty($_POST["IDClub"]))) :
            //seguridad para cada campo del formulario
            foreach ($_POST as $clave => $valor) {
                $_POST[$clave] = SIMUtil::antiinjection($valor);
            } //end for



            // echo $frm["sumadias"];
            //exit;

            //tomar la fecha del servidor
            $fechaActual = date('Y');
            $fechai = $_POST['FechaDeInicio'];
            $fechaf = $_POST['FechaFinal'];
            $annoinicial = date('Y', strtotime($fechai));
            $annofinal = date('Y', strtotime($fechaf));
            $_POST["Año"] = $annoinicial;


            //calcular los dias en que el invitado va a estar en el club
            $fechainicio = new DateTime($_POST["FechaDeInicio"]);
            $fechafinal = new DateTime($_POST["FechaFinal"]);
            $diff = $fechainicio->diff($fechafinal);
            $dias = ($diff->days) + 1;
            $_POST["Dias"] = $dias;






            //seleccionar los dias de la tabla
            $documentoInvitado = $_POST["NumeroDocumentoInvitado"];
            $sql = "SELECT *, SUM(Dias) as sumadias FROM BquillaNoResidentes WHERE NumeroDocumentoInvitado= '$documentoInvitado' and Año=' $fechai '";
            //echo $sql;
            $query = $dbo->query($sql);
            $bquillainvitado = $dbo->fetchArray($query);
            $frm = $bquillainvitado;

            //restringir al socio a seleccionar 15 dias al año
            if ($frm["sumadias"] > 15 or (($frm["sumadias"] + $dias) > 15)) {

                $mensaje1 =  "El Invitado se ha registrado:" . $frm["sumadias"] . " Dias " . "Y no se puede registrar mas de 15 Dias ";
            } else {
                //insertar los no residentes
                $id = $dbo->insert($_POST, "BquillaNoResidentes", "IDBquillaNoResidentes");
                $id_evento_registro = $dbo->lastID("IDBquillaNoResidentes");
                $mensaje1 = "Registro Guardado";
                $IDSocio = $_GET['IDSocio'];

                $NombreVisitante = $_POST['NombreVisitante'];
                $IDClub = $_POST['IDClub'];
                $FechaDeInicio = $_POST['FechaDeInicio'];
                $NumeroDocumentoInvitado =  $_POST['NumeroDocumentoInvitado'];
                $dias = $_POST['Dias'];


                //insertar en la tabla socioinvitado

                for ($i = 1; $i <= $dias; $i++) {
                    $sql = "INSERT INTO SocioInvitado (IDSocio,IDClub,NumeroDocumento,Nombre,FechaIngreso,Estado,Observaciones,UsuarioTrCr)
                VALUES('$IDSocio','$IDClub','$NumeroDocumentoInvitado','$NombreVisitante','$FechaDeInicio','P',' Invitacion no residente','$id_evento_registro')";
                    $dbo->query($sql);
                    $FechaDeInicio = date("y-m-d", strtotime($FechaDeInicio . "+ 1 days"));
                }







                //info del club
                $datos_club = $dbo->fetchAll("Club", " IDClub = '  110  ' ", "array");
                // Ahora creamos el cuerpo del mensaje con la imagen del logo del club
                $msg  .= "<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>" . "<br><br>" . "Cordial Saludo.  \n\n" . "<br><br>" . "Se ha generado una nueva solicitud de un invitado no residente." . "<br><br>" .
                    " Recuerde ingresar al sistema para conocer mas detalles. " . "<br><br>";



                foreach ($_POST as $key => $value) {

                    if (
                        $key <> "g-recaptcha-response" && $key <> "IDClub" && $key <> "IDSocio" && $key <> "action"
                        && $key <> "FechaTrCr" && $key <> "FechaRegistro" && $key <> "Año"
                    ) {

                        if ($key == "NombreVisitante") {
                            $key = "Nombre Visitante";
                        }
                        if ($key == "NumeroDocumentoInvitado") {
                            $key = "Numero Documento Invitado";
                        }
                        if ($key == "Parentesco ") {
                            $key = "Parentesco";
                        }
                        if ($key == "FechaDeInicio") {
                            $key = "Fecha De Inicio";
                        }
                        if ($key == "FechaFinal") {
                            $key = "Fecha Final";
                        }
                        if ($key == "CiudadDeProcedenciaDelVisitante") {
                            $key = "Ciudad De Procedencia Del Visitante ";
                        }
                        if ($key == "Dias") {
                            $key = "Dias ";
                        }
                        // $msg .= "<b>" . $key . " : " . "</b>" . $value . " <br>";
                        $msg .= "<b>" . "$key" .  " : " . "</b>" .  $value . " <br>";
                    }
                }

                SIMHTML::jsAlert("Datos enviado con exito..");


                $correo = "ggonzalez@country.com.co,dchristiansen@country.com.co";
            }

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (!empty($correo_value))
                        $mail->AddAddress($correo_value);
                }
            }


            $mail->Subject = "Registro Datos No Residentes";
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



            // Finalmente enviamos el mensaje
            if ($confirm) {

                //SIMUtil::notifica_actualiza_datos("11",$_POST["Email"],$msg);
                SIMHTML::jsRedirect("bquillanoresidentes.php?msg=1&mensaje1=' $mensaje1 '&IDSocio=' $IDSocio '");

                exit;
            } else
                SIMHTML::jsRedirect("bquillanoresidentes.php?msg=2&mensaje1=' $mensaje1 '&IDSocio=' $IDSocio '");

        else :
            $mensaje3 = "Todos los datos son obligatorios, por favor verifica";
            SIMHTML::jsAlert("Todos los datos son obligatorios, por favor verifica");
            SIMHTML::jsRedirect("bquillanoresidentes.php?msg=2&mensaje3=' $mensaje3 '&IDSocio=' $IDSocio '");
            exit;
        endif;
        break;
} //end switch
switch ($_GET["action"]) {

        //borrar registro de invitados
    case "borrar":

        $id = $_GET['id'];
        $IDSocio = $_GET['IDSocio'];
        $fechadeinicio = $_GET['FechaDeInicio'];


        $anio = date("Y");
        $mes = date("m");
        $dia = date("d");
        $fechaServidor = $anio . "-" . $mes . "-" . $dia;

        //si la fecha es menor a la del servidor no se puede borrar
        if ($fechadeinicio < $fechaServidor) {
            $mensaje1 = "No se puede borrar registro";
            SIMHTML::jsRedirect("historialNoResidentes.php?mensaje1=' $mensaje1'&IDSocio='$IDSocio'");
        } else {
            $query = "DELETE FROM BquillaNoResidentes WHERE IDBquillaNoResidentes=$id";
            $dbo->query($query);
            //borrar tambien de la tabla SocioInvitado
            $query1 = "DELETE FROM SocioInvitado WHERE UsuarioTrCr=$id";
            $dbo->query($query1);
            $mensaje1 = "Registro Eliminado";

            SIMHTML::jsRedirect("historialNoResidentes.php?mensaje1=' $mensaje1'&IDSocio='$IDSocio'");
        }



        //  if ($query) {
        // }

        /// SIMHTML::jsAlert("Dato Eliminado");
        // SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
        break;
}
