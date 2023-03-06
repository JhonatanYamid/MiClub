 <?

	SIMReg::setFromStructure(array(
		"title" => "ServiciosCMS",
		"table" => "ServicioCMS",
		"key" => "IDServicioCMS",
		"mod" => "ServicioCMS"
	));


	$script = "servicios_cms";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "insert":
			/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$files =  SIMFile::upload($_FILES["Foto"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto1"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto1"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto1"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto2"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto2"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto2"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto3"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto3"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto3"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["FotoDestacada"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["FotoDestacada"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoDestacada"] = $files[0]["innername"];


				$files =  SIMFile::upload($_FILES["SWF"], SWFNOTICIA_DIR);
				if (empty($files) && !empty($_FILES["SWF"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["SWF"] = $files[0]["innername"];

				//insertamos los datos del asistente
				$id = $dbo->insert($frm, $table, $key);





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


				$files =  SIMFile::upload($_FILES["Foto"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto1"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto1"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto1"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto2"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto2"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto2"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto3"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto3"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto3"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["FotoDestacada"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["FotoDestacada"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoDestacada"] = $files[0]["innername"];


				$files =  SIMFile::upload($_FILES["SWF"], SWFNOTICIA_DIR);
				if (empty($files) && !empty($_FILES["SWF"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["SWF"] = $files[0]["innername"];


				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");




				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				print_form($_POST["param"]["noticia"], "update",  "Realizar Cambios");



			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "DelImgNot":
			$campo = $_GET['cam'];
			if ($campo == "SWF") {
				$doceliminar = SWFNOTICIA_DIR . $dbo->getFields("Noticia", "$campo", "IDNoticia = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Noticia SET $campo = '' WHERE IDNoticia = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'SWFeliminadoCorrectamente', LANGSESSION));
			} else {
				$doceliminar = IMGNOTICIA_DIR . $dbo->getFields("Noticia", "$campo", "IDNoticia = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Noticia SET $campo = '' WHERE IDNoticia = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			}
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
			exit;
			break;


		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>