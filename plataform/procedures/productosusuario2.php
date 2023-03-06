 <?

	SIMReg::setFromStructure(array(
		"title" => "ProductosDomicilios",
		"table" => "Producto2",
		"key" => "IDProducto",
		"mod" => "Producto"
	));


	$script = "productosusuario2";

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

					$files =  SIMFile::upload($_FILES["Foto1"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];
				} //end if
				//insertamos los datos

				foreach ($frm["IDDia"] as $Dia_seleccion) :
					$array_dia[] = $Dia_seleccion;
				endforeach;

				if (count($array_dia) > 0) :
					$id_dia = implode("|", $array_dia) . "|";
				endif;
				$frm["Dias"] = $id_dia;

				$id = $dbo->insert($frm, $table, $key);

				foreach ($_POST["CategoriaProducto"] as $id_categoria) :
					$sql_inserta_cat_producto = $dbo->query("Insert into ProductoCategoria2 (IDCategoriaProducto,IDProducto ) Values ('" . $id_categoria . "', '" . $id . "')");
				endforeach;

				foreach ($_POST["CaracteristicaProducto"] as $id_carac) :
					$sql_inserta_cat_producto = $dbo->query("INSERT into ProductoCaracteristica (IDCaracteristicaProducto,IDProducto ) Values ('" . $id_carac . "', '" . $id . "')");
				endforeach;


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


					$files =  SIMFile::upload($_FILES["Foto1"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];
				} //end if


				$borrar_categoria_producto = $dbo->query("Delete From ProductoCategoria2 Where IDProducto = '" . $_GET["id"] . "'");
				foreach ($_POST["CategoriaProducto"] as $id_categoria) :
					$sql_inserta_cat_producto = $dbo->query("Insert into ProductoCategoria2 (IDCategoriaProducto,IDProducto ) Values ('" . $id_categoria . "', '" . SIMNet::reqInt("id") . "')");
				endforeach;

				$borrar_carac_producto = $dbo->query("DELETE From ProductoCaracteristica Where IDProducto = '" . $_GET["id"] . "'");
				foreach ($_POST["CaracteristicaProducto"] as $id_carac) :
					$sql_inserta_cat_producto = $dbo->query("INSERT into ProductoCaracteristica (IDCaracteristicaProducto,IDProducto ) Values ('" . $id_carac . "', '" . SIMNet::reqInt("id") . "')");
				endforeach;

				foreach ($frm["IDDia"] as $Dia_seleccion) :
					$array_dia[] = $Dia_seleccion;
				endforeach;

				if (count($array_dia) > 0) :
					$id_dia = implode("|", $array_dia) . "|";
				endif;
				$frm["Dias"] = $id_dia;


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
			$filedelete = IMGPRODUCTO_DIR . $foto;
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
