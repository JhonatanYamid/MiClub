 <?

	SIMReg::setFromStructure(array(
		"title" => "HistorialEnvios",
		"table" => "LogCartaEnviada",
		"key" => "IDLogCartaEnviada",
		"mod" => "CartasFormato"
	));


	$script = "cartaslog";

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
			/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$files =  SIMFile::upload($_FILES["NoticiaImagen"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["NoticiaImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["NoticiaFile"] = $files[0]["innername"];

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

				$files =  SIMFile::upload($_FILES["Adjunto1Documento"], IMGNOTICIA_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto1File"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto2Documento"], IMGNOTICIA_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto2File"] = $files[0]["innername"];


				$files =  SIMFile::upload($_FILES["SWF"], SWFNOTICIA_DIR);
				if (empty($files) && !empty($_FILES["SWF"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["SWF"] = $files[0]["innername"];



				//insertamos los datos del asistente
				$id = $dbo->insert($frm, $table, $key);

				$nombre_club = string;
				$nombre_club = $dbo->getFields("Club", "Nombre", "IDClub = '" . $frm["IDClub"] . "'");
				$TituloNotificacion = "Notificaciones " . $nombre_club;

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {

					$frm["IDModulo"] = 3;
					$frm["TipoNotificacion"] = 'noticias'; //socio
					$frm["Mensaje"] = $frm["Introduccion"];
					if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

					//notiifcar push
					if ($frm["DirigidoA"] == "S") :
						$frm["TipoUsuario"] = 'S'; //socio

						if ($frm["TipoSocio"] == "Socio") {
							$condicion_tipo = " and Socio.TipoSocio = '" . $frm["TipoSocio"] . "'";
						} elseif ($frm["TipoSocio"] == "Estudiante") {
							$condicion_tipo = " and Socio.TipoSocio = '" . $frm["TipoSocio"] . "'";
						}


						//traer socios a los que les interesa la noticia
						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccion WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccion.IDSocio AND SocioSeccion.IDSeccion = '" . $frm["IDSeccion"] . "' and Token <> '' and Token <> '2byte'  " . $condicion_tipo;
						//$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '141131'";

						$qry_socios = $dbo->query($sql_socios);
						while ($r_socios = $dbo->fetchArray($qry_socios)) {

							SIMUtil::envia_cola_notificacion($r_socios, $frm);
							//Guardo el log
							$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . utf8_decode($frm["Titular"]) . "', '" . utf8_decode($frm["Mensaje"]) . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
						} //end while
					elseif ($frm["DirigidoA"] == "E") : //Empleados
						$frm["TipoUsuario"] = 'E'; //socio
						//traer empleados a los que les interesa la noticia
						$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						$qry_empleados = $dbo->query($sql_empleados);
						while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
							SIMUtil::envia_cola_notificacion($r_socios, $frm);
						} //end while
					endif;
				} //end if




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


				$files =  SIMFile::upload($_FILES["NoticiaImagen"], IMGNOTICIA_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["NoticiaImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["NoticiaFile"] = $files[0]["innername"];

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

				$files =  SIMFile::upload($_FILES["Adjunto1Documento"], IMGNOTICIA_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto1File"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto2Documento"], IMGNOTICIA_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto2File"] = $files[0]["innername"];




				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");

				$nombre_club = string;
				$nombre_club = $dbo->getFields("Club", "Nombre", "IDClub = '" . $frm["IDClub"] . "'");
				$TituloNotificacion = "Notificaciones " . $nombre_club;

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					//notiifcar push
					$frm["IDModulo"] = 3;
					$frm["TipoNotificacion"] = 'noticias'; //socio
					$frm["Mensaje"] = $frm["Introduccion"];
					if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

					if ($frm["DirigidoA"] == "S" || $frm["DirigidoA"] == "T") :
						//traer socios a los que les interesa la noticia

						if ($frm["TipoSocio"] == "Socio") {
							$condicion_tipo = " and Socio.TipoSocio = '" . $frm["TipoSocio"] . "'";
						} elseif ($frm["TipoSocio"] == "Estudiante") {
							$condicion_tipo = " and Socio.TipoSocio = '" . $frm["TipoSocio"] . "'";
						}

						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccion WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccion.IDSocio AND SocioSeccion.IDSeccion = '" . $frm["IDSeccion"] . "' and Token <> '' and Token <> '2byte' " . $condicion_tipo;

						//$sql_socios = "SELECT * FROM Socio WHERE (IDSocio = '160553' or IDSocio = 144147)";



						//$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccion WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccion.IDSocio AND SocioSeccion.IDSeccion = '" . $frm["IDSeccion"] . "' and Token <> '' and Token <> '2byte' and Socio.IDSocio = '118543'";
						$frm["TipoUsuario"] = 'S'; //socio
						$qry_socios = $dbo->query($sql_socios);
						while ($r_socios = $dbo->fetchArray($qry_socios)) {
							SIMUtil::envia_cola_notificacion($r_socios, $frm);

							$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . utf8_decode($frm["Titular"]) . "', '" . utf8_decode($frm["Mensaje"]) . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $frm["ID"] . "')");
						} //end while
					elseif ($frm["DirigidoA"] == "E") : //Empleados
						//traer empleados a los que les interesa la noticia
						$frm["TipoUsuario"] = 'E'; //socio
						$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						$qry_empleados = $dbo->query($sql_empleados);
						while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
							SIMUtil::envia_cola_notificacion($r_empleados, $frm);
						} //end while
					endif;
				} //end if


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

		case "DelDocNot":
			$campo = $_GET['cam'];
			$doceliminar = IMGNOTICIA_DIR . $dbo->getFields("Noticia", "$campo", "IDNoticia = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Noticia SET $campo = '' WHERE IDNoticia = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
			exit;
			break;


		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
