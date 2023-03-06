 <?

	SIMReg::setFromStructure(array(
		"title" => "ObjetosPerdidos",
		"table" => "ObjetoPerdido",
		"key" => "IDObjetoPerdido",
		"mod" => "ObjetoPerdido"
	));


	$script = "objetosperdidos";

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

					$files =  SIMFile::upload($_FILES["Foto1"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["FotoEntrega1"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["FotoEntrega1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["FotoEntrega1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["FotoEntrega2"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["FotoEntrega2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["FotoEntrega2"] = $files[0]["innername"];
				} //end if

				$fechaEntrega = $frm["FechaEntrega1"] . " " . $frm["HoraEntrega"];
				$frm["FechaEntrega"] = $fechaEntrega;
				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					$frm["IDModulo"] = 78;
					$frm["TipoNotificacion"] = 'Objeto perdido'; //socio
					$frm["Mensaje"] = "Objeto Perdido" . " " . $frm["Nombre"];



					//traer socios 
					$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "'  and Token <> '' and Token <> '2byte'";


					$qry_socios = $dbo->query($sql_socios);
					while ($r_socios = $dbo->fetchArray($qry_socios)) {

						SIMUtil::envia_cola_notificacion($r_socios, $frm);
						//Guardo el log
						$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              			Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','1', '" . $id . "')");
					}
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


					$files =  SIMFile::upload($_FILES["Foto1"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["FotoEntrega1"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["FotoEntrega1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["FotoEntrega1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["FotoEntrega2"], OBJETOSPERDIDOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["FotoEntrega2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["FotoEntrega2"] = $files[0]["innername"];
				} //end if

				$fechaEntrega = $frm["FechaEntrega1"] . " " . $frm["HoraEntrega"];
				$frm["FechaEntrega"] = $fechaEntrega;



				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));




				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					$frm["IDModulo"] = 78;
					$frm["TipoNotificacion"] = 'Objeto perdido'; //socio
					$frm["Mensaje"] = "Objeto Perdido" . " " . $frm["Nombre"];



					//traer socios 
					$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "'  and Token <> '' and Token <> '2byte'";


					$qry_socios = $dbo->query($sql_socios);
					while ($r_socios = $dbo->fetchArray($qry_socios)) {

						SIMUtil::envia_cola_notificacion($r_socios, $frm);
						//Guardo el log
						$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              			Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','1', '" . $id . "')");
					}
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
			$filedelete = OBJETOSPERDIDOS_DIR . $foto;
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
