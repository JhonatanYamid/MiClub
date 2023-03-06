 <?

	SIMReg::setFromStructure(array(
		"title" => "Servicio",
		"table" => "ServicioMaestro",
		"key" => "IDServicioMaestro",
		"mod" => "ServicioMaestro"
	));


	$script = "serviciosmaestros";

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
			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				if (empty($_FILES["Icono"]["name"])) {
					$id = $dbo->insert($frm, $table, $key);
				} else {
					$files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
					if (empty($files)) {
						SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
						//print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}

					$frm["Icono"] = $files[0]["innername"];
					$frm["IconoName"] = $files[0]["innername"];
					$id = $dbo->insert($frm, $table, $key);
				}

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

				if (empty($_FILES["Icono"]["name"])) {
					$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"),  array("Icono"));
				} //end if
				else {
					$files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
					if (empty($files)) {
						SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
						//print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}

					$frm["Icono"] = $files[0]["innername"];
					$frm["IconoName"] = $files[0]["innername"];


					$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
				}

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
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


	?>