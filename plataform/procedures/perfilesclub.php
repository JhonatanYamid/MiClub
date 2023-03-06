 <?

	SIMReg::setFromStructure(array(
		"title" => "PerfilClub",
		"table" => "Perfil",
		"key" => "IDPerfil",
		"mod" => "Perfil"
	));


	$script = "perfilesclub";

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

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				foreach ($_POST[ModuloPerfil] as $id_modulo) :
					$sql_inserta_moduloperfil = $dbo->query("Insert into ModuloPerfil (IDModulo, IDPerfil) Values ('" . $id_modulo . "', '" . $id . "')");
				endforeach;

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php");
			}

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

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");

				$borrar_modulo_perfil = $dbo->query("Delete From ModuloPerfil Where IDPerfil = '" . SIMNet::reqInt("id") . "'");
				foreach ($_POST[ModuloPerfil] as $id_modulo) :
					$sql_inserta_moduloperfil = $dbo->query("Insert into ModuloPerfil (IDModulo, IDPerfil) Values ('" . $id_modulo . "', '" . SIMNet::reqInt("id") . "')");
				endforeach;


				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				exit;
			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>