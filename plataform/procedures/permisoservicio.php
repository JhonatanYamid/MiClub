 <?

	SIMReg::setFromStructure(array(
		"title" => "PermisosServicios",
		"table" => "PermisoServicio",
		"key" => "IDPermisoServicio",
		"mod" => "PermisoServicio"
	));


	$script = "permisoservicio";

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

				foreach ($_POST["PermisoServicio"] as $id_servicio) :
					$sql_inserta_permiso = $dbo->query("INSERT into ServicioPermiso (IDServicio, IDPermisoServicio) Values ('" . $id_servicio . "', '" . $id . "')");
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


				$borrar_permisos = $dbo->query("DELETE From ServicioPermiso Where IDPermisoServicio = '" . SIMNet::reqInt("id") . "'");
				foreach ($_POST["PermisoServicio"] as $id_servicio) :
					$sql_inserta_moduloperfil = $dbo->query("INSERT into ServicioPermiso (IDServicio, IDPermisoServicio) Values ('" . $id_servicio . "', '" . SIMNet::reqInt("id") . "')");
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