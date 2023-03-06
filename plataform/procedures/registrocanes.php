<?

SIMReg::setFromStructure(array(
    "title" => "RegistroCanes",
    "table" => "Mascota",
    "key" => "IDMascota",
    "mod" => "registrocanes"
));


$script = "registrocanes";

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
            $IDSocio = $_GET['IDSocio'];

            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files =  SIMFile::upload($_FILES["Foto"], BANNERAPP_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto"] = $files[0]["innername"];


                $files =  SIMFile::upload($_FILES["FotoVacuna"], BANNERAPP_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoVacuna"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["FotoVacuna"] = $files[0]["innername"];
            } //end if		*/

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $id_evento_registro = $dbo->lastID("IDMascota");


            //diferencia dias
            $fechainicio = new DateTime($_POST["FechaDeIngreso"]);
            $fechafin = new DateTime($_POST["FechaFin"]);
            $diff = $fechainicio->diff($fechafin);
            $dias = ($diff->days) + 1;

            //datos
            $IDSocio = $_POST["IDSocio"];
            $IDClub = $_POST["IDClub"];
            $NumeroDocumentoInvitado = $_POST["Cedula"];
            $NombreVisitante = $_POST["NombreSocioInvitado"];
            $FechaDeInicio = $_POST["FechaDeIngreso"];


            //insertar en la tabla socioinvitado
            if ($_POST["AQuienPertenece"] == "Invitado") {

                for ($i = 1; $i <= $dias; $i++) {
                    $sql = "INSERT INTO SocioInvitado (IDSocio,IDClub,NumeroDocumento,Nombre,FechaIngreso,Estado,Observaciones,UsuarioTrCr)
            VALUES('$IDSocio','$IDClub','$NumeroDocumentoInvitado','$NombreVisitante','$FechaDeInicio','P',' Invitado con registro para mascota san andres','$id_evento_registro')";
                    $dbo->query($sql);
                    $FechaDeInicio = date("y-m-d", strtotime($FechaDeInicio . "+ 1 days"));
                }
            }

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?IDSocio='$IDSocio'");
            //+ print_r($frm)
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

            //UPLOAD de imagenes
            if (isset($_FILES)) {


                $files =  SIMFile::upload($_FILES["ImagenCumpleanos"], BANNERAPP_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["ImagenCumpleanos"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                $frm["ImagenCumpleanos"] = $files[0]["innername"];
            } //end if								

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

    case "delfoto":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['id'];
        $filedelete = SERVICIO_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;
    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
