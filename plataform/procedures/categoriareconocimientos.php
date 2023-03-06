 <?

	SIMReg::setFromStructure(array(
		"title" => "Categoria",
		"table" => "CategoriaReconocimiento",
		"key" => "IDCategoriaReconocimiento",
		"mod" => "CategoriaReconocimiento"
	));


	$script = "categoriareconocimientos";

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

				$files =  SIMFile::upload($_FILES["ImagenCategoria"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["ImagenCategoria"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["ImagenCategoria"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["BannerCategoria"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["BannerCategoria"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["BannerCategoria"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["BannerInterna"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["BannerInterna"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["BannerInterna"] = $files[0]["innername"];

				//Areas aplica
				foreach (SIMResources::$areassoycentral as $key_area => $value) {
					if ($frm["Area" . $key_area]) {
						$array_area[] = $key_area;
					}
				}
				if (count($array_area) > 0) {
					$frm["Areas"] = "|" . implode("|", $array_area) . "|";
				} else {
					$frm["Areas"] = "";
				}


				$id = $dbo->insert($frm, $table, $key);

				$IDClub = $frm["IDClub"];

				//Opciones
				for ($i = 1; $i <= $frm["NumeroOpciones"]; $i++) {
					$inserta = "INSERT INTO OpcionReconocimiento (IDCategoriaReconocimiento,Opcion,Texto,FechaTrCr,IDClub) VALUES ('" . $id . "','" . $i . "','" . $frm["Opcion" . $i] . "',NOW(),'" . $IDClub . "')";
					$dbo->query($inserta);
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

				$files =  SIMFile::upload($_FILES["ImagenCategoria"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["ImagenCategoria"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["ImagenCategoria"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["BannerCategoria"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["BannerCategoria"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["BannerCategoria"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["BannerInterna"], CLUB_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["BannerInterna"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["BannerInterna"] = $files[0]["innername"];


				//Areas aplica
				foreach (SIMResources::$areassoycentral as $key_area => $value) {
					if ($frm["Area" . $key_area]) {
						$array_area[] = $key_area;
					}
				}
				if (count($array_area) > 0) {
					$frm["Areas"] = "|" . implode("|", $array_area) . "|";
				} else {
					$frm["Areas"] = "";
				}

				//guardar las opciones
				$sql_opcion = "SELECT IDOpcionReconocimiento, Opcion FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento = '" . SIMNet::reqInt("id") . "'";
				$r_opcion = $dbo->query($sql_opcion);
				while ($row_opcion = $dbo->fetchArray($r_opcion)) {
					$array_opcion[$row_opcion["Opcion"]] = $row_opcion;
				}
				$IDClub = $frm["IDClub"];
				//Opciones
				for ($i = 1; $i <= $frm["NumeroOpciones"]; $i++) {
					if (array_key_exists($i, $array_opcion)) {
						$actualiza = "UPDATE OpcionReconocimiento SET Texto = '" . $frm["Opcion" . $i] . "',IDClub='" . $IDClub . "', FechaTrEd=NOW() WHERE IDOpcionReconocimiento = '" . $array_opcion[$i][IDOpcionReconocimiento] . "'";
						$dbo->query($actualiza);
					} else {
						$inserta = "INSERT INTO OpcionReconocimiento (IDCategoriaReconocimiento,Opcion,Texto,FechaTrCr,IDClub) VALUES ('" . SIMNet::reqInt("id") . "','" . $i . "','" . $frm["Opcion" . $i] . "',NOW(),'" . $IDClub . "')";
						$dbo->query($inserta);
					}
				}

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
			$filedelete = DIRECTORIO_DIR . $foto;
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
