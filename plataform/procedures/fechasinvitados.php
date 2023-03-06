 <?

	SIMReg::setFromStructure(array(
		"title" => "FechaEspecialesInvitados",
		"table" => "FechaEspecialInvitado",
		"key" => "IDFechaEspecialInvitado",
		"mod" => "ClubFechaCierre"
	));


	$script = "fechasinvitados";

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

				$files =  SIMFile::upload($_FILES["RestauranteImagen"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["RestauranteImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["RestauranteFile"] = $files[0]["innername"];

				//insertamos los datos
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

				$files =  SIMFile::upload($_FILES["RestauranteImagen"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["RestauranteImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["RestauranteFile"] = $files[0]["innername"];


				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));

				//	print_form( $frm , "update" ,  "Realizar Cambios" );
			} else
				exit;


			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "DelImgNot":
			$campo = $_GET['cam'];
			if ($campo == "SWF") {
				$doceliminar = SWFEvento_DIR . $dbo->getFields("Evento", "$campo", "IDEvento = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'SWFeliminadoCorrectamente', LANGSESSION));
			} else {
				$doceliminar = IMGEVENTO_DIR . $dbo->getFields("Evento", "$campo", "IDEvento = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
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