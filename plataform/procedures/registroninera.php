<?php

switch ($_POST["action"]) {

    case "insert":

        if (!empty($_POST["Nombre"]) && !empty($_POST["NumeroDocumento"])):

            $busca = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '$_POST[NumeroDocumento]' AND TipoSocio = 'Niñera' AND Accion = '$_POST[NumeroDocumento]'";
			$qry = $dbo->query($busca);
            $dat = $dbo->fetchArray($qry);

            if (!empty($dat)) {
                
				$respuesta = "La Niñera ya existia, los datos fueron actualizados.";

                $id = $dbo->update($_POST, "Socio", "IDSocio", $dat[IDSocio]);

                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($_POST["NumeroDocumento"],$id,$alto_barras);
                $update_codigo=$dbo->query("UPDATE Socio SET CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$dat[IDSocio]."'");

                SIMHTML::jsRedirect("registroninera.php?Respuesta=$respuesta&IDSocio=$_POST[IDSocio]");

            } else {

                $_POST["FechaTrCr"] = date("Y-m-d H:i:s");
                $_POST["Clave"] = sha1($_POST["NumeroDocumento"]);
                $_POST["Email"] = $_POST["NumeroDocumento"];

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = $_POST[IDSocio]", "array");

                $_POST["Accion"] = $_POST["NumeroDocumento"];

                $_POST["AccionPadre"] = $datos_socio["AccionPadre"];

                if(empty($_POST["AccionPadre"]))
                    $_POST["AccionPadre"] = $datos_socio["Accion"];

                $_POST["ObservacionGeneral"] = "Accion del Niño a cuidar: ".$_POST["AccionNino"];
                $_POST["ObservacionEspecial"] = "Accion del Niño a cuidar: ".$_POST["AccionNino"];                

                $id = $dbo->insert($_POST, "Socio", "IDSocio");

                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($_POST["NumeroDocumento"],$id,$alto_barras);
                $update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");

                $actuliza = "UPDATE Socio SET IDEstadoSocio = 2 WHERE TipoSocio = 'Niñera' AND AccionPadre = $_POST[AccionPadre] AND IDSocio <> $id";
				// $qr = $dbo->query($actuliza);

                $respuesta = "1";
                SIMHTML::jsRedirect("registroninera.php?Respuesta=$respuesta&IDSocio=$_POST[IDSocio]");
			}

            exit;

        else:
            SIMHTML::jsRedirect("registroninera.php?Respuesta=Todos los datos son obligatorios, por favor verifica&IDSocio=$_POST[IDSocio]");
            exit;
        endif;
        break;

} //end switch
