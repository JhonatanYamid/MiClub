 <?

	SIMReg::setFromStructure(array(
		"title" => "ParámetrosCMS",
		"table" => "ParametroCMS",
		"key" => "IDParametroCMS",
		"mod" => "ParametrosCMS"
	));


	$script = "parametros_cms";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {






		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Foto"], DISENO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto"] = $files[0]["innername"];
					//exit;

					$files =  SIMFile::upload($_FILES["Foto2"], DISENO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], DISENO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
				} //end if								

				$id = $dbo->update($frm, $table, $key, "1");

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=1");
			} else
				exit;

			break;




		default:

			$frm = $dbo->fetchById($table, $key, "1", "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			$view = "views/" . $script . "/form.php";
			break;
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>