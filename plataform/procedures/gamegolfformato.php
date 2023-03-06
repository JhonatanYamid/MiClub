<?
SIMReg::setFromStructure(array(
    "title" => "GameGolfFormato",
    "table" => "GameGolfFormato",
    "key" => "IDGameGolfFormato  ",
    "mod" => "Socio"
));

$script = "gamegolfformato";

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

            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files =  SIMFile::upload($_FILES["Foto1"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto1"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto1"] = $files[0]["innername"];


                $files =  SIMFile::upload($_FILES["Foto2"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto2"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto2"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto3"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto3"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto3"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto4"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto4"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto4"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto5"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto5"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto5"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto6"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto6"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto6"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["FotoPortada"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoPortada"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["FotoPortada"] = $files[0]["innername"];


                $files =  SIMFile::upload($_FILES["Adjunto1File"], CLASIFICADOS_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Adjunto1File"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                $frm["Adjunto1File"] = $files[0]["innername"];
            } //end if

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            //verificar si se notifica
            if ($_POST["NotificarPush"] == "S") {

                $frm["IDModulo"] = 72;
                $frm["TipoNotificacion"] = 'beneficio'; //socio
                $mensaje = $frm["Nombre"] . "\n" . $frm["Introduccion"] . "\n" . $frm["Descripcion"];
                $frm["Mensaje"] = $mensaje;
                $frm["Titular"] = $frm["Nombre"];
                $frm["Mensaje"] = $frm["Introduccion"] . ".";
                if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

                //traer socios a los que les interesa el beneficio
                $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
                //$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '141131'";

                $qry_socios = $dbo->query($sql_socios);
                while ($r_socios = $dbo->fetchArray($qry_socios)) {

                    SIMUtil::envia_cola_notificacion($r_socios, $frm);
                    //Guardo el log
                    $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
                Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
                } //end while

                //traer empleados a los que les interesa el beneficio
                $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
                $qry_empleados = $dbo->query($sql_empleados);

                while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
                    SIMUtil::envia_cola_notificacion($r_socios, $frm);
                } //end while
            } //end if

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
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

                $files =  SIMFile::upload($_FILES["Foto1"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto1"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto1"] = $files[0]["innername"];


                $files =  SIMFile::upload($_FILES["Foto2"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto2"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto2"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto3"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto3"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto3"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto4"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto4"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto4"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto5"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto5"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto5"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Foto6"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto6"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Foto6"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["FotoPortada"], CLASIFICADOS_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoPortada"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["FotoPortada"] = $files[0]["innername"];

                $files =  SIMFile::upload($_FILES["Adjunto1File"], CLASIFICADOS_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Adjunto1File"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                $frm["Adjunto1File"] = $files[0]["innername"];
            } //end if

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            //verificar si se notifica


            if ($_POST["NotificarPush"] == "S") {

                $frm["IDModulo"] = 72;
                $frm["TipoNotificacion"] = 'beneficio'; //socio
                $mensaje = $frm["Nombre"] . "\n" . $frm["Introduccion"] . "\n" . $frm["Descripcion"];
                $frm["Mensaje"] = $mensaje;
                $frm["Titular"] = $frm["Nombre"];
                $frm["Mensaje"] = $frm["Introduccion"] . ".";
                if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

                //traer socios a los que les interesa el beneficio
                $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  " . $condicion_tipo;
                //$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '5533'";

                $qry_socios = $dbo->query($sql_socios);
                while ($r_socios = $dbo->fetchArray($qry_socios)) {

                    SIMUtil::envia_cola_notificacion($r_socios, $frm);
                    //Guardo el log
                    $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
                Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
                } //end while

                //traer empleados a los que les interesa el beneficio
                $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
                $qry_empleados = $dbo->query($sql_empleados);

                while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
                    SIMUtil::envia_cola_notificacion($r_socios, $frm);
                } //end while
            } //end if

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
        $filedelete = CLASIFICADOS_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
        break;



    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";


?>
