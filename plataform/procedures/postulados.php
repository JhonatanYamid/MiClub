 <?

	SIMReg::setFromStructure(array(
		"title" => "Postulados",
		"table" => "Postulado",
		"key" => "IDPostulado",
		"mod" => "Postulado"
	));


	$script = "postulados";

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

					$files =  SIMFile::upload($_FILES["Imagen"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Imagen"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Imagen"] = $files[0]["innername"];

                                     
					$files =  SIMFile::upload($_FILES["ImagenBeneficiario1"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario2"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario3"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario4"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario5"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario6"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario6"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Adjunto1File"], SOCIO_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Adjunto1File"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Adjunto1File"] = $files[0]["innername"];
				} //end if

				//insertamos los datos
				
				  if (empty($frm["Adjunto1File"])) {
                                       SIMHTML::jsAlert("Lo sentimos es obligatorio abjuntar la hoja de vida, intente nuevamente");
				       SIMHTML::jsRedirect($script . ".php");
				       exit;

			break;
                                         }  
  
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

				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Imagen"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Imagen"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Imagen"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["ImagenBeneficiario1"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario2"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario3"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario4"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario5"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["ImagenBeneficiario6"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["ImagenBeneficiario6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["ImagenBeneficiario6"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Adjunto1File"], SOCIO_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Adjunto1File"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Adjunto1File"] = $files[0]["innername"];
				} //end if

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
			$filedelete = SOCIO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		case "DelDocNot":
			$campo = $_GET['cam'];
			$doceliminar = SOCIO_DIR . $dbo->getFields("Postulado", "$campo", "IDPostulado = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Postulado SET $campo = '' WHERE IDPostulado = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'DocumentoEliminadoCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
			exit;
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
