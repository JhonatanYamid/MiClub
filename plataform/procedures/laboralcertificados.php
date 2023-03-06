 <?

	SIMReg::setFromStructure(array(
		"title" => "CertificadosLaborales",
		"table" => "LaboralCertificado",
		"key" => "IDLaboralCertificado",
		"mod" => "Laboral"
	));


	$script = "laboralcertificados";

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

				$files =  SIMFile::upload($_FILES["Archivo"], CLUB_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Archivo"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
				$frm["Archivo"] = $files[0]["innername"];

				$id = $dbo->insert($frm, $table, $key);

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

			break;


		case "edit":

			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$estadoAntiguo = $frm["IDEstado"];
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$files =  SIMFile::upload($_FILES["Archivo"], CLUB_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Archivo"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
				$frm["Archivo"] = $files[0]["innername"];

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");

				if ($frm['IDEstado'] != $frm['estadoAntiguo']) {
					$Mensaje = "Su solicitud de certificado a cambiando de estado";
					$modulo = 118;
					SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDUsuario"], $Mensaje, $modulo, "");
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Seenvionotificaciónalusuariosolicitante', LANGSESSION));
				}

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

		case "delDoc":
			$foto = $_GET['doc'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = CLUB_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = " . $_GET[id] . "   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'DocumentoEliminadoCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
