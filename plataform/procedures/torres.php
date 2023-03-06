 <?

	SIMReg::setFromStructure(array(
		"title" => "Torres",
		"table" => "Torre",
		"key" => "IDTorre",
		"mod" => "Hotel"
	));


	$script = "torres";

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

					$files =  SIMFile::upload($_FILES["Foto1"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
				} //end if


				//insertamos los datos



				$id = $dbo->insert($frm, $table, $key);

				//Actualizo Modulos
				$query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
				while ($r = $dbo->object($query_modulos)) {
					$nombre_campo_id = "IDModulo" . $r->IDModulo;
					$ubicacion_modulo = "";

					if (!empty($frm[$nombre_campo_id])) :
						$activo = "S";
					else :
						$activo = "N";
					endif;
					$sql_inserta_modulo = "Insert Into  PublicidadModulo (IDPublicidad	, IDModulo, Activo) Values ('" . $id . "','" . $r->IDModulo . "','" . $activo . "')";
					$dbo->query($sql_inserta_modulo);
				}


				//Actualizo Categorias
				$query_categorias = $dbo->query("Select * from Categoria Where Publicar = 'S' and IDClub = '" . $frm["IDClub"] . "' Order by Nombre");
				while ($r = $dbo->object($query_categorias)) {
					$nombre_campo_id_categoria = "IDCategoria" . $r->IDCategoria;
					if (!empty($frm[$nombre_campo_id_categoria])) :
						$activo_servicio = "S";
					else :
						$activo_servicio = "N";
					endif;

					$sql_inserta_categoria = "Insert Into  PublicidadCategoria (IDPublicidad, IDCategoria, Activo) Values ('" . $id . "','" . $r->IDCategoria . "','" . $activo_servicio . "')";
					$dbo->query($sql_inserta_categoria);
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


				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Foto1"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], PUBLICIDAD_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
				} //end if

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				//Actualizo Modulos
				$query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
				while ($r = $dbo->object($query_modulos)) {
					$nombre_campo_id = "IDModulo" . $r->IDModulo;
					$ubicacion_modulo = "";

					if (!empty($frm[$nombre_campo_id])) :
						$activo = "S";
					else :
						$activo = "N";
					endif;

					$id_modulo = $dbo->getFields("PublicidadModulo", "IDModulo", "IDModulo = '" . $r->IDModulo . "' and IDPublicidad = '" . SIMNet::reqInt("id") . "'");
					if (empty($id_modulo)) :
						$sql_inserta_modulo = "Insert Into  PublicidadModulo (IDPublicidad	, IDModulo, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDModulo . "','" . $activo . "')";
						$dbo->query($sql_inserta_modulo);
					else :
						$sql_actualiza_modulo = "Update PublicidadModulo Set Activo = '" . $activo . "'  Where  IDPublicidad = '" . SIMNet::reqInt("id") . "'	and IDModulo = '" . $r->IDModulo . "'";
						$dbo->query($sql_actualiza_modulo);
					endif;
				}

				//Actualizo Categorias
				$query_categorias = $dbo->query("Select * from Categoria Where Publicar = 'S' and IDClub = '" . SIMUser::get("centrocomercial") . "' Order by Nombre");
				while ($r = $dbo->object($query_categorias)) {
					$nombre_campo_id_categoria = "IDCategoria" . $r->IDCategoria;
					if (!empty($frm[$nombre_campo_id_categoria])) :
						$activo_servicio = "S";
					else :
						$activo_servicio = "N";
					endif;

					$id_categoria_maestro = $dbo->getFields("PublicidadCategoria", "IDPublicidadCategoria", "IDCategoria = '" . $r->IDCategoria . "' and IDPublicidad = '" . SIMNet::reqInt("id") . "'");
					if (empty($id_categoria_maestro)) :
						$sql_inserta_categoria = "Insert Into  PublicidadCategoria (IDPublicidad, IDCategoria, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDCategoria . "','" . $activo_servicio . "')";
						$dbo->query($sql_inserta_categoria);
					else :
						$sql_actualiza_categoria = "Update PublicidadCategoria Set Activo = '" . $activo_servicio . "' Where  IDPublicidad = '" . SIMNet::reqInt("id") . "'	and IDCategoria = '" . $r->IDCategoria . "'";
						$dbo->query($sql_actualiza_categoria);
					endif;
				}


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


		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = PUBLICIDAD_DIR . $foto;
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
