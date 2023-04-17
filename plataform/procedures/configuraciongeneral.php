<?

SIMReg::setFromStructure(array(
    "title" => "configuracionGeneral",
    "table" => "ConfiguracionClub",
    "key" => "IDConfiguracionClub",
    "mod" => "Socio"
));


$script = "configuraciongeneral";

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

			$files =  SIMFile::upload($_FILES["Foto1"], POPPUP_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto1"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Foto1"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["ImagenSplashHome"], CLUB_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["ImagenSplashHome"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["ImagenSplashHome"] = $files[0]["innername"];

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $id_nuevo_club = $dbo->lastID();

            $frm2 = $frm;
            $frm2[IDClub] = $id_nuevo_club;
            $id2 = $dbo->insert($frm2, "ConfiguracionClub", "IDConfiguracionClub");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;


    case "edit":
        /*  $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar"; */
        $frm1 = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");

        $config = "SELECT * FROM ConfiguracionClub WHERE IDClub = " . SIMNet::reqInt("id");
        $qry = $dbo->query($config);
        $frm2 = $dbo->fetchArray($qry);

        if (!empty($frm2)) :
            $frm = array_merge($frm1, $frm2);
        else :
            $frm = $frm1;
        endif;

        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;



    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
           

			$files =  SIMFile::upload($_FILES["Foto1"], POPPUP_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto1"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Foto1"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["ImagenSplashHome"], CLUB_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["ImagenSplashHome"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["ImagenSplashHome"] = $files[0]["innername"];
            

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
            $sql = "SELECT IDConfiguracionClub FROM ConfiguracionClub WHERE IDClub = " . SIMNet::reqInt("id");
            $qry = $dbo->query($sql);
            $dat = $dbo->fetchArray($qry);

            if (!empty($dat[IDConfiguracionClub])) :
                $frm[IDClub] = SIMNet::reqInt("id");
                $id2 = $dbo->update($frm, "ConfiguracionClub", "IDConfiguracionClub", $dat[IDConfiguracionClub]);
            else :
                $frm[IDClub] = SIMNet::reqInt("id");
                $id2 = $dbo->insert($frm, "ConfiguracionClub", "IDConfiguracionClub");
            endif;




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
			$filedelete = POPPUP_DIR . $foto;
			unlink($filedelete);
		$dbo->query("UPDATE ConfiguracionClub SET $campo = '' WHERE IDClub ='" . $id ."' LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
