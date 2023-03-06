<?php
//require("admin/config.inc.php");
//require("admin/lib/Ecollect.inc.php");
switch ($_POST["action"]) {

    case "insert":


        $_POST["FechaTrCr"] = date("Y-m-d H:i:s");
        $_POST["FechaRegistro"] = date("Y-m-d H:i:s");

        // print_r($_POST);
        //exit;

        if (!empty($_POST["Categoria"]) && !empty($_POST["NombreAcudiente"]) && !empty($_POST["CedulaAcudiente"]) && !empty($_POST["CorreoElectronicoAcudiente"]) && !empty($_POST["NumeroCelular"]) && !empty($_POST["NombreNino"] && !empty($_POST["TarjetaIdentidad"]))) :
            //seguridad para cada campo del formulario
            foreach ($_POST as $clave => $valor) {
                $_POST[$clave] = SIMUtil::antiinjection($valor);
            } //end for


            $frm = SIMUtil::varsLOG($_POST);



            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files =  SIMFile::upload($_FILES["Foto"], TORNEOCHIQUITINESDETENIS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto2"], TORNEOCHIQUITINESDETENIS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto2"] = $files[0]["innername"];
            }
            $id = $dbo->insert($frm, "TorneoChiquitinesDeTenis", "IDTorneoChiquitinesDeTenis");
            $id_evento_registro = $dbo->lastID("IDTorneoChiquitinesDeTenis");

            /* echo $id_evento_registro;
            exit; */
            $mensaje1 = "Registro Guardado";




            //Consulto las transacciones pendientes de la persona
            $sql_pendiente = "SELECT * FROM PagoEcollect WHERE NumeroDocumento = '" . $_POST["CedulaAcudiente"] . "' and TranState = 'PENDING' ORDER BY IDPagoEcollect DESC Limit 1";
            $r_pendiente = $dbo->query($sql_pendiente);
            $row_pendiente = $dbo->fetchArray($r_pendiente);

            if ($row_pendiente["IDPagoEcollect"] <= 0) {
                //  echo "En proceso de crear la transacción...";

                if (!empty($_POST["IDClub"])) {

                    $Token = Ecollect::obtener_token($_POST["IDClub"]);
                    if ($Token == "error") {
                        echo "Lo sentimos servicio de pasarela no disponible, intente mas tarde";
                        exit;
                    } else {

                        $Valor = $frm["Valor"];
                        $NumeroDocumento = $frm["CedulaAcudiente"];
                        $Nombre = $frm["NombreAcudiente"];
                        $Apellido = "";
                        $Correo = $frm["CorreoElectronicoAcudiente"];
                        $Celular = $frm["NumeroCelular"];
                        $datos_respuesta = Ecollect::enviar_transaccion_publica($_POST["IDClub"], $NumeroDocumento, $Nombre, $Apellido, $Correo, $Celular, $Token, $Valor, $id_evento_registro, $_POST["Modulo"]);
                        $array_respuesta = json_decode($datos_respuesta);
                        if ($array_respuesta->ReturnCode == "SUCCESS") {
                            $TicketId = $array_respuesta->TicketId;
                            $Url = $array_respuesta->eCollectUrl;
                            //Guardo la peticion de pago
                            $sql_pago = "INSERT INTO PagoEcollect (IDClub,NumeroDocumento,ValorID, Modulo, Propina, TicketId,SesionTokenCrearPago,Accion,UsuarioTrCr,FechaTrCr)
                                 VALUES ('" . $_POST["IDClub"] . "','" . $_POST["NumeroDocumento"] . "','" . $id_evento_registro . "','" . $_POST["Modulo"] . "','" . $_POST["Propina"] . "','" . $TicketId . "','" . $Token . "','" . $_POST["Accion"] . "','Pago',NOW()) ";
                            $dbo->query($sql_pago);
                            $header = header("Location: " . $Url);
                            echo "URL:" . $Url;
                            exit;
                        } else {
                            $mensaje = "Lo sentimos servicio de pasarela no disponible, intente mas tarde.";
                        }
                    }
                }
            } else {
                $mensaje = "En este momento su # " . $row_pendiente["TicketId"] . " presenta un proceso de pago cuya transacción se encuentra PENDIENTE de recibir
            confirmación por parte de su entidad financiera, por favor espere unos minutos y vuelva a
            consultar más tarde para verificar si su pago fue confirmado de forma exitosa. Si desea
            mayor información sobre el estado actual de su operación puede comunicarse a nuestras
            líneas de atención al cliente (6) 311 9285 o enviar un correo electronico a
            info@campestrepereira.com y preguntar por el estado de la transacción:" . $row_pendiente["TrazabilityCode"];
            }











        else :
            $mensaje3 = "Todos los datos son obligatorios, por favor verifica";
            SIMHTML::jsAlert("Todos los datos son obligatorios, por favor verifica");
            SIMHTML::jsRedirect("torneochiquitinesdetenis.php?");
            exit;
        endif;
        break;
} //end switch
